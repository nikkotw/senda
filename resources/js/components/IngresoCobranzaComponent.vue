<template>
  <div class="ml-1 mr-1">
    <transition name="fade">
      <div v-if="show" class="row card mt-1">
        <div
          class="card-header text-primary bg-transparent font-weight-bold text-left"
        >Ingresos de cobranza</div>

        <div v-if="listadoDeVentas == '' && listadoIngresos == ''" class="card-body row">
          <!--Buscador de clientes-->

          <div class="input-group">
            <input
              disabled
              ref="buscadorClientes"
              type="text"
              class="form-control form-control-sm"
              v-model="clienteBuscado"
              placeholder="Buscar clientes con cuenta corriente  "
              aria-label="Recipient's username"
              aria-describedby="basic-addon2"
            />
            <div class="input-group-append">
              <select
                @change="busquedaPorFiltro"
                v-model="filtroBusquedaCliente"
                class="form-control form-control-sm"
              >
                <option selected value>No aplicar filtros</option>
                <option value="cuit">CUIT</option>
                <option value="nombre">Nombre</option>
                <option value="deudores">Deudores</option>
                <option value="saldosFavor">Saldos a favor</option>
              </select>
            </div>
            <button
              class="btn btn-sm btn-primary btn-block mt-1"
              v-on:click="buscarCliente()"
              type="button"
            >Buscar</button>
          </div>

          <div class="w-100"></div>
          <!--Listado de clientes obtenidos luego de la busqueda-->
          <div class="row mt-1">
            <div class="col col-lg">
              <b-table
                ref="table"
                v-if="listadoClientes != ''"
                id="listadoClientes"
                style="font-size:12px;"
                :outlined="true"
                :small="true"
                :hover="true"
                :fixed="true"
                :no-border-collapse="true"
                head-variant="dark"
                :items="listadoClientes.data"
                :fields="fieldsListadoClientes"
              >
                <template v-slot:cell(monto_maximo_cc)="row">
                  <p class="font-weight-bold text-primary">${{row.item.monto_maximo_cc}}</p>
                </template>

                <template v-slot:cell(total_cc)="row">
                  <p
                    v-if="row.item.total_cc == 0"
                    class="font-weight-bold text-warning"
                  >${{row.item.total_cc}}</p>
                  <p
                    v-if="row.item.total_cc > 0"
                    class="font-weight-bold text-danger"
                  >${{row.item.total_cc}}</p>
                  <p
                    v-if="row.item.total_cc < 0"
                    class="font-weight-bold text-success"
                  >${{row.item.total_cc.toString().slice(1)}}</p>
                </template>

                <template v-slot:cell(acciones)="row">
                  <button
                    @click="getListadoCajasAbiertas(row.item.id,row.index);resetIngresoCobranza()"
                    class="btn btn-sm btn-primary"
                    title="Ingreso de cobranza"
                  >
                    <b-icon-credit-card></b-icon-credit-card>
                  </button>
                  <button
                    @click="getListadoVentasClienteCC(row.item.CUIT,row.item.id)"
                    class="btn btn-sm btn-success"
                    title="Ver ventas del cliente"
                  >
                    <b-icon-eye></b-icon-eye>
                  </button>
                  <button
                    @click="idClienteIngresos = row.item.id; getIngresosCobranza(row.item.id)"
                    class="btn btn-sm btn-warning"
                    title="Ver ingreso de cobranza del cliente"
                  >
                    <b-icon-info></b-icon-info>
                  </button>
                </template>
              </b-table>
            </div>
            <div class="w-100"></div>
            <div class="col col-lg">
              <div
                @click="buscarCliente"
                v-if="listadoClientes != ''"
                align="center"
                class="text-center"
              >
                <b-pagination
                  aria-controls="listado-productos"
                  style="font-size:12px;"
                  v-model="paginaActualCliente"
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

        <div v-if="listadoDeVentas != ''" class="card-body row mt-1">
          <div class="col col-lg">
            <!--Listado de ventas y detalle-->
            <b-table
              class="mt-1"
              id="listadoDeVentas"
              style="font-size:12px;"
              :striped="true"
              :outlined="true"
              :small="true"
              :fixed="true"
              :no-border-collapse="true"
              head-variant="dark"
              :items="listadoDeVentas.data"
              :fields="fieldsListadoDeVentas"
              :tbody-transition-props="{name:'flip-list'}"
            >
              <template v-slot:cell(total)="row">
                <p class="font-weight-bold text-primary">${{row.item.total}}</p>
              </template>

              <template v-slot:cell(tipo_venta)="row">
                <p
                  v-if="row.item.tipo_venta == 'Factura_A'"
                  class="font-weight-bold text-danger"
                >Factura A</p>
                <p
                  v-if="row.item.tipo_venta == 'Factura_B'"
                  class="font-weight-bold text-danger"
                >Factura B</p>
                <p
                  v-if="row.item.tipo_venta == 'Consumidor_Final'"
                  class="font-weight-bold text-danger"
                >Consumidor Final</p>
                <p
                  v-if="row.item.tipo_venta == 'No_Facturar'"
                  class="font-weight-bold text-danger"
                >No se requiere facturación</p>
              </template>
              <template v-slot:cell(facturar)="row">
                <input
                  v-if="row.item.factura==null"
                  type="checkbox"
                  v-model="row.data"
                  @change="agregaFactura(row.data,row.item.id)"
                />
              </template>

              <template v-slot:cell(acciones)="row">
                <b-button
                  variant="success"
                  size="sm"
                  @click="row.toggleDetails"
                  class="mr-2"
                >{{ row.detailsShowing ? 'Ocultar' : 'Ver'}} Detalles</b-button>
              </template>

              <template v-slot:row-details="row">
                <div class="card">
                  <div
                    class="card-header bg-secondary text-white font-weight-bold"
                  >Detalle de la venta</div>
                  <div class="card-body">
                    <b-table
                      class="mt-1"
                      id="listadoDeVentas"
                      style="font-size:12px;"
                      :outlined="true"
                      :small="true"
                      :fixed="true"
                      :no-border-collapse="true"
                      head-variant="light"
                      :items="row.item.detalles"
                      :fields="fieldsDetalleVenta"
                    >
                      <template v-slot:cell(Descripcion)="row">
                        <p class="font-weight-bold">{{row.item.Descripcion}}</p>
                      </template>
                      <template v-slot:cell(cantidad)="row">
                        <p class="font-weight-bold">{{row.item.cantidad}}</p>
                      </template>
                      <template v-slot:cell(subtotal)="row">
                        <p class="font-weight-bold text-primary">${{row.item.subtotal}}</p>
                      </template>
                      <template v-slot:cell(precio_venta)="row">
                        <p class="font-weight-bold text-warning">${{row.item.precio_venta}}</p>
                      </template>
                    </b-table>
                  </div>
                </div>
              </template>
            </b-table>

            <div @click="getListadoVentasClienteCC(cuitClienteEspecifico)" align="center">
              <b-pagination
                aria-controls="listado-productos"
                style="font-size:12px;"
                v-model="paginaActualListadoDeVentas"
                :total-rows="listadoDeVentas.total"
                :per-page="5"
                first-text="Primera"
                prev-text="Anterior"
                next-text="Siguiente"
                last-text="Última"
                align="center"
              ></b-pagination>
            </div>

            <button @click="volver()" class="btn btn-sm btn-primary">Volver</button>
            <b-button v-if="ventasFacturar.length>0" v-b-modal.modal-1 class="btn btn-sm btn-secondary" @click="totalMontoFacturar()">Facturar</b-button>
          </div>
        </div>

        <div v-if="listadoIngresos != ''" class="card-body row mt-1">
          <div class="col col-lg">
            <b-table
              class="mt-1"
              id="listadoDeVentas"
              style="font-size:12px;"
              :striped="true"
              :outlined="true"
              :small="true"
              :fixed="true"
              :no-border-collapse="true"
              head-variant="dark"
              :items="listadoIngresos.data"
              :fields="fieldsListadoIngresos"
            >
              <template v-slot:cell(ingreso)="row">
                <p class="font-weight-bold text-success">${{row.item.ingreso}}</p>
              </template>

              <template v-slot:cell(condicion_venta)="row">
                <p v-if="row.item.condicion_venta == 'Efectivo'" class="font-weight-bold">Efectivo</p>
                <p
                  v-if="row.item.condicion_venta == 'Débito'"
                  class="font-weight-bold"
                >Tarjeta de débito</p>
                <p
                  v-if="row.item.condicion_venta == 'Crédito'"
                  class="font-weight-bold"
                >Tarjeta de crédito</p>
                <p v-if="row.item.condicion_venta == 'Cheque'" class="font-weight-bold">Cheque</p>
              </template>
            </b-table>

            <div @click="getIngresosCobranza(idClienteIngresos)" align="center">
              <b-pagination
                aria-controls="listado-ingresosCobranza"
                style="font-size:12px;"
                v-model="paginaActualListadoDeIngresos"
                :total-rows="listadoIngresos.total"
                :per-page="10"
                first-text="Primera"
                prev-text="Anterior"
                next-text="Siguiente"
                last-text="Última"
                align="center"
              ></b-pagination>
            </div>

            <button @click="volver()" class="btn btn-sm btn-primary">Volver</button>
          </div>
        </div>

        <b-modal
          ref="modalIngresoCobranza"
          id="modalIngresoCobranza"
          size="xl"
          hide-footer
          title="Ingreso de cobranza"
        >
          <div ref="errorIngresoCobranza" class="alert alert-danger fade show" role="alert" hidden>
            <span class="font-weight-bold">ERROR!</span> Por favor complete los campos resaltados en rojo.
          </div>

          <div class="row">
            <div class="col col-lg form-group" style="font-size:12px">
              <div class="form-group">
                <label class="font-weight-bold" for="medio_pago">Condición de venta</label>
                <select
                  ref="condicion_venta"
                  v-model="condicion_venta"
                  id="medio_pago"
                  class="form-control form-control-sm"
                >
                  <option
                    class="font-weight-bold"
                    value
                    selected
                    disabled
                  >Seleccione la condición de venta</option>
                  <option class="font-weight-bold" value="Efectivo">Contado - Efectivo</option>
                  <option class="font-weight-bold" value="Débito">Tarjeta de débito</option>
                  <option class="font-weight-bold" value="Crédito">Tarjeta de crédito</option>
                  <option class="font-weight-bold" value="Cheque">Cheque</option>
                </select>
              </div>
              <div class="form-group text-center">
                <label class="font-weight-bold" for="monto_pago">Monto</label>
                <input
                  ref="monto"
                  v-model="monto"
                  type="number"
                  id="monto_pago"
                  class="form-control form-control-sm"
                />
                <span style="font-size:10px;">Recuerde que ell monto ingresado debe ser mayor a $0</span>
              </div>
              <div v-if="condicion_venta == 'Efectivo'" class="form-group">
                <label class="font-weight-bold" for="caja">Caja donde ingresa el efectivo</label>
                <select
                  ref="cajaSeleccionada"
                  v-model="cajaSeleccionada"
                  id="caja"
                  class="form-control form-control-sm"
                >
                  <option
                    class="font-weight-bold"
                    value
                    selected
                    disabled
                  >Seleccione la caja donde ingresa el efectivo</option>
                  <option
                    v-for="item in listadoCajas"
                    :key="item.id"
                    class="font-weight-bold"
                    :value="item.id"
                  >{{item.descripcion}}</option>
                </select>
              </div>
              <div v-if="condicion_venta == 'Cheque'" class="card">
                <div class="card-header border my-auto bg-dark text-center text-white">
                  <h5 class="font-weight-bold mb-n2">Datos del cheque</h5>
                </div>

                <div class="row ml-1 mr-1">
                  <div class="form-group col col-lg text-center">
                    <label class="font-weight-bold" for="tipo_cheque">Tipo de cheque</label>
                    <select
                      ref="tipo_cheque"
                      v-model="datosCheque.tipo_cheque"
                      id="tipo_cheque"
                      class="form-control form-control-sm"
                    >
                      <option
                        class="font-weight-bold"
                        value
                        selected
                        disabled
                      >Seleccione el tipo de cheque</option>
                      <option class="font-weight-bold" value="Diferido">Diferido</option>
                      <option class="font-weight-bold" value="No_Diferido">No Diferido</option>
                    </select>
                  </div>

                  <div class="form-group col col-lg text-center">
                    <label class="font-weight-bold" for="numero_cheque">Número de cheque</label>
                    <input
                      ref="numero_cheque"
                      v-model="datosCheque.numero_cheque"
                      type="number"
                      id="numero_cheque"
                      class="form-control form-control-sm"
                    />
                  </div>

                  <div class="w-100"></div>

                  <div class="form-group col col-lg text-center">
                    <label class="font-weight-bold" for="fecha_emision">Fecha de emisión</label>
                    <input
                      ref="fecha_emision"
                      v-model="datosCheque.fecha_emision"
                      type="date"
                      id="fecha_emision"
                      class="form-control form-control-sm"
                    />
                  </div>

                  <div class="form-group col col-lg text-center">
                    <label class="font-weight-bold" for="banco">Banco</label>
                    <input
                      ref="banco"
                      v-model="datosCheque.banco"
                      type="text"
                      id="banco"
                      class="form-control form-control-sm"
                    />
                  </div>

                  <div class="w-100"></div>

                  <div
                    v-if="datosCheque.tipo_cheque == 'Diferido'"
                    class="form-group col col-lg text-center"
                  >
                    <label class="font-weight-bold" for="fecha_diferido">Fecha de diferido</label>
                    <input
                      ref="fecha_diferido"
                      v-model="datosCheque.fecha_diferido"
                      type="date"
                      id="fecha_diferido"
                      class="form-control form-control-sm"
                    />
                  </div>
                </div>
              </div>
              <div class="row mt-1">
                <div class="col col-lg text-center">
                  <b-button
                    class="mt-3"
                    variant="success"
                    size="sm"
                    @click="ingresoDeCobranza()"
                  >Ingreso de cobranza</b-button>
                  <b-button
                    class="mt-3"
                    variant="danger"
                    size="sm"
                    @click="$bvModal.hide('modalIngresoCobranza');resetIngresoCobranza()"
                  >Cancelar</b-button>
                </div>
              </div>
            </div>
          </div>
        </b-modal>
        <div>
          

          <b-modal id="modal-1" title="Liquidacion " cancel-title="Cancelar" ok-title="Facturar" @cancel="limpia()"  @ok="cierraModal()">
            <span>Total Ventas  a Facturar: {{ventasFacturar.length}}</span>
            
               <div class="col col-lg">
                  <label class="font-weight-bold">Ajuste efectivo:</label>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input
                      v-model="descuentoEfectivo"
                      @change="AplicaPorcentaje()"
                      type="number"
                      class="form-control"
                      step="0.01"
                    />
                  </div>
                </div>
                <div class="col col-lg">
                  <label class="font-weight-bold">Aumento procentual:</label>
                  <div class="input-group">
                    <input
                      @change="AplicaPorcentaje()"
                      v-model="descuentoPorcentual"
                      type="number"
                      class="form-control"
                      step="0.01"
                    />
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon2">%</span>
                    </div>
                  </div>
                </div>
                <h2 class="mt-2">Monto Total a Facturar: <span class="text-success">${{totalIntereses}}</span></h2>
          </b-modal>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      /* \\\\\\\\\\\\\\\\\ BUSQUEDA DEL CLIENTE \\\\\\\\\\\\\\\\\ */
      show: false,
      clienteBuscado: "", //Cliente buscado
      listadoClientes: [], //Listado de clientes
      fieldsListadoClientes: [
        { key: "Nombre", label: "Nombre" },
        { key: "Domicilio", label: "Domicilio" },
        { key: "CUIT", label: "CUIT/CUIL/DNI" },
        { key: "monto_maximo_cc", label: "Máximo en Cuenta Corriente" },
        { key: "total_cc", label: "Saldo en Cuenta Corriente" },
        "acciones"
      ], //Columnas del listado de clientes
      filtroBusquedaCliente: "", //Filtro de busqueda de los clientes
      paginaActualCliente: 1,
      

      /* \\\\\\\\\\\\\\\\\ VENTAS DEL CLIENTE \\\\\\\\\\\\\\\\\ */
      listadoDeVentas: [], //Listado de ventas del cliente
      fieldsListadoDeVentas: [
        { key: "fecha", label: "Fecha de la venta" },
        { key: "total", label: "Total" },
        { key: "factura", label: "factura" },
        "Facturar",
        "acciones"
      ], //Columnas del listado de ventas
      fieldsDetalleVenta: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "Cantidad" },
        { key: "precio_venta", label: "Precio de venta" },
        { key: "subtotal", label: "Subtotal" }
      ],
      paginaActualListadoDeVentas: 1,
      cuitClienteEspecifico: "", //Datos del cleinte en la venta

      /* \\\\\\\\\\\\\\\\\ INGRESO DE COBRANZA \\\\\\\\\\\\\\\\\ */
      idCliente: "", //Id del cliente que realiza el ingreso
      indexCliente: "", //Index del cliente que realiza el ingreso
      ventasFacturar: [],
      descuentoEfectivo:0,
      descuentoPorcentual:0,
      totalFacturar:0,
      totalIntereses:0,
      condicion_venta: "", //Condición de la venta
      datosCheque: {
        tipo_cheque: "",
        numero_cheque: "",
        fecha_emision: "",
        banco: "",
        fecha_diferido: ""
      }, //Datos que tiene un cheque
      monto: "", //Monto del ingreso
      listadoCajas: [], //Listado de cajas que se muesta en el caso que la venta sea efectivo
      cajaSeleccionada: "", //Caja seleccionada en caso que al venta sea en efectivo

      /* \\\\\\\\\\\\\\\\\ LISTADO DE INGRESOS DE COBRANZA (MOVIMIENTOS DEL CLIENTE) \\\\\\\\\\\\\\\\\ */
      listadoIngresos: [],
      fieldsListadoIngresos: [
        { key: "ingreso", label: "Monto" },
        { key: "fecha", label: "Fecha de la operación" },
        { key: "condicion_venta", label: "Condición de venta" }
      ],
      paginaActualListadoDeIngresos: 1,
      idClienteIngresos: ""
    };
  },
  methods: {
    getIngresosCobranza(id) {
      if (!this.$auth.can("Ver Ingresos Cobranza")) {
        alertify.error(
          "Usted no está autorizado a ver los ingresos de cobranza del cliente"
        );
      } else {
        axios
          .post("api/getIngresosCobranza", {
            id: id,
            page: this.paginaActualListadoDeIngresos
          })
          .then(response => {
            if (response.data.data.length > 0) {
              this.listadoIngresos = response.data;
            } else {
              alertify.error(
                "El cliente no tiene registrado ingresos de cobranza hasta el momento"
              );
            }
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    ingresoDeCobranza() {
      if (!this.$auth.can("Realizar Ingreso Cobranza")) {
        alertify.error(
          "Usted no está autorizado a realizar ingresos de cobranza"
        );
      } else {
        let errors = 0;

        if (this.condicion_venta == "") {
          this.$refs["condicion_venta"].classList.add("border-danger");
          this.$refs["errorIngresoCobranza"].removeAttribute("hidden");
          errors++;

          if (this.monto == "" || this.monto <= 0) {
            this.$refs["monto"].classList.add("border-danger");
            this.$refs["errorIngresoCobranza"].removeAttribute("hidden");
            errors++;
          } else {
            this.$refs["monto"].classList.remove("border-danger");
          }
        } else {
          this.$refs["condicion_venta"].classList.remove("border-danger");
          this.$refs["monto"].classList.remove("border-danger");
          this.$refs["errorIngresoCobranza"].setAttribute("hidden", true);

          if (this.monto == "" || this.monto <= 0) {
            this.$refs["monto"].classList.add("border-danger");
            this.$refs["errorIngresoCobranza"].removeAttribute("hidden");
            errors++;
          } else {
            this.$refs["monto"].classList.remove("border-danger");
            this.$refs["errorIngresoCobranza"].setAttribute("hidden", true);
          }

          if (this.condicion_venta == "Efectivo") {
            if (this.cajaSeleccionada == "") {
              this.$refs["cajaSeleccionada"].classList.add("border-danger");
              this.$refs["errorIngresoCobranza"].removeAttribute("hidden");
              errors++;
            } else {
              this.$refs["cajaSeleccionada"].classList.remove("border-danger");
              this.$refs["errorIngresoCobranza"].setAttribute("hidden", true);
            }
          }

          if (this.condicion_venta == "Cheque") {
            if (this.datosCheque.tipo_cheque == "") {
              this.$refs["tipo_cheque"].classList.add("border-danger");
              errors++;
            } else {
              this.$refs["tipo_cheque"].classList.remove("border-danger");
            }

            if (this.datosCheque.numero_cheque == "") {
              this.$refs["numero_cheque"].classList.add("border-danger");
              errors++;
            } else {
              this.$refs["numero_cheque"].classList.remove("border-danger");
            }

            if (this.datosCheque.fecha_emision == "") {
              this.$refs["fecha_emision"].classList.add("border-danger");
              errors++;
            } else {
              this.$refs["fecha_emision"].classList.remove("border-danger");
            }

            if (this.datosCheque.banco == "") {
              this.$refs["banco"].classList.add("border-danger");
              errors++;
            } else {
              this.$refs["banco"].classList.remove("border-danger");
            }

            if (this.datosCheque.tipo_cheque == "Diferido") {
              if (this.datosCheque.fecha_diferido == "") {
                this.$refs["fecha_diferido"].classList.add("border-danger");
                errors++;
              } else {
                this.$refs["fecha_diferido"].classList.remove("border-danger");
              }
            }
          }
        }

        if (errors == 0) {
          alertify
            .confirm(
              "Realizar ingreso de cobranza",
              "¿Esta seguro de realizar el ingreso de cobranza?",
              () => {
                axios
                  .post("api/realizarIngresoCobranza", {
                    id_cliente: this.idCliente,
                    condicion_venta: this.condicion_venta,
                    monto: this.monto,
                    datosCheque: this.datosCheque,
                    cajaSeleccionada: this.cajaSeleccionada,
                    userid: window.user.user.id
                  })
                  .then(response => {
                    if (response.data == 0) {
                      this.listadoClientes.data[this.indexCliente]["total_cc"] =
                        this.listadoClientes.data[this.indexCliente][
                          "total_cc"
                        ] - this.monto;
                      this.$refs["modalIngresoCobranza"].hide();
                      this.resetIngresoCobranza();
                      alertify.success(
                        "Se ha realizado el ingreso de cobranza"
                      );
                    } else {
                      alertify.error(
                        "Surgió un error al intentar realizar el ingreso de cobranza"
                      );
                    }
                  })
                  .catch(error => {
                    alertify.error(
                      "Surgió un error al intentar realizar el ingreso de cobranza"
                    );
                  });
              },
              () => {
                alertify.error("Se ha cancelado el ingreso de cobranza");
              }
            )
            .set("labels", {
              ok: "Realizar ingreso de cobranza",
              cancel: "Cancelar"
            });
        }
      }
    },
    buscarCliente() {
      if (this.filtroBusquedaCliente == "cuit") {
        axios
          .post("api/getListadoClientesCuentaCorriente", {
            cuit: this.clienteBuscado,
            filtro: this.filtroBusquedaCliente,
            page: this.paginaActualCliente
          })
          .then(response => {
            this.listadoClientes = response.data;
          });
      } else {
        axios
          .post("api/getListadoClientesCuentaCorriente", {
            cliente: this.clienteBuscado,
            filtro: this.filtroBusquedaCliente,
            page: this.paginaActualCliente
          })
          .then(response => {
            this.listadoClientes = response.data;
          });
      }
    },
    busquedaPorFiltro() {
      if (
        this.filtroBusquedaCliente == "deudores" ||
        this.filtroBusquedaCliente == "saldosFavor" ||
        this.filtroBusquedaCliente == ""
      ) {
        this.$refs["buscadorClientes"].setAttribute("disabled", true);
        this.listadoClientes = [];
        this.clienteBuscado = "";
        this.paginaActualCliente = 1;
      } else {
        this.$refs["buscadorClientes"].removeAttribute("disabled");
        this.listadoClientes = [];
        this.clienteBuscado = "";
        this.paginaActualCliente = 1;
      }
    },
    getListadoVentasClienteCC(cuit,id) {
      if (!this.$auth.can("Ver Ventas CC")) {
        
        alertify.error(
          "Usted no está autorizado a ver las ventas del cliente por cuenta corriente"
        );
      } else {
        this.idCliente=id;  
        axios
          .post("api/getListadoVentasClienteCC", {
            cuit: cuit,
            idcliente:id,
            page: this.paginaActualListadoDeVentas
          })
          .then(response => {
            if (response.data.data.length > 0) {
              this.listadoDeVentas = response.data;
              this.cuitClienteEspecifico = cuit;
            } else {
              alertify.error(
                "El cliente no registra ventas por cuenta corriente"
              );
              this.listadoDeVentas = [];
            }
          });
      }
    },
    volver() {
      this.listadoDeVentas = [];
      this.paginaActualListadoDeVentas = 1;
      this.cuitClienteEspecifico = "";

      this.listadoIngresos = [];
       this.ventasFacturar =  [];
      this.paginaActualListadoDeIngresos = 1;
      this.idClienteIngresos = "";
    },
    getListadoCajasAbiertas(id, index) {
      if (!this.$auth.can("Realizar Ingreso Cobranza")) {
        alertify.error(
          "Usted no está autorizado a realizar ingresos de cobranza"
        );
      } else {
        this.indexCliente = index;
        this.idCliente = id;
        axios.post("api/getListadoCajasAbiertas").then(response => {
          this.listadoCajas = response.data;
        });
        this.$refs["modalIngresoCobranza"].show();
      }
    },
    resetIngresoCobranza() {
      this.condicion_venta = "";
      this.monto = "";
      this.datosCheque = {
        tipo_cheque: "",
        numero_cheque: "",
        fecha_emision: "",
        banco: "",
        fecha_diferido: ""
      };
      this.cajaSeleccionada = "";
    },
    agregaFactura(check, data) {
      if (check) {
        this.ventasFacturar.push(data);
      } else {
        this.ventasFacturar = this.ventasFacturar.filter(function(item) {
          return item !== data;
        });
      }

      console.log(this.ventasFacturar);
    },
    totalMontoFacturar(){
      let total = 0 ;
      let ventas = [];
      ventas = this.listadoDeVentas.data;


      this.ventasFacturar.forEach(element=>{
          ventas.forEach(item =>{
              if(element == item.id){
                 total = total + item.total; 
              }
          });
          
      })
      this.totalFacturar= total;
      this.AplicaPorcentaje();

    },
    limpia(){
      console.log("entra");
      this.totalIntereses = 0;
      this.totalFacturar = 0;
    },
    AplicaPorcentaje(){
      this.totalIntereses = this.totalFacturar;

     
      
      this.totalIntereses = this.totalIntereses + (this.totalIntereses*this.descuentoPorcentual/100);
      this.totalIntereses = this.totalIntereses - this.descuentoEfectivo;
    },
    cierraModal(){
       
      console.log("idCliente:"+this.idCliente);
      console.log("Facturas:"+this.ventasFacturar);
      axios.post('api/facturaVentas',{
        idcliente: this.idCliente,
        ventas:this.ventasFacturar,
        descuentoEfectivo:this.descuentoEfectivo,
        porcentual: this.descuentoPorcentual,


      }).then(res=>{
        alertify.success("Facturacion Realizada Correctamente");  
        this.getListadoVentasClienteCC();


      }).catch(err=>{
         console.log(err);
      });


    },
  },
  mounted() {
    this.show = true;
  }
};
</script>
