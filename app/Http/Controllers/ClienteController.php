<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;
use App\Log;
use App\Http\Requests\ClienteRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        return view("clientes.informacion");
    }

    public function saveCliente(Request $request)
    {

        $this->validate($request, [
            'razonSocial' => 'required',
            'cuit' => 'required|unique:clientes|numeric',
            'telefono' => 'required|numeric',
            'email' => 'nullable|email:rfc',
            'direccion' => 'required',
            'ciudad' => 'required',
            'cp' => 'required|numeric',
            'obs' => 'nullable',
            'tipo_iva' => 'required'
        ],[
            'razonSocial.required'=>'Debe ingresar la razón social del cliente',
            'cuit.required'=>'Debe ingresar el CUIT del cliente',
            'cuit.unique' =>'El CUIT ingresado ya pertenece a un cliente',
            'cuit.numeric' =>'El CUIT solo puede contener caracteres numéricos',
            'telefono.required'=>'Debe ingresar el teléfono del cliente',
            'telefono.numeric'=>'El teléfono solo puede contener caracteres numéricos',
            'email.email'=>'Ingrese un correo electrónico con un formato válido',
            'direccion.required'=>'Debe ingresar la direccion del cliente',
            'ciudad.required'=>'Debe ingresar la ciudad del cliente',
            'cp.required'=>'Debe ingresar el codigo postal de la ciudad donde reside el cliente',
            'cp.numeric'=>'El código postal solo puede contener caracteres numéricos',
            'tipo_iva.required'=>'Debe seleccionar un Tipo de IVA'
        ]);

        try {
            DB::beginTransaction();

            $cliente = new Cliente();
            $cliente->Nombre = $request->razonSocial;
            $cliente->CUIT = $request->cuit;
            $cliente->Telefono = $request->telefono;
            $cliente->Mail = $request->email;
            $cliente->Domicilio = $request->direccion;
            $cliente->Localidad = $request->ciudad;
            $cliente->CP = $request->cp;
            $cliente->Obs = $request->obs;
            $cliente->DescuentoHabitual = $request->descuento_habitual;
            $cliente->IVA_tipo = $request->tipo_iva;
            $cliente->Alta = date('d-m-Y');
            $cliente->Activo = 'A';

            if($request->cuenta_corriente == 'accepted'){
                $cliente->cuenta_corriente = 1;
                $cliente->monto_maximo_cc = $request->monto_maximo_cc;
            }
            $cliente->save();


            $log = new Log();
            $log->idpermiso = 35;
            $log->idusuario = $request->userid;
            $log->detalle = "Regristo la información del cliente:". $request->razonSocial;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return;
        }

        return view('CargaCliente');
    }

    public function editCliente(Request $request)
    {

        $this->validate($request, [
            'razonSocial' => 'required',
            'cuit' => 'required|numeric|unique:clientes,cuit,'.$request->id,
            'telefono' => 'required|numeric',
            'email' => 'nullable|email:rfc',
            'direccion' => 'required',
            'ciudad' => 'required',
            'cp' => 'required|numeric',
            'obs' => 'nullable'
        ],[
            'razonSocial.required'=>'Debe ingresar la razón social del cliente',
            'cuit.required'=>'Debe ingresar el CUIT del cliente',
            'cuit.unique' =>'El CUIT ingresado ya pertenece a un cliente',
            'cuit.numeric' =>'El CUIT solo puede contener caracteres numéricos',
            'telefono.required'=>'Debe ingresar el teléfono del cliente',
            'telefono.numeric'=>'El teléfono solo puede contener caracteres numéricos',
            'email.email'=>'Ingrese un correo electrónico con un formato válido',
            'direccion.required'=>'Debe ingresar la direccion del cliente',
            'ciudad.required'=>'Debe ingresar la ciudad del cliente',
            'cp.required'=>'Debe ingresar el codigo postal de la ciudad donde reside el cliente',
            'cp.numeric'=>'El código postal solo puede contener caracteres numéricos'
        ]);

        try {
            DB::beginTransaction();

            $cliente = Cliente::find($request->id);

            $cliente->Nombre = $request->razonSocial;
            $cliente->CUIT = $request->cuit;
            $cliente->Telefono = $request->telefono;
            $cliente->Mail = $request->email;
            $cliente->Domicilio = $request->direccion;
            $cliente->Localidad = $request->ciudad;
            $cliente->CP = $request->cp;
            $cliente->Obs = $request->obs;
            $cliente->DescuentoHabitual = $request->descuento_habitual;
            $cliente->Alta = date('d-m-Y');
            $cliente->Activo = 'A';

            if($request->cuenta_corriente == 'accepted'){
                $cliente->cuenta_corriente = 1;
                $cliente->monto_maximo_cc = $request->monto_maximo_cc;
            }else{
                $cliente->cuenta_corriente = 0;
                $cliente->monto_maximo_cc = 0;
            }
            $cliente->save();


            $log = new Log();
            $log->idpermiso = 35;
            $log->idusuario = $request->userid;
            $log->detalle = "Edito un nuevo cliente".$request->razonSocial;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();


            DB::commit();
            return 0;
        } catch (\Throwable $th) {
            DB::rollback();
            return;
        }





        return view('CargaCliente');
    }

    public function eliminarCliente(Request $request){
        try {
            DB::beginTransaction();

            $cliente = Cliente::where("id","=",$request->id)->first();
            $cliente->delete();

            $log = new Log();
            $log->idpermiso = 35;
            $log->idusuario = $request->userid;
            $log->detalle = "Eliminó cliente";
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getClientes(Request $request){
        if ($request->filtro == 'nombre') {
            $clientes = Cliente::where('Nombre', 'like', '%'.$request->busqueda.'%')->orderBy('id', 'DESC')->paginate(10);
        }elseif ($request->filtro == 'cuit') {
            $clientes = Cliente::where('CUIT', 'like', '%'.$request->busqueda.'%')->orderBy('id', 'DESC')->paginate(10);
        }else{
            $clientes = Cliente::orderBy('id', 'DESC')->paginate(10);
        }
        return $clientes;
    }

    public function getClientesVenta(Request $request){
        
            $clientes = Cliente::where('Nombre', 'like', '%'.$request->busqueda.'%')->orderBy('id', 'DESC')->paginate(10);
            return $clientes;
    }

    public function getInfoCliente(Request $request){
        $cliente = Cliente::where('id','=',$request->id)->first();
        return $cliente;
    }

    public function getClienteInfoCharts(Request $request){
        setlocale(LC_TIME, 'es_ES');
        $cliente = Cliente::where("id","=",$request->id)->first();

        $listado = DB::table('movimientos_cc')
        ->select(DB::raw('SUM(Egreso) as egreso, SUM(Ingreso) as ingreso,MONTH(Fecha)as mes'))
        ->where('id_cliente','=',$request->id)
        ->whereYear('fecha', '=', date('Y'))
        ->groupBy('mes')
        ->orderBy('mes','DESC')
        ->limit(12)
        ->get();

        $ventas = DB::table('ventas')
        ->select(DB::raw('COUNT(*) as ventasMensuales,MONTH(Fecha)as mes'))
        ->where('cuit','=',$cliente->CUIT)
        ->whereYear('fecha', '=', date('Y'))
        ->groupBy('mes')
        ->orderBy('mes','DESC')
        ->limit(12)
        ->get();

        return array('cc' => $listado, 'ventas' => $ventas);
    }

}
