<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Venta;
use App\DetalleVenta;
use App\Caja;
use App\Log;
use PDF;
use App\Cliente;
use App\Historia;
use App\Productos;
use Afip;
use App\Http\Controllers\CajaController;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class ComprobantesController extends Controller
{
    function getAnular()
    {

        $ventas = DB::Table('ventas')
            ->leftjoin('users', 'ventas.iduser', '=', 'users.id')
            ->select('ventas.*', 'users.nombre')
            ->get();

        //$ventas = Venta::Where('factura','<>','Null')->get();
        //$ventas = Venta::Where('factura','=',NULL)->get();
        return $ventas;
    }
    function eliminaVenta(Request $request){
            $venta = Venta::find($request->venta);
            $detalleVenta = DetalleVenta::where('id_venta','=',$venta->id)->get();

            foreach ($detalleVenta as $detalle){
                $producto = Productos::find($detalle->id_producto);
                $producto->stock = $producto->stock + $detalle->cantidad;
                $producto->save();

                $detalle->delete();
            }
            if($venta->condicion_venta=="Efectivo"){
                $caja= Caja::Where('id','=',1)->first();
                $caja->total = $caja->total - $venta->total;
                $caja->save();

            }

            $venta->delete();
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Elimino  Venta - " .$venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
    }

    function ReImprime(Request $request)
    {
        $venta = Venta::where('id', '=', $request->venta)->first();

        $cliente = Cliente::Where('Nombre', '=', $venta->cliente)->first();

        
        
        $detalleVenta = DB::table('detalle_ventas')
        ->leftjoin('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
        ->leftjoin('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
        ->leftjoin('users', 'users.id', '=', 'ventas.iduser')
        ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
        ->where('detalle_ventas.id_venta', '=', $venta->id)
        ->get();
        

        
        $datosCliente = DB::table('clientes')
            ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
            ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva', 'xtipoiva.Codigo')
            ->where('clientes.id', '=', $cliente->id)
            ->first();

        if ($venta->tipo_venta == "Factura_A") {
            $cbeTipo = 1;
        }
        if ($venta->tipo_venta == "Factura_B") {

            $cbeTipo = 6;
        }
        if ($venta->tipo_venta == "Consumidor_Final") {
            $cbeTipo = 6;
        }

        $afip = new Afip(array('CUIT' => 30642270253, 'production' => true));
        $voucher_info = $afip->ElectronicBilling->GetVoucherInfo($venta->factura, 12, $cbeTipo);

        $nroIngresoBrutos = "907-541045-0";
        $inicioAct = "01/04/1991";
        $condicion = $venta->tipo_venta;
        $total = 0;
        $sinIva=0;
        $totalIva = 0;
        $usuario="";
        $diferencia=0;
        //dd($detalleVenta);
        foreach ($detalleVenta as $item) {
            /*$valor = $item->precio_venta;
                $iva = $item->precio_venta;
                $cosumidor= $item->tipo_venta;
                $sinIva= $valor /(1+($iva/100));
                $sinIva = bcdiv($sinIva, '1', 2);
                $importe =$sinIva*(1+($iva/100));
                $importe =  bcdiv($importe, '1', 2);
    
                $valorIva= $valor - $sinIva;
    
                */

            $total = $total + $item->precio_venta * $item->cantidad;
            $sinIva = $item->precio_venta * $item->cantidad / (1 + ($item->IVA / 100));
            $totalIva = $totalIva + $sinIva;
            $docNro = $item->cuit;
            $usuario = $item->nombre;
        }
        $sinIva = bcdiv($sinIva, '1', 2);
        $totalIva = bcdiv($totalIva, '1', 2);
        $total = bcdiv($total, '1', 2);

        $cliente = $datosCliente->Nombre;
        $docNro = $datosCliente->CUIT;
        $domicilio =  $datosCliente->Domicilio;
        $frenteIva = $datosCliente->TipoIva;

        //$valor = 52;
        //$iva = 21;

        // $cantidad = 1;
        //$producto = "Esponja Acero Inox 16gr";
        // dd("esto es sin Iva:".$sinIva. " Esto es final:".$importe);
        $diferencia = $total - $totalIva;



        $countFact = 1;


        try {
            while ($countFact != 3) {
                $connector = new NetworkPrintConnector("192.168.0.253", 9100);
                // Print a "Hello world" receipt"
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
                $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
                $printer->text("SENDA SRL \n");

                $printer->selectPrintMode(Printer::MODE_FONT_B);

                $printer->text("SENDA SOCIEDAD DE RESPONSABILIDAD LIMITADA \n");
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
                if ($condicion == "Factura_A") {
                    $printer->text("Factura A                             Nro:0012-" . $venta->factura . "\n");
                    $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
                    $printer->text("                                      Hora:" . date("H:i:s") . "\n");
                    $printer->text("Razon Social:" . $cliente .  " \n");
                    $printer->text("CUIT:" . $docNro .  " \n");
                    $printer->text("Domicilio:" . $domicilio .  " \n");
                    $printer->text("IVA:" . $frenteIva .  " \n");
                } else {
                    if ($condicion == "Factura_B") {
                        $printer->text("Factura B                             Nro:0012-" . $venta->factura . "\n");
                        $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
                        $printer->text("                                      Hora:" . date("H:i:s") . "\n");
                        $printer->text("Razon Social:" . $cliente->Nombre .  " \n");
                        $printer->text("CUIT:" . $docNro .  " \n");
                        $printer->text("Domicilio:" . $domicilio .  " \n");
                        $printer->text("IVA:" . $frenteIva .  " \n");
                    } else {

                        $printer->text("Consumidor Final                      Nro:0012-" . $venta->factura . "\n");
                        $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
                        $printer->text("                                       Hora:" . date("H:i:s") . "\n");
                    }
                }
                $printer->feed(1);
                $printer->text("Detalle                              CANT     Importe \n");

                foreach ($detalleVenta as $item) {
                    $articulo = substr($item->Descripcion, 0, 20);
                    if ($condicion != "Factura_A") {
                        $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "         $" . $item->precio_venta * $item->cantidad . "\n");
                    } else {
                        $sinIva = ($item->precio_venta * $item->cantidad)  / (1 + ($item->IVA / 100));
                        $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "          $" . bcdiv($sinIva, '1', 2) . "\n");
                    }
                }

                $printer->feed(2);
                if ($condicion != "Factura_A") {
                    $printer->text("                             Total:        $" . $total . "\n");
                } else {
                    $printer->text("                          Subtotal:        $" . $totalIva . "\n");
                    $printer->text("                             Iva:          $" . $diferencia . "\n");
                    $printer->text("                             Total:        $" . $total . "\n");
                }

                $printer->feed(1);
                $printer->text("CAE:" . $voucher_info->CodAutorizacion . "\n");
                $vencimientoCae = date('d-m-Y', strtotime($voucher_info->FchVto));
                $printer->text("VTO CAE:" . $vencimientoCae . "\n");
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - " . $usuario . " \n");


                $printer->cut();

                // Close printer
                $printer->close();
                $countFact++;
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Reimprimio Venta Nro : " . $venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            

            return 0;
        } catch (Trowable $th) {
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error : " . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
        }
    }


    function setAnula(Request $request)
    {
        try {
            $venta = Venta::where('id', '=', $request->venta)->first();


            $cliente = Cliente::Where('Nombre', '=', $venta->cliente)->first();

            $detalleVenta = DB::table('detalle_ventas')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                ->join('users', 'users.id', '=', 'ventas.iduser')
                ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                ->where('detalle_ventas.id_venta', '=', $venta->id)
                ->get();
            
            $condicion = "";
            $cuit = 0;
            $total = 0;
            $totalIva = 0;
            //dd($detalleVenta);
            foreach ($detalleVenta as $item) {
                /*$valor = $item->precio_venta;
                $iva = $item->precio_venta;
                $cosumidor= $item->tipo_venta;
                $sinIva= $valor /(1+($iva/100));
                $sinIva = bcdiv($sinIva, '1', 2);
                $importe =$sinIva*(1+($iva/100));
                $importe =  bcdiv($importe, '1', 2);
    
                $valorIva= $valor - $sinIva;
    
                */

                $total = $total + $item->precio_venta * $item->cantidad;
                $sinIva = $item->precio_venta * $item->cantidad / (1 + ($item->IVA / 100));
                $totalIva = $totalIva + $sinIva;
                $docNro = $item->cuit;
            }
            $sinIva = bcdiv($sinIva, '1', 2);
            $totalIva = bcdiv($totalIva, '1', 2);
            $total = bcdiv($total, '1', 2);

            //$valor = 52;
            //$iva = 21;

            // $cantidad = 1;
            //$producto = "Esponja Acero Inox 16gr";
            // dd("esto es sin Iva:".$sinIva. " Esto es final:".$importe);
            $diferencia = $total - $totalIva;

            if ($venta->tipo_venta == "Factura_A") {
                $docType = 80;
                $cbeTipo = 3;
            } else {
                $docType = 80;
                $cbeTipo = 8;
            }
            if ($venta->tipo_venta == "Consumidor_Final") {
                $docType = 99;
                if ($total >= 10000) {
                    $docType = 96;
                } else {
                    $docNro = 0;
                }
            }

            $afip = new Afip(array('CUIT' => 30642270253, 'production' => true));


            $last_voucher = $afip->ElectronicBilling->GetLastVoucher(12, $cbeTipo);
            $venta->factura = $last_voucher + 1;
            $venta->save();
            $data = array(
                'CantReg'     => 1,  // Cantidad de comprobantes a registrar
                'PtoVta'     => 12,  // Punto de venta
                'CbteTipo'     => $cbeTipo,  // Tipo de comprobante (ver tipos disponibles)
                'Concepto'     => 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                'DocTipo'     => $docType, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
                'DocNro'     => $docNro,  // Número de documento del comprador (0 consumidor final)
                'CbteDesde'     => $last_voucher + 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
                'CbteHasta'     => $last_voucher + 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
                'CbteFch'     => intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'ImpTotal'     => $total, // Importe total del comprobante
                'ImpTotConc'     => 0,   // Importe neto no gravado
                'ImpNeto'     => $totalIva, // Importe neto gravado
                'ImpOpEx'     => 0,   // Importe exento de IVA
                'ImpIVA'     => $diferencia,  //Importe total de IVA
                'ImpTrib'     => 0,   //Importe total de tributos
                'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
                'MonCotiz'     => 1,     // Cotización de la moneda usada (1 para pesos argentinos)
                'Iva'         => array( // (Opcional) Alícuotas asociadas al comprobante
                    array(
                        'Id'         => 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
                        'BaseImp'     => $totalIva, // Base imponible
                        'Importe'     => $diferencia // Importe
                    )
                ),
            );


            $res = $afip->ElectronicBilling->CreateVoucher($data);

            $historia = new Historia;
            $historia->ventaid = $venta->id;
            $historia->vtocae = date("d-m-Y", strtotime($res['CAEFchVto']));
            $historia->cae = $res['CAE'];
            $historia->comprobante = $last_voucher + 1;
            $historia->fecha = date('Y-m-d');
            $historia->hora =  date("H:i:s");
            $historia->save();


            $venta->condicion_venta = "ANULADA";
            $venta->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Anulo Venta Afip : " . $venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            $connector = new NetworkPrintConnector("192.168.0.253", 9100);
            // Print a "Hello world" receipt"
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $printer->text("SENDA SRL \n");

            $printer->selectPrintMode(Printer::MODE_FONT_B);

            $printer->text("SENDA SOCIEDAD DE RESPONSABILIDAD LIMITADA \n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Domicilio Fiscal: Velero Mimosa 152 \n");
            $printer->text("Anulacion Comprobate Nro:" . $venta->factura ."\n");
            $printer->text("CAE:" . $res['CAE']."\n");

            $printer->cut();

            // Close printer
            $printer->close();

            return 0;
        } catch (Throwable $th) {
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Error Anula  : " . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }
}
