<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;
use App\Cliente;
use App\Productos;
use App\MovimientosCC;
use App\DetalleVenta;
Use App\Log;
use App\Events\VentaAutorizada;
use App\Events\VentaEliminada;
use DB;

class AutorizarVentaCCController extends Controller
{
    //Retorna la vista
    public function index(){
        return view('cuentaCorriente.autorizarVentaCuentaCorriente');
    }

    //Obtener listado de ventas no autorizadas
    public function getVentasNoAutorizadas(){
        $ventas = DB::table('ventas')
        ->join('clientes','clientes.CUIT','=','ventas.cuit')
        ->select('ventas.*','clientes.monto_maximo_cc','clientes.total_cc')
        ->where('ventas.estado','=',1)
        ->get();

        return $ventas;
    }

    //Obtener el detalle de una venta específica
    public function getDetalleVentaNoAutorizada(Request $request){
        $detalleVenta = DB::table('detalle_ventas')
        ->join('productoscombinacionescabecera','productoscombinacionescabecera.IdProducto','=','detalle_ventas.id_producto')
        ->select('detalle_ventas.*','productoscombinacionescabecera.Descripcion','productoscombinacionescabecera.stock')
        ->where('detalle_ventas.id_venta','=',$request->id)
        ->get();

        return $detalleVenta;
    }

    //Autorizar una venta de cuenta corriente
    public function autorizarVenta(Request $request){
        try {
            DB::beginTransaction();
            $venta = Venta::where("id","=",$request->id)->first();
            $venta->estado = 2;
            $venta->save();


            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Autoriza Venta  CC- " .$venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new VentaAutorizada());
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }



    //Movimientos de cuenta corriente guardados en los clientes
    public function movimientosToCliente(Request $request){
        try {

            ini_set('max_execution_time', 1800);

            DB::beginTransaction();
            //SELECT id_cliente, SUM(ingreso), SUM(egreso) FROM movimientos_cc GROUP BY id_cliente

            $movimientos = DB::table('ctacte')
            ->select('IdCliente',DB::raw('SUM(debe) as debe'),DB::raw('SUM(haber) as haber'))
            ->groupBy('IdCliente')
            ->get();

            foreach($movimientos as $movimiento){
                $cliente = Cliente::where("IdCliente","=",$movimiento->IdCliente)->first();
                if($cliente != null){
                    $cliente->total_cc = $movimiento->debe - $movimiento->haber;
                    $cliente->save();
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }


    }

    //Editar los cuits para pasarlos a INT
    public function editCuits(Request $request){
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {
            $cliente->cuit = substr($cliente->CUIT,0,2).substr($cliente->CUIT,3,8).substr($cliente->CUIT,-1);
            $cliente->save();
        }

        foreach ($clientes as $cliente) {

            if($cliente->CUIT == "           " || $cliente->CUIT == ""){
                $cliente->CUIT = null;
                $cliente->save();
            }
        }
    }
}
