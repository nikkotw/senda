<template>
  <div class="container">
    <div clas="row">
      <div v-if="loading==true" class="d-flex justify-content-center mt-3">
        <b-button variant="primary" disabled>
          <b-spinner label="Spinning"></b-spinner>
          Enviando A Sucursal...                
        </b-button>
      </div>

      <div class="col-md-12 mt-5" v-if="yesno==false">
        <h5>
          <strong>Ver</strong>
        </h5>
        <select v-on:change="filtrado" v-model="selected" name="selected" id>
          <option value="Todos">Todos</option>
          <option value="Completados">Completados</option>
          <option value="Transito">Transito</option>
          <option value="Pendientes">Pendientes</option>
          <option value="Cancelados">Cancelados</option>
        </select>
      </div>
      <div class="col col-12 mt-5">
        

        <b-table
          class="mt-2"
          id="transferencias"
          :items="datosFiltrados"
          :fields="fieldsTransferencias"
          outlined
          responsive
          hover
          no-border-collapse
          head-variant="dark"
        >
          <template v-slot:cell(estado)="row">
            <p v-if="row.item.estado=='P'" class="badge badge-warning">Preparando</p>

            <p v-if="row.item.estado=='T'" class="badge badge-dark">Transito</p>

            <p v-if="row.item.estado=='C'" class="badge badge-success">Completado</p>

            <p v-if="row.item.estado=='CC'" class="badge badge-danger">Cancelada</p>
          </template>
          <template v-slot:cell(acciones)="row" colspan="1">
            <button
              v-if="row.item.estado=='P' && loading!=true"
              v-on:click="enviarSucursal(row.item.id)"
              class="btn btn-success"
            >
              <i class="fa fa-check" aria-hidden="true"></i>
            </button>
            <button
              v-b-modal.detalle
              v-on:click="masinfo(row.item.id,row.item.sucursal)"
              class="btn btn-info"
            >Detalle pediddo</button>
            <button v-b-modal.remito class="btn btn-default">
              <i class="fa fa-file-pdf-o" style="color:red" aria-hidden="true"></i>
            </button>
            <button v-if="row.item.estado=='P'" v-on:click="elimina()" class="btn btn-danger">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
          </template>
        </b-table>

        <h2 v-if="transferencias.length==0">NO HAY TRANSFERENCIAS</h2>
      </div>
    </div>

    <b-modal id="detalle" :title="destino">
      <b-table
        class="mt-2"
        id="transferencias"
        :items="detalle"
        outlined
        responsive
        hover
        no-border-collapse
        head-variant="dark"
      ></b-table>
    </b-modal>

    <b-modal id="remito" title="Configuracion Remito">
      <label for="nro Remito">Remito Nro:</label>
      <input type="number" />
    </b-modal>
  </div>
</template>

<script>
export default {
  data() {
    return {
      datosFiltrados: [],
      transferencias: [],
      fieldsTransferencias: [
        { key: "sucursal", label: "Destino" },
        { key: "estado", label: "Estado" },
        { key: "fecha_realiza", label: "Enviado" },
        { key: "fecha_recibe", label: "Recepcion" },
        "acciones"
      ],
      empresas: [],
      detalle: [],
      info: false,
      error: "",
      yesno: false,
      remitos: false,
      nrorem: 0,
      bultos: 0,
      suceso: false,
      success: "",
      loading: false,
      destino: "",
      selected: "",
      loading:false,
    };
  },
  mounted() {
    axios
      .get("/api/listadoTransferencias")
      .then(res => {
        if (res.status == 200) {
          console.log("esto es res" + res);
          this.transferencias = res.data;
          console.log("Transferencias" + this.transferencias);
        }
      })
      .catch(err => {
        console.log(err);
      });
    axios
      .get("/api/sucursales")
      .then(res => {
        if (res.status == 200) {
          console.log("esto es res" + res);
          this.empresas = res.data;
          console.log("Empresas" + this.empresas);
        }
      })
      .catch(err => {
        console.log(err);
      });
  },
  methods: {
    masinfo: function(id, destino) {
      this.destino = destino;
      this.info = true;
      console.log(id);
      axios
        .post("/api/detalleTransfer", {
          id: id
        })
        .then(res => {
          if (res.status == 200) {
            this.detalle = res.data;
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    cerrarInfo: function() {
      this.info = false;
    },
    enviarSucursal: function(transferencia) {
      this.loading=true;
      axios
        .post("confirmaPedido", {
          id: transferencia,
          userid:window.user.user.id,
        })
        .then(res => {
          if (res.data == 1) {
            this.suceso = true;
            this.loading = false;
             alertify.set('notifier','position', 'top-center');
            alertify.success( "Transferencia Enviada Correctamente")
            this.success = "Transferencia Enviada Correctamente";
            this.rellena();
          } else {
            this.loading = false;
            this.yesno = true;
             alertify.set('notifier','position', 'top-center');
            alertify.error('Error Al procesar la transferencia - Intente mas tarde')
           
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    inforem: function() {
      this.remitos = true;
    },
    remito: function() {
      axios
        .post("api/generaRemito", {
          nro: this.nrorem,
          bultos: this.bultos
        })
        .then(res => {
          axios
            .get(res.data, {
              responseType: "blob"
            })
            .then(response => {
              const url = window.URL.createObjectURL(new Blob([response.data]));
              const link = document.createElement("a");
              link.href = url;
              link.setAttribute("download", "remito.pdf");
              document.body.appendChild(link);
              link.click();
            });

          console.log("Transferencia Exitosa");
          this.remitos = false;
          this.nrorem = 0;
          this.bultos = 0;
        });
    },
    rellena: function() {
      this.transferencias = [];
      axios
        .get("/api/listadoTransferencias")
        .then(res => {
          if (res.status == 200) {
            this.transferencias = res.data;
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    filtrado: function() {
      if (this.selected == "Todos") {
        this.datosFiltrados = this.transferencias;
      }
      if (this.selected == "Cancelados") {
        this.datosFiltrados = this.transferencias.filter(function(item) {
          return item.estado == "CC";
        });
      }
       if (this.selected == "Pendientes") {
        this.datosFiltrados = this.transferencias.filter(function(item) {
          return item.estado == "P";
        });
      }
       if (this.selected == "Transito") {
        this.datosFiltrados = this.transferencias.filter(function(item) {
          return item.estado == "T";
        });
      }
          if (this.selected == "Completados") {
        this.datosFiltrados = this.transferencias.filter(function(item) {
          return item.estado == "C";
        });
      }
      return this.datosFiltrados;
    }
  }
};
</script>
