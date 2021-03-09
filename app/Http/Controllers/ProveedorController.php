<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;
use App\Events\ProveedorTable;
use DB;
use App\log;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('proveedores.index');
    }

    //BUSCAR PROVEEDORES POR EL NOMBRE
    public function buscarProveedores(Request $request)
    {
        if ($request->filtro == 'nombre') {
            $proveedor = Proveedor::where('nombre', 'like', '%'.$request->proveedorBuscado.'%')->paginate(10);
        } else {
            if ($request->filtro == 'cuit') {
                $proveedor = Proveedor::where('cuit', 'like', '%'.$request->proveedorBuscado.'%')->paginate(10);
            } else {
                $proveedor = Proveedor::paginate(10);
            }
        }

        return $proveedor;
    }

    //DATOS DE UN PROVEEDOR ESPECÍFICO
    public function infoProveedor(Request $request)
    {
        $proveedor = Proveedor::find($request->id);
        return json_encode($proveedor);
    }

    //CREATE - CRUD
    public function createProveedor(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'cuit' => 'required|min:11|max:11|unique:proveedores,cuit,'.$request->id,
            'telefono' => 'required|numeric',
            'email' => 'required|email:rfc',
            'direccion' => 'required',
            'ciudad' => 'required',
            'tipo_moneda' => 'required',
            'tipo_articulos' => 'nullable',
            'banco' => 'nullable',
            'cbu' => 'nullable'
        ], [
            'nombre.required'=>'Debe ingresar la razón social del proveedor',
            'cuit.required'=>'Debe ingresar el CUIT del proveedor',
            'cuit.unique' =>'El CUIT ingresado ya pertenece a un proveedor',
            'cuit.min' =>'El CUIT debe tener un mínimo de 11 caracteres',
            'cuit.max' =>'El CUIT debe tener un máximo de 11 caracteres',
            'cuit.numeric' =>'El CUIT solo puede contener caracteres numéricos',
            'telefono.required'=>'Debe ingresar el teléfono del proveedor',
            'telefono.numeric'=>'El teléfono solo puede contener caracteres numéricos',
            'email.required'=>'Debe ingresar el correo electrónico del proveedor',
            'email.email'=>'Ingrese un correo electrónico con un formato válido',
            'direccion.required'=>'Debe ingresar la direccion del proveedor',
            'ciudad.required'=>'Debe ingresar la ciudad del proveedor',
            'tipo_moneda.required'=>'Debe ingresar el tipo de moneda con el que trabaja el proveedor',
        ]);

        try {
            DB::beginTransaction();
            $proveedor = new Proveedor();

            $proveedor->nombre = $request->nombre;
            $proveedor->cuit = $request->cuit;
            $proveedor->telefono = $request->telefono;
            $proveedor->email = $request->email;
            $proveedor->direccion = $request->direccion;
            $proveedor->ciudad = $request->ciudad;
            $proveedor->tipo_moneda = $request->tipo_moneda;
            $proveedor->tipo_articulos = $request->tipo_articulos;
            $proveedor->banco = $request->banco;
            $proveedor->cbu = $request->cbu;
            $proveedor->retenciones = $request->retenciones;

            $proveedor->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Creó un nuevo proveedor: ".$proveedor->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new ProveedorTable());
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
        }
    }

    //READ
    public function readProveedores()
    {
        $proveedores = Proveedor::all();
        return $proveedores;
    }

    //UPDATE - CRUD
    public function updateProveedor(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required',
            'cuit' => 'required|min:11|max:11|unique:proveedores,cuit,'.$request->id,
            'telefono' => 'required|numeric',
            'email' => 'required|email:rfc',
            'direccion' => 'required',
            'ciudad' => 'required',
            'tipo_moneda' => 'required',
            'tipo_articulos' => 'nullable',
            'banco' => 'nullable',
            'cbu' => 'nullable'
        ], [
            'nombre.required'=>'Debe ingresar la razón social del proveedor',
            'cuit.required'=>'Debe ingresar el CUIT del proveedor',
            'cuit.unique' =>'El CUIT ingresado ya pertenece a un proveedor',
            'cuit.min' =>'El CUIT debe tener un mínimo de 11 caracteres',
            'cuit.max' =>'El CUIT debe tener un máximo de 11 caracteres',
            'cuit.numeric' =>'El CUIT solo puede contener caracteres numéricos',
            'telefono.required'=>'Debe ingresar el teléfono del proveedor',
            'telefono.numeric'=>'El teléfono solo puede contener caracteres numéricos',
            'email.required'=>'Debe ingresar el correo electrónico del proveedor',
            'email.email'=>'Ingrese un correo electrónico con un formato válido',
            'direccion.required'=>'Debe ingresar la direccion del proveedor',
            'ciudad.required'=>'Debe ingresar la ciudad del proveedor',
            'tipo_moneda.required'=>'Debe ingresar el tipo de moneda con el que trabaja el proveedor',
        ]);

        try {
            DB::beginTransaction();
            $proveedor = Proveedor::find($request->id);

            $proveedor->nombre = $request->nombre;
            $proveedor->cuit = $request->cuit;
            $proveedor->telefono = $request->telefono;
            $proveedor->email = $request->email;
            $proveedor->direccion = $request->direccion;
            $proveedor->ciudad = $request->ciudad;
            $proveedor->tipo_moneda = $request->tipo_moneda;
            $proveedor->tipo_articulos = $request->tipo_articulos;
            $proveedor->banco = $request->banco;
            $proveedor->cbu = $request->cbu;
            $proveedor->retenciones = $request->retenciones;

            $proveedor->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Actualizó información de proveedor: " .$proveedor->id;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new ProveedorTable());
            return 1;
        } catch (\Throwable $th) {
            DB::rollback();
            return 0;
        }
    }

    //DELETE - CRUD
    public function deleteProveedor(Request $request)
    {
        try {
            DB::beginTransaction();

            $proveedor = Proveedor::find($request->id);
            $proveedor->delete();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Eliminó proveedor";
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();

            event(new ProveedorTable());
            return 1;
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public function infoProveedorEspecifica(Request $request)
    {
        $datosProveedor = DB::table('proveedores')
        ->select('proveedores.*')
        ->where('proveedores.id','=',$request->id)
        ->get();

        $deudasProveedor =DB::table('pedidos_proveedores')
        ->where('idProveedor','=',$request->id)
        ->groupBy('id')
        ->get()->sum("saldo");

        return [
            'datosProveedor' => $datosProveedor,
            'deudasProveedor' => $deudasProveedor,
        ];
    }
}
