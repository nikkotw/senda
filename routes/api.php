<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//API TEST PARA CUALQUIER TIPO DE TEST. RUTA DIRECTA
Route::get('testing', 'ApiController@testing');

//DASHBOARD
Route::get('getDashboard', 'ApiController@getDashboard');


//API TRANSFERENCIAS
route::get('listadoTransferencias', 'ApiController@listadoTransferencias'); //LISTA TRANSFERENCIAS
route::post('detalleTransfer', 'ApiController@detalleTransfer'); //Listado de Productos en la transferencia pendiente - Menu|Realizar Transferencia
route::post('eliminaProductoTransfer', 'ApiController@eliminaProductoTransfer'); //Elimina productos de una tranferencia - Menu|Realizar Transferencia
route::post('editaProductoTransfer', 'ApiController@editaProductoTransfer');
Route::post('productosStock', "ProductoController@productosStock"); //- Obtiene los productos con su Stock
route::get('transferAll', "ApiController@transferencias");  //lista Todas las transferencias
Route::post('carritoAddStock', 'TransferenciaController@CarritoAddStock');
Route::post('newRecepcion', 'TransferenciaController@recepcion');
route::get('recepciones', 'TransferenciaController@verRecepciones');
route::post('detalleRecepcion', 'TransferenciaController@detallaRecepcion');
route::post('checkFaltantes', 'TransferenciaController@checkFaltantes');
Route::post('addDiferencias', 'TransferenciaController@addDiferencias');

Route::Get('productosStock', "ProductoController@productosStock");
route::get('transferAll', "ApiController@transferencias");
//LISTADO DE SUCURSALES
route::get('sucursales', "ApiController@sucursales");

//API CLIENTES
route::post('saveCliente', 'ClienteController@saveCliente');
route::post('editCliente', 'ClienteController@editCliente');
route::post('eliminarCliente', 'ClienteController@eliminarCliente');
route::post('getClientes', 'ClienteController@getClientes');
route::post('getInfoCliente', 'ClienteController@getInfoCliente');
route::post('getClienteInfoCharts', 'ClienteController@getClienteInfoCharts');

//REMITO PDF AFIP
route::post('generaRemito', 'ApiController@remitoTransporte');

//API TRANSFERENCIAS
route::get('listadoTransferencias', 'ApiController@listadoTransferencias');
route::post('detalleTransfer', 'ApiController@detalleTransfer');
route::post('eliminaProductoTransfer', 'ApiController@eliminaProductoTransfer');
route::post('editaProductoTransfer', 'ApiController@editaProductoTransfer');
//REMITO PDF AFIP
route::post('generaRemito', 'ApiController@remitoTransporte');

//Nuevas rutas CRUD Proveedores
Route::post('createProveedor', 'ProveedorController@createProveedor');
Route::get('readProveedores', "ProveedorController@readProveedores");
Route::post('infoProveedorEspecifica', "ProveedorController@infoProveedorEspecifica");
Route::post('updateProveedor', 'ProveedorController@updateProveedor');
Route::post('deleteProveedor', 'ProveedorController@deleteProveedor');
Route::post('buscarProveedores', "ProveedorController@buscarProveedores");
Route::post('infoProveedor', "ProveedorController@infoProveedor");

//CRUD Productos
Route::post('getProductos', 'ProductoController@getProductos');
Route::post('getInfoEditProducto', 'ProductoController@getInfoEditProducto');
Route::post('editProducto', 'ProductoController@editProducto');
Route::post('saveProducto', 'ProductoController@saveProducto');
Route::post('incrementarStock', 'ProductoController@incrementarStock');
Route::post('aumentoProducto', 'ProductoController@aumentoProducto');
Route::post('bajarProducto', 'ProductoController@bajarProducto');
Route::post('deleteProducto', 'ProductoController@deleteProducto');

//Pedidos - Create
Route::post('editPrecioVenta1', 'PedidoProveedorController@editPrecioVenta1');
Route::post('editPrecioVenta2', 'PedidoProveedorController@editPrecioVenta2');
Route::post('editPrecioVenta3', 'PedidoProveedorController@editPrecioVenta3');
Route::post('getListadoProductos', 'PedidoProveedorController@getListadoProductos');
Route::post('createPedidoAndDetalles', 'PedidoProveedorController@createPedidoAndDetalles');

//Pedidos - Listado
Route::post('getPedidos', 'PedidoProveedorController@getPedidos');
Route::post('getDetallePedido', 'PedidoProveedorController@getDetallePedido');
Route::post('deletePedido', 'PedidoProveedorController@deletePedido');

Route::post('getPagos', 'PedidoProveedorController@getPagos');
Route::post('createPago', 'PedidoProveedorController@createPago');
Route::post('deletePago', 'PedidoProveedorController@deletePago');

//PDFS
Route::get('pdfPedidos/{detallePedido}/{idProveedor}/{fecha_pedido}/{fecha_arribo}/{total}', 'PedidoProveedorController@generarPDF');
Route::get('pdfProductos/{id}', 'ProductoController@generarPDF');

//Realizar ventas
Route::post('getProductosStock', 'VentasController@getProductosStock');
Route::post('saveVentas', 'VentasController@saveVentas');
Route::post('deleteVentas', 'VentasController@deleteVentas');

//Caja
Route::post('getCajas', 'CajaController@getCajas');
Route::post('abrirCaja', 'CajaController@abrirCaja');
Route::post('datosCierreCaja', 'CajaController@datosCierreCaja');
Route::post('cerrarCaja', 'CajaController@cerrarCaja');
Route::post('getDatosClienteRegistradoVenta', 'CajaController@getDatosClienteRegistradoVenta');
Route::post('getVentas', 'CajaController@getVentas');
Route::post('getVentasAutorizadas', 'CajaController@getVentasAutorizadas');
Route::post('getDetalleVenta', 'CajaController@getDetalleVenta');
Route::post('verificarCuentaCorriente', 'CajaController@verificarCuentaCorriente');
Route::post('autorizarVentaCuentaCorriente', 'CajaController@autorizarVentaCuentaCorriente');
Route::post('finalizarVentaCuentaCorriente', 'CajaController@finalizarVentaCuentaCorriente');
Route::post('finalizarVenta', 'CajaController@finalizarVenta'); 
route::post('realizarExtraccion', 'CajaController@realizarExtraccion'); // Extraccion caja
route::post('realizarIngreso' , 'CajaController@realizarIngreso'); // Ingreso de la caja



//
route::post('facturaVentas', 'ingresoCobranzaController@facturaVentas');
//Autorización Ventas CC
Route::post('getVentasNoAutorizadas', 'AutorizarVentaCCController@getVentasNoAutorizadas');
Route::post('getDetalleVentaNoAutorizada', 'AutorizarVentaCCController@getDetalleVentaNoAutorizada');
Route::post('autorizarVenta', 'AutorizarVentaCCController@autorizarVenta');
Route::post('saveVentaCC', 'CajaController@saveVentaCC');

//EDICIONES EN LA BASE DE DATOS
Route::get('ctacteToMovimientoCC', 'AutorizarVentaCCController@ctacteToMovimientoCC');
Route::get('movimientosToCliente', 'AutorizarVentaCCController@movimientosToCliente');
Route::get('editCuits', 'AutorizarVentaCCController@editCuits');

//Ingresos de cobranza
Route::post('getListadoClientesCuentaCorriente', 'IngresoCobranzaController@getListadoClientesCuentaCorriente');
Route::post('getListadoVentasClienteCC', 'IngresoCobranzaController@getListadoVentasClienteCC');
Route::post('getDetalleVentaCC', 'IngresoCobranzaController@getDetalleVentaCC');
Route::post('getListadoCajasAbiertas', 'IngresoCobranzaController@getListadoCajasAbiertas');
Route::post('realizarIngresoCobranza', 'IngresoCobranzaController@realizarIngresoCobranza');
Route::post('getIngresosCobranza', 'IngresoCobranzaController@getIngresosCobranza');

//Notas de crédito
Route::post('getVentaPorFactura', 'NotasCreditoController@getVentaPorFactura');
Route::post('registrarClienteNotasCredito', 'NotasCreditoController@registrarClienteNotasCredito');
Route::post('getDetalleVentaNotaCredito', 'NotasCreditoController@getDetalleVentaNotaCredito');
Route::post('getClientesNotaCredito', 'NotasCreditoController@getClientesNotaCredito');
Route::post('emitirNotaCredito', 'NotasCreditoController@emitirNotaCredito');

Route::post('saveUsuario', 'UserController@saveUsuario');
Route::post('getUsers', 'UserController@getUsers');
Route::post('changeState', 'UserController@changeState');
Route::post('getUserInfo', 'UserController@getUserInfo');
Route::post('updateUser', 'UserController@updateUser');
Route::post('deleteUser', 'UserController@deleteUser');
Route::post('changePassword', 'UserController@changePassword');
Route::post('resetPassword', 'UserController@resetPassword');
route::post('getClientesVenta', 'ClienteController@getClientesVenta');

//Pedidos
route::post('addPedido','PedidosController@add');

//Reporte
route::post('reporte', 'ReportesController@reporte');
route::post('obtenerUsuario', 'ReportesController@usuarios');
route::post('reporteUsuario', 'ReportesController@reporteUsuario');
route::post('getRerpoteVentas', 'ReportesController@ventasReporte');
route::post('getVentaCliente', 'ReportesController@getVentaCliente');


//anulas
Route::post('getAnular', 'ComprobantesController@getAnular');
Route::post('setAnula', 'ComprobantesController@setAnula');
Route::post('reFactura', 'ComprobantesController@ReImprime');
Route::post('eliminaVenta', 'ComprobantesController@eliminaVenta');

//presupuestos
route::post('savePresupuesto', 'PresupuestoController@savePresupuesto');
route::post('buscaPresu', 'PresupuestoController@buscaPresupuesto');
route::post('actualizaPresu', 'PresupuestoController@actualiza');


//
route::post('buscaPedido ', 'PedidosController@buscaPedido');