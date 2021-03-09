<?php

namespace App\Http\Controllers;
use DB;
use PDF;
use Illuminate\Http\Request;
use App\Presupuesto;
use App\Cliente;
use App\DetallePresupuesto;

use Illuminate\Support\Facades\Storage;


class PresupuestoController extends Controller
{

    function savePresupuesto(Request $request)
    {

        $presupuesto = new Presupuesto;
        $presupuesto->fecha = date('Y-m-d');
        $presupuesto->cliente = $request->cliente;
        $presupuesto->idcliente = $request->idCliente;
        $presupuesto->total = $request->total;
        $presupuesto->save();



        $productos = $request->detalleVenta;

        foreach ($productos as $item) {

            $detallePresu = new DetallePresupuesto;

            $detallePresu->idpresupuesto = $presupuesto->id;
            $detallePresu->idProducto = $item['IdProducto'];
            $detallePresu->cantidad = $item['cantidad'];
            $detallePresu->save();
        }
        $total = $presupuesto->total;
        $presuNro = $presupuesto->id;
        $cliente = $request->cliente;
        $presupuesto = DB::table('detalle_presupuestos')->join('productoscombinacionescabecera', 'detalle_presupuestos.idProducto', '=', 'productoscombinacionescabecera.IdProducto')
            ->select('productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.precio_venta_1', 'detalle_presupuestos.cantidad')
            ->where('detalle_presupuestos.idPresupuesto', '=', $presuNro)
            ->get();


        $pdf = PDF::loadView('pdf.presupuesto', compact('presupuesto', 'presuNro', 'total', 'cliente'))->setPaper('a4', 'portrait');

        $route = $presuNro . '- Presupuesto - ' . $cliente . '.pdf';
        Storage::put('public/presupuestos/' . $route, $pdf->output());

        //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
        $url = 'public/presupuestos/' . $route;

        $download_path = storage_path('public/presupuestos/' . $route);
        $filename = $route;
        //$headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
        $testLink = Storage::url('presupuestos/' . $filename);

        return $testLink;
    }

    function buscaPresupuesto(Request $request)
    {

        $presupuesto = Presupuesto::Where('id', '=', $request->busqueda)->first();
        if ($presupuesto != null) {
            $cliente = Cliente::where('id', '=', $presupuesto->idCliente)->first();
            $detalle = DB::table('detalle_presupuestos')->join('productoscombinacionescabecera', 'detalle_presupuestos.idProducto', '=', 'productoscombinacionescabecera.IdProducto')
                ->select('productoscombinacionescabecera.*', 'detalle_presupuestos.cantidad')
                ->where('detalle_presupuestos.idPresupuesto', '=', $request->busqueda)
                ->get();
            return compact('detalle', 'presupuesto', 'cliente');
        } else {
            return 10;
        }
        return 20;
    }

    function actualiza(Request $request)
    {
        try {
            $presupuesto = Presupuesto::Where('id', '=', $request->presupuesto)->first();
            $detalle = DetallePresupuesto::where('idPresupuesto', '=', $request->presupuesto)->get();
            $presuNro = $presupuesto->id;
            $cliente = $presupuesto->cliente;

            foreach ($detalle as $item) {
                $item->delete();
            }
            $presupuesto->total = $request->total;
            $productos = $request->detalleVenta;

            foreach ($productos as $item) {

                $detallePresu = new DetallePresupuesto;

                $detallePresu->idpresupuesto = $presupuesto->id;
                $detallePresu->idProducto = $item['IdProducto'];
                $detallePresu->cantidad = $item['cantidad'];
                $detallePresu->save();
            }
            $total = $presupuesto->total;
            $presupuesto = DB::table('detalle_presupuestos')->join('productoscombinacionescabecera', 'detalle_presupuestos.idProducto', '=', 'productoscombinacionescabecera.IdProducto')
                ->select('productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.precio_venta_1', 'detalle_presupuestos.cantidad')
                ->where('detalle_presupuestos.idPresupuesto', '=', $presuNro)
                ->get();


            $pdf = PDF::loadView('pdf.presupuesto', compact('presupuesto', 'presuNro', 'total', 'cliente'))->setPaper('a4', 'portrait');

            $route = $presuNro . '- Presupuesto - ' . $cliente . '.pdf';
            $url = 'public/presupuestos/' . $route;
            Storage::delete($url);
            Storage::put('public/presupuestos/' . $route, $pdf->output());



            $filename = $route;

            $testLink = Storage::url('presupuestos/' . $filename);
            return $testLink;
        } catch (Throwable $th) {
            return 99;
        }
    }
}
