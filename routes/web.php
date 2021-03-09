<?php
use App\Sucursales;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//RUTA DE PRUEBA WEBSERVICE


Route::get('logout', 'Usercontroller@logout');
Route::post('realiza_transferencia', 'TransferenciaController@realiza_transferencia');
Route::post('acceso', 'UserController@acceso');

Route::group(['middleware' => ['auth']], function () {

    Route::get('carritostock', 'TransferenciaController@carritostock');
    Route::post('carritoAddStock', 'TransferenciaController@CarritoAddStock');
    Route::post('carritoDeleteTransfer', 'TransferenciaController@CarritoDeleteTransfer');


    route::post('confirmaPedido', 'TransferenciaController@confirmaPedido');

    Route::get('recepcion', function () {
        return view('stock.recepciones');
    });

    Route::get('pedidos', function () {
        return view('pedidos');
    });
    Route::group(['middleware' => ['permission:Abrir Caja|Cerrar Caja|Extraer Dinero Caja|Ver Listado Ventas|Ver Listado Ventas CC']], function () {
        Route::get('/Caja', 'CajaController@index');
    });

    route::get('Reportes', function () {
        return view('Reportes');
    });
    route::get('anulaciones', function () {
        return view('anulaciones');
    });

    route::get('presupuestos', function () {
        return view('presupuestos');
    });


    Route::group(['middleware' => ['permission:Realizar Ventas']], function () {
        Route::get('/Ventas', 'VentasController@index');
    });

    Route::group(['middleware' => ['permission:Emitir Nota Credito']], function () {
        Route::get('NotasCredito', 'NotasCreditoController@index');
    });

    Route::group(['middleware' => ['permission:Autorizar Ventas CC']], function () {
        Route::get('/AutorizarVentasCuentaCorriente', 'AutorizarVentaCCController@index');
    });

    Route::group(['middleware' => ['permission:Realizar Ingreso Cobranza|Ver Ingresos Cobranza|Ver Ventas CC']], function () {
        Route::get('/IngresoCobranza', 'IngresoCobranzaController@index');
    });

    Route::group(['middleware' => ['permission:Ver Info Proveedores|Editar Proveedores|Eliminar Proveedores|Crear Proveedores']], function () {
        Route::get('Proveedores', 'ProveedorController@index');
    });

    Route::group(['middleware' => ['permission:Crear Pedidos']], function () {
        Route::get('PedidosProveedor', 'PedidoProveedorController@index');
    });

    Route::group(['middleware' => ['permission:Ver Info Pedidos|Gestionar Pagos Pedido|Eliminar Pedidos|Descargar Detalle Pedido']], function () {
        Route::get('GestionPedidos', 'PedidoProveedorController@indexGestion');
    });

    Route::group(['middleware' => ['permission:Crear Cliente|Editar Cliente|Eliminar Cliente|Ver Informacion Cliente']], function () {
        Route::get('Clientes', 'ClienteController@index');
    });

    Route::group(['middleware' => ['permission:Crear Productos|Aumentar Bajar Precios Productos|Editar Productos|Eliminar Productos|Aumentar Stock']], function () {
        Route::get('Productos', 'ProductoController@index');
    });

    Route::group(['middleware' => ['permission:Realizar Transferencias']], function () {
        Route::get('transferencia', 'TransferenciaController@transferencia');
    });

    Route::group(['middleware' => ['permission:Crear Usuario']], function () {
        Route::get('/RegistrarUsuarios', 'UserController@index');
    });

    Route::group(['middleware' => ['permission:Editar Usuario|Eliminar Usuario|Restablecer Contraseña']], function () {
        Route::get('/GestionUsuarios', 'UserController@indexGestion');
    });

    Route::group(['middleware' => ['permission:Listado Transferencias']], function () {
        route::get('listadoTransferencias', 'TransferenciaController@listadoTransferencias');
    });

    Route::group(['middleware' => ['permission:Recepciones']], function () {
        Route::get('recepcion', function () {
            return view('stock.recepciones');
        });
    });



    route::post('confirmaRecep', 'TransferenciaController@confirmaRecep');
    Route::post('storePago', "PedidoProveedorController@storePago");

    route::get('dashboard', function () {
        return view('dashboard');
    });

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
});

Route::get('/', function () {
    if (auth::check()) {
        return view('dashboard');
    } else {
        //$sucursal = Sucursales::Where('estado', '=', 'P')->first();
        return view('auth.login', compact('sucursal'));
    }
})->name("/");
