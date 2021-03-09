<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cliente;
use App\Venta;
use App\Caja;
use App\Log;
use App\DetalleVenta;
use App\MovimientosCC;
use App\MovimientosCaja;
use App\ChequesVentas;
use App\Http\Controllers\CajaController;
use DB;
use function GuzzleHttp\json_encode;

class IngresoCobranzaController extends Controller
{
    public function index(){
        return view('cuentaCorriente.IngresoCobranza');
    }

    public function getListadoClientesCuentaCorriente(Request $request){

        if($request->filtro == ''){
            $clientes = Cliente::where('cuenta_corriente','=',1)->paginate(10);
        }

        if($request->filtro == 'cuit'){
            $clientes = DB::table('clientes')
            ->where('cuenta_corriente','=',1)
            ->where(function ($query) use ($request) {
                $query->where('CUIT','like', '%' . $request->cuit . '%');
            })
            ->paginate(10);
        }

        if($request->filtro == 'nombre'){
            $clientes = DB::table('clientes')
            ->where('cuenta_corriente','=',1)
            ->where(function ($query) use ($request) {
                $query->where('Nombre','like', '%' . $request->cliente . '%');
            })
            ->paginate(10);
        }

        if($request->filtro == 'deudores'){
            $clientes = DB::table('clientes')
            ->where('cuenta_corriente','=',0)
            ->where('total_cc','>',0)
            ->paginate(10);
        }

        if($request->filtro == 'saldosFavor'){
            $clientes = DB::table('clientes')
            ->where('cuenta_corriente','=',1)
            ->where(function ($query) use ($request) {
                $query->where('total_cc','<',0);
            })
            ->paginate(10);
        }

        return $clientes;
    }

    public function getListadoVentasClienteCC(Request $request){
        
        $ventas = Venta::where('idcliente','=',$request->idcliente)->where("tipo_venta","=","Cuenta_Corriente")->orderBy('id','DESC')->paginate(5);
           
        $i = 0;

        foreach ($ventas as $venta) {
            $detalles = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera','productoscombinacionescabecera.IdProducto','=','detalle_ventas.id_producto')
            ->select('detalle_ventas.cantidad','detalle_ventas.subtotal','detalle_ventas.precio_venta','productoscombinacionescabecera.Descripcion')
            ->where('id_venta','=',$venta->id)->get();

            $venta = [
                'id' => $venta->id,
                'factura' => $venta->factura,
                'cliente' => $venta->cliente,
                'fecha' => $venta->fecha,
                'cuit' => $venta->cuit,
                'domicilio' => $venta->domicilio,
                'total' => $venta->total,
                'estado' => $venta->estado,
                'tipo_venta' => $venta->tipo_venta,
                'condicion_venta' => $venta->condicion_venta,
                'detalles' => $detalles
            ];

            $ventas[$i] = $venta;

            $i++;

        }
        //dd($ventas);
        return $ventas;
    }

    public function getListadoCajasAbiertas(Request $request){
        $cajas = Caja::where('estado','=',1)->get();
        return $cajas;
    }

    public function realizarIngresoCobranza(Request $request){
        try {
            DB::beginTransaction();

            //Ingreso de cobranza por débito y crédito
            if($request->condicion_venta == "Débito" || $request->condicion_venta == "Crédito"){
                // Generar un movimiento de cuenta corriente de ingreso //
                $movimiento = new MovimientosCC();
                $movimiento->ingreso = $request->monto;
                $movimiento->fecha = date('d-m-Y');
                $movimiento->id_cliente = $request->id_cliente;
                $movimiento->condicion_venta = $request->condicion_venta;


                $cliente = Cliente::where('id','=',$request->id_cliente)->first(); //Buscar el cliente que realizó el ingreso de cobranza
                $cliente->total_cc = $cliente->total_cc - $request->monto; //Modificar el saldo del cliente

                $cliente->save(); //Guardar los datos en el cliente
                $movimiento->save(); //Crear el movimiento
            }

            //Ingreso de cobranza por cheque
            if($request->condicion_venta == "Cheque"){

                //Crear un cheque
                $cheque = new ChequesVentas();
                $cheque->id_cliente = $request->id_cliente;
                $cheque->tipo_cheque = $request->datosCheque['tipo_cheque'];
                $cheque->numero_cheque = $request->datosCheque['numero_cheque'];
                $cheque->fecha_emision = $request->datosCheque['fecha_emision'];
                $cheque->banco = $request->datosCheque['banco'];

                if($request->datosCheque['tipo_cheque'] == "Diferido"){
                    $cheque->fecha_diferido = $request->datosCheque['fecha_diferido'];
                }

                $cheque->save(); //Guardar los datos del cheque

                $id_cheque = $cheque->id; //Obtener el id del cheque guardado

                //Generar el movimiento de cuenta corriente de ingreso
                $movimiento = new MovimientosCC();
                $movimiento->ingreso = $request->monto;
                $movimiento->fecha = date('d-m-Y');
                $movimiento->id_cliente = $request->id_cliente;
                $movimiento->condicion_venta = $request->condicion_venta;
                $movimiento->id_cheque = $id_cheque;

                $cliente = Cliente::where('id','=',$request->id_cliente)->first(); //Buscar el cliente que realizó el ingreso de cobranza
                $cliente->total_cc = $cliente->total_cc - $request->monto; //Modificar el saldo del cliente

                $cliente->save(); //Guardar los datos en el cliente
                $movimiento->save(); //Crear el movimiento
            }

            //Ingreso de cobranza por efectivo
            if($request->condicion_venta == "Efectivo"){
                //Generar el movimiento de cuenta corriente de ingreso
                $movimientoCC = new MovimientosCC();
                $movimientoCC->ingreso = $request->monto;
                $movimientoCC->fecha = date('d-m-Y');
                $movimientoCC->id_cliente = $request->id_cliente;
                $movimientoCC->condicion_venta = $request->condicion_venta;

                $movimientoCC->save();

                $id_movimiento_cc = $movimientoCC->id;

                $cliente = Cliente::where('id','=',$request->id_cliente)->first(); //Buscar el cliente que realizó el ingreso de cobranza
                $cliente->total_cc = $cliente->total_cc - $request->monto; //Modificar el saldo del cliente

                $cliente->save();

                $caja = Caja::where('id','=',1)->first(); //Buscar la caja en la que se almacena el efectivo
                $caja->total = $caja->total + $request->monto; //Modificar el total de efectivo que tiene la caja

                $caja->save();

                $movimientoCaja = new MovimientosCaja();
                $movimientoCaja->id_caja = 1;
                $movimientoCaja->id_movimiento_cc = $id_movimiento_cc;
                $movimientoCaja->ingreso = $request->monto;
                $movimientoCaja->fecha = date('d-m-Y');

                $movimientoCaja->save();
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Realizó ingreso de cobranza ".$request->condicion_venta."- " . $request->monto;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
        }

    }

    public function getIngresosCobranza(Request $request){
        $ingresos = MovimientosCC::where('id_cliente','=',$request->id)->where('ingreso','>',0)->paginate(10);

        return $ingresos;
    }
    function facturaVentas(Request $request){
        try{
            $ventas = $request->ventas;
            foreach($ventas as $item){
                $detalleVentas = DetalleVenta::where('id_venta','=',$item)->get();
                
                foreach($detalleVentas as $detalle){
                    $detalle->subtotal = $detalle->subtotal + (($detalle->subtotal*$request->porcentual)/100);
                    $detalle->save();
                }
                $venta = Venta::where('id','=',$item)->first();
                $venta->descuento = $request->descuentoEfectivo;
                $venta->total = $venta->total + (($venta->total*$request->porcentual)/100);
                $venta->total = $venta->total - $request->descuentoEfectivo;
                $venta->save();
                
                $cliente = Cliente::where('id','=',$venta->idcliente)->first();
                $factura = new CajaController;
                if($cliente->IVA_tipo==0){                
                    $factura->facturaA($venta->id,$venta->idcliente);
                    
                }else{
                    $factura->facturaB($venta->id,$venta->idcliente);
                }
             
                
            }

                 $log = new Log;
                $log->idpermiso = 99;
                $log->idusuario = 1;
                $log->detalle = "Facturo Ventas CC: ". json_encode($ventas) ;
                $log->fecha  = date('Y-m-d');
                $log->hora  = date("H:i:s");
                $log->save();
                return 0;
        }catch(Throwable $error){
            $log = new Log;
            $log->idpermiso = 99;
            $log->idusuario = 1;
            $log->detalle = "Error AL Facturar VENTAS CC: " . $error->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

        }

        
        return 1 ;

    }

}
