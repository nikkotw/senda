<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col col-sm-1">
        <b-modal
          id="modal-1"
          title="Listado Ventas"
          cancel-title="Cancelar"
          ok-title="Obtener listado"
          @ok="obteterListado()"
        >
          <label for="fechaDesde">Desde</label>
          <b-form-datepicker id="fechaDesde" v-model="desde" class="mb-2"></b-form-datepicker>
          <label for="fechaHasta">Hasta</label>
          <b-form-datepicker id="fechaHasta" v-model="hasta" class="mb-2"></b-form-datepicker>
        </b-modal>
      </div>
      <div class="col">
        <div style="margin:auto;width:30%">
          <h2>Reportes</h2>
          <b-form-select class="mt-t" v-model="selected" @change="getDatos">
            <b-form-select-option value="caja">Caja</b-form-select-option>
            <b-form-select-option value="usuario">Usuario</b-form-select-option>
            <b-form-select-option value="venta">Venta</b-form-select-option>
          </b-form-select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="select-users">
          <b-form-select v-if="check" class="mt-3" v-model="user" @change="getDatos">
            <b-form-select-option
              v-for="user in usuarios"
              :value="user.id"
              :key="user.id"
            >{{user.nombre}}</b-form-select-option>
          </b-form-select>
        </div>

        <b-form-datepicker
          v-if="selected=='venta'|| selected=='caja'"
          id="fecha"
          v-model="fecha"
          class="mb-2 float-left"
          @context="getDatos()"
          button-only
          v-on:click="getDatos()"
        ></b-form-datepicker>
        <button
          v-if="selected=='venta'"
          class="mb-2 ml-2 float-left btn btn-primary btn-small"
          v-b-modal.modal-busqueda
        >
          <i class="fas fa-search"></i>
        </button>
         <button v-if="selected=='venta'" class="btn btn-light btn-small mb-2 ml-2 float-left" v-b-modal.modal-1>
          <i class="far fa-file-pdf" style="color:#ff0000"></i> Listado
        </button>
        <b-modal
          id="modal-busqueda"
          title="Buscar Por Cliente"
          cancel-title="Cancelar"
          ok-title="Buscar Ventas"
          @ok="buscarXCliente()"
        >
          <label for="cliente">Cliente</label>
          <b-form-input type="text" id="cliente" v-model="cliente" class="mb-2"></b-form-input>
          <label for="fechaDesde">Desde</label>
          <b-form-datepicker id="fechaDesde" v-model="desde" class="mb-2"></b-form-datepicker>
          <label for="fechaHasta">Hasta</label>
          <b-form-datepicker id="fechaHasta" v-model="hasta" class="mb-2"></b-form-datepicker>
        </b-modal>
        <div v-if="ventasEmpleados.length>0" class="usuarios">
          <div class="card-body" v-for="item in ventasEmpleados" :key="item.id">
            <div class="card border-left-primary shadow h-100 py-2 mt-2">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div
                    class="text-xs font-weight-bold text-primary text-uppercase mb-1"
                  >{{item.nombre}}</div>
                  <div
                    class="h5 mb-0 font-weight-bold"
                    style="color:Green"
                  >${{item.maximo.toFixed(2)}}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="items.total>0">
      <div class="row">
        <div class="col">
          <b-table
            id="listado-reporte"
            v-if="items.total>0"
            :items="items.data"
            :fields="fieldsUsuarios"
            class="mt-3"
            hover
            no-border-collapse
            head-variant="dark"
            selectable
            select-mode="single"
            @row-selected="testeo()"
          >
            <template v-slot:cell(detalle)="row">
              {{
              row.item.detalle
              }}
            </template>
          </b-table>
          <div
            class="col text-center"
            v-if="items.total != undefined && items.total > 5"
            @click="reporteUsuarios()"
          >
            <b-pagination
              v-if="check && items.total>0"
              aria-controls="listado-reporte"
              size="sm"
              style="font-size:12px;"
              v-model="paginaActualUsuarios"
              :total-rows="items.total"
              :per-page="5"
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
    <div class="row" v-else>
      <b-table
        class="ventas-caja"
        id="ventas-caja"
        v-if="items.length>0"
        :items="items"
        outlined
        responsive
        hover
        no-border-collapse
        head-variant="dark"
      >
        <template v-slot:cell(fecha)="row" colspan="1">
          <span>{{new Date(row.item.fecha).toLocaleDateString("en-GB",{timeZone: 'UTC'})}}</span>
        </template>
      </b-table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      fecha: "",
      fieldsUsuarios: [
        { key: "detalle", label: "Detalle" },
        { key: "fecha", label: "Fecha" },
        { key: "hora", label: "Hora" },
        { key: "nombre", label: "Usuario" }
      ],
      selected: "",
      items: [],
      desde: "",
      hasta: "",
      cliente: "",
      usuarios: [],
      user: 0,
      totalpagina: 0,
      ventasEmpleados: [],
      check: false,
      paginaActualUsuarios: 1
    };
  },
  mounted() {
    console.log("Component mounted.");
  },
  methods: {
    getDatos() {
      this.items = [];
      this.ventasEmpleados = [];
      console.log(this.user);

      if (this.selected == "usuario") {
        if (!this.check) {
          this.getUsuarios();
          this.check = true;
        } else {
          axios
            .post("api/reporteUsuario", {
              user: this.user,
              reporte: this.selected,
              page: this.paginaActualUsuarios
            })
            .then(res => {
              this.items = res.data;
              console.log(res.data);
              this.totalpagina = res.data.total;
            })
            .catch(err => {
              console.log(err);
            });
        }
      } else {
        this.check = false;
        axios
          .post("api/reporte", { reporte: this.selected, fecha: this.fecha })
          .then(res => {
            if (this.selected == "venta") {
              this.items = res.data.ventas;
              this.ventasEmpleados = res.data.ventasEmpleados;
            } else {
              this.items = res.data;
            }
          })
          .catch(err => {
            console.log(err);
          });
      }
    },
    reporteUsuarios() {
      axios
        .post("api/reporteUsuario", {
          user: this.user,
          reporte: this.selected,
          page: this.paginaActualUsuarios
        })
        .then(res => {
          this.items = res.data;
          console.log(res.data);
          this.totalpagina = res.data.total;
        })
        .catch(err => {
          console.log(err);
        });
    },
    getUsuarios() {
      axios
        .post("api/obtenerUsuario", { page: this.paginaActualUsuarios })
        .then(res => {
          this.usuarios = res.data;
        })
        .catch(err => {
          console.log(err);
        });
    },
    testeo(index) {
      console.log(index);
      let cadena = this.reporteUsuarios[index];
    },
    buscarXCliente() {
      axios
        .post("api/getVentaCliente", {
          cliente: this.cliente.toLowerCase(),
          hasta: this.hasta,
          desde: this.desde
        })
        .then(res => {
          if (res.data != 20) {
            this.items = res.data.ventas;
          } else {
            alertify.error("No hay Consultas disponibles");
          }
        })
        .catch(err => {
          console.log(err);
        });

      this.cliente = "";
      this.hasta = "";
      this.desde = "";
    },

    obteterListado() {
      axios
        .post("api/getRerpoteVentas", { desde: this.desde, hasta: this.hasta })
        .then(res => {
          if (res != 0) {
            axios({
              url: res.data,
              method: "GET",
              responseType: "blob"
            }).then(response => {
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
              let nombre = "ReporteVentas.pdf";
              fileLink.setAttribute("download", nombre);
              document.body.appendChild(fileLink);
              fileLink.click();
            });
          }
        });
    }
  }
};
</script>
<style scoped>
.ventas-caja {
  width: 80%;
  margin: auto;
}
.card-body {
  display: inline-block;

  width: 13%;
}
.select-users {
  margin: auto;
  width: 25%;
  margin-left: 42%;
}

table {
  width: 50%;
  margin: auto;
}

@media screen and (max-width: 600px) {
  .card-body {
    width: 10%;
    margin: auto;
  }

  table {
    margin-right: 30px;
    width: 30%;
  }
  thead {
    display: none;
  }
  tr:nth-of-type(2n) {
    background-color: inherit;
  }
  tr td:first-child {
    background: #f0f0f0;
    font-weight: bold;
    font-size: 1.3em;
  }
  tbody td {
    display: block;
    text-align: center;
  }
  tbody td:before {
    content: attr(data-th);
    display: block;
    text-align: center;
  }
}
</style>