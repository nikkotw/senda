<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Caja;
use App\CierreCaja;
use App\User;
use App\Venta;

use App\MovimientosCaja;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;


class ReportesController extends Controller
{


    function reporte(Request $request)
    {

        if ($request->reporte == "venta") {
            $ventas = DB::table('ventas')
                ->leftJoin('users', 'ventas.iduser', '=', 'users.id')
                ->select('ventas.factura', 'ventas.cliente', 'ventas.cuit', 'ventas.domicilio', 'ventas.total', 'ventas.fecha', 'ventas.tipo_venta', 'ventas.condicion_venta', 'users.nombre')
                ->where('ventas.fecha', '=', $request->fecha)
                ->get();

            $ventasEmpleados = DB::table('ventas')
                ->join('users', 'ventas.iduser', '=', 'users.id')
                ->select(DB::raw('sum(ventas.total) as maximo'), 'users.nombre')
                ->where('ventas.fecha', '=', $request->fecha)
                ->groupby('users.nombre')
                ->get();

            return compact('ventas', 'ventasEmpleados');
        }
        if ($request->reporte == "caja") {
            $caja = MovimientosCaja::where('fecha', '=', $request->fecha)
                ->select('ingreso', 'egreso', 'obs', 'fecha')
                ->get();
            return $caja;
        }
    }
    function reporteUsuario(Request $request)
    {
        $log = DB::table('logs')
            ->join('users', 'logs.idusuario', '=', 'users.id')
            ->select('logs.*', 'users.nombre')
            ->where('users.id', '=', $request->user)
            ->orderBy('logs.id', 'DESC')
            ->paginate(5);



        return $log;
    }
    function usuarios(Request $request)
    {
        $users = User::All();
        return $users;
    }

    function getVentaCliente(Request $request)
    {
        $cliente = $request->cliente;
        $desde = $request->desde;
        $hasta = $request->hasta;

        if ($hasta == null || $desde == null) {
            $ventas = DB::table('ventas')
                ->leftjoin('users', 'ventas.iduser', '=', 'users.id')
                ->select('ventas.factura', 'ventas.cliente', 'ventas.cuit', 'ventas.domicilio', 'ventas.total', 'ventas.fecha', 'ventas.tipo_venta', 'ventas.condicion_venta', 'users.nombre')
                ->where(DB::raw('lower(ventas.cliente)'), 'like', '%' . $cliente . '%')
                ->orderBy('ventas.fecha', 'ASC')
                ->get();
        } else {
            $ventas = DB::table('ventas')
                ->leftjoin('users', 'ventas.iduser', '=', 'users.id')
                ->select('ventas.factura', 'ventas.cliente', 'ventas.cuit', 'ventas.domicilio', 'ventas.total', 'ventas.fecha', 'ventas.tipo_venta', 'ventas.condicion_venta', 'users.nombre')
                ->where(DB::raw('ventas.fecha'), '>=', $desde)
                ->where(DB::raw('ventas.fecha'), '<=', $hasta)
                ->where(DB::raw('lower(ventas.cliente)'), 'like', '%' . $cliente . '%')
                ->orderBy('ventas.fecha', 'ASC')
                ->get();
        }





        if ($ventas->count() == 0) {
            return 20;
        } else {
            return compact('ventas');
        }
    }

    function ventasReporte(Request $request)
    {
        $ventas = DB::table('ventas')
            ->join('users', 'ventas.iduser', '=', 'users.id')
            ->select('ventas.*', 'users.nombre')
            ->where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->get();

        $total = DB::table('ventas')
            ->select(DB::raw('sum(ventas.total) as maximo'))
            ->where('fecha', '>=', $request->desde)
            ->where('fecha', '<=', $request->hasta)
            ->get();
        if ($ventas->count() == 0) {
            return 0;
        }

        $pdf = PDF::loadView('pdf.ventasReporte', compact('ventas', 'total'))->setPaper('a4', 'portrait');
        Storage::put('public/pdf/reporte-' . date('d-m-y') . '.pdf', $pdf->output());

        //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
        $url = 'public/pdf/reporte-' . date('d-m-y') . '.pdf';

        $download_path = storage_path('public/pdf/reporte-' . date('d-m-y') . '.pdf');
        $filename = 'reporte-' . date('d-m-y') . '.pdf';
        //$headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
        $testLink = Storage::url('pdf/' . $filename);
        return $testLink;
    }
}
