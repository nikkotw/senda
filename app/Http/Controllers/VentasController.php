<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;
use App\Productos;
use App\Venta;
use App\Log;
use App\DetalleVenta;
use App\Events\VentaCreada;
use App\Events\VentaAutorizada;
use App\Events\VentaEliminada;
use DB;


class VentasController extends Controller
{
    //Retorna la vista principal
    public function index(){
        return view('facturacion.ventas');
    }

    //Obtener los productos con stock
    public function getProductosStock(Request $request){

        
        $string = $request->producto;

        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY); 

        $productos = Productos::where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->Where('Descripcion', 'like', "%{$value}%");
                $q->orWhere('codBarras', 'like', "%{$value}%");
               
                
            }
            })->Where('stock', '>', 0)      
            ->orderBy('IdProducto','DESC')
            ->paginate(10);
        /*
        $productos = DB::table('productoscombinacionescabecera')
        ->where(function ($query) use ($request) {
            $query->where('Descripcion','like', '%' . $request->producto . '%')
            ->orWhere('CodBarras','like', '%' . $request->producto . '%');
        })
        ->where('stock','>',0)
        ->orderBy('IdProducto', 'DESC')
        ->paginate(10);
        */
        return $productos;
    }

    //Guardar las ventas para procesar en caja
    public function saveVentas(Request $request){
        try {

            DB::beginTransaction();
            $venta = new Venta();

            $venta->total = $request->total;
            $venta->fecha = date('Y-m-d');
            $venta->descuento = $request->descuento;
            $venta->iduser = $request->userid;
            if($request->autorizar){
                $venta->estado = 1;
            }else{
                $venta->estado = 0;
            }

            $venta->tipo_venta = $request->tipo_venta;

            if($request->tipo_venta == 'Factura_A' || $request->tipo_venta == 'Factura_B' || $request->datosRequeridos){
                $venta->cliente = $request->cliente;
                $venta->cuit = $request->cuit;
                $venta->domicilio = $request->domicilio;
            }

            $venta->save();

            $id_venta = $venta->id; //Id de la venta guardada recientemente

            //$id_venta;
            //Guardar detalles de la venta y descontar stock de los productos
            foreach($request->detalleVenta as $detalle){
                $detalleVenta = new DetalleVenta();

                $detalleVenta->id_venta = $id_venta;
                $detalleVenta->id_producto = $detalle['IdProducto'];
                $detalleVenta->cantidad = $detalle['cantidad'];
                $detalleVenta->subtotal = $detalle['subtotal'];
                $detalleVenta->precio_venta = $detalle['precio_venta'];
                $detalleVenta->restantesNotaCredito = $detalle['cantidad'];

                $productoDescontarStock = Productos::find($detalle['IdProducto']);
                $productoDescontarStock->stock = $productoDescontarStock->stock - $detalle['cantidad'];

                $productoDescontarStock->save();
                $detalleVenta->save();
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Realizo Nueva Venta - " .$id_venta;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();



            DB::commit();
            event(new VentaCreada());
            event(new VentaAutorizada());

            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }

    }

    //Eliminar una venta junto a su detalle
    public function deleteVentas(Request $request){
        try {
            DB::beginTransaction();

            $venta = Venta::find($request->id);
            $detalleVenta = DetalleVenta::where('id_venta','=',$venta->id)->get();

            foreach ($detalleVenta as $detalle){
                $producto = Productos::find($detalle->id_producto);
                $producto->stock = $producto->stock + $detalle->cantidad;
                $producto->save();

                $detalle->delete();
            }

            $venta->delete();
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Rechaza Venta - " .$venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new VentaEliminada());
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
        }
    }
}
