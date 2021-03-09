<template>
  <div class="ml-1 mr-1">
    <div class="row mt-1 justify-content-md-center">
      <!-- CREAR PEDIDOS -->
      <!-- Crear nuevo pedido -->
      <div v-if="showListadoPedidos == true && $auth.can('Crear Pedidos')" class="col col-lg">
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Crear un pedido
            <button
              type="button"
              v-if="showCreatePedido == true"
              class="float-right btn btn-secondary"
              @click="listarProveedores();showCreatePedido = false;"
            >Crear un pedido</button>
            <button
              type="button"
              v-if="showCreatePedido == false"
              class="float-right btn btn-secondary"
              @click="cancelarPedido()"
            >Cancelar</button>
          </div>
        </div>
      </div>

      <!-- Separador -->
      <div class="w-100"></div>

      <div v-if="showConfirmarPedido" class="col col-lg mt-1">
        <button @click="descargarPdf" class="btn btn-success">Generar PDF</button>
      </div>

      <div v-if="showConfirmarPedido" class="w-100"></div>

      <!-- Datos básicos del pedido -->
      <div v-if="showCreatePedido == false" class="col col-lg mt-1">
        <div class="card">
          <div
            class="card-header text-left font-weight-bold bg-transparent"
          >Datos básicos del pedido</div>
          <ul class="list-group list-group-flush">
            <!--Proveedor al que se le hace el pedido-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Proveedor:</span>
                </div>
                <select
                  :disabled="showConfirmarPedido"
                  v-model="proveedor"
                  @change="listarProductos();limpiarDetallePedido()"
                  class="form-control"
                >
                  <option
                    disabled
                    selected
                    value
                  >Seleccione el proveedor al que le quiere realizar el pedido</option>
                  <option
                    v-for="proveedor in listadoProveedores"
                    :key="proveedor.id"
                    :value="proveedor.id"
                  >{{proveedor.nombre}} - {{proveedor.cuit}}</option>
                </select>
              </div>
            </li>
            <!--Fecha en la que se realizó el pedido-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha en la que se realizó el pedido:</span>
                </div>
                <input
                  :disabled="showConfirmarPedido"
                  class="form-control"
                  type="date"
                  v-model="fechaPedido"
                />
              </div>
            </li>
            <!--Fecha estimada de arribo-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha estimada de arribo del pedido:</span>
                </div>
                <input
                  :disabled="showConfirmarPedido"
                  class="form-control"
                  type="date"
                  v-model="fechaArribo"
                />
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Separador -->
      <div
        class="w-100"
        v-if="proveedor != '' && showCreatePedido == false && showConfirmarPedido == false"
      ></div>

      <!-- Listar productos de proveedor específico -->
      <div
        class="col col-lg mt-1"
        v-if="proveedor != '' && showCreatePedido == false && showConfirmarPedido == false"
      >
        <div class="card">
          <div class="card-header text-left bg-transparent font-weight-bold">
            Productos del proveedor
            <button
              type="button"
              v-if="showProductosProveedor == true"
              class="float-right btn btn-secondary"
              @click="showProductosProveedor = false;"
            >Ver los productos</button>
            <button
              type="button"
              v-if="showProductosProveedor == false"
              class="float-right btn btn-secondary"
              @click="showProductosProveedor = true"
            >Ocultar los productos</button>
          </div>
          <div v-if="showProductosProveedor == false">
            <div class="input-group">
              <input
                type="text"
                class="form-control"
                v-model="productoEspecifico"
                placeholder="Buscar producto específico"
              />
              <div class="input-group-append">
                <button class="btn btn-primary" v-on:click="listarProductos()" type="button">Buscar</button>
              </div>
            </div>

            <table class="table table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Precio de costo</th>
                  <th>Precio 1</th>
                  <th>Precio 2</th>
                  <th>Precio 3</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in listadoProductos" :key="item.id">
                  <td>{{item.descripcion}}</td>
                  <td>{{item.precio_costo}}</td>
                  <td>
                    <input
                      @dblclick="editarPrecioVenta1(item.IdProducto,item.precio_venta_1,$event)"
                      @focusout="editarPrecioVenta1(item.IdProducto,item.precio_venta_1,$event)"
                      :readonly="true"
                      class="form-control"
                      type="number"
                      v-model="item.precio_venta_1"
                      data-toggle="tooltip"
                      title="Haz doble click para editar. Luego haz click afuera para finalizar la edición."
                    />
                  </td>
                  <td>
                    <input
                      @dblclick="editarPrecioVenta2(item.IdProducto,item.precio_venta_2,$event)"
                      @focusout="editarPrecioVenta2(item.IdProducto,item.precio_venta_2,$event)"
                      :readonly="true"
                      class="form-control"
                      type="number"
                      v-model="item.precio_venta_2"
                      data-toggle="tooltip"
                      title="Haz doble click para editar. Luego haz click afuera para finalizar la edición."
                    />
                  </td>
                  <td>
                    <input
                      @dblclick="editarPrecioVenta3(item.IdProducto,item.precio_venta_3,$event)"
                      @focusout="editarPrecioVenta3(item.IdProducto,item.precio_venta_3,$event)"
                      :readonly="true"
                      class="form-control"
                      type="number"
                      v-model="item.precio_venta_3"
                      data-toggle="tooltip"
                      title="Haz doble click para editar. Luego haz click afuera para finalizar la edición."
                    />
                  </td>
                  <td>
                    <button
                      @click="listarDetallePedido(item.IdProducto,$event);"
                      class="btn btn-success btn-sm"
                    >+</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="card-footer">
              <nav aria-label="Navegación">
                <ul class="pagination justify-content-center">
                  <li class="page-item" v-if="paginationListadoProducto.current_page > 1">
                    <a
                      class="page-link"
                      href="#"
                      @click.prevent="changePageListadoProducto(paginationListadoProducto.current_page - 1)"
                    >Atrás</a>
                  </li>
                  <li
                    class="page-item"
                    v-for="page in pagesNumberListadoProducto"
                    :key="page"
                    v-bind:class="[ page == isActivedListadoProducto ? 'active' : '']"
                  >
                    <a
                      class="page-link"
                      href="#"
                      @click.prevent="changePageListadoProducto(page)"
                    >{{page}}</a>
                  </li>
                  <li class="page-item">
                    <a
                      class="page-link"
                      href="#"
                      v-if="paginationListadoProducto.current_page < paginationListadoProducto.last_page"
                      @click.prevent="changePageListadoProducto(paginationListadoProducto.current_page + 1)"
                    >Siguiente</a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <!-- Separador -->
      <div class="w-100" v-if="showCreatePedido == false"></div>

      <!-- Listar el detalle de pedido -->
      <div class="col col-lg mt-1" v-if="showCreatePedido == false">
        <div class="card" v-if="listadoDetallePedido.length != 0">
          <div class="card-header text-left font-weight-bold">
            Detalle del pedido
            <button
              type="button"
              v-if="showDetallePedido == true && this.showConfirmarPedido == false"
              class="float-right btn btn-secondary"
              @click="showDetallePedido = false;"
            >Ver detalle del pedido</button>
            <button
              type="button"
              v-if="showDetallePedido == false && this.showConfirmarPedido == false"
              class="float-right btn btn-secondary"
              @click="showDetallePedido = true"
            >Ocultar detalle del pedido</button>
          </div>

          <div v-if="showDetallePedido == false">
            <table class="table table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio de costo</th>
                  <th>Acciones</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in listadoDetallePedido" :key="item.IdProducto">
                  <td>{{item.descripcion}}</td>
                  <td>
                    <input
                      step="0.01"
                      :disabled="showConfirmarPedido"
                      @keyup="calcularTotal"
                      class="form-control"
                      type="number"
                      v-model="item.cantidad"
                    />
                  </td>
                  <td>{{item.precio_costo}}</td>
                  <td>
                    <button
                      :disabled="showConfirmarPedido"
                      @click="quitarDetallePedido(item.IdProducto);"
                      class="btn btn-danger btn-sm"
                    >-</button>
                  </td>
                  <td style="color:green;font-weight: bold;">
                    <input
                      type="number"
                      step="0.01"
                      :readonly="true"
                      v-bind:value="(item.subtotal = item.cantidad * item.precio_costo).toFixed(2)"
                      style="color:green;background-color: transparent;border: 0px solid;font-weight: bold;"
                    />
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Precio de costo</th>
                  <th>Acciones</th>
                  <th>Total: ${{total.toFixed(2)}}</th>
                </tr>
              </tfoot>
            </table>
            <div class="card-footer font-weight-bold">
              <button
                class="btn btn-success"
                v-if="this.showConfirmarPedido == false"
                @click="confirmarPedido()"
              >Confirmar pedido</button>
              <button
                class="btn btn-success"
                v-if="this.showConfirmarPedido == true"
                @click="createPedido"
              >Cargar pedido</button>
              <button
                class="btn btn-danger"
                v-if="this.showConfirmarPedido == true"
                @click="showConfirmarPedido = false"
              >Volver</button>
            </div>
          </div>
        </div>
      </div>
      <!-- CREAR PEDIDOS -->

      <div class="w-100"></div>

      <!-- LISTAR PEDIDOS -->
      <!--Listado de pedidos-->
      <div v-if="showCreatePedido == true && $auth.can('Ver Info Pedidos')" class="col col-lg mt-1">
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Listado de pedidos
            <button
              type="button"
              v-if="showListadoPedidos == true"
              @click="showListadoPedidos = false;listarProveedores()"
              class="float-right btn btn-secondary"
            >Buscar pedidos</button>
            <button
              type="button"
              v-if="showListadoPedidos == false"
              @click="cancelarListado()"
              class="float-right btn btn-secondary"
            >Cancelar</button>
          </div>
        </div>
      </div>

      <!--Separador-->
      <div v-if="showCreatePedido == true && showListadoPedidos == false" class="w-100"></div>

      <!--Buscar el proveedor-->
      <div v-if="showCreatePedido == true && showListadoPedidos == false " class="col col-lg mt-1">
        <div class="card">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">Proveedor:</span>
            </div>
            <select
              v-model="proveedor"
              @change="listarPedidosProveedores();limpiarListado()"
              class="form-control"
            >
              <option
                disabled
                selected
                value
              >Seleccione el proveedor del cual quiere ver los pedidos</option>
              <option
                v-for="proveedor in listadoProveedores"
                :key="proveedor.id"
                :value="proveedor.id"
              >{{proveedor.nombre}} - {{proveedor.cuit}}</option>
            </select>
          </div>
        </div>
      </div>

      <!--Separador-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length == 0"
        class="w-100"
      ></div>

      <!--Listar pedidos del proveedor-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length == 0"
        class="col col-lg mt-1"
      >
        <h2
          v-if="listadoPedidosProveedor.length == 0"
        >No se encuentran pedidos de este proveedor, o en su defecto no ha seleccionado ninguno</h2>
        <div v-if="listadoPedidosProveedor.length != 0" class="card">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Fecha del pedido</th>
                <th>Fecha de arribo</th>
                <th>Total</th>
                <th>Saldo</th>
                <th>Ver Información y Pagos</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in listadoPedidosProveedor" :key="item.id">
                <td>{{item.fecha}}</td>
                <td>{{item.fecha_arribo}}</td>
                <td>${{item.total}}</td>
                <td v-if="item.saldo > 0" class="text-success font-weight-bold">${{item.saldo}}</td>
                <td v-if="item.saldo == 0" class="text-warning font-weight-bold">${{item.saldo}}</td>
                <td
                  v-if="item.saldo < 0"
                  class="text-danger font-weight-bold"
                >${{item.saldo.toString().slice(1)}}</td>
                <td>
                  <button
                    @click="mostrarInfoPedido(item.id)"
                    class="btn btn-primary"
                  >Ver Info - Pagos</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="card-footer">
            <nav aria-label="Navegación">
              <ul class="pagination justify-content-center">
                <li class="page-item" v-if="paginationListadoPedidos.current_page > 1">
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="changePageListadoPedidos(paginationListadoPedidos.current_page - 1)"
                  >Atrás</a>
                </li>
                <li
                  class="page-item"
                  v-for="page in pagesNumberListadoPedidos"
                  :key="page"
                  v-bind:class="[ page == isActivedListadoPedidos ? 'active' : '']"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="changePageListadoPedidos(page)"
                  >{{page}}</a>
                </li>
                <li class="page-item">
                  <a
                    class="page-link"
                    href="#"
                    v-if="paginationListadoPedidos.current_page < paginationListadoPedidos.last_page"
                    @click.prevent="changePageListadoPedidos(paginationListadoPedidos.current_page + 1)"
                  >Siguiente</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <!--Separador-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length != 0"
        class="w-100"
      ></div>

      <!--Listar datos básicos del pedido-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length != 0"
        class="col col-lg mt-1"
      >
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Datos básicos del pedido
            <button
              type="button"
              v-if="showDatosPedidos == false"
              @click="showDatosPedidos = true"
              class="float-right btn btn-secondary"
            >Ver datos básicos del pedido</button>
            <button
              type="button"
              v-if="showDatosPedidos == true"
              @click="showDatosPedidos = false"
              class="float-right btn btn-secondary"
            >Ocultar</button>
          </div>

          <table v-if="showDatosPedidos == true" class="table table-bordered">
            <thead>
              <tr>
                <th>Fecha de pedido</th>
                <th>Fecha de arribo</th>
                <th>Total</th>
                <th>Saldo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in datosPedidoEspecifico" :key="item.id">
                <td>{{item.fecha}}</td>
                <td>{{item.fecha_arribo}}</td>
                <td>${{item.total}}</td>
                <td v-if="item.saldo > 0" class="text-success font-weight-bold">${{item.saldo}}</td>
                <td v-if="item.saldo == 0" class="text-warning font-weight-bold">${{item.saldo}}</td>
                <td
                  v-if="item.saldo < 0"
                  class="text-danger font-weight-bold"
                >${{item.saldo.toString().slice(1)}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!--Separador-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && listadoDetallePedido.length != 0"
        class="w-100"
      ></div>

      <!--Listar detalles del pedido-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && listadoDetallePedido.length != 0"
        class="col col-lg mt-1"
      >
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Detalles del pedido
            <button
              type="button"
              v-if="showDetallePedido == true"
              @click="showDetallePedido = false"
              class="float-right btn btn-secondary"
            >Ver detalle del pedido</button>
            <button
              type="button"
              v-if="showDetallePedido == false"
              @click="showDetallePedido = true"
              class="float-right btn btn-secondary"
            >Ocultar</button>
          </div>

          <table v-if="showDetallePedido == false" class="table table-bordered">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio de costo</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in listadoDetallePedido" :key="item.id">
                <td>{{item.descripcion}}</td>
                <td>{{item.cantidad}}</td>
                <td>${{item.precio_costo}}</td>
                <td>${{item.subtotal}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!--Separador-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length != 0"
        class="w-100"
      ></div>

      <!--Añadir pagos al pedido-->
      <div
        v-if="$auth.can('Crear Pagos') && showCreatePedido == true && showListadoPedidos == false && datosPedidoEspecifico.length != 0"
        class="col col-lg mt-1"
      >
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Agregar pagos al pedido
            <button
              type="button"
              v-if="showAddPagos == true"
              @click="showAddPagos = false"
              class="float-right btn btn-secondary"
            >Agregar pagos al pedido</button>
            <button
              type="button"
              v-if="showAddPagos == false"
              @click="cancelarPago()"
              class="float-right btn btn-secondary"
            >Ocultar</button>
          </div>

          <div v-if="showAddPagos == false" class="card-body">
            <!--Monto del pago-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Monto del pago:</span>
                </div>
                <input class="form-control" type="number" v-model="montoNuevoPago" />
              </div>
            </li>

            <!--Fecha del pago-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Fecha en la que se realizó:</span>
                </div>
                <input class="form-control" type="date" v-model="fechaNuevoPago" />
              </div>
            </li>

            <!--Medio de pago-->
            <li class="list-group-item">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Medio de pago:</span>
                </div>
                <select class="form-control" v-model="medioNuevoPago">
                  <option disabled selected value>Seleccione el medio de pago</option>
                  <option>Efectivo</option>
                  <option>Transferencia Bancaria</option>
                  <option>Cheque</option>
                </select>
              </div>
            </li>

            <div class="mt-1">
              <button class="btn btn-success" @click="createPago">Añadir Pago</button>
              <button class="btn btn-danger" @click="cancelarPago">Cancelar Pago</button>
            </div>
          </div>
        </div>
      </div>

      <!--Separador-->
      <div
        v-if="showCreatePedido == true && showListadoPedidos == false && listadoPagosPedidos.length != 0"
        class="w-100"
      ></div>

      <!--Listado de pagos del pedido-->
      <div
        v-if="$auth.can('Ver Pagos') && listadoPagosPedidos.length != 0 && showCreatePedido == true && showListadoPedidos == false"
        class="col col-lg mt-1"
      >
        <div class="card">
          <div class="card-header font-weight-bold bg-transparent text-left">
            Ver listado de pagos
            <button
              type="button"
              v-if="showListadoPagos == false"
              @click="showListadoPagos = true"
              class="float-right btn btn-secondary"
            >Ver listado de pagos</button>
            <button
              type="button"
              v-if="showListadoPagos == true"
              @click="showListadoPagos = false"
              class="float-right btn btn-secondary"
            >Ocultar</button>
          </div>
          <table v-if="showListadoPagos == true" class="table table-bordered">
            <thead>
              <th>Monto</th>
              <th>Fecha</th>
              <th>Medio de pago</th>
              <th>Acciones</th>
            </thead>
            <tbody>
              <tr v-for="item in listadoPagosPedidos" :key="item.id">
                <td>{{item.monto}}</td>
                <td>{{item.fecha}}</td>
                <td>{{item.medio_pago}}</td>
                <td>
                  <button
                    @click="eliminarPago(item.id,item.monto,item.idPedido)"
                    class="btn btn-danger"
                  >Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- LISTAR PEDIDOS -->
    </div>
  </div>
</template>

<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
<script>
export default {
  data() {
    return {
      //+++++++++++++++++++++ Listado Pedidos +++++++++++++++++++++//

      //Use la variable proveedor (Acordar de resetearla a 0 cuando cancelamos)
      //Use la variable listadoDetallePedido (Acordar de resetearla a [] cuando cancelamos)
      //Use la variable showDetallePedido (Acordar de resetearla a true cuando cancelamos)

      //Paginación de la tabla de los productos por proveedor
      paginationListadoPedidos: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },

      //Mostar las secciones
      showListadoPedidos: true,
      showDatosPedidos: true,
      showAddPagos: true,
      showListadoPagos: true,

      //Crear pagos
      montoNuevoPago: "",
      fechaNuevoPago: "",
      medioNuevoPago: "",

      //Listados
      listadoPedidosProveedor: [],
      listadoPagosPedidos: [],
      datosPedidoEspecifico: [],

      //+++++++++++++++++++++ Listado Pedidos +++++++++++++++++++++//
      //+++++++++++++++++++++ Create Pedido +++++++++++++++++++++//
      //Mostar las secciones
      showCreatePedido: true, //Sección de create
      showProductosProveedor: false, //Sección de los productos del proveedor
      showDetallePedido: false, //Sección del detalle de la compra
      showConfirmarPedido: false, //Mostrar únicamente la sección del detalle del pedido

      //Listados
      listadoProveedores: [], //Listado de los proveedores que se impirme en el select
      listadoProductos: [], //Listado de productos del proveedor seleccionado
      listadoDetallePedido: [], //Detalle del pedido

      proveedor: "", //Proveedor que se selecciona para el pedido
      total: 0, //Total de la compra
      fechaPedido: "", //Fecha en la que se realizó el pedido
      fechaArribo: "", //Fecha de arribo del pedido
      productoEspecifico: "", //Producto específico buscado en la tabla de productos
      //Paginación de la tabla de los productos por proveedor
      paginationListadoProducto: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      }

      //+++++++++++++++++++++ Create Pedido +++++++++++++++++++++//
    };
  },
  computed: {
    //Paginación del listado de los productos (Crear pedidos)//
    isActivedListadoProducto: function() {
      return this.paginationListadoProducto.current_page;
    },
    pagesNumberListadoProducto: function() {
      if (!this.paginationListadoProducto.to) {
        return [];
      }
      var from = this.paginationListadoProducto.current_page - 5;

      if (from < 1) {
        from = 1;
      }

      var to = from + 5 * 5;

      if (to >= this.paginationListadoProducto.last_page) {
        to = this.paginationListadoProducto.last_page;
      }

      var pagesArray = [];

      while (from <= to) {
        pagesArray.push(from);
        from++;
      }

      return pagesArray;
    },
    //Paginación del listado de los productos (Crear pedidos)//

    //Paginación del listado de los pedidos (Listar pedidos)//
    isActivedListadoPedidos: function() {
      return this.paginationListadoPedidos.current_page;
    },
    pagesNumberListadoPedidos: function() {
      if (!this.paginationListadoPedidos.to) {
        return [];
      }
      var from = this.paginationListadoPedidos.current_page - 5;

      if (from < 1) {
        from = 1;
      }

      var to = from + 5 * 5;

      if (to >= this.paginationListadoPedidos.last_page) {
        to = this.paginationListadoPedidos.last_page;
      }

      var pagesArray = [];

      while (from <= to) {
        pagesArray.push(from);
        from++;
      }

      return pagesArray;
    }
    //Paginación del listado de los pedidos (Listar pedidos)//
  },
  methods: {
    ///+++++++++++++++++++++ Listar Pedidos +++++++++++++++++++++//
    descargarPdf: function() {
      var url = "";

      if (this.fechaArribo == "") {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(this.listadoDetallePedido) +
          "/" +
          this.proveedor +
          "/" +
          this.fechaPedido +
          "/0-0-0/" +
          this.total;
      } else {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(this.listadoDetallePedido) +
          "/" +
          this.proveedor +
          "/" +
          this.fechaPedido +
          "/" +
          this.fechaArribo +
          "/" +
          this.total;
      }

      console.log(url);

      axios
        .get(url, {
          responseType: "blob"
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "pedido.pdf");
          document.body.appendChild(link);
          link.click();
        });
    },
    limpiarListado: function() {
      this.showDatosPedidos = true;
      this.showAddPagos = true;
      this.showListadoPagos = true;
      this.showDetallePedido = false;
      this.montoNuevoPago = "";
      this.fechaNuevoPago = "";
      this.medioNuevoPago = "";
      this.listadoPedidosProveedor = [];
      this.listadoPagosPedidos = [];
      this.datosPedidoEspecifico = [];
      this.listadoDetallePedido = [];
    },
    cancelarListado: function() {
      this.showListadoPedidos = true;
      this.showDatosPedidos = true;
      this.showAddPagos = true;
      this.showListadoPagos = true;
      this.showDetallePedido = false;
      this.montoNuevoPago = "";
      this.fechaNuevoPago = "";
      this.medioNuevoPago = "";
      this.listadoPedidosProveedor = [];
      this.listadoPagosPedidos = [];
      this.datosPedidoEspecifico = [];
      this.listadoDetallePedido = [];
      this.proveedor = "";
    },
    cancelarPago: function() {
      this.montoNuevoPago = "";
      this.fechaNuevoPago = "";
      this.medioNuevoPago = "";
      this.showAddPagos = true;
    },
    eliminarPago: function(id, monto_pago, idpedido) {
      if (!this.$auth.can("Eliminar Pagos")) {
        alertify.error(
          "Usted no está autorizado para eliminar pagos de los pedidos"
        );
      } else {
        axios
          .post("api/deletePago", {
            id: id,
            monto_pago: monto_pago
          })
          .then(response => {
            alertify.success("Pago eliminado correctamente");
            this.mostrarInfoPedido(idpedido);
          })
          .catch(response => {
            alertify.error("Ha ocurrido un error al eliminar el pago");
          });
      }
    },
    createPago: function() {
      if (
        this.montoNuevoPago == "" ||
        this.fechaNuevoPago == "" ||
        this.medioNuevoPago == ""
      ) {
        alertify.error(
          "Por favor verifique que todos los datos del pago esten completos."
        );
      } else {
        axios
          .post("api/createPago", {
            monto: this.montoNuevoPago,
            fecha: this.fechaNuevoPago,
            medio_pago: this.medioNuevoPago,
            idPedido: this.datosPedidoEspecifico[0]["id"]
          })
          .then(response => {
            this.mostrarInfoPedido(this.datosPedidoEspecifico[0]["id"]);
            this.montoNuevoPago = "";
            this.fechaNuevoPago = "";
            this.medioNuevoPago = "";
            alertify.success("Pago añadido correctamente");
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    listarPedidosProveedores: function() {
      axios
        .post("api/getPedidosProveedor", {
          id: this.proveedor
        })
        .then(response => {
          this.listadoPedidosProveedor = response.data.pedidosProveedor.data;
          this.paginationListadoPedidos =
            response.data.paginationListadoPedidos;
          console.log(this.listadoPedidosProveedor);
        })
        .catch(error => {
          console.log(error);
        });
    },
    changePageListadoPedidos: function(page) {
      this.paginationListadoPedidos.current_page = page;
      this.listarProductos(page);
    },
    mostrarInfoPedido(id) {
      axios
        .post("api/getInfoPedido", {
          id: id
        })
        .then(response => {
          this.listadoPagosPedidos = response.data.pagos;
          this.listadoDetallePedido = response.data.detallePedido;
          this.datosPedidoEspecifico = response.data.pedidoEspecifico;
        })
        .catch(error => {
          console.log(error);
        });
    },
    //+++++++++++++++++++++ Create Pedido +++++++++++++++++++++//
    //Limpiar detalle pedido
    limpiarDetallePedido: function() {
      this.listadoDetallePedido = [];
    },
    //Retorna una lista de todos los proveedores
    listarProveedores: function() {
      axios
        .get("/api/readProveedores")
        .then(response => {
          this.listadoProveedores = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    //Retorna una lista de todos los productos de un proveedor específico
    listarProductos: function(page) {
      axios
        .post("/api/getListadoProductos", {
          page: page,
          idProveedor: this.proveedor,
          productoEspecifico: this.productoEspecifico
        })
        .then(response => {
          console.log(response);
          this.listadoProductos = response.data.productos.data;
          this.paginationListadoProducto =
            response.data.paginationListadoProducto;
        })
        .catch(error => {
          console.log(error);
        });
    },
    //Cambia de página en la table de CreatePedido
    changePageListadoProducto: function(page) {
      this.paginationListadoProducto.current_page = page;
      this.listarProductos(page);
    },
    //Edita el precio de venta1
    editarPrecioVenta1(id, precio_venta_1, event) {
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");
        axios
          .post("/api/editPrecioVenta1", {
            id: id,
            precio_venta_1: precio_venta_1
          })
          .then(response => {
            alertify.success("Precio de venta 1 editado correctamente");
          })
          .catch(error => {
            alertify.error("El precio de venta 1 no se pudo editar");
          });
      }
    },
    //Edita el precio de venta2
    editarPrecioVenta2(id, precio_venta_2, event) {
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");
        axios
          .post("/api/editPrecioVenta2", {
            id: id,
            precio_venta_2: precio_venta_2
          })
          .then(response => {
            alertify.success("Precio de venta 2 editado correctamente");
          })
          .catch(error => {
            alertify.error("El precio de venta 2 no se pudo editar");
          });
      }
    },
    //Edita el precio de venta3
    editarPrecioVenta3(id, precio_venta_3, event) {
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");
        axios
          .post("/api/editPrecioVenta3", {
            id: id,
            precio_venta_3: precio_venta_3
          })
          .then(response => {
            alertify.success("Precio de venta 3 editado correctamente");
          })
          .catch(error => {
            alertify.error("El precio de venta 3 no se pudo editar");
          });
      }
    },
    //Añade un producto al detalle del pedido
    listarDetallePedido(id, event) {
      event.target.setAttribute("disabled", true);

      let buscar = true;

      for (let i = 0; i < this.listadoDetallePedido.length; i++) {
        if (this.listadoDetallePedido[i]["IdProducto"] == id) {
          alertify.error(
            "No se puede ingresar dos veces el producto en el pedido"
          );
          buscar = false;
        }
      }

      if (buscar) {
        axios
          .post("/api/getProductoDetalle", {
            id: id
          })
          .then(response => {
            this.listadoDetallePedido.push(response.data);
            alertify.success("Producto añadido correctamente al pedido");
            event.target.removeAttribute("disabled");
          })
          .catch(error => {
            console.log(error);
            event.target.removeAttribute("disabled");
          });
      }
    },
    //Quita un producto del detalle del pedido
    quitarDetallePedido(id) {
      for (let i = 0; i < this.listadoDetallePedido.length; i++) {
        if (this.listadoDetallePedido[i]["IdProducto"] == id) {
          this.listadoDetallePedido.splice(i, 1);
          alertify.error("Producto eliminado correctamente del pedido");
          this.calcularTotal();
          break;
        }
      }
    },
    //Calcula el total de la compra
    calcularTotal() {
      this.total = 0;
      for (let i = 0; i < this.listadoDetallePedido.length; i++) {
        this.total = this.total + this.listadoDetallePedido[i]["subtotal"];
      }
    },
    //Verifica errores y muestra el detalle del pedido solo para cargarlo
    confirmarPedido() {
      let errors = 0;

      for (let i = 0; i < this.listadoDetallePedido.length; i++) {
        if (
          this.listadoDetallePedido[i]["cantidad"] == 0 ||
          this.listadoDetallePedido[i]["cantidad"] < 0
        ) {
          alertify.error(
            "Por favor verifique que la cantidad del producto " +
              this.listadoDetallePedido[i]["descripcion"] +
              " sea mayor o distinta de 0"
          );
          errors++;
        }
      }

      if (this.fechaPedido == "") {
        errors++;
        alertify.error(
          "Por favor ingrese la fecha en la que se realizó el pedido"
        );
      }

      if (errors == 0) {
        this.showConfirmarPedido = true;
      }
    },
    //Cancelar el pedido y limpiar todos los campos
    cancelarPedido() {
      alertify.confirm(
        "Cancelar creación de pedido",
        "¿Está seguro que desea cancelar la creación del pedido? Se borraran todo lo creado hasta el momento.",
        () => {
          this.showCreatePedido = true;
          this.showProductosProveedor = false;
          this.showDetallePedido = false;
          this.showConfirmarPedido = false;

          this.listadoProveedores = [];
          this.listadoProductos = [];
          this.listadoDetallePedido = [];

          this.proveedor = "";
          this.total = 0;
          this.fechaPedido = "";
          this.fechaArribo = "";
          this.productoEspecifico = "";

          alertify.success("Se cancelo la creación del pedido");
        },
        () => {
          this.showCreatePedido = false;
        }
      );
    },
    //Almacenar pedido y detalles en la base de datos
    createPedido() {
      alertify.confirm(
        "Cargar Pedido",
        "Esta a punto de cargar un pedido en la base de datos. ¿Está usted seguro?",
        () => {
          axios
            .post("/api/createPedidoAndDetalles", {
              idProveedor: this.proveedor,
              fecha: this.fechaPedido,
              total: this.total,
              fecha_arribo: this.fechaArribo,
              detallePedido: this.listadoDetallePedido
            })
            .then(response => {
              alertify.success("Se ha cargado correctamente el pedido");
              this.showCreatePedido = true;
              this.showProductosProveedor = false;
              this.showDetallePedido = false;
              this.showConfirmarPedido = false;

              this.listadoProveedores = [];
              this.listadoProductos = [];
              this.listadoDetallePedido = [];

              this.proveedor = "";
              this.total = 0;
              this.fechaPedido = "";
              this.fechaArribo = "";
              this.productoEspecifico = "";
            })
            .catch(error => {
              alertify.error("Ha surgido un error al cargar el pedidos");
            });
        },
        () => {
          alertify.error("Se cancelo la carga del pedido");
        }
      );
    }
    //+++++++++++++++++++++ Create Pedido +++++++++++++++++++++//
  }
};
</script>
