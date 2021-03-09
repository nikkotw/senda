<template>
  <div class="container">
    <div class="row justify-content-center">
      <h2>Accion</h2>
      <b-form-select class="mt-t" v-model="selected" @change="getVentaAnular">
        <b-form-select-option value="Elimina">Elimina Venta</b-form-select-option>
        <b-form-select-option value="Anula">Anula Venta Afip</b-form-select-option>
      </b-form-select>
     
        <b-form-datepicker
          id="fecha"
          v-model="fecha"
          class="mb-2 mt-2"
          @context="filtraFecha()"
          button-only
          v-on:click="filtraFecha()"
        ></b-form-datepicker>
      <b-table
        class="mt-3"
        id="listado-reporte"
        v-if="filtrado.length>0 && !calendar"
        :items="filtrado"
        :fields="fieldsAnula"
        outlined
        responsive
        hover
        no-border-collapse
        head-variant="dark"
      >
        <template v-slot:cell(fecha)="row" colspan="1">
          <span>{{new Date(row.item.fecha).toLocaleDateString("en-GB",{timeZone: 'UTC'})}}</span>
        </template>

        <template v-slot:cell(acciones)="row" colspan="1">
          <div class="text-center">
            <b-button
              size="sm"
              variant="danger"
              style=" box-shadow: none !important;"
              v-if="selected=='Elimina'"
              @click="elimina(row.item.id,row.index)"
            >
              <i class="fa fa-trash" aria-hidden="true"></i>
            </b-button>
          </div>
          <div class="text-center">
            <b-button
              size="sm"
              variant="danger"
              style=" box-shadow: none !important;"
              v-if="selected=='Anula' && row.item.condicion_venta!='ANULADA'"
              @click="anula(row.item.id)"
            >
              <i class="fas fa-ban"></i>
            </b-button>
            <b-button
              size="sm"
              variant="primary"
              style=" box-shadow: none !important;"
              v-if="selected=='Anula' && row.item.condicion_venta!='ANULADA'"
              @click="reImprime(row.item.id)"
            >
              <i class="fas fa-receipt"></i>
            </b-button>
          </div>
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: "",
      items: [],
      ventas: [],
      filtrado: [],
      fecha: "",
      calendar: false,
      totalpagina: 0,
      check: false,
      fieldsAnula: [
        { key: "id", label: "Venta" },
        { key: "factura", label: "Factura" },
        { key: "cliente", label: "cliente" },
        { key: "total", label: "Total" },
        { key: "tipo_venta", label: "Tipo Venta" },
        { key: "condicion_venta", label: "Condicion" },
        { key: "descuento", label: "Descuento" },
        { key: "fecha", label: "Fecha" },
        { key: "nombre", label: "Usuario" },
        "acciones"
      ],
      paginaActualUsuarios: 1
    };
  },
  mounted() {
    console.log("Component mounted.");
  },
  methods: {
    getVentaAnular() {
      axios.post("api/getAnular", {}).then(res => {
        this.ventas = res.data;
        this.calendar = true;
      });
      this.fecha = "";
    },
    filtraFecha() {
      this.filtrado = this.ventas.filter(item => {
        return item.fecha == this.fecha;
      });
      if (this.filtrado.length > 0) {
        this.calendar = false;
        console.log(this.filtrado);
      }
    },
    anula(venta) {
      console.log(venta);
      axios
        .post("api/setAnula", { venta: venta, userid: window.user.user.id })
        .then(res => {
          if (res.data != 0) {
            alertify.error("Surgio Un error al Anular La venta");
          } else {
            alertify.success("Venta Anulada Correctamente");
          }
        });
    },
    reImprime(venta) {
      axios
        .post("api/reFactura", { venta: venta, userid: window.user.user.id })
        .then(res => {
          if (res.data != 0) {
            alertify.error("Surgio Un error al Anular La venta");
          } else {
            alertify.success("Comprobante Emitido Correctamente");
          }
        });
    },
    async elimina(venta, index) {
      if(this.filtrado[index].factura!=null && this.filtrado[index].condicion_venta!='ANULADA'){
        alertify.error("Debera Anular Primero la Factura");

      }else{

      
      alertify
        .confirm(
          "¿Esta seguro de Eliminar La venta?",
          () => {
            axios
              .post("api/eliminaVenta", {
                venta: venta,
                userid: window.user.user.id
              })
              .then(response => {
                if (response.data == 0) {
                  this.filtrado.splice(index,1);
                  alertify.success("Venta Eliminada correctamente");
                } else {
                  alertify.error(
                    "Ha ocurrido un error al intentar eliminar la venta"
                  );
                }
              });
          },
          () => {
            alertify.error("Se ha cancelado la operación");
          }
        )
        .set("labels", { ok: "Eliminar Venta", cancel: "Cancelar" });
        }
    },
    afterDelete(index) {
      console.log("llega");
      this.filtrado.splice(index, 1);
    }
  }
};
</script>
