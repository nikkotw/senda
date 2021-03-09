<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Log;
use Session;
use App\User;
use App\Events\UsersTable;

class UserController extends Controller
{

    function acceso(Request $request){
        if (Auth::attempt(['user' => $request->user, 'password' => $request->password])) {
            if(Auth::user()->estado == 0){
                Auth::logout();
                return redirect('/')->with('inhabilitado', 'El usuario se encuentra inhabilitado');;
            }else{
                return redirect('/');
            }

        } else {
            return redirect('/')->with('message', 'Los datos ingresados son incorrectos');;
        }
    }

    function logout(){
        Auth::logout();
        return redirect('/');
    }

    function index(Request $request){
        return view('usuarios.usuarios');
    }

    function indexGestion(Request $request){
        return view('usuarios.gestion');
    }

    function saveUsuario(Request $request){
        $this->validate($request, [
            'nombre' => 'required',
            'dni' => 'required|unique:users,cuil|numeric',
            'telefono' => 'required|numeric',
            'direccion' => 'required',
            'usuario' => 'required|unique:users,user',
            'password' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $user = new User();
            $user->user = $request->usuario;
            $user->nombre = $request->nombre;
            $user->domicilio = $request->direccion;
            $user->telefono = $request->telefono;
            $user->cuil = $request->dni;
            $user->password = Hash::make($request->password);
            $user->ftp = 1;
            $user->estado = 1;

            $user->save();

            foreach ($request->permisos as $permiso) {
                $user->givePermissionTo($permiso);
            }

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Creo nuevo usuario: " .$request->user;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    function getUsers(Request $request){
        if ($request->filter == 'nombre') {
            $user = User::where('nombre', 'like', '%'.$request->search.'%')->where('id','!=',1)->paginate(10);
        } else {
            if ($request->filter == 'cuil') {
                $user = User::where('cuil', 'like', '%'.$request->search.'%')->where('id','!=',1)->paginate(10);
            } else {
                $user  = User::where('id','!=',1)->paginate(10);
            }
        }
        return $user;
    }

    function changeState(Request $request){
        try {
            DB::beginTransaction();
            $user = User::find($request->id);
            $user->estado == 1  ? $user->estado = 0 : $user->estado = 1;
            $user->save();

            $userLogout = User::find($request->id);
            Auth::setUser($userLogout);
            Auth::logout();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Inhabilito el usuario: " .$user->user;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            event(new UsersTable($request->page,$request->filter,$request->search));
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

    }

    function getUserInfo(Request $request){
        $user = User::find($request->id);
        $userPermissions = $user->getDirectPermissions();
        $permissions = [];
        foreach ($userPermissions as $permission) {
            array_push($permissions,$permission->name);
        }

        return(['user' => $user, 'permissions' => $permissions]);
    }

    function updateUser(Request $request){
        $this->validate($request, [
            'nombre' => 'required',
            'dni' => 'required|numeric|unique:users,cuil,'.$request->id,
            'telefono' => 'required|numeric',
            'direccion' => 'required',
            'usuario' => 'required|unique:users,user,'.$request->id,
        ]);

        try {
        DB::beginTransaction();

        $user = User::find($request->id);
        $user->user = $request->usuario;
        $user->nombre = $request->nombre;
        $user->domicilio = $request->direccion;
        $user->telefono = $request->telefono;
        $user->cuil = $request->dni;
        $user->save();

        $user->syncPermissions();

        foreach ($request->permisos as $permiso) {
            $user->givePermissionTo($permiso);
        }


        $log = new Log;
        $log->idpermiso = 100;
        $log->idusuario = $request->userid;
        $log->detalle = "Actualizó información del usuario: " .$request->usuario;
        $log->fecha  = date('Y-m-d');
        $log->hora  = date("H:i:s");
        $log->save();

        DB::commit();
        event(new UsersTable($request->page,$request->filter,$request->search));
        return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    function deleteUser(Request $request){
        try {
            DB::beginTransaction();
            $user = User::find($request->id);
            $user->syncPermissions();
            $user->delete();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Eliminó usuario";
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();


            DB::commit();
            event(new UsersTable($request->page,$request->filter,$request->search));
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    function changePassword(Request $request){
        try {
            DB::beginTransaction();
            $user = User::find($request->id);
            $user->password = Hash::make($request->password);
            $user->ftp = 0;
            $user->save();
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    function resetPassword(Request $request){
        try {
            DB::beginTransaction();
            $user = User::find($request->id);
            $random= str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890');
            $newPassword = substr($random, 0, 10);

            $user->password = Hash::make($newPassword);
            $user->ftp = 1;
            $user->save();

            $log = new Log;
            $log->idpermiso = 100;
            $log->idusuario = $request->userid;
            $log->detalle = "Reseteo contraseña del usuario ".$user->user;
            $log->fecha  = date('Y-m-d');
            $log->hora  = date("H:i:s");
            $log->save();

            DB::commit();
            return $newPassword;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
