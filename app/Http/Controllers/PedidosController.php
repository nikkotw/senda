<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CajaController;
use App\Pedido;
use App\Cliente;
use App\DetallePedido;
use App\User;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Env;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;

class PedidosController extends Controller
{
    //
    public function add(Request $request){
        /*$connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
        // Print a "Hello world" receipt"
        $printer = new Printer($connector);
        $printer->cut();
        $printer->close();
        dd("nada");
        */
            $pedido = new Pedido;
            $pedido->idCliente = $request->cliente;
            $pedido->total = $request->total;
            $pedido->fecha = date('Y-m-d');
            $pedido->direccion = 'Direccion';
            $pedido->estado =1;          // Estados       //1: Enviado a deposito    //2: Facturado   //3: Cobrado
            $pedido->save();        
            
            $detalle = new Collection($request->pedido);
            //dd($detalle);
                foreach($detalle as $item){
                    $detalle = new DetallePedido;
                    $detalle->idPedido = $pedido->id;
                    $detalle->idProducto = $item['IdProducto'];   
                    $detalle->cantidad = $item['cantidad'];
                    $detalle->save();
                }
                $usuario = User::Where('id','=',$request->idUser)->first();
                $this->imprimeDeposito($pedido->idCliente,$pedido->id,$pedido->direccion,$usuario);
            
            return 0; 

        }

    function imprimeDeposito($cliente, $pedido,$domicilio,$usuario){
              
                $cliente = Cliente::Where("id","=",$cliente)->first();
               // $detallePedido = DetallePedido::Where("id",'=',$pedido);

                $detallePedido = DB::table('detalle_pedido')
                ->join('productoscombinacionescabecera', 'productoscombinacionescabecera.IdProducto', '=', 'detalle_pedido.idProducto')
                ->select('detalle_pedido.*', 'productoscombinacionescabecera.Descripcion','productoscombinacionescabecera.precio_venta_1', 'productoscombinacionescabecera.stock', 'productoscombinacionescabecera.IVA')
                ->where('detalle_pedido.idPedido', '=', $pedido)
                ->get();

                $contador=0;
                $connector = new NetworkPrintConnector(env("PRINTER_IP"), 9100);
                // Print a "Hello world" receipt"
                $printer = new Printer($connector);
               while($contador<2){
               
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("PEDIDO N°". $pedido. "\n");
                $printer -> setTextSize(2, 1);
                $printer->text($cliente->Nombre ."\n");

                $printer -> setTextSize(2, 1);    
                $printer->text("Domicilio :".$domicilio." \n");  
                $printer->selectPrintMode(Printer::MODE_FONT_B);
                $printer->feed(3);
                $printer->text("Detalle                             CANT \n");
                foreach ($detallePedido as $item) {
                    $articulo = substr($item->Descripcion, 0, 30);
                    $printer->text(str_pad($articulo,30) . "                        " . $item->cantidad  .  " \n");
                }
                $printer->feed(3);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("********PEDIDO******** \n");
                $printer->text("Realizado por  - " . $usuario . " \n");

                $printer->cut();
                $contador++;
               }
               

               $printer->close();
    }

    function buscaPedido(Request $request){
        $pedido = Pedido::Where('id', '=', $request->busqueda)->first();
        if($pedido!=null) {
            $cliente = Cliente::where('id', '=', $pedido->idcliente)->first();
            $detalle = DB::table('detalle_pedido')
                ->join('productoscombinacionescabecera', 'detalle_pedido.idProducto', '=', 'productoscombinacionescabecera.IdProducto')
                ->select('productoscombinacionescabecera.*', 'detalle_pedido.cantidad')
                ->where('detalle_pedido.idPedido', '=', $request->busqueda)
                ->get();   
                return compact('detalle', 'presupuesto','cliente');
            
        }else{
            return 10;
        }
    
            
        return 20;
    }

}

