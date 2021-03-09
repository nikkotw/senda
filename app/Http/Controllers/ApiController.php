<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DetalleTransferStock;
use App\Sucursales;
use App\Caja;
use App\Venta;
use PDF;
use App\Cliente;
use Illuminate\Support\Facades\Storage;
use App\Events\CarritoTransfer;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class ApiController extends Controller
{
    //

    public function transferencias()
    {
        $carrito = DB::table('detalle_transferstock')
            ->join('productoscombinacionescabecera', 'detalle_transferstock.idproducto', '=', 'productoscombinacionescabecera.idProducto')
            ->select('detalle_transferstock.*', 'productoscombinacionescabecera.Descripcion')
            ->get();
        return $carrito;
    }
    public function sucursales()
    {
        $sucursales = Sucursales::All();
        return $sucursales;
    }
    public function cliente(Request $request)
    {
        $cliente = Cliente::where('nombre', 'like', '%' . $request->busca . '%')->get();

        return json_encode($cliente);
    }

    function CC(Request $request)
    {
        $cliente = Cliente::Where('id', '=', $request->id)->first();
        $listado = DB::table('ctacte')
            ->select(DB::raw('SUM(Debe) as deuda, MONTH(Fecha)as mes'))
            ->where('Idcliente', '=', $cliente->IdCliente)
            ->groupBy('mes')
            ->orderBy('mes', 'DESC')
            ->limit(5)
            ->get();
        return json_encode($listado);

    }

    function infocliente(Request $request)
    {
        $cliente = Cliente::Where('id', '=', $request->id)->first();
        return json_encode($cliente);
    }

    function detalleTransfer(Request $request)
    {
        $productos = DB::table('transferencias_detalle')
            ->join('productoscombinacionescabecera as productos', 'transferencias_detalle.idproducto', '=', 'productos.idProducto')
            ->select('transferencias_detalle.cantidad', 'transferencias_detalle.id', 'productos.Descripcion')
            ->where('transferencias_detalle.idTransferencia', '=', $request->id)
            ->get();

        return $productos;
    }


    function listadoTransferencias()
    {
        $productos = DB::table('transferencias_stock')
            ->join('sucursales as origen', 'transferencias_stock.idsucorigen', '=', 'origen.id')
            ->join('sucursales as destino', 'transferencias_stock.idsucdestino', '=', 'destino.id')
            ->select('origen.sucursal as origen', 'destino.sucursal', 'transferencias_stock.*')
            ->get();


        return json_encode($productos);
    }
    function eliminaProductoTransfer(Request $request)
    {
        $articulo = DetalleTransferStock::where('id', '=', $request->id)->first();
        $articulo->delete();
        event(new CarritoTransfer());
        return "1";
    }
    function editaProductoTransfer(Request $request)
    {
        $articulo = DetalleTransferStock::where('id', '=', $request->id)->first();
        $articulo->cantidad = $request->cantidad;
        $articulo->save();
        return "1";
    }

    function remitoTransporte(Request $request)
    {
        $productos = DB::table('detalle_transferstock')
            ->join('productoscombinacionescabecera', 'detalle_transferstock.idproducto', '=', 'productoscombinacionescabecera.idProducto')
            ->select('detalle_transferstock.*', 'productoscombinacionescabecera.Descripcion')
            ->where('detalle_transferstock.idsucursal', '=', 2)
            ->get();

        $sucursalOrigen = Sucursales::where('id', '=', 1)->first();
        $sucursalDestino = Sucursales::where('id', '=', 2)->first();
        $remito = $request->nro;
        $bultos = $request->bultos;
        $pdf = PDF::loadView('pdf.transporte', compact('productos', 'sucursalOrigen', 'sucursalDestino', 'bultos', 'remito'))->setPaper('a4', 'portrait');
        Storage::put('public/pdf/remito - ' . $remito . '.pdf', $pdf->output());

        //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
        $url = 'public/pdf/remito - ' . $remito . '.pdf';

        //$download_path = storage_path('public/pdf/'.$transfer->id.'-transferenca-'.date('d-m-y').'.pdf');
        $filename = 'remito - ' . $remito . '.pdf';
        $headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
        $testLink = Storage::url('pdf/' . $filename);

        return $testLink;
    }

    function getDashboard(Request $request)
    {
        $hoy = date('Y-m-d');
        
        $ventasDia  = DB::table('ventas')
            ->select(DB::raw('SUM(total) as total'))
            ->where('fecha', '=', date('Y-m-d'))
            ->get();

        $ventasWeek = DB::table('ventas')
            ->select(DB::raw('SUM(total) as total, DAYOFWEEK(Fecha)as dia'), 'fecha')
            ->where(DB::raw('YEARWEEK(fecha,1)'), '=', DB::raw('YEARWEEK(CURDATE(), 1)'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'DESC')
            ->get();


        $ventasMonth = DB::table('ventas')
            ->select(DB::raw(' MONTH(Fecha) as mes,SUM(total) as total'))
            ->limit(1)
            ->groupBy('mes')
            ->get();

        

        $graficoMensual = DB::table('ventas')
            ->select(DB::raw('SUM(total) as total, MONTH(Fecha) as mes'))
            ->limit(12)
            ->groupBy('mes')
            ->orderBy('mes', 'ASC')
            ->get();


        $dia = date('Y-m-d');
        $movCajas = DB::table('movimientos_caja')
            ->select(DB::raw('SUM(ingreso) as ingreso, SUM(egreso) as egreso'))
            ->where('fecha', '=', $dia)
            ->get();
        $apertura = Caja::Where('id', '=', 1)->first();
        $caja = $movCajas[0]->ingreso - $movCajas[0]->egreso;
        $caja = bcdiv($caja, '1', 2);
        $caja = $caja + $apertura->monto_inicio;


        $log = DB::Table('logs')
            ->join('users', 'logs.idusuario', '=', 'users.id')
            ->Select('logs.*', 'users.user')
            ->limit(10)
            ->orderBy('logs.id', 'DESC')
            ->get();

        $masVendido = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera as producto', 'detalle_ventas.id_producto', '=', 'producto.idProducto')
            ->select(DB::raw('SUM(detalle_ventas.cantidad) as cantidad'), 'producto.Descripcion')
            ->groupBy('producto.Descripcion')
            ->orderBy('cantidad', 'desc')
            ->limit(10)
            ->get();

        $menosVendido = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera as producto', 'detalle_ventas.id_producto', '=', 'producto.idProducto')
            ->select(DB::raw('SUM(detalle_ventas.cantidad) as cantidad'), 'producto.Descripcion')
            ->groupBy('producto.Descripcion')
            ->orderBy('cantidad', 'asc')
            ->limit(10)
            ->get();

        return compact('ventasDia', 'ventasWeek', 'ventasMonth', 'caja', 'log', 'masVendido', 'menosVendido', 'graficoMensual');
    }

    function testing(Request $request)
    {
        $datosCliente = DB::table('clientes')
        ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
        ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva', 'xtipoiva.Codigo')
        ->where('clientes.id', '=', 4735)
        ->first();
 
        $cliente = $datosCliente->Nombre;
        $docNro = $datosCliente->CUIT;
        $domicilio =  $datosCliente->Domicilio;
        $frenteIva = $datosCliente->TipoIva;
        
        //dd($de);
        $detalleVenta = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion','productoscombinacionescabecera.precio_venta_1', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*')
            ->where('detalle_ventas.id_venta', '=', 37)
            ->get();
            $nroIngresoBrutos = "907-541045-0";
            $inicioAct = "01/04/1991";
            $last_voucher = 1;
            $venta = Venta::where('id','=',37)->first();
            //dd($venta->id);
        
            $countFact=2;
            try {
                
                while($countFact!=3){
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
                // Print a "Hello world" receipt"
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
               /* $printer->text("PEDIDO N° 324 \n");
                $printer -> setTextSize(2, 1);
                $printer->text($cliente ."\n");

                $printer -> setTextSize(2, 1);    
                $printer->text("Domicilio :".$domicilio." \n");
                $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
                //$printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
                //$printer->text("SENDA SRL \n");
                */
                $img = EscposImage::load("img/logo1.png");
               // $printer ->graphics($img);
               // $printer -> graphics($img, Printer::IMG_DOUBLE_WIDTH);
                $printer -> graphics($img, Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT);
                $printer->feed(2);
              

                $printer->selectPrintMode(Printer::MODE_FONT_B);
                       
                $printer->text("SENDA SRL - TRELEW \n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Domicilio Fiscal: Velero Mimosa 152 \n");
                $printer->text("CUIT : 30642270253 \n");
                $printer->text("PV - 0012 \n");
                $printer->text("IngBrutos:" . $nroIngresoBrutos . "\n");
                $printer->text("InicioAct:" . $inicioAct . "\n");
                $printer->text("IVA - Responsable Inscripto \n");
                $printer->text("Factura Electrónica \n");

                $printer->feed(1);
                $printer->text("----------------------------------------------------\n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Consumidor Final                      Nro:0012-" . $last_voucher . "\n");
                $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
                $printer->text("                                      Hora:" . date("H:i:s") . "\n");
                $printer->text("                                      ID:" . $venta->id . "\n");
                $printer->text("----------------------------------------------------\n");
            
                $printer->feed(1);
                $printer->text("Detalle                      P/U        CANT     Importe \n");
                
                   
                foreach ($detalleVenta as $item) {
                    $articulo = substr($item->Descripcion, 0, 20);
                    $printer->text(str_pad($articulo, 20) . "        $". str_pad($item->precio_venta,5)."        " . $item->cantidad . "     $" . $item->subtotal . "\n");
                }

                $printer->feed(2);
                if($venta->descuento>=(($venta->total*10)/100)){
                    $printer->text("                             Descuento: $" . $venta->descuento . "\n");
                }
                
                $printer->text("                             Total:        $" . $venta->total . "\n");

                $printer->feed(1);
                //$printer->text("CAE:" . $res['CAE'] . "\n");
                //$printer->text("VTO CAE:" . $res['CAEFchVto'] . "\n");
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - Nikkotw \n");


                $printer->cut();

                // Close printer
                $printer->close();
                $countFact++;
            }
                return 0;
            } catch (Throwable  $e) {
                return $e->getMessage();
                echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
            }
     }
}
