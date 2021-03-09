<template>
  <div class="ml-1 mr-1">
    <b-modal
      ref="modalRegistrarPago"
      id="modalRegistrarPago"
      size="xl"
      hide-footer
      header-bg-variant="dark"
      :hideHeaderClose="true"
    >
      <template v-slot:modal-title>
        <span class="text-white font-weight-bold">Registrar pago</span>
      </template>
      <div class="card row border-0 text-center">
        <div class="card-body row mt-n4 mb-n4">
          <div class="form-group col col-xl">
            <input
              ref="monto"
              v-model="datosPago.monto"
              type="number"
              step="0.01"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold" style="font-size: 12px"
              >Monto</label
            >
          </div>
          <div class="form-group col col-xl">
            <input
              ref="fecha_pago"
              v-model="datosPago.fechaPago"
              type="date"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold" style="font-size: 12px"
              >Fecha del pago</label
            >
          </div>
          <div class="w-100"></div>
          <div class="form-group col col-xl">
            <select
              ref="medio_pago"
              v-model="datosPago.medioPago"
              class="form-control form-control-sm"
            >
              <option disabled selected value>
                Seleccione el medio de pago
              </option>
              <option>Efectivo</option>
              <option>Transferencia Bancaria</option>
              <option>Cheque</option>
            </select>
          </div>
          <div class="w-100"></div>
          <div class="form-group col col-xl">
            <textarea
              v-model="datosPago.obs"
              type="date"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold" style="font-size: 12px"
              >Observaciones</label
            >
          </div>
          <div class="w-100"></div>
          <div class="col col-xl">
            <button @click="savePago()" class="btn btn-sm btn-success">
              Añadir pago
            </button>
            <button
              @click="$bvModal.hide('modalRegistrarPago')"
              class="btn btn-sm btn-danger"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </b-modal>

    <div class="row card mt-1">
      <div
        class="card-header text-primary bg-transparent font-weight-bold text-left"
      >
        Gestión de pedidos
        <button
          v-if="pagos"
          class="btn btn-sm btn-primary float-right"
          @click="
            datosPago = {
              monto: '',
              fechaPago: '',
              medioPago: '',
              obs: '',
            }
          "
          v-b-modal.modalRegistrarPago
        >
          Registrar pago
        </button>
      </div>

      <div v-if="!pagos && detallePedido == ''" class="card-body row">
        <div class="input-group">
          <input
            :disabled="
              filtroBusqueda == 'saldo_favor' ||
              filtroBusqueda == 'saldo_contra' ||
              filtroBusqueda == ''
            "
            v-if="
              filtroBusqueda != 'fecha_pedido' &&
              filtroBusqueda != 'fecha_arribo'
            "
            ref="buscadorClientes"
            type="text"
            class="form-control form-control-sm"
            v-model="busqueda"
            placeholder="Buscar pedidos"
          />
          <input
            v-if="
              filtroBusqueda == 'fecha_pedido' ||
              filtroBusqueda == 'fecha_arribo'
            "
            ref="buscadorClientes"
            type="date"
            class="form-control form-control-sm"
            v-model="busqueda"
            placeholder="Buscar pedidos"
          />
          <div class="input-group-append">
            <select
              v-model="filtroBusqueda"
              @change="busqueda = ''"
              class="form-control form-control-sm"
            >
              <option selected value>No aplicar filtros</option>
              <option value="cuit">CUIT Proveedor</option>
              <option value="saldo_favor">Saldo a favor</option>
              <option value="saldo_contra">Saldo en contra</option>
              <option value="fecha_pedido">Fecha en la que se realizó</option>
              <option value="fecha_arribo">Fecha estimada de llegada</option>
            </select>
          </div>
          <button
            class="btn btn-sm btn-primary btn-block mt-1"
            v-on:click="getPedidos()"
            type="button"
          >
            Buscar
          </button>
        </div>

        <div ref="spinnerListadoPedidos" hidden class="col text-center mt-2">
          <div class="spinner-border text-dark" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <b-table
          v-if="listadoPedidos.total > 0"
          class="mt-1"
          style="font-size: 12px"
          outlined
          small
          hover
          fixed
          no-border-collapse
          head-variant="dark"
          :items="listadoPedidos.data"
          :fields="fieldsListadoPedidos"
        >
          <template v-slot:cell(nombre)="row">
            <span class="font-weight-bold">{{ row.item.nombre }}</span>
          </template>

          <template v-slot:cell(fecha)="row">
            <span>{{
              new Date(row.item.fecha).toLocaleDateString("en-GB", {
                timeZone: "UTC",
              })
            }}</span>
          </template>

          <template v-slot:cell(fecha_arribo)="row">
            <span v-if="row.item.fecha_arribo != null">{{
              new Date(row.item.fecha_arribo).toLocaleDateString("en-GB", {
                timeZone: "UTC",
              })
            }}</span>
          </template>

          <template v-slot:cell(total)="row">
            <span class="font-weight-bold text-success"
              >${{ row.item.total }}</span
            >
          </template>

          <template v-slot:cell(saldo)="row">
            <span
              v-if="row.item.saldo > 0"
              class="font-weight-bold text-success"
              >${{ row.item.saldo }}</span
            >
            <span
              v-if="row.item.saldo == 0"
              class="font-weight-bold text-primary"
              >${{ row.item.saldo }}</span
            >
            <span v-if="row.item.saldo < 0" class="font-weight-bold text-danger"
              >${{ row.item.saldo.toString().slice(1) }}</span
            >
          </template>

          <template v-slot:cell(acciones)="row">
            <!--PDF de verificación-->
            <button
              v-if="$auth.can('Descargar Detalle Pedido')"
              @click="
                descargarPdf(
                  row.item.id,
                  row.item.fecha_arribo,
                  row.item.fecha,
                  row.item.total,
                  row.item.idProveedor
                )
              "
              class="btn btn-sm btn-success"
              title="Descargar PDF"
            >
              <i class="fas fa-file-pdf"></i>
            </button>

            <!--Detalle del pedido-->
            <button
              v-if="$auth.can('Ver Info Pedidos')"
              @click="getDetalle(row.item.id, row.index)"
              class="btn btn-sm btn-primary"
              title="Ver detalle del pedido"
            >
              <i class="fas fa-info-circle"></i>
            </button>

            <button
              v-if="$auth.can('Gestionar Pagos Pedido')"
              class="btn btn-sm btn-warning text-white"
              style="background-color: #ffa500"
              title="Gestionar Pagos"
              @click="getPagos(row.item.id, row.index)"
            >
              <i class="fas fa-money-check-alt"></i>
            </button>

            <button
              v-if="$auth.can('Eliminar Pedidos')"
              @click="deletePedido(row.item.id)"
              class="btn btn-sm btn-danger"
              title="Eliminar pedido"
            >
              <i class="fas fa-trash-alt"></i>
            </button>
          </template>
        </b-table>

        <div
          class="col text-center"
          v-if="listadoPedidos.total != undefined && listadoPedidos.total > 10"
          @click="getPedidos()"
        >
          <b-pagination
            size="sm"
            style="font-size: 12px"
            v-model="paginaActualListadoPedidos"
            :total-rows="listadoPedidos.total"
            :per-page="10"
            first-text="Primera"
            prev-text="Anterior"
            next-text="Siguiente"
            last-text="Última"
            align="center"
          ></b-pagination>
        </div>
      </div>

      <div v-if="!pagos && detallePedido != ''" class="card-body row">
        <div class="col col-xl">
          <div class="row">
            <div style="font-size: 12px" class="col col-xl">
              <input
                class="form-control form-control-sm"
                type="text"
                style="background-color: transparent"
                :value="datosPedidoEspecifico.nombre"
                readonly
              />
              <label class="font-weight-bold">Proveedor</label>
            </div>
            <div class="w-100"></div>
            <div style="font-size: 12px" class="form-group col col-xl">
              <input
                class="form-control form-control-sm"
                type="text"
                style="background-color: transparent"
                :value="
                  new Date(
                    datosPedidoEspecifico.fecha
                  ).toLocaleDateString('en-GB', { timeZone: 'UTC' })
                "
                readonly
              />
              <label class="font-weight-bold"
                >Fecha en la que se realizó el pedido</label
              >
            </div>
            <div
              v-if="datosPedidoEspecifico.fecha_arribo != null"
              style="font-size: 12px"
              class="form-group col col-xl"
            >
              <input
                class="form-control form-control-sm"
                type="text"
                style="background-color: transparent"
                :value="
                  new Date(
                    datosPedidoEspecifico.fecha_arribo
                  ).toLocaleDateString('en-GB', { timeZone: 'UTC' })
                "
                readonly
              />
              <label class="font-weight-bold">Fecha estimada de llegada</label>
            </div>
            <div v-else style="font-size: 12px" class="form-group col col-xl">
              <input
                class="form-control form-control-sm"
                type="text"
                style="background-color: transparent"
                value="Sin especificar"
                readonly
              />
              <label class="font-weight-bold">Fecha estimada de llegada</label>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <b-table
            class="mt-1"
            style="font-size: 12px"
            outlined
            small
            hover
            fixed
            no-border-collapse
            head-variant="dark"
            :items="detallePedido"
            :fields="fieldsDetallePedido"
          >
            <template v-slot:cell(Descripcion)="row">
              <span class="font-weight-bold">{{ row.item.Descripcion }}</span>
            </template>
            <template v-slot:cell(precio_costo)="row">
              <span class="font-weight-bold text-primary"
                >${{ row.item.precio_costo }}</span
              >
            </template>
            <template v-slot:cell(subtotal)="row">
              <span class="font-weight-bold text-success"
                >${{ row.item.subtotal }}</span
              >
            </template>
          </b-table>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <h5 class="float-right">
            Total:
            <span class="text-success font-weight-bold"
              >${{ datosPedidoEspecifico.total }}</span
            >
          </h5>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <h5 class="text-right" v-if="datosPedidoEspecifico.saldo > 0">
            Saldo:
            <span class="font-weight-bold text-success"
              >${{ datosPedidoEspecifico.saldo }}</span
            >
          </h5>
          <h5 class="text-right" v-if="datosPedidoEspecifico.saldo == 0">
            Saldo:
            <span class="font-weight-bold text-primary"
              >${{ datosPedidoEspecifico.saldo }}</span
            >
          </h5>
          <h5 class="text-right" v-if="datosPedidoEspecifico.saldo < 0">
            Saldo:
            <span class="font-weight-bold text-danger"
              >${{ datosPedidoEspecifico.saldo.toString().slice(1) }}</span
            >
          </h5>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <button
            v-b-modal.ConfirmaPedido
            class="btn btn-sm btn-success"
            title="Cargar Pedido"
          >
            Carga Pedido
            <i class="fas fa-check"></i>
          </button>
          <button
            @click="
              detallePedido = [];
              datosPedidoEspecifico = [];
            "
            class="btn btn-sm btn-primary"
          >
            Volver
          </button>
        </div>
      </div>
      <b-modal
        id="ConfirmaPedido"
        size="xl"
        hide-footer
        title="Validar Carga Pedido"
      >
        <div class="container-fluid">
          <div class="card">
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >
              Confirmacion Pedido
            </div>

            <div class="card-body">
              <div class="form-group">
                <b-table
                  id="listado-pedido"
                  class="mt-1"
                  style="font-size: 12px"
                  outlined
                  small
                  hover
                  fixed
                  no-border-collapse
                  head-variant="dark"
                  :items="detallePedido"
                  :fields="fieldsListadoDetallePedido"
                >
                  <template v-slot:cell(cantidad)="row">
                      <input
                    style="font-size:12px;"
                    type="number"
                    v-model="row.item.cantidad"
                    @change="modificaCantidad(row.index,row.item.cantidad)"
                    class="input-stock my-auto bg-transparent text-center font-weight-bold form-control form-control-sm "
                  />
                  </template>
                </b-table>

                <b-button
                  class="mt-3"
                  variant="success"
                  block
                  @click="confirmaStock()"
                >
                  Cargar STOCK
                </b-button>
                  <b-button
                  class="mt-3"
                  variant="primary"
                  block
                  @click="$bvModal.hide('ConfirmaPedido')"
                >
                  Volver
                </b-button>
              </div>
            </div>
          </div>
        </div>
      </b-modal>
      <div v-if="pagos" class="card-body row">
        <div style="font-size: 12px" class="col col-xl">
          <input
            class="form-control form-control-sm"
            type="text"
            style="background-color: transparent"
            :value="datosPedidoEspecifico.nombre"
            readonly
          />
          <label class="font-weight-bold">Proveedor</label>
        </div>
        <div class="w-100"></div>
        <div style="font-size: 12px" class="form-group col col-xl">
          <input
            class="form-control form-control-sm"
            type="text"
            style="background-color: transparent"
            :value="
              new Date(datosPedidoEspecifico.fecha).toLocaleDateString(
                'en-GB',
                { timeZone: 'UTC' }
              )
            "
            readonly
          />
          <label class="font-weight-bold"
            >Fecha en la que se realizó el pedido</label
          >
        </div>
        <div
          v-if="datosPedidoEspecifico.fecha_arribo != null"
          style="font-size: 12px"
          class="form-group col col-xl"
        >
          <input
            class="form-control form-control-sm"
            type="text"
            style="background-color: transparent"
            :value="
              new Date(
                datosPedidoEspecifico.fecha_arribo
              ).toLocaleDateString('en-GB', { timeZone: 'UTC' })
            "
            readonly
          />
          <label class="font-weight-bold">Fecha estimada de llegada</label>
        </div>
        <div v-else style="font-size: 12px" class="form-group col col-xl">
          <input
            class="form-control form-control-sm"
            type="text"
            style="background-color: transparent"
            value="Sin especificar"
            readonly
          />
          <label class="font-weight-bold">Fecha estimada de llegada</label>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <b-table
            class="mt-1"
            style="font-size: 12px"
            outlined
            small
            hover
            fixed
            no-border-collapse
            head-variant="dark"
            v-if="listadoPagos.data != ''"
            :fields="fieldsListadoPagos"
            :items="listadoPagos.data"
          >
            <template v-slot:cell(acciones)="row">
              <button
                @click="deletePago(row.item.id)"
                class="btn btn-sm btn-danger"
                title="Eliminar pago"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </template>

            <template v-slot:cell(monto)="row">
              <span class="font-weight-bold text-success"
                >${{ row.item.monto }}</span
              >
            </template>

            <template v-slot:cell(fecha)="row">
              <span>{{
                new Date(row.item.fecha).toLocaleDateString("en-GB", {
                  timeZone: "UTC",
                })
              }}</span>
            </template>
          </b-table>
          <h5 v-else>No se han encontrado pagos asociados al pedido</h5>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <h5 class="text-right" v-if="saldoPagos > 0">
            Saldo:
            <span class="font-weight-bold text-success">${{ saldoPagos }}</span>
          </h5>
          <h5 class="text-right" v-if="saldoPagos == 0">
            Saldo:
            <span class="font-weight-bold text-primary">${{ saldoPagos }}</span>
          </h5>
          <h5 class="text-right" v-if="saldoPagos < 0">
            Saldo:
            <span class="font-weight-bold text-danger"
              >${{ saldoPagos.toString().slice(1) }}</span
            >
          </h5>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl">
          <button
            @click="
              datosPedidoEspecifico = [];
              listadoPagos = [];
              pagos = false;
            "
            class="btn btn-sm btn-primary"
          >
            Volver
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Echo from "laravel-echo";
window.Pusher = require("pusher-js");

window.Echo = new Echo({
  broadcaster: "pusher",
  key: "b46f98164b270b0a3c38",
  wsHost: window.location.hostname,
  wsPort: 6001,
  disableStats: true,
});

export default {
  data() {
    return {
      busqueda: "",
      filtroBusqueda: "",
      listadoPedidos: [],
      modificaPedido:[],
      paginaActualListadoPedidos: 1,
      fieldsListadoPedidos: [
        { key: "nombre", label: "Proveedor" },
        { key: "fecha", label: "Fecha de pedido" },
        { key: "fecha_arribo", label: "Fecha estimada de llegada" },
        { key: "total", label: "Total" },
        { key: "saldo", label: "Saldo" },
        "acciones",
      ],
      fieldsListadoDetallePedido:[{ key: "Descripcion", label: "Producto" },
                                { key: "cantidad", label: "Cantidad" },
        ],
      datosPedidoEspecifico: [],
      detallePedido: [],
      fieldsDetallePedido: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "Cantidad" },
        { key: "precio_costo", label: "Precio de costo" },
        { key: "subtotal", label: "Subtotal" },
      ],
      pagos: false,
      datosPago: {
        monto: "",
        fechaPago: "",
        medioPago: "",
        obs: "",
      },
      listadoPagos: [],
      fieldsListadoPagos: [
        { key: "monto", label: "Monto" },
        { key: "fecha", label: "Fecha de pago" },
        { key: "medio_pago", label: "Medio de pago" },
        { key: "obs", label: "Observaciones" },
        "acciones",
      ],
      saldoPagos: 0,
    };
  },
  methods: {
    modificaCantidad(index,data){
      this.modificaPedido[index].cantidad = data;
    },
    confirmaStock(){
      let cantidades =[];
      this.modificaPedido.forEach(element=>{
        cantidades.push(element.Descripcion + " <span style='color:green;font-size:25px'> =  </span>  " + element.cantidad +"<br>");
      })
      
      alertify.confirm('<h5>Esta seguro que confirma Carga Stock: </h5><br>' + cantidades).setting({
        'title':'Carga Stock',
        'labels':{
          ok:'Confirmar',
          cancel:'Cancelar'
        },
        'reverseButtons': true,
        'onok': function(){
            alertify.success("aceptado");
        },
        'oncancel': function(){
          alertify.error("cancelo la carga de stock");
        }

      }).show();

    },
    getPedidos: async function () {
      this.listadoPedidos = [];
      this.$refs["spinnerListadoPedidos"].removeAttribute("hidden");

      try {
        const response = await axios.post("api/getPedidos", {
          filtroBusqueda: this.filtroBusqueda,
          busqueda: this.busqueda,
          page: this.paginaActualListadoPedidos,
        });
        if (response.data.data.length != 0) {
          this.listadoPedidos = response.data;
        }
      } catch (error) {
        throw Error("Ha ocurrido un error al buscar los pedidos");
      }
      this.$refs["spinnerListadoPedidos"].setAttribute("hidden", "hidden");
    },
    descargarPdf: async function (
      idPedido,
      fechaArribo,
      fechaPedido,
      total,
      idProveedor
    ) {
      try {
        const response = await axios.post("api/getDetallePedido", {
          id: idPedido,
        });
        var detallePedido = response.data;
      } catch (error) {
        throw Error("Error");
      }

      console.log(detallePedido);

      var url = "";

      if (fechaArribo == null) {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(detallePedido) +
          "/" +
          idProveedor +
          "/" +
          fechaPedido +
          "/0-0-0/" +
          total;
      } else {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(detallePedido) +
          "/" +
          idProveedor +
          "/" +
          fechaPedido +
          "/" +
          fechaArribo +
          "/" +
          total;
      }

      await axios
        .get(url, {
          responseType: "blob",
        })
        .then((response) => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "pedido.pdf");
          document.body.appendChild(link);
          link.click();
        });
    },
    getDetalle: async function (id, index) {
      try {
        const response = await axios.post("api/getDetallePedido", {
          id: id,
          userid: window.user.user.id,
        });
        this.datosPedidoEspecifico = this.listadoPedidos.data[index];
        this.detallePedido = response.data;
        this.modificaPedido = response.data;
      } catch (error) {
        throw Error("Ha ocurrido un error al cargar el detalle del pedido");
      }
    },
    deletePedido: async function (id) {
      try {
        alertify.confirm(
          "Eliminar usuario",
          "¿Esta segur@ que desea elminar el Pedido?",
          () => {
            axios
              .post("api/deletePedido", {
                id: id,
                userid: window.user.user.id,
              })
              .then((response) => {
                alertify.success("Pedido eliminado correctamente");
              })
              .catch((error) => {
                alertify.error("Ha ocurrido un error al eliminar al usuario");
              });
          },
          function () {}
        );
      } catch (error) {
        throw Error(error);
        alertify.error("Ha ocurrido un error al eliminar el pedido");
      }
    },
    getPagos: async function (id, index) {
      try {
        const response = await axios.post("api/getPagos", {
          id: id,
        });
        this.listadoPagos = response;
      } catch (error) {
        throw Error(error);
      }
      this.datosPedidoEspecifico = this.listadoPedidos.data[index];
      this.saldoPagos = this.datosPedidoEspecifico.saldo;
      this.pagos = true;
    },
    savePago: async function () {
      var validation = true;
      if (validation) {
        if (this.datosPago.monto == "") {
          this.$refs["monto"].classList.add("border-danger");
          validation = false;
        } else {
          this.$refs["monto"].classList.remove("border-danger");
        }

        if (this.datosPago.fechaPago == "") {
          this.$refs["fecha_pago"].classList.add("border-danger");
          validation = false;
        } else {
          this.$refs["fecha_pago"].classList.remove("border-danger");
        }

        if (this.datosPago.medioPago == "") {
          this.$refs["medio_pago"].classList.add("border-danger");
          validation = false;
        } else {
          this.$refs["medio_pago"].classList.remove("border-danger");
        }
      }

      if (validation) {
        try {
          const response = await axios.post("api/createPago", {
            datosPago: this.datosPago,
            idPedido: this.datosPedidoEspecifico.id,
            userid: window.user.user.id,
          });
          if (response.data != 0) {
            alertify.error("Error al cargar el pago");
          } else {
            alertify.success("Pago cargado correctamente");
            this.datosPago = {
              monto: "",
              fechaPago: "",
              medioPago: "",
              obs: "",
            };
            this.$refs["modalRegistrarPago"].hide();
          }
        } catch (error) {
          throw Error(error);
          alertify.error("Ha ocurrido un error al intentar crear el pago");
        }
      } else {
        alertify.error("Por favor complete los campos resaltados en rojo");
      }
    },
    deletePago: async function (id) {
      try {
        const response = await axios.post("api/deletePago", {
          id: id,
          userid: window.user.user.id,
        });
        alertify.success("Pago eliminado correctamente");
      } catch (error) {
        throw Error(error);
        alertify.error("Ha ocurrido un error al intentar eliminar el pago");
      }
    },
  },
  beforeMount() {
      window.Echo.channel("channel-PedidoTable").listen("PedidoTable", (e) => {
      this.filtroBusqueda = "";
      this.busqueda = "";
      this.paginaActualListadoPedidos = 1;
      this.listadoPedidos = e.pedidos;
    });
    window.Echo.channel("channel-PagosTable").listen("PagosTable", (e) => {
      this.listadoPagos.data = e.pagos;
      this.saldoPagos = e.saldo.saldo;
      console.log(e.pagos);
      console.log(e.saldo.saldo);
    });
  },
};
</script>
<style scoped>
.input-stock {
  width: 20%;
  border-color: rgb(0, 170, 28);
  margin: auto;
}
.input-stock:focus {
  border-color: #ff7300;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(194, 41, 3, 0.25); 
}

</style>