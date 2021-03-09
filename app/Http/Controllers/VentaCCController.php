<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VentaCC;
use App\Cliente;
use App\Productos;
use App\DetalleVentaCC;
use DB;

class VentaCCController extends Controller
{
    //Retornar la vista
    public function index(){
        return view('cuentaCorriente.ventasCuentaCorriente');
    }

    //Conseguir el listado de los clientes
    public function listarClientes(Request $request){

        try {
            $clientes = DB::table('clientes')
            ->where('cuenta_corriente','=', 1)
            ->where(function ($query) use ($request) {
                $query->where('CUIT','like', '%' . $request->cliente . '%')
                ->orWhere('Nombre','like', '%' . $request->cliente . '%');
            })
            ->paginate(5);

            return $clientes;
        } catch (\Throwable $th) {
            return $th;
        }

    }

    //Datos cliente específico
    public function clienteEspecifico(Request $request){
        try {
            $cliente = Cliente::find($request->id);
            return json_encode($cliente);
        } catch (\Throwable $th) {
            return 0;
        }
    }

    //Listado productos
    public function listarProductos(Request $request){
        try {
            $productos = DB::table('productoscombinacionescabecera')
            ->where('stock','>', 0)
            ->where(function ($query) use ($request) {
                $query->where('Descripcion','like', '%' . $request->producto . '%')
                ->orWhere('CodBarras','like', '%' . $request->producto . '%');
            })
            ->paginate(5);

            return $productos;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    //Almacenar venta
    public function cargarVenta(Request $request){
        try{
            DB::beginTransaction();

            $ventaCC = new VentaCC();

            $ventaCC->id_cliente = $request->id_cliente;
            $ventaCC->fecha = date('d-m-Y',strtotime($request->fecha));
            $ventaCC->total = $request->total;
            $ventaCC->estado = $request->estado;

            $ventaCC->save();
            $id_venta = $ventaCC->id; //Id de la venta guardada

            //Guardar detalles de la venta y descontar stock
            foreach($request->detalleVenta as $detalle){
                $detalleVenta = new DetalleVentaCC();

                $detalleVenta->id_venta = $id_venta;
                $detalleVenta->id_producto = $detalle['IdProducto'];
                $detalleVenta->cantidad = $detalle['cantidad'];
                $detalleVenta->subtotal = $detalle['subtotal'];
                $detalleVenta->precio_venta = $detalle['precio_venta'];

                $productoDescontarStock = Productos::find($detalle['IdProducto']);
                $productoDescontarStock->stock = $productoDescontarStock->stock - $detalle['cantidad'];

                $productoDescontarStock->save();
                $detalleVenta->save();
            }

            if($request->estado != 1){
                //Actualizar total en cuenta la cuenta corriente del cliente
                $cliente = Cliente::find($request->id_cliente);
                $cliente->total_cc = $cliente->total_cc + $request->total;
                $cliente->save();
            }
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Realizo Nueva Venta - " .$id_venta;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
        }
    }

    function facturaVentas(Request $request){
        dd($request->ventas);
        $datosCliente = DB::table('clientes')
        ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
        ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva', 'xtipoiva.Codigo')
        ->where('clientes.id', '=', $request->idcliente)
        ->first();

        $ventasFactura = $request->ventas;
        
        $total = 0;
        $sinIva = 0;
        $totalIva = 0;
        //$descuentoEfectivo=  $request->descuentoEfectivo;
        $descuentoPorcentual=  $request->porcentual;
        

        foreach($ventasFactura as $element){
            $detalleVenta = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->join('users', 'users.id', '=', 'ventas.iduser')
            ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
            ->where('detalle_ventas.id_venta', '=', $element)
            ->get();
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
                
                $precio =  $item->precio_venta*$item->cantidad;
                //Linea donde Agrego Al precio Porcentaje A facturar
                $precio = $precio + ($precio*$descuentoPorcentual/100);
                $total = $total +$precio;
                $sinIva= $precio /(1+($item->IVA/100));
                $totalIva= $totalIva + $sinIva;
            }


        }
        

        //$afip = new Afip(array('CUIT' => 30642270253, 'production' => true));
    
        //dd($detalleVenta);
     

        $cliente = $datosCliente->Nombre;
        $docNro = $datosCliente->CUIT;
        $domicilio =  $datosCliente->Domicilio;
        $frenteIva = $datosCliente->TipoIva;

        $sinIva = bcdiv($sinIva, '1', 2);
        $totalIva = bcdiv($totalIva, '1', 2);
        $total = bcdiv($total, '1', 2);

        //$valor = 52;
        //$iva = 21;

        // $cantidad = 1;
        //$producto = "Esponja Acero Inox 16gr";
        // dd("esto es sin Iva:".$sinIva. " Esto es final:".$importe);
        $diferencia = $total - $totalIva;

       
        if($frenteIva=="Resp. Inscripto"){
            $cbeTipo = 1;
        }else {
            $cbeTipo = 6;
        }
        $docType = 80;


        echo "Descuento Porcentual:".$descuentoPorcentual."<br>";
        echo "Sin Iva:".$totalIva."<br>";
        echo "Diferencia:".$diferencia."<br>";
        echo "Total:".$total."<br>";
        //$last_voucher = $afip->ElectronicBilling->GetLastVoucher(12, $cbeTipo);
        /*
        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> 12,  // Punto de venta
            'CbteTipo' 	=> $cbeTipo,  // Tipo de comprobante (ver tipos disponibles)
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> $docType, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> $docNro,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' 	=> $last_voucher+1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' 	=> $last_voucher+1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> $total, // Importe total del comprobante
            'ImpTotConc' 	=> 0,   // Importe neto no gravado
            'ImpNeto' 	=> $totalIva, // Importe neto gravado
            'ImpOpEx' 	=> 0,   // Importe exento de IVA
            'ImpIVA' 	=> $diferencia,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)
            'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
                array(
                    'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
                    'BaseImp' 	=> $totalIva, // Base imponible
                    'Importe' 	=> $diferencia // Importe
                )
            ),
        );


        $res = $afip->ElectronicBilling->CreateVoucher($data);
        */
        //$option_types = $afip->ElectronicBilling->GetOptionsTypes();

        $nroIngresoBrutos = "907-541045-0";
        $inicioAct = "01/04/1991";


        $last_voucher = $last_voucher + 1;
        $countFact=1;

        try {
            while($countFact!=3){
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
            if($cbeTipo==1){
              $printer->text("Factura A                             Nro:0012-" . $last_voucher . "\n");    
            }else{
              $printer->text("Factura B                             Nro:0012-" . $last_voucher . "\n");
            }
            
            $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
            $printer->text("                                      Hora:" . date("H:i:s") . "\n");
            $printer->text("                                      ID:" . $venta->id . "\n");
            $printer->text("----------------------------------------------------\n");
            $printer->text("Razon Social:" . $cliente .  " \n");
            $printer->text("CUIT:" . $docNro .  " \n");
            $printer->text("Domicilio:" . $domicilio .  " \n");
            $printer->text("IVA:" . $frenteIva .  " \n");
            $printer->text("----------------------------------------------------\n");
            $printer->feed(1);
            $printer->text("Detalle                              CANT     Importe \n");
            if($cbeTipo==6){
                foreach ($detalleVenta as $item) {

                    $articulo = substr($item->Descripcion, 0, 20);
                    $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "         $" . $item->precio_venta * $item->cantidad . "\n");
                }

            }else{
                foreach ($detalleVenta as $item) {
                    $articulo = substr($item->Descripcion, 0, 20);    
                    $sinIva = ($item->precio_venta * $item->cantidad) / (1 + ($item->IVA / 100));
                    $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "          $" . bcdiv($sinIva, '1', 2) . "\n");
                }

            }
            

            $printer->feed(2);
            if($cbeTipo==1){
            $printer->text("                          Subtotal:        $" . $totalIva . "\n");
            $printer->text("                             Iva:          $" . $diferencia . "\n");
            }
            
           
            $printer->text("                             Total:        $" . $total . "\n");

            $printer->feed(1);
            //$printer->text("CAE:" . $res['CAE'] . "\n");
            //$printer->text("VTO CAE:" . $res['CAEFchVto'] . "\n");
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Gracias por su compra \n");
            //$printer->text("Fue Atendido por  - " . $usuario . " \n");


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
