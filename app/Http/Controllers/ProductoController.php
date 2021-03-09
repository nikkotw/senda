<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Productos;
use App\Proveedor;
use App\DetalleTransferStock;
use App\Sucursales;
use App\Log;
use PDF;

class ProductoController extends Controller
{
    //Api que devuelve el listado de productos.
    function getProductos(Request $request){
        $string = $request->producto;

        // split on 1+ whitespace & ignore empty (eg. trailing space)
        $searchValues = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY); 

        $productos = Productos::where(function ($q) use ($searchValues) {
        foreach ($searchValues as $value) {
            $q->Where('Descripcion', 'like', "%{$value}%");
            
            
        }
        })->orWhere('CodBarras','like','%'.$string.'%')        
        ->orderBy('IdProducto','DESC')
        ->paginate(10);


        /*
      
        */
        return $productos ;
    }

    //Retornar la vista
    public function index(){
        return view('productos.index');
    }

    //Crear producto
    public function saveProducto(Request $request){
        $this->validate($request, [
            'CodBarras' => 'required|numeric|unique:productoscombinacionescabecera',
            'descripcion' => 'required',
            'stock' => 'required|numeric',
            'iva' => 'required|numeric',
            'precio_costo' => 'required|numeric',
            'precio_venta_1' => 'required|numeric',
            'precio_venta_2' => 'nullable|numeric',
            'precio_venta_3' => 'nullable|numeric',
            'id_proveedor' => 'nullable|numeric',
        ],[
            'CodBarras.unique'=>'El código de barras ya corresponde a un producto cargado en el sistema',
        ]);

        try {
            DB::beginTransaction();
            $producto = new Productos();
            $producto->CodBarras = $request->CodBarras;
            $producto->Descripcion = $request->descripcion;
            $producto->stock = $request->stock;
            $producto->IVA = $request->iva;
            $producto->precio_costo = $request->precio_costo;
            $producto->precio_venta_1 = $request->precio_venta_1;
            $producto->precio_venta_2 = $request->precio_venta_2;
            $producto->precio_venta_3 = $request->precio_venta_3;
            $producto->id_proveedor = $request->id_proveedor;
            $producto->modif_precio = date('Y-m-d');

            $producto->save();
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

    }

    public function getInfoEditProducto(Request $request){

        $producto = DB::table('productoscombinacionescabecera')
        ->select('productoscombinacionescabecera.*')
        ->where('productoscombinacionescabecera.IdProducto','=',$request->id)
        ->first();

        if($producto->id_proveedor != null){
            $producto = DB::table('productoscombinacionescabecera')
            ->join('proveedores','proveedores.id', '=','productoscombinacionescabecera.id_proveedor')
            ->select('productoscombinacionescabecera.*')
            ->where('productoscombinacionescabecera.IdProducto','=',$request->id)
            ->first();
        }

        return json_encode($producto);
    }

    public function editProducto(Request $request){
        $this->validate($request, [
            'CodBarras' => 'required|unique:productoscombinacionescabecera,CodBarras, '. $request->IdProducto.',IdProducto',
            'descripcion' => 'required',
            'stock' => 'required|numeric',
            'iva' => 'required|numeric',
            'precio_costo' => 'required|numeric',
            'precio_venta_1' => 'required|numeric',
            'precio_venta_2' => 'nullable|numeric',
            'precio_venta_3' => 'nullable|numeric',
            'id_proveedor' => 'nullable|numeric',
        ],[
            'CodBarras.unique'=>'El código de barras ya corresponde a un producto cargado en el sistema',
        ]);

        try {
            DB::beginTransaction();
            $producto = Productos::where("IdProducto","=",$request->IdProducto)->first();
            $producto->CodBarras = $request->CodBarras;
            $producto->Descripcion = $request->descripcion;
            $producto->stock = $request->stock;
            $producto->IVA = $request->iva;
            $producto->precio_costo = $request->precio_costo;

            if(!($producto->precio_costo == $request->precio_costo) || !($producto->precio_venta_1 == $request->precio_venta_1) ||
            !($producto->precio_venta_2 == $request->precio_venta_2) || !($producto->precio_venta_3 == $request->precio_venta_3)){
                $producto->modif_precio = date('Y-m-d');
            }
            $producto->precio_venta_1 = $request->precio_venta_1;
            $producto->precio_venta_2 = $request->precio_venta_2;
            $producto->precio_venta_3 = $request->precio_venta_3;

            $producto->id_proveedor = $request->id_proveedor;

            $producto->save();
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function incrementarStock(Request $request){
        try {
            DB::beginTransaction();
            $producto = Productos::where("IdProducto","=",$request->id)->first();
            $producto->stock = $producto->stock + $request->stock;
            $producto->save();


            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Sumo Stock - Producto: " .$producto->Descripcion ." - Cantidad:".$request->stock ;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function deleteProducto(Request $request){
        try {
            DB::beginTransaction();
            $producto = Productos::where("IdProducto","=",$request->id)->first();
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Elimina Producto: " .$producto;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            $producto->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    //Mostrar todos los productos
    public function readProductos(Request $request){

        $productos = DB::table('productoscombinacionescabecera')
        ->join('proveedores','proveedores.id', '=','productoscombinacionescabecera.id_proveedor')
        ->select('proveedores.nombre','productoscombinacionescabecera.descripcion','productoscombinacionescabecera.stock',
        'productoscombinacionescabecera.precio_costo','productoscombinacionescabecera.precio_venta_1',
        'productoscombinacionescabecera.precio_venta_2','productoscombinacionescabecera.precio_venta_3')
        ->where('productoscombinacionescabecera.descripcion','like','%'.$request->productoEspecifico.'%')
        ->paginate(5);

        return [
            'pagination' => [
                'total' => $productos->total(),
                'current_page' => $productos->currentPage(),
                'per_page' => $productos->perPage(),
                'last_page' => $productos->lastPage(),
                'from' => $productos->firstItem(),
                'to' => $productos->lastPage(),
            ],
            'productos' => $productos
        ];
    }

    //Productos proveedor específico
    public function readProductosEspecificos(Request $request){

        $productos = DB::table('productoscombinacionescabecera')
        ->join('proveedores','proveedores.id', '=','productoscombinacionescabecera.id_proveedor')
        ->select('proveedores.nombre','productoscombinacionescabecera.IdProducto','productoscombinacionescabecera.descripcion','productoscombinacionescabecera.stock',
        'productoscombinacionescabecera.precio_costo','productoscombinacionescabecera.precio_venta_1',
        'productoscombinacionescabecera.precio_venta_2','productoscombinacionescabecera.precio_venta_3')
        ->where('productoscombinacionescabecera.id_proveedor','=',$request->idProveedor)
        ->where('productoscombinacionescabecera.descripcion','like','%'.$request->productoEspecifico.'%')
        ->paginate(5);

        return [
            'pagination' => [
                'total' => $productos->total(),
                'current_page' => $productos->currentPage(),
                'per_page' => $productos->perPage(),
                'last_page' => $productos->lastPage(),
                'from' => $productos->firstItem(),
                'to' => $productos->lastPage(),
            ],
            'productos' => $productos
        ];

    }

    //Aumento de precio de productos
    public function aumentoProducto(Request $request){

        $productos = DB::table('productoscombinacionescabecera')
        ->join('proveedores','proveedores.id', '=','productoscombinacionescabecera.id_proveedor')
        ->select('productoscombinacionescabecera.IdProducto','productoscombinacionescabecera.precio_costo','productoscombinacionescabecera.precio_venta_1','productoscombinacionescabecera.precio_venta_2','productoscombinacionescabecera.precio_venta_3')
        ->where('productoscombinacionescabecera.id_proveedor','=',$request->idProveedor)
        ->get();

        foreach($productos as $producto){
            $productoUpdate = Productos::where('IdProducto','=',$producto->IdProducto)->first();

            $productoUpdate->precio_costo = $producto->precio_costo+($producto->precio_costo*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_1 = $producto->precio_venta_1+($producto->precio_venta_1*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_2 = $producto->precio_venta_2+($producto->precio_venta_2*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_3 = $producto->precio_venta_3+($producto->precio_venta_3*(($request->porcentaje)/100));
            $productoUpdate->modif_precio = date('Y-m-d');
            $productoUpdate->save();
        }
        $log = new Log;
        $log->idpermiso = 100;
        $log->idusuario = $request->userid;
        $log->detalle = "Aumento de Precios Masivo %: " .$request->porcentaje;
        $log->fecha  = date('Y-m-d');
        $log->hora  = date("H:i:s");
        $log->save();

    }

    //Baja de precio de productos
    public function bajarProducto(Request $request){
        $productos = DB::table('productoscombinacionescabecera')
        ->join('proveedores','proveedores.id', '=','productoscombinacionescabecera.id_proveedor')
        ->select('productoscombinacionescabecera.IdProducto','productoscombinacionescabecera.precio_costo','productoscombinacionescabecera.precio_venta_1','productoscombinacionescabecera.precio_venta_2','productoscombinacionescabecera.precio_venta_3')
        ->where('productoscombinacionescabecera.id_proveedor','=',$request->idProveedor)
        ->get();

        foreach($productos as $producto){
            $productoUpdate = Productos::where('IdProducto','=',$producto->IdProducto)->first();

            $productoUpdate->precio_costo = $producto->precio_costo-($producto->precio_costo*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_1 = $producto->precio_venta_1-($producto->precio_venta_1*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_2 = $producto->precio_venta_2-($producto->precio_venta_2*(($request->porcentaje)/100));
            $productoUpdate->precio_venta_3 = $producto->precio_venta_3-($producto->precio_venta_3*(($request->porcentaje)/100));
            $productoUpdate->modif_precio = date('Y-m-d');
            $productoUpdate->save();
        }
        $log = new Log;
        $log->idpermiso = 100;
        $log->idusuario = $request->userid;
        $log->detalle = "Baja de Precios Masivo %: " .$request->porcentaje;
        $log->fecha  = date('Y-m-d');
        $log->hora  = date("H:i:s");
        $log->save();
    }

    //Generar pdf

    public function generarPDF(Request $request){
        $productos = Productos::where('id_proveedor','=',$request->id)->get();
        $proveedor = Proveedor::where('id','=',$request->id)->first();

        $pdf = PDF::loadView('pdf.productos',compact('productos','proveedor'))->setPaper('a4', 'landscape');
        return $pdf->download('invoice.pdf');
    }

}


