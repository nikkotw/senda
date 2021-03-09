<template>
  <transition name="fade">
    <div v-if="show" class="ml-1 mr-1">
      <b-modal
        ref="modalCrearCliente"
        id="modalCrearCliente"
        size="xl"
        hide-footer
        title="Registrar cliente"
      >
        <div class="row" ref="registroCliente">
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="razon_social">Razón social</label>
            <input
              v-model="datosRegistrarCliente.razonSocial"
              type="text"
              id="razon_social"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="cuit">CUIT/CUIL/DNI</label>
            <input
              v-model="datosRegistrarCliente.cuit"
              type="number"
              onkeypress="if(this.value.length==11) return false;"
              id="cuit"
              class="form-control form-control-sm"
              required
            />
            <div class="invalid-feedback">
              Por favor ingrese un
              <strong>CUIT</strong> (11 caracteres como máximo) o un
              <strong>DNI</strong> (8 caracteres como máximo) correcto.
            </div>
          </div>
          <div class="w-100"></div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="telefono">Teléfono</label>
            <input
              v-model="datosRegistrarCliente.telefono"
              type="number"
              id="telefono"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="eMail">E-Mail</label>
            <input
              v-model="datosRegistrarCliente.email"
              type="email"
              id="eMail"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="w-100"></div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="direccion">Dirección</label>
            <input
              v-model="datosRegistrarCliente.direccion"
              type="text"
              id="direccion"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="w-100"></div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="ciudad">Ciudad</label>
            <input
              v-model="datosRegistrarCliente.ciudad"
              id="ciudad"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="col col-lg form-group">
            <label class="font-weight-bold" style="font-size:12px;" for="cp">Código postal</label>
            <input
              v-model="datosRegistrarCliente.cp"
              type="number"
              id="cp"
              class="form-control form-control-sm"
              required
            />
          </div>
          <div class="w-100"></div>
          <div class="col col-lg text-center">
            <button @click="registrarCliente()" class="btn btn-sm btn-success">Registrar cliente</button>
            <button
              @click="datosRegistrarCliente={razonSocial: '',
        cuit: '',
        telefono: '',
        email: '',
        direccion: '',
        ciudad: '',
        cp: ''};$bvModal.hide('modalCrearCliente')"
              class="btn btn-sm btn-danger"
            >Cancelar</button>
          </div>
        </div>
      </b-modal>

      <b-modal
        ref="modalDetalleNotaCredito"
        id="modalDetalleNotaCredito"
        size="xl"
        hide-footer
        title="Productos disponibles para realizar notas de crédito"
      >
        <div class="row">
          <div class="col col-lg">
            <b-table
              class="text-left text-muted"
              :items="detalleVenta"
              :fields="fieldsDetalleVenta"
              style="font-size:12px;"
              outlined
              small
              hover
              fixed
              no-border-collapse
              head-variant="dark"
            >
              <template v-slot:cell(Descripcion)="row">
                <p class="font-weight-bold">{{row.item.Descripcion}}</p>
              </template>
              <template v-slot:cell(cantidad)="row">
                <p class="font-weight-bold">{{row.item.restantesNotaCredito}}</p>
              </template>
              <template v-slot:cell(subtotal)="row">
                <p class="font-weight-bold text-primary">${{row.item.subtotal}}</p>
              </template>
              <template v-slot:cell(precio_venta)="row">
                <p class="font-weight-bold text-warning">${{row.item.precio_venta}}</p>
              </template>
              <template v-slot:cell(acciones)="row">
                <button
                  @click="addDetalleNotaCredito(row.index)"
                  class="btn btn-sm btn-primary"
                >Añadir</button>
              </template>
            </b-table>
          </div>
        </div>
      </b-modal>

      <div class="card mt-1">
        <div class="card-header text-primary bg-transparent font-weight-bold text-left">
          Notas de crédito
          <button
            v-b-modal.modalCrearCliente
            class="btn btn-sm btn-success float-right"
            v-if="notaCredito && Object.keys(datosCliente).length == 0"
          >Registrar cliente</button>
        </div>

        <transition name="fade">
          <div v-if="Object.keys(datosVenta).length === 0" class="card-body mt-n4 mb-n4">
            <div ref="busquedaFactura" class="row mt-3">
              <div class="col col-lg form-group">
                <label>Ingrese el número de factura de la venta que requiere la nota de crédito</label>
                <input
                  v-model.number="nroFactura"
                  type="number"
                  class="form-control form-control-sm"
                />
              </div>
              <div class="w-100"></div>
              <div class="col col-lg form-group">
                <button @click="buscarVenta" class="btn btn-sm btn-primary">Buscar venta</button>
              </div>
            </div>
          </div>
        </transition>

        <transition name="fade">
          <div v-if="Object.keys(datosVenta).length != 0 && !notaCredito" class="card-body">
            <div class="card">
              <div class="card-header bg-dark text-white font-weight-bold">Datos de la venta</div>

              <div class="card-body">
                <p>
                  <span class="font-weight-bold">Número de factura:</span>
                  {{datosVenta.factura}}
                </p>
                <p>
                  <span class="font-weight-bold">Fecha:</span>
                  {{datosVenta.fecha}}
                </p>

                <p v-if="datosVenta.tipo_venta == 'Consumidor_Final'">
                  <span class="font-weight-bold">Tipo de venta:</span>Consumidor final
                </p>
                <p v-if="datosVenta.tipo_venta == 'Factura_A'">
                  <span class="font-weight-bold">Tipo de venta:</span>Factura A
                </p>
                <p v-if="datosVenta.tipo_venta == 'Factura_B'">
                  <span class="font-weight-bold">Tipo de venta:</span>Factura B
                </p>

                <p v-if="datosVenta.condicion_venta == 'Débito'">
                  <span class="font-weight-bold">Condición de venta:</span>Tarjeta de débito
                </p>
                <p v-if="datosVenta.condicion_venta == 'Crédito'">
                  <span class="font-weight-bold">Condición de venta:</span>Tarjeta de crédito
                </p>
                <p v-if="datosVenta.condicion_venta == 'Efectivo'">
                  <span class="font-weight-bold">Condición de venta:</span>Contado - Efectivo
                </p>
                <p v-if="datosVenta.condicion_venta == 'Cuenta_Corriente'">
                  <span class="font-weight-bold">Condición de venta:</span>Cuenta Corriente
                </p>

                <p>
                  <span class="font-weight-bold">Total de la venta:</span>
                  ${{datosVenta.total}}
                </p>
              </div>
            </div>
          </div>
        </transition>

        <div v-if="notaCredito">
          <transition name="fade">
            <div class="card-body" v-if="Object.keys(datosCliente).length == 0">
              <div class="row">
                <div class="col col-xl text-right mt-1">
                  <div class="input-group">
                    <input
                      type="text"
                      :disabled="filtroBusquedaCliente == ''"
                      class="form-control form-control-sm"
                      v-model="clienteBuscado"
                      placeholder="Buscar clientes en el sistema"
                    />
                    <div class="input-group-append">
                      <select
                        @change="clienteBuscado = ''"
                        v-model="filtroBusquedaCliente"
                        class="form-control form-control-sm"
                      >
                        <option selected value>No aplicar filtros</option>
                        <option value="cuit">CUIT/CUIL/DNI</option>
                        <option value="nombre">Nombre</option>
                      </select>
                    </div>
                    <button
                      class="btn btn-sm btn-primary btn-block mt-1"
                      v-on:click="buscarClientes()"
                      type="button"
                    >Buscar</button>
                  </div>
                </div>
                <div class="w-100"></div>
                <div class="col col-xl mt-1">
                  <b-table
                    id="listado-clientes"
                    class="mt-1"
                    style="font-size:12px;"
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
                    <template v-slot:cell(acciones)="row">
                      <button
                        @click="seleccionarCliente(row.index)"
                        class="btn btn-sm btn-warning"
                      >Seleccionar</button>
                    </template>
                  </b-table>

                  <div
                    v-if="listadoClientes.total != undefined && listadoClientes.total > 10"
                    @click="buscarClientes"
                  >
                    <b-pagination
                      size="sm"
                      style="font-size:12px;"
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
              </div>
            </div>
          </transition>
          <transition name="fade">
            <div class="card-body" v-if="Object.keys(datosCliente).length != 0">
              <div class="card mb-2">
                <div
                  class="card-header text-primary text-left bg-transparent font-weight-bold"
                >Datos</div>

                <div class="card-body">
                  <div class="row">
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          readonly
                          :value="datosCliente.Nombre"
                          type="text"
                          id="cliente"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="cliente">Cliente</label>
                      </div>
                    </div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          readonly
                          :value="datosCliente.Domicilio"
                          type="text"
                          id="domicilio"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="domicilio">Dirección</label>
                      </div>
                    </div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          readonly
                          :value="datosCliente.CUIT"
                          type="text"
                          id="cuit"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="cuit">CUIT/CUIL/DNI</label>
                      </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          v-if="datosVenta.tipo_venta == 'Consumidor_Final'"
                          readonly
                          value="Consumidor Final"
                          type="text"
                          id="domicilio"
                          class="form-control form-control-sm"
                        />
                        <input
                          v-if="datosVenta.tipo_venta == 'Factura_A'"
                          readonly
                          value="Factura A"
                          type="text"
                          id="domicilio"
                          class="form-control form-control-sm"
                        />
                        <input
                          v-if="datosVenta.tipo_venta == 'Factura_B'"
                          readonly
                          value="Factura B"
                          type="text"
                          id="domicilio"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="tipo_facturacion">Tipo de facturación</label>
                      </div>
                    </div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          readonly
                          :value="datosVenta.factura"
                          type="text"
                          id="factura"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="factura">Número de factura</label>
                      </div>
                    </div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          v-if="datosVenta.condicion_venta == 'Débito'"
                          readonly
                          value="Tarjeta de débito"
                          type="text"
                          id="condicion_venta"
                          class="form-control form-control-sm"
                        />
                        <input
                          v-if="datosVenta.condicion_venta == 'Crédito'"
                          readonly
                          value="Tarjeta de crédito"
                          type="text"
                          id="condicion_venta"
                          class="form-control form-control-sm"
                        />
                        <input
                          v-if="datosVenta.condicion_venta == 'Efectivo'"
                          readonly
                          value="Contado - Efectivo"
                          type="text"
                          id="condicion_venta"
                          class="form-control form-control-sm"
                        />
                        <input
                          v-if="datosVenta.condicion_venta == 'Cuenta_Corriente'"
                          readonly
                          value="Cuenta Corriente"
                          type="text"
                          id="condicion_venta"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="condicion_venta">Condición de venta</label>
                      </div>
                    </div>
                    <div class="col col-lg">
                      <div class="md-form">
                        <input
                          readonly
                          :value="datosVenta.fecha"
                          type="text"
                          id="fecha"
                          class="form-control form-control-sm"
                        />
                        <label class="active" for="fecha">Fecha</label>
                      </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col col-lg">
                      <h4>
                        Total de la venta:
                        <span
                          class="font-weight-bold text-primary"
                        >${{datosVenta.total}}</span>
                      </h4>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header text-primary text-left bg-transparent font-weight-bold">
                  Detalle
                  <button
                    @click="notaCreditoDetalle()"
                    class="btn btn-primary btn-sm float-right border-0"
                  >Añadir detalle</button>
                </div>
                <transition name="fade">
                  <div v-if="detalleNotaCredito != ''" class="card-body">
                    <b-table
                      class="text-center text-muted"
                      :items="detalleNotaCredito"
                      :fields="fieldsDetalleNotaCrédito"
                      style="font-size:12px;"
                      outlined
                      small
                      hover
                      fixed
                      no-border-collapse
                      head-variant="dark"
                      :tbody-transition-props="{name:'fade'}"
                    >
                      <template v-slot:cell(Descripcion)="row">
                        <p class="font-weight-bold">{{row.item.Descripcion}}</p>
                      </template>
                      <template v-slot:cell(cantidad)="row">
                        <div class="md-form my-auto">
                          <select
                            @change="modificarCantidad(row.index,$event)"
                            class="form-control form-control-sm"
                          >
                            <option v-for="n in row.item.cantidad" :key="n" :value="n">{{n}}</option>
                          </select>
                        </div>
                      </template>
                      <template v-slot:cell(precio_venta)="row">
                        <p class="font-weight-bold text-warning">${{row.item.precio_venta}}</p>
                      </template>
                      <template v-slot:cell(acciones)="row">
                        <button
                          @click="quitarDetalleNotaCredito(row.index)"
                          class="btn btn-sm btn-danger"
                        >Quitar</button>
                      </template>
                    </b-table>
                  </div>
                </transition>
                <h4 v-if="totalNotaCredito > 0">
                  Total de la nota de crédito:
                  <span
                    class="font-weight-bold text-primary"
                  >${{totalNotaCredito}}</span>
                </h4>
              </div>
            </div>
          </transition>
        </div>

        <div v-if="Object.keys(datosVenta).length != 0">
          <div class="row mb-2">
            <div class="col col-lg">
              <transition name="fade">
                <button
                  v-if="!notaCredito"
                  @click="notaCreditoOk()"
                  class="btn btn-sm btn-primary"
                >Continuar</button>
              </transition>
              <transition name="fade">
                <button
                  v-if="notaCredito && datosCliente != '' && detalleNotaCredito != ''"
                  class="btn btn-sm btn-success"
                  @click="emitirNotaDeCredito()"
                >Emitir Nota de Crédito</button>
              </transition>
              <button @click="volver()" class="btn btn-sm btn-danger">Volver</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  data() {
    return {
      show: false,
      datosVenta: {}, //Venta buscada por el número de factura
      datosCliente: {}, //Datos del cliente al que corresponda la venta
      nroFactura: "", //Número de factura
      notaCredito: false,
      datosRegistrarCliente: {
        razonSocial: "",
        cuit: "",
        telefono: "",
        email: "",
        direccion: "",
        ciudad: "",
        cp: ""
      }, //Datos para registrar al cliente
      clienteBuscado: "",
      filtroBusquedaCliente: "",
      detalleVenta: [],
      fieldsDetalleVenta: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "Cantidad" },
        { key: "precio_venta", label: "Precio de venta" },
        { key: "subtotal", label: "Subtotal" },
        "Acciones"
      ],
      detalleNotaCredito: [],
      fieldsDetalleNotaCrédito: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "Cantidad" },
        { key: "precio_venta", label: "Precio de venta" },
        "Acciones"
      ], //Campos del detalle de la nota de crédito
      totalNotaCredito: 0,
      listadoClientes: [],
      fieldsListadoClientes: [
        { key: "Nombre", label: "Cliente" },
        { key: "Domicilio", label: "Domicilio" },
        { key: "CP", label: "Código Postal" },
        { key: "CUIT", label: "CUIT/CUIL/DNI" },
        { key: "Localidad", label: "Ciudad" },
        { key: "Telefono", label: "Teléfono" },
        { key: "Mail", label: "Correo Electrónico" },
        "acciones"
      ],
      paginaActualListadoClientes: 1
    };
  },
  methods: {
    buscarVenta() {
      this.datosVenta = {};

      if (this.nroFactura == "") {
        alertify.error("Por favor ingrese un número de factura válido");
      } else {
        axios
          .post("api/getVentaPorFactura", {
            nroFactura: this.nroFactura
          })
          .then(response => {
            if (response.data != 0) {
              this.$refs["busquedaFactura"].setAttribute("hidden", true);
              this.datosVenta = Object.assign(response.data.venta);
              this.datosCliente = Object.assign(response.data.cliente);
            } else {
              alertify.error(
                "El número de factura no corresponde a una venta registrada en el sistema"
              );
            }
          });
      }
    },
    volver() {
      this.datosVenta = {};
      this.datosCliente = {};
      this.notaCredito = false;
      this.nroFactura = "";
      this.datosRegistrarCliente = {
        razonSocial: "",
        cuit: "",
        telefono: "",
        email: "",
        direccion: "",
        ciudad: "",
        cp: ""
      };
      this.totalNotaCredito = 0;
      this.detalleVenta = [];
      this.detalleNotaCredito = [];
      this.listadoClientes = [];
      this.paginaActualListadoClientes = 1;
    },
    notaCreditoOk() {
      if (Object.keys(this.datosCliente).length === 0) {
        alertify.alert(
          "ALERTA",
          "La venta no tiene asociado un cliente. Por favor complete los datos a continuación para poder realizar la nota de crédito"
        );
        this.notaCredito = true;
      } else {
        this.notaCredito = true;
      }
    },
    registrarCliente: function() {
      var errors = true;

      if (
        this.datosRegistrarCliente.razonSocial == "" ||
        this.datosRegistrarCliente.cuit == "" ||
        this.datosRegistrarCliente.telefono == "" ||
        this.datosRegistrarCliente.email == "" ||
        this.datosRegistrarCliente.direccion == "" ||
        this.datosRegistrarCliente.ciudad == "" ||
        this.datosRegistrarCliente.cp == ""
      ) {
        errors = false;
      }

      if (
        this.datosRegistrarCliente.cuit.length == 11 ||
        this.datosRegistrarCliente.cuit.length == 8
      ) {
        console.log(this.datosRegistrarCliente.cuit.length);
      } else {
        this.datosRegistrarCliente.cuit = "";
        errors = false;
      }

      if (errors) {
        axios
          .post("api/registrarClienteNotasCredito", {
            idVenta: this.datosVenta.id,
            razonSocial: this.datosRegistrarCliente.razonSocial,
            cuit: this.datosRegistrarCliente.cuit,
            telefono: this.datosRegistrarCliente.telefono,
            email: this.datosRegistrarCliente.email,
            direccion: this.datosRegistrarCliente.direccion,
            ciudad: this.datosRegistrarCliente.ciudad,
            cp: this.datosRegistrarCliente.cp,
            userid: window.user.user.id,
          })
          .then(response => {
            if (response.data == 0) {
              this.datosRegistrarCliente = {
                razonSocial: "",
                cuit: "",
                telefono: "",
                email: "",
                direccion: "",
                ciudad: "",
                cp: ""
              };
              this.datosCliente = response.data;
              alertify.success("Cliente Registrado correctamente");
              this.$refs["modalCrearCliente"].hide();
              this.listadoClientes = [];
              this.paginaActualListadoClientes = 1;
            } else {
              alertify.error(
                "Hay errores en el formulario de alta del cleinte"
              );
            }
          })
          .catch(error => {
            if (error.response.data.errors.cuit) {
              this.datosRegistrarCliente.cuit = "";
              this.$refs["registroCliente"].classList.add("was-validated");
              alertify.error(
                "El cuit registrado ya corresponde a un cliente registrado"
              );
              errors = false;
            }
          });
      } else {
        this.$refs["registroCliente"].classList.remove("needs-validation");
        this.$refs["registroCliente"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    notaCreditoDetalle() {
      axios
        .post("api/getDetalleVentaNotaCredito", {
          id: this.datosVenta.id
        })
        .then(response => {
          this.detalleVenta = response.data;
          if (this.detalleVenta == "") {
            alertify.error(
              "Ya no pueden realizarse notas de crédito para esta venta"
            );
          } else {
            this.$refs["modalDetalleNotaCredito"].show();
          }
        });
    },
    addDetalleNotaCredito(index) {
      let error = true;

      this.detalleNotaCredito.forEach(element => {
        if (element.Descripcion == "Anulación del comprobante de referencia") {
          this.detalleNotaCredito = [];
        }

        if (this.detalleVenta[index].Descripcion == element.Descripcion) {
          alertify.error("No se puede ingresar el mismo producto dos veces");
          error = false;
        }
      });

      if (error) {
        this.totalNotaCredito = 0;
        this.detalleVenta[index].cantidadSelect = 1;
        this.detalleNotaCredito.push(this.detalleVenta[index]);
        this.detalleNotaCredito.forEach(element => {
          this.totalNotaCredito =
            element.precio_venta * element.cantidadSelect +
            this.totalNotaCredito;
        });
        alertify.success("Producto añadido correctamente a la nota de crédito");
      }
    },
    quitarDetalleNotaCredito(index) {
      this.totalNotaCredito = 0;
      this.detalleNotaCredito.splice(index, 1);
      this.detalleNotaCredito.forEach(element => {
        this.totalNotaCredito =
          element.precio_venta * element.cantidadSelect + this.totalNotaCredito;
      });
    },
    modificarCantidad(index, event) {
      this.totalNotaCredito = 0;

      this.detalleNotaCredito[index].cantidadSelect = parseInt(
        event.target.value
      );
      this.detalleNotaCredito.forEach(element => {
        this.totalNotaCredito =
          element.precio_venta * element.cantidadSelect + this.totalNotaCredito;
      });
    },
    buscarClientes: async function() {
      try {
        const response = await axios.post("api/getClientesNotaCredito", {
          filtro: this.filtroBusquedaCliente,
          busqueda: this.clienteBuscado,
          page: this.paginaActualListadoClientes
        });

        this.listadoClientes = response.data;
      } catch (error) {
        alertify.error("No se encontraron clientes");
      }
    },
    seleccionarCliente: function(index) {
      this.datosCliente = this.listadoClientes.data[index];
      this.listadoClientes = [];
      this.paginaActualListadoClientes = 1;
    },
    emitirNotaDeCredito: async function() {
      try {
        const response = await axios.post("api/emitirNotaCredito", {
          detalle: this.detalleNotaCredito,
          datosVenta: this.datosVenta,
          cliente: this.datosCliente,
          total: this.totalNotaCredito,
          userid: window.user.user.id,
        });
        alertify.success("Nota de crédito emitida correctamente");
        this.volver();
      } catch (error) {
        alertify.error("Error al emitir nota de crédito");
      }
    }
  },
  mounted() {
    this.show = true;
  }
};
</script>
