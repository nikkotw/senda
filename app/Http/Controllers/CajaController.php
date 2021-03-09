<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Cliente;
use App\Caja;
use App\CierreCaja;
use App\Productos;
use App\User;
use App\Venta;
use App\MovimientosCC;
use App\MovimientosCaja;
use App\DetalleVenta;
use App\Events\VentaAutorizada;
use Illuminate\Support\Facades\Storage;
use App\Events\VentaEliminada;
use App\Events\VentaFinalizada;
use DB;
use App\Log;
use GuzzleHttp\Client;
use Session;
use Afip;
use PDF;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Dompdf\Exception;
use PhpParser\Node\Stmt\Finally_;

class CajaController extends Controller
{
    //Retorna la vista principal
    public function index()
    {
        $caja = Caja::all();
        return view('caja.caja', compact('caja'));
    }

    //Listado de cajas
    public function getCajas()
    {

        $caja = Caja::all();
        return $caja;
    }

    //Abrir una caja
    public function abrirCaja(Request $request)
    {
        try {
            DB::beginTransaction();

            $caja = Caja::where('id', '=', $request->id)->first();
            $caja->estado = 1;
            $caja->fecha_apertura = date('Y-m-d');
            $caja->horario_apertura = date("h:i:s") . "\n";

            if ($request->selected == 'radio2') {
                $caja->total = $request->monto_abrir;
            }

            $caja->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Realizó Apertura de Caja";
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

    //Realizar una extracción
    public function realizarExtraccion(Request $request)
    {
        try {
            DB::beginTransaction();

            $caja = Caja::where('id', '=', $request->id)->first();
            $caja->total = $caja->total - $request->montoExtraccion;
            $caja->save();

            $movimientoCaja = new MovimientosCaja();
            $movimientoCaja->id_caja = $request->id;
            $movimientoCaja->egreso = $request->montoExtraccion;
            $movimientoCaja->fecha = date('Y-m-d');
            $movimientoCaja->obs = $request->motivoExtraccion;
            $movimientoCaja->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Extracción de Caja - " . $request->montoExtraccion;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public function realizarIngreso(Request $request){
        try {
            DB::beginTransaction();

            $caja = Caja::where('id', '=', $request->id)->first();
            $caja->total = $caja->total + $request->montoIngreso;
            $caja->save();

            $movimientoCaja = new MovimientosCaja();
            $movimientoCaja->id_caja = $request->id;
            $movimientoCaja->ingreso = $request->montoIngreso;
            $movimientoCaja->fecha = date('Y-m-d');
            $movimientoCaja->obs = $request->motivoIngreso;
            $movimientoCaja->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Ingreso dinero en Caja - " . $request->montoIngreso;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }

    }

    //Datos para el cierre de caja
    public function datosCierreCaja(Request $request)
    {
        $caja = Caja::where('id', '=', $request->id)->first();
        return ($caja);
    }

    //Cierre de caja
    public function cerrarCaja(Request $request)
    {

        try {
            DB::beginTransaction();

            $caja = Caja::where('id', '=', $request->id)->first();

            $cierreCaja = new CierreCaja();
            $cierreCaja->id_caja = $caja->id;
            $cierreCaja->horario_cierre = date("h:i:s") . "\n";
            $cierreCaja->fecha_cierre = date('Y-m-d');
            $cierreCaja->fecha_apertura = $caja->fecha_apertura;
            $cierreCaja->horario_apertura = $caja->horario_apertura;
            $cierreCaja->monto = $request->monto;
            $cierreCaja->monto_sistema = $caja->total;
            $cierreCaja->obs = $request->obs;
            $cierreCaja->diferencia = $caja->total - $request->monto;
            $cierreCaja->monto_inicio = $caja->monto_inicio;

            $caja->fecha_apertura = "";
            $caja->horario_apertura = "";
            $caja->estado = 0;
            $caja->total = $request->monto_nuevo;
            $caja->monto_inicio = $request->monto_nuevo;

            $caja->save();
            $cierreCaja->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Cierre Caja";
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    //Datos de los clientes registrados
    public function getDatosClienteRegistradoVenta(Request $request)
    {
        $cliente = Cliente::where('CUIT', $request->cuit)->orWhere('CUIT', $request->cuitEditado)->get();
        return $cliente;
    }

    //Listado de ventas
    public function getVentas()
    {
        $ventas = Venta::where("estado", "=", 0)->get();
        return $ventas;
    }

    //Listado de ventas autorizadas
    public function getVentasAutorizadas()
    {
        $ventas = Venta::where("estado", "=", 2)->get();
        return $ventas;
    }

    //Detalle de las ventas
    public function getDetalleVenta(Request $request)
    {
        $detalleVenta = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
            ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock')
            ->where('detalle_ventas.id_venta', '=', $request->id)
            ->get();

        return $detalleVenta;
    }

    //Verificar si un cliente puede comparar por cuenta corriente
    public function verificarCuentaCorriente(Request $request)
    {

        if ($request->cuit == "") {
            return 0;
        } else {
            $cliente = Cliente::where('CUIT', '=', $request->cuit)->orWhere('CUIT', '=', $request->cuitEditado)->first();
            if ($cliente == null) {
                return 0;
            } else {
                if ($cliente->cuenta_corriente == 1) {
                    return $cliente;
                } else {
                    return 0;
                }
            }
        }
    }


    public function saveVentaCC(Request $request)
    {
        try {
            $venta = new Venta();

            $venta->total = $request->total;
            $venta->fecha = date('Y-m-d');
            $venta->descuento = $request->descuento;
            $venta->iduser = $request->userid;
            $venta->idcliente = $request->idCliente;

            if ($request->autorizar) {
                $venta->estado = 1;
            } else {
                $venta->estado = 3;
            }

            $venta->tipo_venta = $request->tipo_venta;

            $venta->cliente = $request->cliente;
            $venta->cuit = $request->cuit;
            $venta->domicilio = $request->domicilio;


            $venta->save();
            $cliente = Cliente::where('id', '=', $request->idCliente)->first(); //Se busca el cliente de la cuenta corriente
            $cliente->total_cc = $cliente->total_cc + $venta->total; //Se modifica el saldo
            $cliente->save();

            $id_venta = $venta->id;

            foreach ($request->detalleVenta as $detalle) {
                $detalleVenta = new DetalleVenta();

                $detalleVenta->id_venta = $id_venta;
                $detalleVenta->id_producto = $detalle['IdProducto'];
                $detalleVenta->cantidad = $detalle['cantidad'];
                $detalleVenta->subtotal = $detalle['subtotal'];
                $detalleVenta->precio_venta = $detalle['precio_venta'];
                $detalleVenta->restantesNotaCredito = $detalle['cantidad'];

                $detalleVenta->subtotal = $detalleVenta->subtotal + (($detalleVenta->subtotal * $request->interes) / 100);

                $productoDescontarStock = Productos::find($detalle['IdProducto']);
                $productoDescontarStock->stock = $productoDescontarStock->stock - $detalle['cantidad'];

                $productoDescontarStock->save();
                $detalleVenta->save();
            }

            if ($venta->estado != 3) {
                return 0;
            } else { 
                $resp = $this->entregaMercaderia($id_venta,$request->idCliente);
            }
            return $resp;
        } catch (\Throwable $th) {

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error - Linea: " . $th->getLine() . "--" . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }
    //Mandar venta a autorización
    public function autorizarVentaCuentaCorriente(Request $request)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::where("id", "=", $request->id)->first();
            $venta->estado = 1;
            $venta->cliente = $request->cliente;
            $venta->domicilio = $request->domicilio;
            $venta->cuit = $request->cuit;
            $venta->tipo_venta = $request->tipo_venta;
            $venta->condicion_venta = $request->condicion_venta;
            $venta->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Autorizó una venta por Cuenta Corriente - " . $venta->cliente;
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

    //Finalizar una venta por cuenta coriente
    public function finalizarVentaCuentaCorriente(Request $request)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::where('id', '=', $request->id)->first(); //Se busca la venta por el id

            if ($venta->estado == 0) {
                $venta->cliente = $request->cliente;
                $venta->cuit = $request->cuit;
                $venta->domicilio = $request->domicilio;
                $venta->tipo_venta = $request->tipo_venta;
                $venta->condicion_venta = $request->condicion_venta;
                $venta->estado = 3; //Se pone el estado de venta finalizada


                $cliente = Cliente::where('id', '=', $request->id_cliente)->first(); //Se busca el cliente de la cuenta corriente
                $cliente->total_cc = $cliente->total_cc + $venta->total; //Se modifica el saldo

                //Se crea un movimiento de egreso de CC
                $movimiento = new MovimientosCC();
                $movimiento->id_venta = $request->id;
                $movimiento->egreso = $venta->total;
                $movimiento->fecha =  date('Y-m-d');
                $movimiento->id_cliente = $request->id_cliente;

                $venta->save();
                $cliente->save();
                $movimiento->save();
            } else {
                $venta->estado = 3; //Se pone el estado de venta finalizada

                $cliente = Cliente::where('id', '=', $request->id_cliente)->first(); //Se busca el cliente de la cuenta corriente
                $cliente->total_cc = $cliente->total_cc + $venta->total; //Se modifica el saldo

                //Se crea un movimiento de egreso de CC
                $movimiento = new MovimientosCC();
                $movimiento->id_venta = $request->id;
                $movimiento->egreso = $venta->total;
                $movimiento->fecha =  date('Y-m-d');
                $movimiento->id_cliente = $request->id_cliente;

                $venta->save();
                $cliente->save();
                $movimiento->save();
            }

            $this->entregaMercaderia($venta->id,$venta->idcliente);
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Finalizó una venta por cuenta corriente: " . $venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new VentaFinalizada());
            return 1;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    //Finalizar una venta que no es por cuenta corriente
    public function finalizarVenta(Request $request)
    {
        try {
            

            $venta = new Venta();

            $venta->total = $request->total;
            $venta->fecha = date('Y-m-d');
            $venta->descuento = $request->descuento;
            $venta->iduser = $request->userid;
            $venta->idcliente = $request->idCliente;

            if ($request->autorizar) {
                $venta->estado = 1;
            } else {
                $venta->estado = 0;
            }

            $venta->tipo_venta = $request->tipo_venta;

            if ($request->tipo_venta == 'Factura_A' || $request->tipo_venta == 'Factura_B' || $request->datosRequeridos || $request->tipo_venta == 'Cuenta_Corriente') {
                $venta->cliente = $request->cliente;
                $venta->cuit = $request->cuit;
                $venta->domicilio = $request->domicilio;
            } else {
                $venta->cliente = "Consumidor_Final";
            }
            if($request->tipo_venta =="Vale_Empleados"){
                $venta->cliente = $request->empleado;
            }
            if($request->escuela){
                $venta->condicion_venta =  "Formalidad";
                
            }
          
            $venta->save();

            $id_venta = $venta->id; //Id de la venta guardada recientemente


            //Guardar detalles de la venta y descontar stock de los productos
            foreach ($request->detalleVenta as $detalle) {
                $detalleVenta = new DetalleVenta();

                $detalleVenta->id_venta = $id_venta;
                $detalleVenta->id_producto = $detalle['IdProducto'];
                $detalleVenta->cantidad = $detalle['cantidad'];
                $detalleVenta->subtotal = $detalle['subtotal'];
                $detalleVenta->precio_venta = $detalle['precio_venta'];
                $detalleVenta->restantesNotaCredito = $detalle['cantidad'];

                $productoDescontarStock = Productos::find($detalle['IdProducto']);
                if(!$request->escuela){
                    $productoDescontarStock->stock = $productoDescontarStock->stock - $detalle['cantidad'];
                }
                

                $productoDescontarStock->save();
                $detalleVenta->save();
            }


            $detalleVenta = DB::table('detalle_ventas')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA')
                ->where('detalle_ventas.id_venta', '=', $id_venta)
                ->get();

            $venta->estado = 3;
            $venta->condicion_venta = $request->condicion_venta;

            $venta->save(); //Guardo la venta con los datos cargados

            if ($request->condicion_venta == 'Efectivo' || $request->variosMetodos) {
                $caja = Caja::where('id', '=', $request->cajaSeleccionada)->first(); //Buscar la caja en la que se almacena el efectivo
                if ($request->variosMetodos) {
                    $caja->total = $caja->total + $request->efectivo;

                    $movimientoCaja = new MovimientosCaja();
                    $movimientoCaja->id_caja = $request->cajaSeleccionada;
                    $movimientoCaja->id_venta = $venta->id;
                    $movimientoCaja->ingreso = $request->efectivo;
                    $movimientoCaja->fecha = date('Y-m-d');
                    $movimientoCaja->save(); //Guardo el movimiento de caja en caso que sea efectivo
                } else {
                    //Si escuela es true No se hace el movimiento en caja ya que es solo una formalidad.
                    //Chequeo tambien si fue un pedido cargado para que no haga el movimiento de caja y despues lo carguen manual. A veces venden al interior y esa plata no entra inmediato
                    
                    if(!$request->escuela){
                        if(!$request->pedido){
                            $caja->total = $caja->total + $request->total; //Modificar el total de efectivo que tiene la caja
                            $movimientoCaja = new MovimientosCaja();
                            $movimientoCaja->id_caja = $request->cajaSeleccionada;
                            $movimientoCaja->id_venta = $request->id_venta;
                            $movimientoCaja->ingreso = $request->total;
                            $movimientoCaja->obs = "Venta Nro:".$venta->id;
                            $movimientoCaja->fecha = date('Y-m-d');
                            $movimientoCaja->save(); //Guardo el movimiento de caja en caso que sea efectivo
                        }

                    }
                   
                }


                $caja->save(); //Guardo el total de la caja


            }
            
            DB::beginTransaction();
            if ($request->tipo_venta == 'No_Facturar') {
                
                $detalleVenta = DB::table('detalle_ventas')
                    ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                    ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                    ->join('users', 'users.id', '=', 'ventas.iduser')
                    ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                    ->where('detalle_ventas.id_venta', '=', $venta->id)
                    ->get();
                $idVenta = $venta->id;
                $this->ticket($venta->id);  
                return 0;  
                /*$pdf = PDF::loadView('pdf.venta-sinf', compact('detalleVenta', 'idVenta'))->setPaper('a4', 'portrait');
                Storage::put('public/pdf/' . $venta->id . '-venta-' . date('d-m-y') . '.pdf', $pdf->output());

                //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
                $url = 'public/pdf/' . $venta->id . '-venta-' . date('d-m-y') . '.pdf';

                $download_path = storage_path('public/pdf/' . $venta->id . '-venta-' . date('d-m-y') . '.pdf');
                $filename = $venta->id . '-venta-' . date('d-m-y') . '.pdf';
                //$headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
                $testLink = Storage::url('pdf/' . $filename);
                */
              
            }

            if ($request->tipo_venta == 'Vale_Empleados') {

                $detalleVenta = DB::table('detalle_ventas')
                    ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                    ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                    ->join('users', 'users.id', '=', 'ventas.iduser')
                    ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                    ->where('detalle_ventas.id_venta', '=', $venta->id)
                    ->get();
                $idVenta = $venta->id;
                $empleado = $venta->cliente;
                $pdf = PDF::loadView('pdf.vale', compact('detalleVenta', 'idVenta','empleado'))->setPaper('a4', 'portrait');
                Storage::put('public/pdf/' . $venta->id . '-vale-' . date('d-m-y') . '.pdf', $pdf->output());

                //DONDE GUARDAMOS EL ARCHIVO Y LUEGO LO ENVIAMOS A VUE PARA VERLO
                $url = 'public/pdf/' . $venta->id . '-vale-' . date('d-m-y') . '.pdf';

                $download_path = storage_path('public/pdf/' . $venta->id . '-vale-' . date('d-m-y') . '.pdf');
                $filename = $venta->id . '-vale-' . date('d-m-y') . '.pdf';
                //$headers = ['Content-Type: application/zip', 'Content-Disposition: attachment; filename={$filename}'];
                $testLink = Storage::url('pdf/' . $filename);
            }

            dd("Llega");


            $datosCliente = DB::table('clientes')
                ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
                ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva','xtipoiva.Codigo')
                ->where('clientes.id', '=', $request->idCliente)
                ->first();
            
            if ($datosCliente==null) {

                if ($request->tipo_venta == 'Factura_A') {
                    $respuesta = $this->facturaA($id_venta, $request->idCliente);
                }
                if ($request->tipo_venta == "Factura_B") {
                    $respuesta = $this->facturaB($id_venta, $request->idCliente);
                }
                if ($request->tipo_venta == "Consumidor_Final") {
                    $respuesta = $this->consumidorFinal($id_venta);
                }
            }else{
                if($datosCliente->Codigo!=0 && $datosCliente->Codigo!=3){
                    $respuesta  = $this->facturaB($id_venta,$request->idCliente);
                }else{
                    if($datosCliente->Codigo==0){
                        $respuesta = $this->facturaA($id_venta,$request->idCliente);
                    }
                    else{
                        $respuesta = $this->consumidorFinal($id_venta);
                    }
                }


            }






            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Finalizó una venta: " . $venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new VentaFinalizada());
            if ($request->tipo_venta == 'No_Facturar' || $request->tipo_venta=="Vale_Empleados") {
                return $testLink;
            } else {
                if ($respuesta == 20) {
                    $respuesta = $this->ticket($venta->id);
                    return $respuesta;
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Surgio un error :  En La linea" . $th->getLine() . "| " . $th->getMessage() . "         | En la venta Nro:" . $venta->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }

    public function facturaA($venta, $cliente,$escuela = false,$pedidoEscuela=[])
    {
        try {
            $datosCliente = DB::table('clientes')
                ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
                ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva', 'xtipoiva.Codigo')
                ->where('clientes.id', '=', $cliente)
                ->first();

            //dd($datosCliente);
            $detalleVenta = DB::table('detalle_ventas')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                ->join('users', 'users.id', '=', 'ventas.iduser')
                ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                ->where('detalle_ventas.id_venta', '=', $venta)
                ->get();

            $total = 0;
            $sinIva = 0;
            $totalIva = 0;
            $usuario = "";
            $descuento = 0;
            $afip = new Afip(array('CUIT' => env("CUIT"), 'production' => env("PRODUCTION")));
        
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
                
                
                $total = $total + $item->subtotal;
                $sinIva= $item->subtotal /(1+($item->IVA/100));
                $totalIva= $totalIva + $sinIva;
                $usuario = $item->nombre;
                $descuento=$item->descuento;
            }

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

           

            $cbeTipo = 1;
            $docType = 80;


            $last_voucher = $afip->ElectronicBilling->GetLastVoucher(env("PTO_VTA"), $cbeTipo);

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

            //$option_types = $afip->ElectronicBilling->GetOptionsTypes();

            $venta = Venta::where('id', '=', $venta)->first();
            $venta->factura = $last_voucher + 1;
            $venta->save();


            /*echo $res['CAE'] . "<br>"; //CAE asignado el comprobante
                $vencimientoCae= date("d-m-Y", strtotime($res['CAEFchVto']));
                echo $vencimientoCae . "<br>"; //Fecha de vencimiento del CAE (yyyy-mm-dd)
                */


            $nroIngresoBrutos = "907-541045-0";
            $inicioAct = "01/04/1991";


            $last_voucher = $last_voucher + 1;
            $countFact=1;
            $total = $total - $descuento;

            try {
                while($countFact!=3){
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
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
                $printer->text("Factura A                             Nro:0012-" . $last_voucher . "\n");
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
                $printer->text("Detalle                      P/U        CANT     Importe \n");

                foreach ($detalleVenta as $item) {
                    $articulo = substr($item->Descripcion, 0, 20);
                    $sinIva = ($item->subtotal) / (1 + ($item->IVA / 100));
                    $printer->text(str_pad($articulo, 20) . "        $". str_pad($item->precio_venta,5)."        " . $item->cantidad . "     $" . bcdiv($sinIva, '1', 2) . "\n");
                    $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "          $" . bcdiv($sinIva, '1', 2) . "\n");
                }

                $printer->feed(2);
                $printer->text("                          Subtotal:        $" . $totalIva . "\n");
                $printer->text("                             Iva:          $" . $diferencia . "\n");

                if($descuento>=(($total*10)/100)){
                    $printer->text("                             Descuento:    $" . $descuento . "\n");
                }
                
               
                $printer->text("                             Total:        $" . $total . "\n");

                $printer->feed(1);
                $printer->text("CAE:" . $res['CAE'] . "\n");
                $printer->text("VTO CAE:" . $res['CAEFchVto'] . "\n");
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - " . $usuario . " \n");


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
        } catch (\Throwable $th) {
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error : " . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }
    function facturaB($venta, $cliente)
    {
        try {
            $datosCliente = DB::table('clientes')
                ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
                ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva')
                ->where('clientes.id', '=', $cliente)
                ->first();

            //dd($datosCliente);
            $detalleVenta = DB::table('detalle_ventas')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                ->join('users', 'users.id', '=', 'ventas.iduser')
                ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                ->where('detalle_ventas.id_venta', '=', $venta)
                ->get();

            $total = 0;
            $sinIva = 0;
            $totalIva = 0;
            $usuario = "";

            $afip = new Afip(array('CUIT' => env("CUIT"), 'production' => env("PRODUCTION")));

            //dd($detalleVenta);
            foreach ($detalleVenta as $item) {

                $total = $total + $item->subtotal;
                    $sinIva= $item->subtotal /(1+($item->IVA/100));
                
                $totalIva = $totalIva + $sinIva;
                $usuario = $item->nombre;
            }

            $cliente = $datosCliente->Nombre;
            $docNro = $datosCliente->CUIT;
            $domicilio =  $datosCliente->Domicilio;
            $frenteIva = $datosCliente->TipoIva;

            $sinIva = bcdiv($sinIva, '1', 2);
            $totalIva = bcdiv($totalIva, '1', 2);
            $total = bcdiv($total, '1', 2);


            $diferencia = $total - $totalIva;
     
            if($total>10000){
                $cbeTipo = 6;
                $docType = 80;
            }else{
                $cbeTipo = 6;
                $docType = 80;
            }
           


            $last_voucher = $afip->ElectronicBilling->GetLastVoucher(env("PTO_VTA"), $cbeTipo);

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

            //$option_types = $afip->ElectronicBilling->GetOptionsTypes();

            $venta = Venta::where('id', '=', $venta)->first();
            $venta->factura = $last_voucher + 1;
            $venta->save();


            /*echo $res['CAE'] . "<br>"; //CAE asignado el comprobante
                $vencimientoCae= date("d-m-Y", strtotime($res['CAEFchVto']));
                echo $vencimientoCae . "<br>"; //Fecha de vencimiento del CAE (yyyy-mm-dd)
                */


            $nroIngresoBrutos = "907-541045-0";
            $inicioAct = "01/04/1991";


            $last_voucher = $last_voucher + 1;
            $countFact=1;
          
           
            
            try {
                while($countFact!=3){
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
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
                $printer->text("PV - ".env." \n");
                $printer->text("IngBrutos:" . $nroIngresoBrutos . "\n");
                $printer->text("InicioAct:" . $inicioAct . "\n");
                $printer->text("IVA - Responsable Inscripto \n");
                $printer->text("Factura Electrónica \n");

                $printer->feed(1);
                $printer->text("----------------------------------------------------\n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Factura B                             Nro:0012-" . $last_voucher . "\n");
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
                $printer->text("Detalle                      P/U        CANT     Importe \n");

                foreach ($detalleVenta as $item) {
                    $articulo = substr($item->Descripcion, 0, 20);
                    $printer->text(str_pad($articulo, 20) . "        $". str_pad($item->precio_venta,5)."        " . $item->cantidad . "     $" . $item->subtotal . "\n");
                }
                $printer->feed(2);
                $total=$venta->total;
                if($venta->descuento>=(($total*10)/100)){
                    $printer->text("                         Descuento:        $" . $venta->descuento . "\n");
                }
                
                
                $printer->text("                             Total:        $" . $total . "\n");

                $printer->feed(1);
                $printer->text("CAE:" . $res['CAE'] . "\n");
                $printer->text("VTO CAE:" . $res['CAEFchVto'] . "\n");
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - " . $usuario . " \n");


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
        } catch (\Throwable $th) {
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error : " . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }

    function consumidorFinal($venta)
    {
        try {
            $detalleVenta = DB::table('detalle_ventas')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
                ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
                ->join('users', 'users.id', '=', 'ventas.iduser')
                ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
                ->where('detalle_ventas.id_venta', '=', $venta)
                ->get();

            $total = 0;
            $sinIva = 0;
            $totalIva = 0;
            $usuario = "";

            $afip = new Afip(array('CUIT' => env("CUIT"), 'production' => env("PRODUCTION")));

            //dd($detalleVenta);
            foreach($detalleVenta as $item){
                /*$valor = $item->precio_venta;
                $iva = $item->precio_venta;
                $cosumidor= $item->tipo_venta;
                $sinIva= $valor /(1+($iva/100));
                $sinIva = bcdiv($sinIva, '1', 2);
                $importe =$sinIva*(1+($iva/100));
                $importe =  bcdiv($importe, '1', 2);

                $valorIva= $valor - $sinIva;

                */

                $total = $total + $item->precio_venta*$item->cantidad;
                $sinIva= $item->precio_venta*$item->cantidad /(1+($item->IVA/100));
                $totalIva= $totalIva + $sinIva;
                $cuit = $item->cuit;
                $cliente = $item->cliente;
                $domicilio = $item->domicilio;
                $usuario = $item->nombre;
            }


            
            $sinIva = bcdiv($sinIva, '1', 2);
            $totalIva = bcdiv($totalIva, '1', 2);
            $total = bcdiv($total, '1', 2);
            $diferencia = $total - $totalIva;
            /* echo "Total : " .$total."<br>";
            echo "precio sin iva : " .$totalIva."<br>";
            echo "diferencia : " .$diferencia."<br>";
                */

            $cbeTipo = 6;
            $docType = 99;
            if ($total < 10000) {
                $docNro = 0;
            }

            $last_voucher = $afip->ElectronicBilling->GetLastVoucher(env("PTO_VTA"), $cbeTipo);
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
                'ImpNeto' 	=> $totalIva, // Importe neto gravado
                'ImpOpEx'     => 0,   // Importe exento de IVA
                'ImpIVA' 	=> $diferencia,  //Importe total de IVA
                'ImpTrib'     => 0,   //Importe total de tributos
                'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
                'MonCotiz'     => 1,     // Cotización de la moneda usada (1 para pesos argentinos)
                'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
                    array(
                        'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
                        'BaseImp' 	=> $totalIva, // Base imponible
                        'Importe' 	=> $diferencia // Importe
                    )
                ),
            );

            $res = $afip->ElectronicBilling->CreateVoucher($data);

            //$option_types = $afip->ElectronicBilling->GetOptionsTypes();

            $venta = Venta::where('id', '=', $venta)->first();
            $venta->factura = $last_voucher + 1;
            $venta->save();


            /*echo $res['CAE'] . "<br>"; //CAE asignado el comprobante
            $vencimientoCae= date("d-m-Y", strtotime($res['CAEFchVto']));
            echo $vencimientoCae . "<br>"; //Fecha de vencimiento del CAE (yyyy-mm-dd)
            */


            $nroIngresoBrutos = "907-541045-0";
            $inicioAct = "01/04/1991";


            $last_voucher = $last_voucher + 1;
            $countFact=1;

            try {
                
                while($countFact!=3){
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
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
                $printer->text("CAE:" . $res['CAE'] . "\n");
                $printer->text("VTO CAE:" . $res['CAEFchVto'] . "\n");
                $printer->feed(1);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - " . $usuario . " \n");


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
        } catch (\Throwable $th) {
            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error : " . $th->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }
    function ticket($venta)
    {

        $detalleVenta = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->join('users', 'users.id', '=', 'ventas.iduser')
            ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
            ->where('detalle_ventas.id_venta', '=', $venta)
            ->get();
        $nroIngresoBrutos = "907-541045-0";
        $inicioAct = "01/04/1991";
        

        

        $countFact=1;
        try {
            while($countFact!=3){
            $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
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

            $printer->feed(1);
            $printer->text("----------------------------------------------------\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Ticket                                Comprobante:" . $venta . "\n");
            $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
            $printer->text("                                      Hora:" . date("H:i:s") . "\n");
            $printer->text("----------------------------------------------------\n");
            $printer->feed(1);
            $printer->text("Detalle                      P/U        CANT     Importe \n");
            $total = 0;
            foreach ($detalleVenta as $item) {
                $articulo = substr($item->Descripcion, 0, 20);
                $total = $total + $item->precio_venta;
                $usuario = $item->nombre;
                $printer->text(str_pad($articulo, 20) . "        $". str_pad($item->precio_venta,5)."        " . $item->cantidad . "     $" . $item->subtotal . "\n");
            }

            $printer->feed(2);
            $total = $detalleVenta[0]->total;
            if($detalleVenta[0]->descuento>=(($total*10)/100)){
                $printer->text("                          Descuento:       $" .$detalleVenta[0]->descuento  . "\n");
            }
            
            $printer->text("                             Total:        $" . $total . "\n");


            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Gracias por su compra \n");
            $printer->text("Fue Atendido por  - " . $usuario . " \n");
            $printer->cut();
            $printer->close();
            $countFact++;
        }
            return 22;
        } catch (Exception $e) {

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error : " . $e->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
        } 
    }
    function entregaMercaderia($venta,$cliente)
    {
        $datosCliente = DB::table('clientes')
            ->join('xtipoiva', 'xtipoiva.codigo', '=', 'clientes.IVA_tipo')
            ->select('clientes.Nombre', 'clientes.Domicilio', 'clientes.CUIT', 'xtipoiva.TipoIva', 'xtipoiva.Codigo')
            ->where('clientes.id', '=', $cliente)
            ->first();
        $countFact = 1;

        $nroIngresoBrutos = "907-541045-0";
        $inicioAct = "01/04/1991";
        $detalleVenta = DB::table('detalle_ventas')
            ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_ventas.id_producto')
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.id_venta')
            ->join('users', 'users.id', '=', 'ventas.iduser')
            ->select('detalle_ventas.*', 'productoscombinacionescabecera.Descripcion', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA', 'ventas.*', 'users.nombre')
            ->where('detalle_ventas.id_venta', '=', $venta)
            ->get();
        $usuario = "";
        try {

            while ($countFact != 3) {
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
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
                $printer->text("----------------------------------------------------\n");

                $printer->text("Cuenta Corriente                        \n");
                $printer->text("Cliente:" . $datosCliente->Nombre . "                    \n");
                $printer->text("CUIT:" . $datosCliente->CUIT . "                    \n");



                $printer->feed(1);
                $printer->text("----------------------------------------------------\n");
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("Remito                        \n");

                $printer->text("                                      Fecha:" . date('d-m-Y') . "\n");
                $printer->text("                                      Hora:" . date("H:i:s") . "\n");
                $printer->text("                                      ID:" . $venta . "\n");
                $printer->text("----------------------------------------------------\n");
                $printer->feed(1);
                $printer->text("Detalle                              CANT     \n");

                foreach ($detalleVenta as $item) {
                    $usuario = $item->nombre;
                    $articulo = substr($item->Descripcion, 0, 20);
                    $printer->text(str_pad($articulo, 20) . "                 " . $item->cantidad . "          \n");
                }

                $printer->feed(3);

                $printer->text("Firma :  \n");
                $printer->feed(1);
                $printer->text("Aclaracion :  \n");
                $printer->text("Dni :  \n");
                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Gracias por su compra \n");
                $printer->text("Fue Atendido por  - " . $usuario . " \n");


                $printer->cut();

                // Close printer
                $printer->close();
                $countFact++;
            }

            return 0;
        } catch (Throwable  $e) {

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = 1;
            $log->detalle = "Surgio un error - Linea: " . $e->getLine() . "--" . $e->getMessage();
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();
            return 20;
        }
    }
}
