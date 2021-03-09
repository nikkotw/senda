<template>
  <div class="container">
    <div class="row justify-content-center">
      <h2>Recepciones</h2>
      <div class="mt-2 col-md-12">
        <b-table
          id="Recepciones"
          :fields="fieldItemsRecepcion"
          striped
          hover
          :items="recepciones"
          head-variant="dark"
        >
          <template v-slot:cell(fecha)="row" colspan="1">
            <span>{{new Date(row.item.fecha).toLocaleDateString("en-GB",{timeZone: 'UTC'})}}</span>
          </template>
          <template v-slot:cell(estado)="row" colspan="1">
            <span class="badge badge-warning" v-if="row.item.estado=='P'">Pendiente</span>
            <span class="badge badge-success" v-if="row.item.estado=='C'">Completado</span>
          </template>
          <template v-slot:cell(acciones)="row" colspan="1">
            <button
              v-b-modal.detalleT
              @click="detalle(row.item.id,row.item.estado)"
              class="btn btn-primary"
            >Ver Detalle</button>
          </template>
        </b-table>
        <b-modal id="detalleT" ref="modalDetalles" title="Detalle Transferencia" size="xl">
          <b-table
            id="detalleRecepcion"
            :fields="fieldItemsDetalle"
            striped
            hover
            :items="detalles"
            head-variant="dark"
          >
            <template v-slot:cell(Recibido)="row" colspan="1">
              <div v-if="estado!='C'">
                <input type="number" style="width:50px;" v-model="row.data" />
                <button
                  class="btn btn-success btn-sm"
                  @click="confirma(row.item.id, row.data, $event)"
                  :ref="row.item.id"
                >
                  <i class="fa fa-check" aria-hidden="true"></i>
                </button>
              </div>
              <div v-else>
                <span>{{row.item.recibido}}</span>
              </div>
            </template>
          </b-table>
          <template v-slot:modal-footer="{ ok, cancel}">
            <!-- Emulate built in modal footer ok and cancel button actions -->
            <b-button
              size="sm"
              v-if="estado!='C'"
              variant="success"
              @click="AddTransferencias()"
            >Acepta Transferencias</b-button>
            <b-button size="sm" variant="danger" @click="cancel()">Cerrar</b-button>
          </template>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      recepciones: [],
      estado: "",
      diferencia: [],
      detalles: [],
      fieldItemsRecepcion: [
        { key: "sucursal", label: "Sucursal" },
        { key: "fecha", label: "Fecha Despacho" },
        { key: "fechaRecepcion", label: "Fecha Llegada" },
        { key: "estado", label: "Estado" },
        { key: "idTransferencia", label: "Origen - Transferencia Nro " },
        "acciones"
      ],
      fieldItemsDetalle: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "cantidad" },
        "Recibido",
        "acciones"
      ]
    };
  },
  mounted() {
    axios
      .get("api/recepciones")
      .then(res => {
        this.recepciones = res.data;
        console.log(res.data);
      })
      .catch(error => {
        console.log(error);
      });
  },
  methods: {
    detalle(id, estado) {
      this.estado = estado;
      axios
        .post("api/detalleRecepcion", { id: id })
        .then(res => {
          this.detalles = res.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    confirma(id, cantidad, event) {
      this.$refs[id].setAttribute("disabled", true);

      if (cantidad != undefined) {
        this.diferencia.push({ id: id, cantidad: cantidad });
        console.log(this.diferencia);
      } else {
        alertify.error("Error! - La cantidad No es valida");
      }
    },
    AddTransferencias() {
      axios
        .post("api/checkFaltantes", { diferencias: this.diferencia })
        .then(res => {
          if (res.data == 1) {
            alertify.success("Transferencia Cargada Correctamente");
            this.$refs["modalDetalles"].hide();
          }
        })
        .catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
