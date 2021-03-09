<template>
  <div class="ml-1 mr-1">
    <div class="card mt-1">
      <!--
            <button @click="movimientosToCliente">Movimientos a Cliente</button>
      -->

      <div
        class="card-header text-primary bg-transparent font-weight-bold text-left"
      >Autorizar ventas por cuenta corriente</div>

      <h5 v-if="listadoVentas.length == 0">En este momento no hay ventas que requieran autorización</h5>

      <div class="card-body" v-if="listadoVentas.length > 0">
        <!--Listado de ventas no autorizadas-->
        <div v-if="detalleVenta == ''">
          <b-table
            id="listado-ventas"
            style="font-size:12px;"
            outlined
            small
            hover
            no-border-collapse
            head-variant="dark"
            :items="listadoVentas"
            :fields="fieldsListadoVentas"
          >
            <template v-slot:cell(cliente)="row">
              <p class="font-weight-bold">{{row.item.cliente}}</p>
            </template>

            <template v-slot:cell(cuit)="row">
              <p class="font-weight-bold">{{row.item.cuit}}</p>
            </template>

            <template v-slot:cell(domicilio)="row">
              <p class="font-weight-bold">{{row.item.domicilio}}</p>
            </template>

            <template v-slot:cell(fecha)="row">
              <p class="font-weight-bold">{{row.item.fecha}}</p>
            </template>

            <template v-slot:cell(descuento)="row">
              <p class="font-weight-bold text-primary">{{row.item.descuento}}%</p>
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

            <template v-slot:cell(total)="row">
              <p class="font-weight-bold text-primary">${{row.item.total}}</p>
            </template>

            <template v-slot:cell(monto_maximo_cc)="row">
              <p class="font-weight-bold text-primary">${{row.item.monto_maximo_cc}}</p>
            </template>

            <template v-slot:cell(total_cc)="row">
              <p
                v-if="row.item.total_cc > 0"
                class="font-weight-bold text-danger"
              >${{row.item.total_cc}}</p>
              <p
                v-if="row.item.total_cc == 0"
                class="font-weight-bold text-warning"
              >${{row.item.total_cc}}</p>
              <p
                v-if="row.item.total_cc < 0"
                class="font-weight-bold text-success"
              >${{(row.item.total_cc).toString().slice(1)}}</p>
            </template>

            <template v-slot:cell(acciones)="row">
              <button
                @click="getDetalleVentaNoAutorizada(row.item.id,row.index)"
                title="Detalle de la venta"
                class="btn btn-sm btn-primary"
              >
                <b-icon-document-text></b-icon-document-text>
              </button>
              <button
                @click="deleteVenta(row.item.id)"
                title="Eliminar venta"
                class="btn btn-sm btn-danger"
              >
                <b-icon-trash></b-icon-trash>
              </button>
              <button
                @click="autorizarVenta(row.item.id)"
                title="Autorizar venta"
                class="btn btn-sm btn-success"
              >
                <b-icon-check></b-icon-check>
              </button>
            </template>
          </b-table>
        </div>

        <!-- Detalle de la venta -->
        <div v-if="detalleVenta != ''">
          <div class="card">
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >Datos básicos de la venta</div>
            <div class="card-body">
              <!--Datos de la venta en el detalle-->
              <b-table
                id="listado-ventas"
                class="mt-1"
                style="font-size:12px;"
                outlined
                small
                hover
                no-border-collapse
                responsive
                head-variant="dark"
                :items="datosVentaEspecifica"
                :fields="fieldsVentaEspecifica"
              >
                <template v-slot:cell(cliente)="row">
                  <p class="font-weight-bold">{{row.item.cliente}}</p>
                </template>

                <template v-slot:cell(cuit)="row">
                  <p class="font-weight-bold">{{row.item.cuit}}</p>
                </template>

                <template v-slot:cell(domicilio)="row">
                  <p class="font-weight-bold">{{row.item.domicilio}}</p>
                </template>

                <template v-slot:cell(fecha)="row">
                  <p class="font-weight-bold">{{row.item.fecha}}</p>
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

                <template v-slot:cell(total)="row">
                  <p class="font-weight-bold text-primary">${{row.item.total}}</p>
                </template>

                <template v-slot:cell(descuento)="row">
                  <p class="font-weight-bold text-primary">{{row.item.descuento}}%</p>
                </template>

                <template v-slot:cell(monto_maximo_cc)="row">
                  <p class="font-weight-bold text-primary">${{row.item.monto_maximo_cc}}</p>
                </template>

                <template v-slot:cell(total_cc)="row">
                  <p
                    v-if="row.item.total_cc > 0"
                    class="font-weight-bold text-danger"
                  >${{row.item.total_cc}}</p>
                  <p
                    v-if="row.item.total_cc == 0"
                    class="font-weight-bold text-warning"
                  >${{row.item.total_cc}}</p>
                  <p
                    v-if="row.item.total_cc < 0"
                    class="font-weight-bold text-success"
                  >${{(row.item.total_cc).toString().slice(1)}}</p>
                </template>
              </b-table>
            </div>
          </div>

          <div class="card mt-1 mb-1">
            <!--Header de al tabla del detalle-->
            <div
              class="card-header text-primary bg-transparent font-weight-bold text-left"
            >Detalle de la venta</div>

            <div class="card-body">
              <!--Detalle de la venta-->
              <b-table
                style="font-size:12px;"
                outlined
                small
                hover
                no-border-collapse
                responsive
                head-variant="dark"
                :items="detalleVenta.data"
                :fields="fieldsDetalleVenta"
              >
                <template v-slot:cell(precio_venta)="row">
                  <p class="font-weight-bold text-primary">${{row.item.precio_venta}}</p>
                </template>

                <template v-slot:cell(subtotal)="row">
                  <p class="font-weight-bold text-primary">${{row.item.subtotal}}</p>
                </template>
              </b-table>
            </div>
          </div>

          <!--Botones de acciones-->
          <button
            title="Volver al listado de ventas"
            class="btn btn-sm btn-primary"
            @click="detalleVenta = []"
          >Volver</button>
          <button
            @click="autorizarVenta(datosVentaEspecifica[0]['id'])"
            title="Proceder a la facturación"
            class="btn btn-sm btn-success"
          >Autorizar</button>
          <button
            @click="deleteVenta(datosVentaEspecifica[0]['id'])"
            title="Rechazar venta"
            class="btn btn-sm btn-danger"
          >Rechazar venta</button>
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
  disableStats: true
});

export default {
  data() {
    return {
      listadoVentas: [], //ARRAY con el listado de las ventas
      fieldsListadoVentas: [
        { key: "cliente", label: "Cliente" },
        { key: "cuit", label: "CUIT/CUIL/DNI" },
        { key: "domicilio", label: "Domicilio" },
        { key: "fecha", label: "Fecha de la venta" },
        { key: "tipo_venta", label: "Tipo de de facturación" },
        { key: "monto_maximo_cc", label: "Máximo en Cuenta Corriente" },
        { key: "total_cc", label: "Saldo en Cuenta Corriente" },
        { key: "total", label: "Total de la venta" },
        { key: "descuento", label: "Descuento" },
        "acciones"
      ], //COLUMNAS de la tabla de las ventas
      datosVentaEspecifica: [], //ARRAY con los datos de una venta específica
      fieldsVentaEspecifica: [
        { key: "cliente", label: "Cliente" },
        { key: "cuit", label: "CUIT/CUIL/DNI" },
        { key: "domicilio", label: "Domicilio" },
        { key: "fecha", label: "Fecha de la venta" },
        { key: "tipo_venta", label: "Tipo de de facturación" },
        { key: "monto_maximo_cc", label: "Monto máximo en Cuenta Corriente" },
        { key: "total_cc", label: "Saldo en Cuenta Corriente" },
        { key: "total", label: "Total de la venta" },
        { key: "descuento", label: "Descuento" }
      ], //COLUMNAS de la tabla de las ventas
      detalleVenta: [], //ARRAY con el detalle de la venta
      fieldsDetalleVenta: [
        { key: "Descripcion", label: "Producto" },
        { key: "precio_venta", label: "Precio de venta" },
        { key: "cantidad", label: "Cantidad" },
        { key: "subtotal", label: "Subtotal" }
      ] //COLUMNAS de la tabla del detalle de la venta
    };
  },
  methods: {
    movimientosToCliente() {
      axios.post("api/movimientosToCliente");
    },
    getVentasNoAutorizadas() {
      axios.post("api/getVentasNoAutorizadas").then(response => {
        this.listadoVentas = response.data;
      });
    },
    getDetalleVentaNoAutorizada(id, index) {
      if (!this.$auth.can("Rechazar Ventas")) {
        alertify.error("Usted no está autorizado a ver el detalle de la venta");
      } else {
        this.detalleVenta = [];
        this.datosVentaEspecifica = [];

        axios
          .post("api/getDetalleVentaNoAutorizada", {
            id: id
          })
          .then(response => {
            this.detalleVenta = response;
            this.datosVentaEspecifica.push(this.listadoVentas[index]);
          });
      }
    },
    deleteVenta(id) {
      if (!this.$auth.can("Rechazar Ventas")) {
        alertify.error("Usted no está autorizado a rechazar ventas");
      } else {
        alertify
          .confirm(
            "Rechazar venta",
            "¿Esta seguro de rechazar la venta?",
            () => {
              axios
                .post("api/deleteVentas", {
                  id: id,
                  userid: window.user.user.id
                })
                .then(response => {
                  if (response.data == 1) {
                    this.detalleVenta = [];
                    this.datosVentaEspecifica = [];
                    alertify.success("Venta rechazada correctamente");
                  } else {
                    alertify.error(
                      "Ha ocurrido un error al intentar eliminar la venta"
                    );
                  }
                })
                .catch(error => {
                  alertify.error(
                    "Ha ocurrido un error al intentar eliminar la venta"
                  );
                });
            },
            () => {
              alertify.error("Se ha cancelado la operación");
            }
          )
          .set("labels", { ok: "Rechazar venta", cancel: "Cancelar" });
      }
    },
    autorizarVenta(id) {
      if (!this.$auth.can("Autorizar Ventas CC")) {
        alertify.error(
          "Usted no está autorizado a realizar la autorización de ventas por cuenta corriente"
        );
      } else {
        axios
          .post("api/autorizarVenta", {
            id: id,
            userid: window.user.user.id
          })
          .then(response => {
            if (response.data == 1) {
              alertify.success("Venta autorizada correctamente");
            } else {
              alertify.error(
                "Ha ocurrido un error al intentar rechazar la venta"
              );
            }
          })
          .catch(error => {
            alertify.error(
              "Ha ocurrido un error al intentar rechazar la venta"
            );
          });
      }
    },
    editCuits() {
      axios.post("api/editCuits").then(response => {
        console.log(response);
      });
    }
  },
  beforeMount() {
    this.getVentasNoAutorizadas();

    window.Echo.channel("channel-VentaAutorizada").listen(
      "VentaAutorizada",
      e => {
        this.listadoVentas = e.ventasNoAutorizadas;
      }
    );

    window.Echo.channel("channel-VentaEliminada").listen(
      "VentaEliminada",
      e => {
        this.listadoVentas = e.ventasNoAutorizadas;
      }
    );
  }
};
</script>
