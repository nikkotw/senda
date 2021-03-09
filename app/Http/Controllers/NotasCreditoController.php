<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cliente;
use App\Venta;
use App\NotasCredito;
use App\DetalleVenta;
use App\Log;
use DB;
use App\DetalleNotaCredito;
use App\Productos;
class NotasCreditoController extends Controller
{
    public function index(){
        return view('notasCredito.notasCredito');
    }

    public function getVentaPorFactura(Request $request){
        $venta = Venta::where('factura','=',$request->nroFactura)->first();
         
        if($venta==null){
                     $venta = Venta::where('id','=',$request->nroFactura)->first();
            }
        if($venta == null){
            return 0;
        }else{
            if($venta->cuit != ""){
                $cliente = Cliente::where('CUIT','=',$venta->cuit)->first();
                return([
                    'venta' => $venta,
                    'cliente' => $cliente
                ]);
            }else{
                return([
                    'venta' => $venta,
                    'cliente' => 0
                ]);
            }
        }
   }

    public function registrarClienteNotasCredito(Request $request){
        $this->validate($request, [
            'razonSocial' => 'required',
            'cuit' => 'required|unique:clientes|numeric',
            'telefono' => 'required|numeric',
            'email' => 'nullable|email:rfc',
            'direccion' => 'required',
            'ciudad' => 'required',
            'cp' => 'required|numeric',
            'obs' => 'nullable'

        ],[
            'razon.required'=>'Debe ingresar la razón social del cliente',
            'cuit.required'=>'Debe ingresar el CUIT del cliente',
            'cuit.unique' =>'El CUIT ingresado ya pertenece a un cliente',
            'cuit.numeric' =>'El CUIT solo puede contener caracteres numéricos',
            'telefono.required'=>'Debe ingresar el teléfono del cliente',
            'telefono.numeric'=>'El teléfono solo puede contener caracteres numéricos',
            'email.email'=>'Ingrese un correo electrónico con un formato válido',
            'direccion.required'=>'Debe ingresar la direccion del cliente',
            'ciudad.required'=>'Debe ingresar la ciudad del cliente',
            'cp.required'=>'Debe ingresar el codigo postal de la ciudad donde reside el cliente',
            'cp.numeric'=>'El código postal solo puede contener caracteres numéricos'
        ]);

        try {
            DB::beginTransaction();

            $cliente = new Cliente();
            $cliente->Nombre = $request->razonSocial;
            $cliente->CUIT = $request->cuit;
            $cliente->Telefono = $request->telefono;
            $cliente->Mail = $request->email;
            $cliente->Domicilio = $request->direccion;
            $cliente->Localidad = $request->ciudad;
            $cliente->CP = $request->cp;
            $cliente->Alta = date('d-m-Y');
            $cliente->Activo = 'A';

            $cliente->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Registra Cliente por Nota de credito  - " . $request->razonSocial;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            /*
            $venta = Venta::where('id','=',$request->idVenta)->first();
            $venta->cliente = $cliente->Nombre;
            $venta->cuit = $cliente->CUIT;
            $venta->domicilio = $cliente->Domicilio;

            $venta->save();
            */

            DB::commit();
            return;
        } catch (\Throwable $th) {
            return;
        }



    }

    public function getClientesNotaCredito(Request $request){
        if ($request->filtro == 'nombre') {
            $clientes = Cliente::where('Nombre', 'like', '%'.$request->busqueda.'%')->orderBy('id', 'DESC')->paginate(10);
        }elseif ($request->filtro == 'cuit') {
            $clientes = Cliente::where('CUIT', 'like', '%'.$request->busqueda.'%')->orderBy('id', 'DESC')->paginate(10);
        }else{
            $clientes = Cliente::orderBy('id', 'DESC')->paginate(10);
        }

        return $clientes;
    }

    public function getDetalleVentaNotaCredito(Request $request){
        $detalles = DB::table('detalle_ventas')
        ->join('productoscombinacionescabecera','productoscombinacionescabecera.IdProducto','=','detalle_ventas.id_producto')
        ->select('detalle_ventas.cantidad','detalle_ventas.subtotal','detalle_ventas.precio_venta','detalle_ventas.restantesNotaCredito','productoscombinacionescabecera.Descripcion','productoscombinacionescabecera.IdProducto')
        ->where('id_venta','=',$request->id)
        ->where('notaCredito','!=',1)
        ->get();

        return $detalles;
    }

    public function emitirNotaCredito(Request $request){
        try {
            DB::beginTransaction();
            $notaCredito = new NotasCredito();
            $notaCredito->id_venta = $request->datosVenta['id'];
            $notaCredito->fecha = date('d-m-y');
            $notaCredito->id_cliente = $request->cliente['id'];
            $notaCredito->total = $request->total;

            $notaCredito->save();


            foreach ($request->detalle as $detalle) {
                $detalleNotaCredito = new DetalleNotaCredito();
                $detalleNotaCredito->id_producto = $detalle["IdProducto"];
                $detalleNotaCredito->id_nota_credito = $notaCredito->id;
                $detalleNotaCredito->precio_venta = $detalle["precio_venta"];
                $detalleNotaCredito->cantidad = $detalle["cantidadSelect"];
                $detalleNotaCredito->subtotal =  $detalle["cantidadSelect"] * $detalle["precio_venta"];

                $detalleNotaCredito->save();

                $detalleVenta = DetalleVenta::where("id_venta","=",$request->datosVenta['id'])->where("id_producto",'=',$detalle["IdProducto"])->first();
                $detalleVenta->restantesNotaCredito = $detalleVenta->restantesNotaCredito - $detalle["cantidadSelect"];

                if($detalleVenta->restantesNotaCredito != 0){
                    $detalleVenta->notaCredito = 2;
                }else{
                    $detalleVenta->notaCredito = 1;
                }

                $detalleVenta->save();

                $producto = Productos::where('IdProducto',"=",$detalle["IdProducto"])->first();
                $producto->stock = $producto->stock + $detalle["cantidadSelect"];

                $producto->save();

                if($request->datosVenta['cliente'] == ""){
                    $venta = Venta::where('id','=',$request->datosVenta['id'])->first();

                    $venta->cliente = $request->cliente["Nombre"];
                    $venta->cuit = $request->cliente["CUIT"];
                    $venta->domicilio = $request->cliente["Domicilio"];

                    $venta->save();
                }

                $cliente = Cliente::where("id","=",$request->cliente["id"])->first();
                $cliente->total_cc = $cliente->total_cc - $request->total;

                $cliente->save();
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Realizo Nota de credito nro:  - " . $notaCredito->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
}
