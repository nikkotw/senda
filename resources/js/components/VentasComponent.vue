<template>
  <div class="ml-1 mr-1">
    <transition name="fade">
      <div v-if="show" class="row">
        <div class="col col-xl mt-1">
          <div v-if="!confirmaVenta" class="card">
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >
              Datos de la venta
              <div v-if="tipo_venta != ''" class="float-right">
                <button class="btn btn-primary" v-b-modal.buscaInfo>
                  Presu/Pedido
                </button>
                <b-modal
                  ref="buscaInfo"
                  id="buscaInfo"
                  size="xs"
                  title="Buscar Informacion"
                  ok-title="Buscar informacion"
                  @ok="cargaInfo()"
                >
                  <div class="form-group label-floating mt-3">
                    <b-form-group
                      label="Individual radios"
                      v-slot="{ ariaDescribedby }"
                    >
                      <b-form-radio
                        v-model="buscaTipo"
                        :aria-describedby="ariaDescribedby"
                        name="some-radios"
                        value="Pedido"
                        >Pedido</b-form-radio
                      >
                      <b-form-radio
                        v-model="buscaTipo"
                        :aria-describedby="ariaDescribedby"
                        name="some-radios"
                        value="Presupuesto"
                        >Presupuesto</b-form-radio
                      >
                    </b-form-group>
                    <label class="font-weight-bold" for="nroPresu">Nro</label>
                    <input
                      type="Text"
                      v-model="buscaInfo"
                      id="nroPresu"
                      ref="nroPresu"
                      class="form-control form-control-sm"
                    />
                  </div>
                </b-modal>
              </div>
              <transition name="fade">
                <h1
                  v-if="
                    clienteRegistrado != '' &&
                    clienteNoRegistrado == false &&
                    (cuit.length == 11 || cuit.length == 8)
                  "
                  class="float-right badge badge-success"
                >
                  El cliente se encuentra registrado
                </h1>
              </transition>
              <transition name="fade">
                <h1
                  v-if="
                    (tipo_venta == 'Factura_A' ||
                      tipo_venta == 'Factura_B' ||
                      datosRequeridos) &&
                    clienteRegistrado == '' &&
                    !clienteNoRegistrado
                  "
                  class="float-right badge badge-info"
                >
                  Ingrese un CUIT/CUIL/DNI válido
                </h1>
              </transition>
              <transition name="fade">
                <h4
                  v-if="
                    clienteRegistrado == '' &&
                    clienteNoRegistrado &&
                    (cuit.length == 11 || cuit.length == 8)
                  "
                  class="float-right badge badge-danger"
                >
                  El cliente no se encuentra registrado
                </h4>
              </transition>
            </div>

            <div class="card-body mt-n3 mb-n3 row" style="font-size: 12px">
              <div class="col col-lg">
                <div class="form-group">
                  <select
                    v-model="tipo_venta"
                    id="tipo_venta"
                    ref="tipo_venta"
                    class="font-weight-bold form-control form-control-sm"
                    @change="resetDatosCliente"
                  >
                    <option class="font-weight-bold" value selected disabled>
                      Seleccione el tipo de facturación que requiere la venta
                    </option>
                    <option class="font-weight-bold" value="Factura_A">
                      Factura A
                    </option>
                    <option class="font-weight-bold" value="Factura_B">
                      Factura B
                    </option>
                    <option class="font-weight-bold" value="Consumidor_Final">
                      Consumidor Final
                    </option>
                    <option class="font-weight-bold" value="No_Facturar">
                      No se requiere facturación
                    </option>
                    <option class="font-weight-bold" value="Cuenta_Corriente">
                      Cuenta Corriente
                    </option>
                    <option class="font-weight-bold" value="Pedido">
                      Pedido
                    </option>
                    <option
                      class="font-weight-bold"
                      value="Vale_Empleados"
                      style="color: #ff0000"
                    >
                      <span>Vale Empleados</span>
                    </option>
                  </select>
                </div>
              </div>

              <div class="w-100"></div>
              <transition name="fade">
                <div v-if="tipo_venta == 'Vale_Empleados'" class="col col-xl">
                  <div class="form-group label-floating">
                    <input
                      type="text"
                      v-model="empleado"
                      id="empleado"
                      class="form-control form-control-sm"
                    />
                    <label class="font-weight-bold" for="Empleado"
                      >Empleado</label
                    >
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div class="col col-xl">
                  <div class="form-group">
                    <b-form-checkbox
                      v-if="
                        tipo_venta == 'Consumidor_Final' ||
                        tipo_venta == 'No_Facturar'
                      "
                      value="accepted"
                      unchecked-value="not_accepted"
                      @change="requerirDatos"
                    >
                      <h6 class="mt-1">
                        Se requieren cargar los datos del cliente
                      </h6>
                    </b-form-checkbox>
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div
                  v-if="
                    tipo_venta == 'Consumidor_Final' ||
                    tipo_venta == 'No_Facturar' ||
                    tipo_venta == 'Cuenta_Corriente' ||
                    tipo_venta == 'Factura_A' ||
                    tipo_venta == 'Factura_B'
                  "
                  class="w-100"
                ></div>
              </transition>

              <transition name="fade">
                <div
                  v-if="
                    tipo_venta == 'Factura_A' ||
                    tipo_venta == 'Factura_B' ||
                    tipo_venta == 'Cuenta_Corriente' ||
                    datosRequeridos
                  "
                  class="col col-xl"
                >
                  <div class="form-group label-floating">
                    <input
                      type="number"
                      v-model="cuit"
                      id="cuit"
                      ref="inputCuit"
                      v-on:keyup.enter="abreModal"
                      onkeypress="if(this.value.length==11) return false;"
                      class="form-control form-control-sm"
                    />
                    <label class="font-weight-bold" for="cuit"
                      >CUIT/CUIL/DNI</label
                    >
                  </div>
                </div>
              </transition>

              <b-modal ref="my-modal" id="my-modal" size="xl" hide-footer>
                <h2 style="text-align: center">Busqueda Clientes</h2>
                <div class="input-group">
                  <input
                    type="text"
                    ref="clienteSearch"
                    class="form-control form-control-sm"
                    v-model="searchCliente"
                    placeholder="Buscar clientes"
                    v-focus
                  />
                  <div class="input-group-append"></div>
                  <button
                    class="btn btn-sm btn-primary btn-block mt-1"
                    v-on:click="getClientes()"
                    type="button"
                  >
                    Buscar
                  </button>
                </div>
                <div>
                  <b-table
                    id="listado-clientes"
                    class="mt-1"
                    style="font-size: 12px"
                    outlined
                    small
                    hover
                    fixed
                    no-border-collapse
                    head-variant="dark"
                    v-if="listadoClientes != ''"
                    :items="listadoClientes.data"
                    :fields="fieldsListadoClientes"
                  >
                    <template v-slot:cell(Nombre)="row">
                      <span class="font-weight-bold">{{
                        row.item.Nombre
                      }}</span>
                    </template>

                    <template v-slot:cell(CUIT)="row">
                      <span class="font-weight-bold">{{ row.item.CUIT }}</span>
                    </template>

                    <template v-slot:cell(Telefono)="row">
                      <span v-if="row.item.Telefono != 0">{{
                        row.item.Telefono
                      }}</span>
                      <span v-else>Sin especificar</span>
                    </template>

                    <template v-slot:cell(Mail)="row">
                      <span v-if="row.item.Mail != null">{{
                        row.item.Mail
                      }}</span>
                      <span v-else>Sin especificar</span>
                    </template>

                    <template v-slot:cell(acciones)="row">
                      <button
                        @click="setInfoCliente(row.item.id)"
                        class="btn btn-sm btn-success"
                      >
                        <i class="fa fa-check"></i>
                      </button>
                    </template>
                  </b-table>

                  <div
                    class="col text-center"
                    v-if="
                      listadoClientes.total != undefined &&
                      listadoClientes.total > 10
                    "
                    @click="getClientes()"
                  >
                    <b-pagination
                      size="sm"
                      style="font-size: 12px"
                      v-model="paginaActualListadoClientes"
                      :total-rows="listadoClientes.total"
                      :per-page="10"
                      first-text="Primera"
                      prev-text="Anterior"
                      next-text="Siguiente"
                      last-text="Última"
                      align="center"
                    ></b-pagination>
                  </div>
                </div>
              </b-modal>

              <transition name="fade">
                <div
                  v-if="
                    tipo_venta == 'Factura_A' ||
                    tipo_venta == 'Factura_B' ||
                    tipo_venta == 'Cuenta_Corriente' ||
                    datosRequeridos
                  "
                  class="col col-xl"
                >
                  <div class="form-group">
                    <input
                      v-model="cliente"
                      type="text"
                      id="nombre"
                      ref="inputNombre"
                      class="form-control form-control-sm"
                    />
                    <label
                      class="font-weight-bold"
                      ref="labelNombre"
                      for="nombre"
                      >Nombre</label
                    >
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div
                  v-if="
                    tipo_venta == 'Factura_A' ||
                    tipo_venta == 'Factura_B' ||
                    tipo_venta == 'Cuenta_Corriente' ||
                    datosRequeridos
                  "
                  class="col col-xl"
                >
                  <div class="form-group">
                    <input
                      v-model="domicilio"
                      type="text"
                      id="domicilio"
                      ref="inputDomicilio"
                      class="form-control form-control-sm"
                    />
                    <label
                      class="font-weight-bold"
                      ref="labelDomicilio"
                      for="domicilio"
                      >Domicilio</label
                    >
                  </div>
                </div>
              </transition>
            </div>
          </div>
          <div v-if="confirmaVenta" class="card">
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >
              Finalizar venta
            </div>
            <div class="card-body">
              <b-form-checkbox
                v-model="variosMetodos"
                v-if="
                  tipo_venta != 'Cuenta_Corriente' &&
                  tipo_venta != 'Vale_Empleados'
                "
                switch
                >Varios metodos de pago</b-form-checkbox
              >
              <h5
                class="font-weight-bold"
                v-if="!variosMetodos && tipo_venta != 'Cuenta_Corriente'"
              >
                Seleccione la condición de venta
              </h5>
              <h5
                class="font-weight-bold"
                v-if="tipo_venta == 'Cuenta_Corriente'"
              >
                Venta En cuenta Corriente
              </h5>
              <b-form-group v-if="!variosMetodos">
                <b-form-radio-group
                  v-if="
                    tipo_venta != 'Cuenta_Corriente' &&
                    tipo_venta != 'Vale_Empleados'
                  "
                  id="condicion_venta"
                  v-model="condicion_venta"
                  :options="options_condicion_venta"
                  button-variant="outline-success"
                  class="btn-block"
                  size="sm"
                  buttons
                  name="radios-btn-condicion-venta"
                ></b-form-radio-group>
              </b-form-group>

              <div
                v-if="
                  condicion_venta == 'Efectivo' ||
                  tipo_venta == 'Vale_Empleados'
                "
                class="row"
              >
                <div class="col col-lg">
                  <label class="font-weight-bold">Descuento efectivo:</label>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input
                      v-model="descuentoEfectivo"
                      @focusout="descuentoRedondeo()"
                      type="number"
                      class="form-control"
                      step="0.01"
                    />
                  </div>
                </div>
                <div class="col col-lg">
                  <label class="font-weight-bold">Descuento procentual:</label>
                  <div class="input-group">
                    <input
                      v-model="descuentoPorcentual"
                      @focusout="descuentoRedondeo()"
                      type="number"
                      class="form-control"
                      step="0.01"
                    />
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="variosMetodos" class="row">
                <div class="col col-lg">
                  <label class="font-weight-bold">efectivo:</label>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input
                      v-model="efectivo"
                      @change="checkMetodos"
                      type="number"
                      class="form-control"
                      step="0.01"
                    />
                  </div>
                </div>
                <div class="col col-lg">
                  <label class="font-weight-bold">Debito</label>
                  <div class="input-group">
                    <input
                      v-model="debito"
                      @change="checkMetodos"
                      type="number"
                      class="form-control"
                    />
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon2">
                        <i class="fa fa-credit-card"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col col-lg">
                  <label class="font-weight-bold">credito</label>
                  <div class="input-group">
                    <input
                      v-model="credito"
                      @change="checkMetodos"
                      type="number"
                      class="form-control"
                    />
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon2">
                        <i class="fa fa-credit-card"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <h6 class="col col-lg">
                  Total de la venta:
                  <span class="font-weight-bold text-success"
                    >${{ totalDescuentos.toFixed(2) }}</span
                  >
                  <br />
                  <div v-if="variosMetodos">
                    Total Medios de Pago:
                    <span
                      v-if="totalMetodos > total"
                      class="font-weight-bold text-success"
                      >${{ totalMetodos }}</span
                    >
                    <span v-else class="font-weight-bold text-danger"
                      >${{ totalMetodos }}</span
                    >
                  </div>
                </h6>
              </div>
            </div>
          </div>
        </div>

        <div class="w-100"></div>

        <div v-if="!confirmaVenta" class="col col-xl mt-1">
          <div class="card">
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >
              Detalle de la venta
              <b-form-checkbox
                v-if="cantidadProductos > 0 && $auth.can('Escuela')"
                class="float-right"
                v-model="escuela"
                switch
                >Escuela</b-form-checkbox
              >
              <span
                v-if="cantidadProductos > 0"
                style="float: right; margin-right: 50%"
                >Cantidad Productos : {{ cantidadProductos }}</span
              >
            </div>

            <div
              class="col col-xl float-right mt-1 mb-n3"
              style="font-size: 12px"
            >
              <button
                v-b-modal.modal-add-producto
                @click="getProductosStock()"
                class="btn btn-sm btn-success float-right mr-1"
              >
                Añadir producto
              </button>
            </div>

            <div class="w-100"></div>

            <div class="card-body mb-n3" style="font-size: 12px">
              <b-table
                id="table-transition-example"
                :items="detalleVenta"
                :fields="fieldsDetalleVenta"
                outlined
                small
                responsive
                hover
                fixed
                no-border-collapse
                head-variant="dark"
                :tbody-transition-props="{ name: 'fade' }"
              >
                <template v-slot:head(descripción)="data">
                  <span class="text-white font-weight-bold">
                    {{ data.label }}
                  </span>
                </template>

                <template v-slot:head(precio_de_venta)="data">
                  <span class="text-white font-weight-bold">
                    {{ data.label }}
                  </span>
                </template>

                <template v-slot:head(cantidad)="data">
                  <span class="text-white font-weight-bold">
                    {{ data.label }}
                  </span>
                </template>

                <template v-slot:head(subtotal)="data">
                  <span class="text-white font-weight-bold">
                    {{ data.label }}
                  </span>
                </template>

                <template v-slot:head(acciones)="data">
                  <span class="text-white font-weight-bold">
                    {{ data.label }}
                  </span>
                </template>

                <template v-slot:cell(descripción)="row">
                  <span class="font-weight-bold">
                    {{ row.item.Descripcion }}
                  </span>
                </template>

                <template v-slot:cell(acciones)="row">
                  <button
                    @click="quitarDetalleVenta(row.index)"
                    class="btn btn-sm btn-danger"
                    size="sm"
                    style="box-shadow: none !important"
                  >
                    Quitar producto
                  </button>
                </template>

                <template v-slot:cell(precio_de_venta)="row">
                  <select
                    @change="calcularTotal(row.index)"
                    style="font-size: 12px"
                    v-model="row.item.precio_venta"
                    class="bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                  >
                    <option :value="row.item.precio_venta_1">
                      Precio de venta 1: ${{ row.item.precio_venta_1 }}
                    </option>
                    <option
                      v-if="row.item.precio_venta_2 != 0"
                      :value="row.item.precio_venta_2"
                    >
                      Precio de venta 2: ${{ row.item.precio_venta_2 }}
                    </option>
                    <option
                      v-if="row.item.precio_venta_3 != 0"
                      :value="row.item.precio_venta_3"
                    >
                      Precio de venta 3: ${{ row.item.precio_venta_3 }}
                    </option>
                  </select>
                </template>

                <template v-slot:cell(cantidad)="row">
                  <input
                    style="font-size: 12px"
                    @change="validarCantidad(row.index)"
                    type="number"
                    v-model="row.item.cantidad"
                    class="my-auto bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                  />
                </template>

                <template
                  v-slot:cell(subtotal)="row"
                  class="text-center my-auto"
                >
                  <span class="text-success font-weight-bold">
                    ${{ row.item.subtotal.toFixed(2) }}
                  </span>
                </template>
              </b-table>

              <div class="row">
                <div class="col col-xl">
                  <h6 class="float-right">
                    Subtotal:
                    <span class="text-success font-weight-bold mt-1"
                      >${{ total.toFixed(2) }}</span
                    >
                  </h6>
                </div>

                <div class="w-100"></div>

                <div class="col col-xl text-left"></div>

                <div class="col col-xl">
                  <h6 class="float-right">
                    Total:
                    <span class="text-success font-weight-bold mt-1">
                      ${{ total.toFixed(2) }}
                    </span>
                  </h6>
                </div>

                <div class="w-100"></div>

                <div v-if="ventaCC" class="col col-xl text-right">
                  <h6>
                    Monto máximo en cuenta corriente:
                    <span class="font-weight-bold text-info">
                      ${{ maximoCC }}
                    </span>
                  </h6>

                  <h6 v-if="totalcc > 0">
                    Saldo en cuenta corriente:
                    <span class="font-weight-bold text-danger">
                      ${{ totalcc }}
                    </span>
                  </h6>

                  <h6 v-if="totalcc < 0">
                    Saldo en cuenta corriente:
                    <span class="font-weight-bold text-success">
                      ${{ totalcc.toString().slice(1) }}
                    </span>
                  </h6>

                  <h6 v-if="totalcc == 0">
                    Saldo en cuenta corriente:
                    <span class="font-weight-bold text-info">
                      ${{ totalcc }}
                    </span>
                  </h6>

                  <h6>
                    Disponible para gastar sin autorización:
                    <span
                      v-if="totalcc > maximoCC"
                      class="font-weight-bold text-info"
                      >$0</span
                    >
                    <span v-else class="font-weight-bold text-info">
                      ${{ maximoCC - totalcc }}
                    </span>
                  </h6>
                  <h6 v-if="ventaCC">
                    % Interes Aplicado:
                    <span class="font-weight-bold text-danger"
                      >%{{ intereses }}</span
                    >
                  </h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="w-100"></div>
        <div class="col col-xl mt-1">
          <div>
            <b-spinner v-if="loading" label="Loading..."></b-spinner>
          </div>
          <div
            v-if="detalleVenta.length > 0 && !loading"
            class="form-group col"
          >
            <button
              class="btn btn-sm btn-info"
              v-if="
                ventaCC &&
                this.totalcc +
                  (this.total - (this.descuento * this.total) / 100) >
                  this.maximoCC
              "
              @click="saveVenta(true)"
            >
              Solicitar autorización
            </button>

            <button
              v-else-if="!confirmaVenta"
              class="btn btn-sm btn-primary"
              @click="confirmarVenta()"
            >
              Confirmar Venta
            </button>

            <button
              class="btn btn-sm btn-primary"
              v-if="confirmaVenta"
              @click="
                confirmaVenta = false;
                totalDescuentos = 0;
                descuentoEfectivo = 0;
                descuentoPorcentual = 0;
                condicion_venta = '';
              "
            >
              Volver
            </button>

            <button class="btn btn-sm btn-danger" @click="cancelarVenta()">
              Rechazar Venta
            </button>

            <button
              class="btn btn-sm btn-success"
              v-if="confirmaVenta && !loading"
              @click="saveVenta(false)"
            >
              Finalizar Venta
            </button>
          </div>
        </div>

        <b-modal
          id="modal-add-producto"
          size="xl"
          hide-footer
          title="Añadir producto a la compra"
        >
          <div class="container-fluid">
            <div class="card">
              <div
                class="card-header text-primary bg-transparent font-weight-bold text-left"
              >
                Listado de productos
              </div>

              <div class="card-body">
                <div class="form-group">
                  <div class="form-group mt-1 text-center">
                    <div class="input-group">
                      <input
                        v-model="productoBuscado"
                        type="text"
                        ref="busqueda"
                        class="form-control form-control-sm"
                        placeholder="Ingrese el código de barra o el nombre del producto"
                        v-on:keyup.enter="getProductosStock()"
                        v-focus
                      />
                      <div class="input-group-append"></div>
                    </div>
                  </div>

                  <b-table
                    class="mt-1"
                    style="font-size: 12px"
                    id="listado-productos"
                    outlined
                    small
                    hover
                    responsive
                    no-border-collapse
                    head-variant="dark"
                    :items="listadoProductos.data"
                    :fields="fieldsListadoProductos"
                    :tbody-transition-props="{
                      name: 'fade',
                    }"
                  >
                    <template v-slot:head(CodBarras)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(Descripcion)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(stock)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(precio_venta_1)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(precio_venta_2)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(precio_venta_3)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:head(acciones)="data">
                      <span class="text-white font-weight-bold">{{
                        data.label
                      }}</span>
                    </template>

                    <template v-slot:cell(CodBarras)="row">
                      <p class="font-weight-bold">{{ row.item.CodBarras }}</p>
                    </template>

                    <template v-slot:cell(Descripcion)="row">
                      <p class="font-weight-bold">{{ row.item.Descripcion }}</p>
                    </template>

                    <template v-slot:cell(stock)="row">
                      <p class="font-weight-bold text-success">
                        {{ row.item.stock }}
                      </p>
                    </template>

                    <template v-slot:cell(precio_venta_1)="row">
                      <p class="font-weight-bold text-info">
                        ${{ row.item.precio_venta_1 }}
                      </p>
                    </template>

                    <template v-slot:cell(precio_venta_2)="row">
                      <p class="font-weight-bold text-info">
                        ${{ row.item.precio_venta_2 }}
                      </p>
                    </template>

                    <template v-slot:cell(precio_venta_3)="row">
                      <p class="font-weight-bold text-info">
                        ${{ row.item.precio_venta_3 }}
                      </p>
                    </template>

                    <template v-slot:cell(modif_precio)="row">
                      <span v-if="row.item.modif_precio != null">
                        {{
                          new Date(
                            row.item.modif_precio
                          ).toLocaleDateString("en-GB", { timeZone: "UTC" })
                        }}
                      </span>
                      <span v-else>Sin registrar</span>
                    </template>

                    <template v-slot:cell(cantidad)="row">
                      <input
                        style="font-size: 12px"
                        @change="validarStock(row.data, row.index)"
                        type="number"
                        v-model="row.data"
                        class="my-auto bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                        :ref="row.index"
                      />
                    </template>

                    <template v-slot:cell(acciones)="row">
                      <div class="text-center">
                        <b-button
                          @click="
                            cargarDetalleVenta(row.index, row.data);
                            calcularTotal;
                          "
                          size="sm"
                          variant="success"
                          style="box-shadow: none !important"
                          >Añadir producto</b-button
                        >
                      </div>
                    </template>
                  </b-table>

                  <div
                    v-if="
                      listadoProductos.total != undefined &&
                      listadoProductos.total > 5
                    "
                    @click="getProductosStock"
                  >
                    <b-pagination
                      aria-controls="listado-productos"
                      size="sm"
                      style="font-size: 12px"
                      v-model="paginaActualListadoProductos"
                      :total-rows="listadoProductos.total"
                      :per-page="5"
                      first-text="Primera"
                      prev-text="Anterior"
                      next-text="Siguiente"
                      last-text="Última"
                      align="center"
                    ></b-pagination>
                  </div>

                  <b-button
                    class="mt-3"
                    variant="primary"
                    block
                    @click="$bvModal.hide('modal-add-producto')"
                  >
                    Volver al detalle de la compra
                  </b-button>
                </div>
              </div>
            </div>
          </div>
        </b-modal>
      </div>
    </transition>
  </div>
</template>
<script>
Vue.directive("focus", {
  inserted: function (el) {
    el.focus();
  },
  update: function (el) {
    Vue.nextTick(function () {
      el.focus();
    });
  },
});
export default {
  data() {
    return {
      options_condicion_venta: [
        { text: "Efectivo", value: "Efectivo" },
        { text: "Tarjeta de débito", value: "Débito" },
        { text: "Tarjeta de crédito", value: "Crédito" },
      ],
      pedido:false,
      condicion_venta: "",
      escuela: false,
      confirmaVenta: false,
      totalDescuentos: 0,
      descuentoEfectivo: 0,
      descuentoPorcentual: 0,
      show: false,
      cuit: "",
      cliente: "",
      buscaTipo:"",
      empleado: "",
      Vale_Empleado: false,
      domicilio: "",
      aplicaDCC: false,
      totalcc: 0,
      maximoCC: 0,
      clienteRegistrado: [],
      clienteNoRegistrado: false,
      variosMetodos: false,
      descuento: 0,
      intereses: 0,
      loading: false,
      tipo_venta: "",
      efectivo: 0,
      debito: 0,
      credito: 0,
      datosRequeridos: false,
      cuentaCorriente: false,
      totalMetodos: 0,
      idCliente: 0,
      searchCliente: "",
      listadoClientes: [],
      cantidadProductos: 0,
      fieldsListadoClientes: [
        { key: "Nombre", label: "Cliente" },
        { key: "Domicilio", label: "Domicilio" },
        { key: "CUIT", label: "CUIT/CUIL/DNI" },
        { key: "Localidad", label: "Ciudad" },
        { key: "Telefono", label: "Teléfono" },
        { key: "Mail", label: "Correo Electrónico" },
        "acciones",
      ],
      paginaActualListadoClientes: 1,
      ventaCC: false,
      //Listado de productos a añadir
      listadoProductos: [], //Listado de los productos que tienen stock
      fieldsListadoProductos: [
        { key: "CodBarras", label: "Código de barras" },
        { key: "Descripcion", label: "Descripción" },
        { key: "stock", label: "Stock" },
        { key: "precio_venta_1", label: "Precio 1" },
        { key: "precio_venta_2", label: "Precio 2" },
        { key: "precio_venta_3", label: "Precio 3" },
        { key: "modif_precio", label: "Ultima modif." },
        "Cantidad",
        "acciones",
      ], //Columnas de la tabla del listado de produtos
      paginaActualListadoProductos: 1,
      buscaInfo: 0,
      productoBuscado: "", //Producto buscado por nombre
      //Detalle de la venta
      detalleVenta: [], //Listado detalle de venta
      fieldsDetalleVenta: [
        "descripción",
        "precio_de_venta",
        "cantidad",
        "subtotal",
        "acciones",
      ], //Columnas de la tabla del detalla de venta
      paginaActualDetalleVenta: 1, //Pagina actual detalle de venta
      datosPresu: [], //Cargar Presupuesto Provisorio
      errorStock: false, //Error para controlar la cantidad
      total: 0, //Total de la venta
      fecha_venta: "", //Fecha en la que se realizó la venta
      selected: "radio3",
      options: [
        { text: "Factura A", value: "radio1" },
        { text: "Consumidor Final", value: "radio3" },
        { text: "No se requiere facturación", value: "radio4" },
      ],
    };
  },
  beforeMount() {
    this.fecha_venta = new Date().toLocaleDateString();
    this.fecha_venta = this.fecha_venta.replace("/", "-").replace("/", "-");
    console.log(this.fecha_venta);
    console.log(window.user);
  },
  methods: {
    abreModal() {
      this.$refs["my-modal"].show();
    },
    getClientes: async function () {
      try {
        const response = await axios.post("api/getClientesVenta", {
          busqueda: this.searchCliente,
          page: this.paginaActualListadoClientes,
        });

        this.listadoClientes = response.data;
      } catch (error) {
        alertify.error("Error al buscar clientes");
      }
    },
    descuentoRedondeo() {
      console.log("ESTO ES DESCUENTO : " + this.total);
      this.totalDescuentos = this.total;
      if (this.descuentoPorcentual != 0) {
        this.totalDescuentos =
          this.totalDescuentos -
          (this.totalDescuentos * this.descuentoPorcentual) / 100;
      }
      if (this.descuentoEfectivo != 0) {
        this.totalDescuentos = this.totalDescuentos - this.descuentoEfectivo;
      }
    },
    verificarCuentaCorriente(cuit, cuitEditado) {
      axios
        .post("api/verificarCuentaCorriente", {
          cuit: cuit,
          cuitEditado: cuitEditado,
        })
        .then((response) => {
          if (response.data["cuenta_corriente"] == 1) {
            this.cuentaCorriente = true;
          } else {
            this.cuentaCorriente = false;
          }
        })
        .catch((error) => {
          alertify.error(
            "Ha ocurrido un error al intentar enviar la verificación de cuenta corriente"
          );
        });
    },
    async cargaInfo() {
      let url;
      console.log(this.buscaTipo)
      if (this.buscaTipo != "") {
        if (this.buscaTipo == "Pedido") {
          url = "api/buscaPedido";
          this.pedido=true;
        } else {
          url = "api/buscaPresu";
          this.pedido=false;
        }
        this.detalleVenta = [];
        this.datosPresu = [];

        await axios
          .post(url, { busqueda: this.buscaInfo })
          .then((res) => {
            if (res.data != 20) {
              if (res.data === 10) {
                alertify.warning(
                  "No se encontro ningun " +
                    this.buscaTipo +
                    " con el numero: " +
                    this.buscaInfo
                );
                this.buscaInfo="";
                this.buscaTipo="";
                this.cancelarVenta();
              } else {
                this.datosPresu = res.data.detalle;
                console.log("esto es cliente" + res.data.cliente);
                if (res.data.cliente != undefined) {
                  console.log(res.data.cliente.CUIT);
                  this.cuit = res.data.cliente.CUIT;
                  this.cliente = res.data.cliente.Nombre;
                  this.idCliente = res.data.cliente.id;
                  this.getDatosClienteRegistradoVenta();
                  this.buscaInfo="";
                 this.buscaTipo="";
                }
              }
            } else {
              alertify.error("Error al procesar los datos");
            }
          })
          .catch((err) => {
            console.log(err);
          });

        await this.datosPresu.forEach((element) => {
          if (element.stock < element.cantidad) {
            alertify.error(
              element.Descripcion + " No Posee el stock Suficiente"
            );
          } else {
            this.detalleVenta.push({
              IdProducto: element.IdProducto,
              Descripcion: element.Descripcion,
              precio_venta_3: element.precio_venta_3,
              precio_venta_2: element.precio_venta_2,
              precio_venta_1: element.precio_venta_1,
              cantidad: element.cantidad,
              subtotal: element.precio_venta_1 * element.cantidad,
              stock: element.stock,
              precio_venta: element.precio_venta_1,
            });
          }
        });
        this.calcularTotal();
      }else{
        alertify.error("Debe seleccionar Una opcion a buscar");
      }
    },
    resetDatosCliente() {
      console.log(this.tipo_venta);
      if (this.cuit != "") {
        this.$refs["inputNombre"].setAttribute("disabled", true);
        this.$refs["inputNombre"].setAttribute("readonly", true);
        this.$refs["inputDomicilio"].setAttribute("disabled", true);
        this.$refs["inputDomicilio"].setAttribute("readonly", true);
      }
      this.clienteNoRegistrado = false;
      this.ventaCC = false;
      this.clienteRegistrado = [];
      this.cliente = "";
      this.domicilio = "";
      this.cuit = "";
    },
    requerirDatos() {
      if (this.datosRequeridos == true) {
        this.datosRequeridos = false;
        this.cuit = "";
        this.ventaCC = false;
        this.domicilio = "";
        this.cliente = "";
        this.clienteNoRegistrado = false;
      } else {
        this.datosRequeridos = true;
      }
    },
    checkMetodos() {
      this.totalMetodos =
        Number(this.efectivo) + Number(this.debito) + Number(this.credito);
    },
    getDatosClienteRegistradoVenta() {
      if (
        this.cuit.toString().length == 11 ||
        this.cuit.toString().length == 8
      ) {
        let cuitEditado =
          this.cuit.toString().slice(0, 2) +
          "-" +
          this.cuit.toString().slice(2, 10) +
          "-" +
          this.cuit.toString().slice(10 - 11);

        axios
          .post("api/getDatosClienteRegistradoVenta", {
            cuitEditado: cuitEditado,
            cuit: this.cuit,
          })
          .then((response) => {
            if (response.data.length > 0) {
              this.clienteRegistrado = response.data;
              this.cliente = this.clienteRegistrado[0]["Nombre"];
              this.$refs["labelNombre"].classList.add("active");
              this.domicilio = this.clienteRegistrado[0]["Domicilio"];
              this.$refs["labelDomicilio"].classList.add("active");
              this.clienteNoRegistrado = false;
              this.verificarCuentaCorriente(this.cuit, cuitEditado);
            } else {
              this.clienteNoRegistrado = true;
            }
          })
          .catch((error) => {
            alertify.error(
              "Ha ocurrido un error al enviar el CUIT a la base de datos"
            );
          });
      } else {
        this.cliente = "";
        this.domicilio = "";
        this.clienteRegistrado = [];
        this.clienteNoRegistrado = false;
        this.cuentaCorriente = false;
        this.condicion_venta = "";
        this.descuento = 0;
        this.ventaCC = false;
        e;
      }
    },
    getProductosStock() {
      axios
        .post("api/getProductosStock", {
          producto: this.productoBuscado,
          page: this.paginaActualListadoProductos,
        })
        .then((response) => {
          this.listadoProductos = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    cargarDetalleVenta(index) {
      if (this.errorStock != true) {
        let error = 0;
        let cantidad = 1;
        if (this.$refs[index].value != "") {
          cantidad = this.$refs[index].value;
        } else {
          cantidad = 1;
        }

        this.detalleVenta.forEach((element) => {
          if (
            this.listadoProductos.data[index].IdProducto == element.IdProducto
          ) {
            alertify.error("No se puede ingresar el mismo producto dos veces");
            error++;
          }
        });

        if (error == 0) {
          this.detalleVenta.push({
            IdProducto: this.listadoProductos.data[index].IdProducto,
            Descripcion: this.listadoProductos.data[index].Descripcion,
            precio_costo: this.listadoProductos.data[index].precio_costo,
            precio_venta_1: this.listadoProductos.data[index].precio_venta_1,
            precio_venta_2: this.listadoProductos.data[index].precio_venta_2,
            precio_venta_3: this.listadoProductos.data[index].precio_venta_3,
            stock: this.listadoProductos.data[index].stock,
            precio_venta: this.listadoProductos.data[index].precio_venta_1,
            cantidad: cantidad,
            subtotal:
              this.listadoProductos.data[index].precio_venta_1 * cantidad,
          });

          this.total = 0;
          this.cantidadProductos = 0;

          this.detalleVenta.forEach((element) => {
            this.total = this.total + element.subtotal;
            this.cantidadProductos =
              this.cantidadProductos + parseInt(element.cantidad);
          });
          if (this.ventaCC) {
            this.total = this.total + (this.total * this.intereses) / 100;
          }

          alertify.success("Producto agregado correctamente");
          this.$refs[index].value = "";
          this.productoBuscado = "";
        }
      } else {
        alertify.error("La cantidad Supera el stock");
      }
    },
    setInfoCliente(id) {
      console.log(id);
      let selected;
      selected = this.listadoClientes.data.filter((res) => {
        if (res.id == id) {
          return res;
        }
      });
      this.maximoCC = selected[0].monto_maximo_cc;
      this.totalcc = selected[0].total_cc;
      this.cuit = selected[0].CUIT.toString();
      this.cliente = selected[0].Nombre;
      this.domicilio = selected[0].Domicilio;
      this.idCliente = selected[0].id;
      this.intereses = selected[0].DescuentoHabitual;

      this.searchCliente = "";

      if (this.tipo_venta == "Cuenta_Corriente") {
        this.ventaCC = true;
      }

      this.verificarCuentaCorriente();
      this.getDatosClienteRegistradoVenta();
      this.calcularTotal();

      this.$refs["my-modal"].hide();
    },
    quitarDetalleVenta(index) {
      this.detalleVenta.splice(index, 1);

      alertify.error("Producto quitado correctamente");

      this.total = 0;
      this.cantidadProductos = 0;
      this.detalleVenta.forEach((element) => {
        this.cantidadProductos =
          this.cantidadProductos + parseInt(element.cantidad);
        this.total = this.total + element.subtotal;
      });
    },
    aumentarCantidad(index) {
      if (this.detalleVenta[index].cantidad == this.detalleVenta[index].stock) {
        alertify.error("La cantidad no puede superar el stock");
      } else {
        this.detalleVenta[index].cantidad++;
        this.detalleVenta[index].subtotal =
          this.detalleVenta[index].cantidad *
          this.detalleVenta[index].precio_venta;

        this.total = 0;
        this.cantidadProductos = 0;
        this.detalleVenta.forEach((element) => {
          this.cantidadProductos =
            this.cantidadProductos + parseInt(element.cantidad);
          this.total = this.total + element.subtotal;
        });
      }
    },
    disminuirCantidad(index) {
      if (this.detalleVenta[index].cantidad == 1) {
        alertify.error("La cantidad no puede ser 0");
      } else {
        this.detalleVenta[index].cantidad--;
        this.detalleVenta[index].subtotal =
          this.detalleVenta[index].cantidad *
          this.detalleVenta[index].precio_venta;

        this.total = 0;
        this.cantidadProductos = 0;
        this.detalleVenta.forEach((element) => {
          this.total = this.total + element.subtotal;
          this.cantidadProductos =
            this.cantidadProductos + parseInt(element.cantidad);
        });
      }
    },
    validarCantidad(index) {
      console.log("cantidad recibida es:" + this.$refs[index]);
      if (this.detalleVenta[index].cantidad < 1) {
        this.detalleVenta[index].cantidad = 1;
        alertify.error("La cantidad no puede ser 0");
      } else {
        if (
          this.detalleVenta[index].cantidad > this.detalleVenta[index].stock
        ) {
          this.detalleVenta[index].cantidad = 1;
          alertify.error("La cantidad no puede superar el stock");
        }
      }

      this.detalleVenta[index].subtotal =
        this.detalleVenta[index].cantidad *
        this.detalleVenta[index].precio_venta;

      this.total = 0;
      this.cantidadProductos = 0;
      this.detalleVenta.forEach((element) => {
        this.cantidadProductos =
          this.cantidadProductos + parseInt(element.cantidad);
        this.total = this.total + element.subtotal;
      });
      if (this.ventaCC) {
        this.total = this.total + (this.total * this.descuento) / 100;
      }
    },
    validarStock(cantidad, index) {
      if (this.listadoProductos.data[index].stock < cantidad) {
        alertify.error("Cantidad Supera El Stock");
        this.errorStock = true;
      } else {
        this.errorStock = false;
      }
    },
    calcularTotal(index) {
      console.log("Entra");
      //this.detalleVenta[index].subtotal =
      //this.detalleVenta[index].cantidad *
      //this.detalleVenta[index].precio_venta;

      this.total = 0;

      this.detalleVenta.forEach((element) => {
        this.total = this.total + element.subtotal;
      });
      console.log(this.ventaCC);
      if (this.ventaCC) {
        this.total = this.total + (this.total * this.intereses) / 100;
      }
    },
    saveVenta(autorizar) {
      let error = true;
      console.log(this.cuit.length);
      if (this.total >= 10000) {
        if (this.cuit == "") {
          error = false;
          alertify.error("Debe Cargar los datos del cliente");
        }
      }

      if (this.variosMetodos) {
        let suma =
          parseInt(this.efectivo) +
          parseInt(this.credito) +
          parseInt(this.debito);
        console.log("Esto es Suma :" + suma);
        if (this.total > suma) {
          alertify.error("Falta dinero para completar la venta");
          error = false;
        }
        if (this.total < suma) {
          alertify.warning("ESTA SOBRANDO DINERO");
          error = false;
        }
      } else {
        console.log("eto es ventacc" + this.ventaCC);
        if (this.ventaCC) {
          this.condicion_venta = "Cuenta_Corriente";
        }

        if (this.tipo_venta == "Vale_Empleados") {
          this.condicion_venta = "Vale_Empleado";
        }
        if (this.condicion_venta == "") {
          alertify.error("Por favor ingrese la condición de venta");
          error = false;
        }
      }

      let url = "";
      if (this.ventaCC) {
        url = "api/saveVentaCC";
        this.totalDescuentos = this.total - (this.descuento * this.total) / 100;
      } else {
        url = "api/finalizarVenta";
      }
      console.log(this.ventaCC);
      console.log(url);

      if (error) {
        this.loading = true;
        axios
          .post(url, {
            pedido : this.pedido,
            escuela: this.escuela,
            interes: this.intereses,
            empleado: this.empleado,
            cuit: this.cuit,
            cliente: this.cliente,
            domicilio: this.domicilio,
            tipo_venta: this.tipo_venta,
            condicion_venta: this.condicion_venta,
            variosMetodos: this.variosMetodos,
            efectivo: this.efectivo,
            debito: this.debito,
            credito: this.credito,
            fecha: this.fecha_venta,
            total: this.totalDescuentos,
            cajaSeleccionada: 1,
            descuento: this.total - this.totalDescuentos,
            datosRequeridos: this.datosRequeridos,
            detalleVenta: this.detalleVenta,
            ventaCC: this.ventaCC,
            autorizar: autorizar,
            idCliente: this.idCliente,
            userid: window.user.user.id,
          })
          .then((response) => {
            console.log(autorizar);
            if (response.data == 0) {
              if (autorizar) {
                alertify.success("La venta se ha enviado a autorización");
              }
              this.limpia();
            } else {
              if (response.data != 20) {
                axios({
                  url: response.data,
                  method: "GET",
                  responseType: "blob",
                }).then((response) => {
                  var fileURL = window.URL.createObjectURL(
                    new Blob([response.data])
                  );
                  var fileLink = document.createElement("a");

                  fileLink.href = fileURL;
                  const today = new Date();
                  const date =
                    today.getFullYear() +
                    "-" +
                    (today.getMonth() + 1) +
                    "-" +
                    today.getDate();

                  if (this.tipo_venta == "Vale_Empleados") {
                    fileLink.setAttribute(
                      "download",
                      "Vale - Empleado" + date + ".pdf"
                    );
                  } else {
                    fileLink.setAttribute("download", "venta.pdf");
                  }

                  document.body.appendChild(fileLink);
                  fileLink.click();
                });
                this.limpia();
              } else {
                alertify.error(
                  "Ha ocurrido un error al momento de cargar la venta"
                );
                this.limpia();
              }
            }
          });
      }
    },
    cancelarVenta() {
      this.cantidadProductos = 0;
      this.empleado = "";
      this.total = 0;
      this.datosRequeridos = false;
      this.clienteRegistrado = [];
      this.clienteNoRegistrado = false;
      this.detalleVenta = [];
      this.ventaCC = false;
      this.tipo_venta = "";
      this.tipo_venta = "";
      this.cuit = "";
      this.cliente = "";
      this.domicilio = "";
      this.total = 0;
      this.aplicaDCC = false;
      this.detalleVenta = [];
      this.paginaActualListadoProductos = 1;
      this.confirmaVenta = false;
      alertify.error("La venta se ha cancelado correctamente");
    },
    confirmarVenta() {
      let error = true;
      console.log(this.cuit.length);
      if (this.total >= 10000) {
        if (this.cuit == "") {
          error = false;
          alertify.error("Debe Cargar los datos del cliente");
        }
      }

      if (this.tipo_venta == "") {
        alertify.error("Por favor seleccione el tipo de venta");
        this.$refs["tipo_venta"].classList.add("border-danger");
        error = false;
      } else {
        this.$refs["tipo_venta"].classList.remove("border-danger");
        if (
          this.tipo_venta == "Factura_A" ||
          this.tipo_venta == "Factura_B" ||
          this.tipo_venta == "Cuenta_Corriente" ||
          this.datosRequeridos
        ) {
          console.log("Cantidad de length es:" + this.cuit.length);
          if (!(this.cuit.length == 8 || this.cuit.length == 11)) {
            alertify.error("Por favor ingrese un CUIL/CUIT/DNI válido");
            this.$refs["inputCuit"].classList.add("border-danger");
            error = false;
          } else {
            this.$refs["inputCuit"].classList.remove("border-danger");
          }
          if (this.cliente == "") {
            alertify.error("Por favor ingrese el nombre del cliente");
            this.$refs["inputNombre"].classList.add("border-danger");
            error = false;
          } else {
            this.$refs["inputNombre"].classList.remove("border-danger");
          }
          if (this.domicilio == "") {
            alertify.error("Por favor ingrese el domicilio del cliente");
            this.$refs["inputDomicilio"].classList.add("border-danger");
            error = false;
          } else {
            this.$refs["inputDomicilio"].classList.remove("border-danger");
          }
        }
      }
      if (this.tipo_venta == "Cuenta_Corriente") {
        if (this.total > this.maximoCC - this.totalcc) {
          alertify.error("El monto supera el maximo permitido por cliente");
          error = false;
        }
      }

      if (this.tipo_venta == "Pedido") {
        axios.post("api/PedidoAdd");
        console.log("LLegamos");
        error = false;
      }
      if (error) {
        this.totalDescuentos = this.total;
        this.confirmaVenta = true;
        this.descuentoPorcentual = this.descuento;
        this.descuentoRedondeo();
      }
    },
    limpia() {
      this.pedido=false;
      this.escuela = false;
      this.intereses = 0;
      this.empleado = "";
      this.total = 0;
      this.datosRequeridos = false;
      this.clienteRegistrado = [];
      this.clienteNoRegistrado = false;
      this.detalleVenta = [];
      this.ventaCC = false;
      this.tipo_venta = "";
      this.tipo_venta = "";
      this.cuit = "";
      this.cliente = "";
      this.domicilio = "";
      this.detalleVenta = [];
      this.paginaActualListadoProductos = 1;
      this.condicion_venta = "";
      this.confirmaVenta = false;
      this.descuentoEfectivo = 0;
      this.descuentoPorcentual = 0;
      this.totalDescuentos = 0;
      this.checkMetodos = false;
      this.debito = 0;
      this.credito = 0;
      this.efectivo = 0;
      this.aplicaDCC = false;
      this.maximoCC = 0;
      this.totalcc = 0;
      this.loading = false;
      this.idCliente = 0;
      this.cantidadProductos = 0;
    },
  },

  mounted() {
    console.log("COMPONENTE CARGADO");
    this.show = true;
  },
};
</script>
