<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PedidoProveedor;
use App\Proveedor;
use App\Log;
use App\PagoProveedor;
use App\Events\PedidoTable;
use App\Events\PagosTable;
use App\Productos;
use App\DetallePedidoProveedor;
use PDF;
use Response;

class PedidoProveedorController extends Controller
{
    //Retorna la vista

    public function index(){
        return view('pedidosProveedores.pedidosProveedores');
    }

    public function indexGestion(){
        return view('pedidosProveedores.gestionPedidos');
    }

    //Editar precio de venta 1
    public function editPrecioVenta1(Request $request){
        try {
            DB::beginTransaction();
            $producto = Productos::where('IdProducto','=',$request->id)->first();
            $tmp_precio=$producto->precio_venta;
            $producto->precio_venta_1 = $request->precio_venta_1;
            $producto->save();
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Edita Precio Venta 1- " . $tmp_precio ."|por |" . $request->precio_venta_1 ;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
    //Editar precio de venta 2
    public function editPrecioVenta2(Request $request){
        try {
            DB::beginTransaction();
            $producto = Productos::where('IdProducto','=',$request->id)->first();
            $tmp_precio=$producto->precio_venta_2;
            $producto->precio_venta_2 = $request->precio_venta_2;
            $producto->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Edita Precio Venta 2  - " . $tmp_precio ."|por |" . $request->precio_venta_2 ;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }
    //Editar precio de venta 3
    public function editPrecioVenta3(Request $request){
        try {
            DB::beginTransaction();
            $producto = Productos::where('IdProducto','=',$request->id)->first();
            $tmp_precio=$producto->precio_venta_3;
            $producto->precio_venta_3 = $request->precio_venta_3;
            $producto->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Edita Precio Venta 3  - " . $tmp_precio ."|por |" . $request->precio_venta_3 ;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();



            DB::commit();


            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    //Obtener listado de productos de un proveedor específico
    public function getListadoProductos(Request $request){
        $productos=Productos::where('id_proveedor','=',$request->idProveedor)
        ->where(function ($query) use ($request) {
            $query->where('Descripcion','like', '%' . $request->productoBuscado . '%')
            ->orWhere('CodBarras','like', '%' . $request->productoBuscado . '%');
        })
        ->paginate(5);

        return $productos;
    }

    //Guardar un pedido con los detalles respectivos
    public function createPedidoAndDetalles(Request $request){

        try {
            DB::beginTransaction();
            $pedidoProveedor = new PedidoProveedor();

            $pedidoProveedor->idProveedor = $request->idProveedor;
            $pedidoProveedor->cuit = $request->cuit;
            $pedidoProveedor->fecha = $request->fecha;
            $pedidoProveedor->total = $request->total;
            $pedidoProveedor->saldo = 0-$request->total;
            $pedidoProveedor->fecha_arribo = $request->fecha_arribo;

            $pedidoProveedor->save();

            $idPedido = $pedidoProveedor->id; //Obtener el id del pedido al que vamos a asociar los detalles

            //Guardar detalles del pedido
            foreach($request->detallePedido as $detalle){
                $detallePedido = new DetallePedidoProveedor();

                $detallePedido->id_pedido = $idPedido;
                $detallePedido->id_producto = $detalle['IdProducto'];
                $detallePedido->cantidad = $detalle['cantidad'];
                $detallePedido->precio_costo = $detalle['precio_costo'];
                $detallePedido->subtotal = $detalle['subtotal'];

                $detallePedido->save();
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Crea Pedido Proveedor Nro: " .$idPedido;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            event(new PedidoTable());
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }


    }

    //Seleccionar todos los pedidos de un proveedor específicos
    public function getPedidos(Request $request){

        if($request->filtroBusqueda == "cuit"){
            $pedidos = PedidoProveedor::select('pedidos_proveedores.*','proveedores.nombre')->where('pedidos_proveedores.cuit','like','%'.$request->busqueda.'%')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }elseif($request->filtroBusqueda == "saldo_contra"){
            $pedidos = PedidoProveedor::where('saldo','<',0)->select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }elseif($request->filtroBusqueda == "saldo_favor"){
            $pedidos = PedidoProveedor::where('saldo','>',0)->select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }elseif($request->filtroBusqueda == "fecha_pedido"){
            $pedidos = PedidoProveedor::where('fecha','=',$request->busqueda)->select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }elseif($request->filtroBusqueda == "fecha_arribo"){
            $pedidos = PedidoProveedor::where('fecha_arribo','=',$request->busqueda)->select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }elseif($request->filtroBusqueda == ""){
            $pedidos = PedidoProveedor::select('pedidos_proveedores.*','proveedores.nombre')->join('proveedores','proveedores.id','=','pedidos_proveedores.idProveedor')->paginate(10);
        }

        return $pedidos;
    }

    //Tomar toda la información relacionada a un pedido
    public function getDetallePedido(Request $request){
        $detallePedido = DetallePedidoProveedor::where('id_pedido',$request->id)
        ->join('productoscombinacionescabecera','productoscombinacionescabecera.IdProducto','=','detalle_pedido_proveedor.id_producto')
        ->select('detalle_pedido_proveedor.*','productoscombinacionescabecera.Descripcion')
        ->get();
        return $detallePedido;
    }

    //Eliminar pedidos y detalles
    public function deletePedido(Request $request){
        try {
            DB::beginTransaction();
            $pedido = PedidoProveedor::where('id',$request->id)->first();
            $detallePedido = DetallePedidoProveedor::where('id_pedido',$request->id)->get();

            foreach ($detallePedido as $detalle) {
                $detalle->delete();
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Elimina Pedido Proveedor Nro: " .$request->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();


            $pedido->delete();
            DB::commit();
            event(new PedidoTable());
            return;
        } catch (\Throwable $th) {
            return $th;
            DB::rollback();
        }
    }

    //Almacenar los pagos y actualizar los saldos
    public function createPago(Request $request){
        try {
            DB::beginTransaction();
            $pago = new PagoProveedor();

            $pago->idPedido = $request->idPedido;
            $pago->monto = $request->datosPago["monto"];
            $pago->fecha = $request->datosPago["fechaPago"];
            $pago->medio_pago = $request->datosPago["medioPago"];
            $pago->obs = $request->datosPago["obs"];

            $pago->save();

            $pedido = PedidoProveedor::where("id",$request->idPedido)->first();
            $pedido->saldo = $pedido->saldo + $request->datosPago["monto"];
            $pedido->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Crea Pago Proveedor - pedido Nro: " .$request->idPedido;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new PagosTable($request->idPedido));
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public function getPagos(Request $request){
        $pagos = PagoProveedor::where('idPedido',$request->id)->get();
        return $pagos;
    }

    //Eliminar los pedidos
    public function deletePago(Request $request){
        try {
            DB::beginTransaction();
            $pago_pedido = PagoProveedor::find($request->id);
            $pedido = PedidoProveedor::find($pago_pedido->idPedido);
            $pedido->saldo = $pedido->saldo - $pago_pedido->monto;

            $pedido->save();
            $pago_pedido->delete();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Elimina Pago Proveedor - pedido Nro: " .$request->idPedido;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            
            DB::commit();


            event(new PagosTable($pedido->id));
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }

    }

    //Generar PDF
    public function generarPDF(Request $request){

        $detallePedido = json_decode($request->detallePedido);

        $proveedor = DB::table('proveedores')
        ->select('proveedores.nombre')
        ->where('proveedores.id','=',$request->idProveedor)
        ->get();

        $proveedor = $proveedor[0]->nombre;

        $fecha_pedido = date('d-m-y',strtotime($request->fecha_pedido));

        if($request->fecha_arribo == '0-0-0'){
            $fecha_arribo = "Sin especificar";
        }else{
            $fecha_arribo = date('d-m-y',strtotime($request->fecha_arribo));
        }


        $total = $request->total;

        $pdf = PDF::loadView('pdf.pedidos',compact('detallePedido','proveedor','fecha_pedido','fecha_arribo','total'))->setPaper('a4', 'landscape');

        return $pdf->download('pedido.pdf');

    }
}
