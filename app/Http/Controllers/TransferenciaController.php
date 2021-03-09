<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Productos;
use App\DetalleTransferStock;
use App\Sucursales;
use App\TransferenciaOk;
use App\TransferenciaDetalle;
use PDF;
use GuzzleHttp\Client;
use App\Recepcion;
use App\Recepcion_detalle;
use Illuminate\Support\Facades\Storage;
use App\Events\CarritoTransfer;
use App\Log;


class TransferenciaController extends Controller
{
    //FUncion donde Muestro vista de transferencias
    function transferencia()
    {
        return view('stock.transferencia');
    }

    //Funcion tiempo real del carrito de transferencias
    function carritostock()
    {
        $carrito = DB::table('detalle_transferstock')
            ->join('productoscombinacionescabecera', 'detalle_transferstock.idproducto', '=', 'productoscombinacionescabecera.idProducto')
            ->select('detalle_transferstock.*', 'productoscombinacionescabecera.Descripcion')
            ->get();
        
        $sucursal = Sucursales::ALL();
        return view('stock.carritostock', compact('carrito', 'sucursal'));
    }

    //Funcion que agrega provisoriamente el traspaso de sucursales
    function CarritoAddStock(Request $request)
    {
        try {
            DB::beginTransaction();
            $producto = Productos::Where('idProducto', '=', $request->producto)->first();
            if ($request->cantidad == null or $request->cantidad == '') {
                $cantidad = 1;
            } else {
                $cantidad = $request->cantidad;
            }
            //dd($cantidad);

            if ($producto->stock < $cantidad) {
                return 2;
            }
            $busca = DetalleTransferStock::Where('idproducto', '=', $request->producto)
                ->where('idsucursal', '=', $request->sucursal)->first();
            if (!empty($busca)) {

                $busca->cantidad += $cantidad;
                if ($producto->stock < $busca->cantidad) {

                    return 2;
                }

                $busca->save();
                event(new CarritoTransfer());
                DB::commit();
                return 1;
            } else {
                $stock = new DetalleTransferStock;
                $stock->idproducto = $request->producto;
                $stock->cantidad = $cantidad;
                $stock->idsucursal = $request->sucursal;
                $stock->idusuario = 1;
                $stock->save();
                event(new CarritoTransfer());
                DB::commit();
                return 1;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return 0 . "surgio un error" . $th;
        }
    }

    //Inicia Transpaso de mercaderia Entre sucursales
    // Transferencia
    //P:Preparacion  | T:Transito  | C:Completado -> La otra sucursal debera confirmar. | CC:CANCELADO
    //EN DETALLE
    //A:Abierto | C:CERRADO
    function realiza_transferencia(Request $request)
    {

        try {
            DB::beginTransaction();
            $productos = DB::table('detalle_transferstock')
                ->join('productoscombinacionescabecera', 'detalle_transferstock.idproducto', '=', 'productoscombinacionescabecera.idProducto')
                ->select('detalle_transferstock.*', 'productoscombinacionescabecera.Descripcion')
                ->where('detalle_transferstock.idsucursal', '=', $request->id)
                ->get();
            $transfer = new TransferenciaOk;
            $sucu = Sucursales::Where('estado', '=', 'P')->first();
            $transfer->idsucorigen =  $sucu->id;
            $transfer->idsucdestino = $request->id;
            $transfer->estado = "P";
            $transfer->idtransfer_suc = 0;
            $transfer->save();

            $sucursalOrigen = Sucursales::where('id', '=', $transfer->idsucorigen)->first();
            $sucursalDestino = Sucursales::where('id', '=', $transfer->idsucdestino)->first();

            foreach ($productos as $item) {
                $nuevo = new TransferenciaDetalle;
                $nuevo->idproducto = $item->idproducto;
                $nuevo->cantidad = $item->cantidad;
                $nuevo->idusuario = 0;
                $nuevo->idTransferencia = $transfer->id;
                $nuevo->save();
            }
            DB::table('detalle_transferstock')->where('idsucursal', '=', $request->id)->delete();
            $pdf = PDF::loadView('pdf.test', compact('productos', 'sucursalOrigen', 'sucursalDestino'))->setPaper('a4', 'landscape');
            Storage::put('public/pdf/' . $transfer->id . '-transferenca-' . date('d-m-y') . '.pdf', $pdf->output());

            //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
            $url = 'public/pdf/' . $transfer->id . '-transferenca-' . date('d-m-y') . '.pdf';

            $download_path = storage_path('public/pdf/' . $transfer->id . '-transferenca-' . date('d-m-y') . '.pdf');
            $filename = $transfer->id . '-transferenca-' . date('d-m-y') . '.pdf';
            $headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
            $testLink = Storage::url('pdf/' . $filename);
            
            //return response($download_path, 200,$headers);
           
            event(new CarritoTransfer());
            DB::commit();
            return $testLink;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 'ERROR' . $th;
        }
    }
    //Funcion donde elimino Producto de Posible Transferencia
    function CarritoDeleteTransfer(Request $request)
    {
        try {
            DB::beginTransaction();
            DB::table('detalle_transferstock')->where('id', '=', $request->id)->delete();
            event(new CarritoTransfer());
            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;

            return 0;
        }
    }

    //Listado de transfencias
    function listadoTransferencias()
    {
        $transfer = DB::table('sucursales')
            ->RightJoin('transferencias_stock', function ($join) {
                $join->on('sucursales.id', '=', 'transferencias_stock.idsucorigen');
                $join->on('sucursales.id', '=', 'transferencias_stock.idsucdestino');
            })
            ->select('transferencias_stock.*', 'sucursales.sucursal')
            ->get();

        return view('stock.listadoTransferencias', compact('transfer'));
    }

    //Confirma La transferencia y se envian los datos a la sucursal mediante VPN
    function confirmaPedido(Request $request)
    {
        try {
            $id = $request->id;
        $transferencia = TransferenciaOk::where("id", $id)->first();
        $sucursal = Sucursales::Where("id", $transferencia->idsucdestino)->first();



        $vpn = $sucursal->vpn;
        

        $client = new \GuzzleHttp\Client();
        $url = $vpn . "api/newRecepcion";
        //dd($url);
        //$request = $client->post($url,  ['body'=>$data]);

        $productos = DB::table('transferencias_detalle')
            ->join('productoscombinacionescabecera as product', 'transferencias_detalle.idproducto', '=', 'product.idProducto')
            ->select('product.Descripcion', 'transferencias_detalle.*')
            ->where('transferencias_detalle.idTransferencia', '=', $id)
            ->get();
         
        $r = $client->post($url,   [
            'form_params' => [
                
                "productos" => json_encode($productos),
                "sucursal" => $transferencia->idsucorigen,
                "idTransf" => $transferencia->id,

            ]
        ]);

        $respuesta = $r->getBody();
        if ($respuesta == "1") {
            $transferencia->fecha_realiza = date('Y-m-d');
            $transferencia->estado = "T";
            $transferencia->save();
            foreach($productos as $item){
                $producto=Productos::where('idProducto',$item->idproducto)->first();
                $producto->stock = $producto->stock - $item->cantidad;
                $producto->save();
            }


            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Confirmo Transferencia Nro - " . $transferencia->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();


            return 1;
        } else {
            return 0;
        }
        } catch (\Throwable $th) {
            return $th;
        }
        catch (\GuzzleHttp\Exception\BadResponseException $e) {
            return $e->getResponse()->getBody()->getContents();
        }
        
    }

    //Recibe por vpn los productos que arriban a la sucursal
    function Recepcion(Request $request)
    {
        try {
            DB::beginTransaction();
            $productos = json_decode($request->productos);
            $recepcion = new Recepcion;
            $recepcion->sucorigen = $request->sucursal;

            $recepcion->idTransferencia = $request->idTransf;

            $recepcion->fecha = date('Y/m/d');
            $recepcion->estado = 'P';

            $recepcion->save();

            foreach ($productos as $item) {
                $detalle_recepcion = new Recepcion_detalle;
                $detalle_recepcion->idproducto = $item->idproducto;
                $detalle_recepcion->cantidad = $item->cantidad;
                $detalle_recepcion->idrecepcion = $recepcion->id;
                $detalle_recepcion->save();
            }
            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    //Muestra todas las Recepciones
    function verRecepciones()
    {

        $recepcion = DB::table('recepcion')
            ->join('sucursales', 'recepcion.sucorigen', '=', 'sucursales.id')
            ->select('sucursales.sucursal', 'recepcion.*')
            ->get();
        return $recepcion;
    }

    //Todas las Transferencias
    function transferAll(Request $request)
    {
        return  json_encode(TransferenciaDetalle::All());
    }

    //Detalle de lo que fue recibido
    function detallaRecepcion(Request $request)
    {
        $recepcion = DB::table('recepcion_detalle')
            ->join('productoscombinacionescabecera as producto', 'recepcion_detalle.idproducto', '=', 'producto.idProducto')
            ->select('producto.Descripcion', 'recepcion_detalle.*')
            ->where('recepcion_detalle.idrecepcion', '=', $request->id)
            ->get();
        return $recepcion;
    }
    //Guarda Todos los faltantes que posee Luego de recibir los productos y Notifica a la sucursal Origen
    function checkFaltantes(Request $request)
    {
        try {
            DB::beginTransaction();
            $diferencias = $request->diferencias;

            foreach ($diferencias as $item) {
                $cantidad = $item['cantidad'];
                $id = $item['id'];
                $recepcion = Recepcion_detalle::Where('id', '=', $id)->first();
                
                $recepcion->diferencias = $recepcion->cantidad - $cantidad;
                $recepcion->recibido = $cantidad;
                $recepcion->save();
                $producto = Productos::where('idProducto', '=', $recepcion->idproducto)->first();

                $producto->stock = $producto->stock + $cantidad;
                $producto->save();
                $idRecepcion = $recepcion->idrecepcion;

                $nroTransfer = Recepcion::where('id', '=', $idRecepcion)->first();
            }
            $nroTransfer->estado = "C";
            $nroTransfer->fechaRecepcion = date('Y-m-s');
            
            $nroTransfer->save();
            
            $diferecias_pass = Recepcion_detalle::Where('idrecepcion', '=', $nroTransfer->id)->get();    
            $vpn= DB::table('recepcion')
                        ->join('sucursales','recepcion.sucorigen','=','sucursales.id')
                        ->where('sucursales.id','=',$nroTransfer->sucorigen)
                        ->select('sucursales.vpn')
                        ->first();

            $client = new \GuzzleHttp\Client();
            $url = $vpn->vpn . "api/addDiferencias";
            
            $r = $client->post($url,   [
                'form_params' => [
                    "diferencias" => json_encode($diferecias_pass),
                    "idTransf" => $nroTransfer->idTransferencia,
    
                ]
            ]);
    
            $respuesta = $r->getBody();

            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
    function AddDiferencias(Request $request){
        try {
            DB::beginTransaction();
            $diferencias = json_decode($request->diferencias);
            $cuenta_diff=0;
            foreach($diferencias as $item){
                $transferencia = TransferenciaDetalle::Where('idTransferencia','=',$request->idTransf)
                                                            ->where('idproducto','=',$item->idproducto)
                                                            ->first();
                $transferencia->recibido = $item->recibido;
    
                if($transferencia->cantidad != $item->recibido){
                    $cuenta_diff++;
                }
                $transferencia->save();
            }
            $tranfer = TransferenciaOk::where('id','=',$request->idTransf)->first();
            if($cuenta_diff>0){
               
                $tranfer->estado = "CD";
            }
            $tranfer->estado="C";
            $tranfer->fecha_recibe=date('Y-m-d');
            $tranfer->save();
            DB::commit();
            return 1;

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
      

    }
}
