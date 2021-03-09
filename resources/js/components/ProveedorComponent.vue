<template>
  <div class="ml-1 mr-1">
    <!--Crear proveedor-->
    <b-modal
      ref="modalCrearProveedor"
      id="modalCrearProveedor"
      size="xl"
      hide-footer
      title="Crear un nuevo proveedor"
    >
      <div class="row">
        <div class="col">
          <div
            ref="erroresCreate"
            hidden
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
          >
            <h6 class="font-weight-bold">Listado de errores</h6>
            <ul>
              <li v-if="errorsCreate.ciudad">{{errorsCreate.ciudad[0]}}</li>
              <li v-if="errorsCreate.cuit">{{errorsCreate.cuit[0]}}</li>
              <li v-if="errorsCreate.direccion">{{errorsCreate.direccion[0]}}</li>
              <li v-if="errorsCreate.email">{{errorsCreate.email[0]}}</li>
              <li v-if="errorsCreate.nombre">{{errorsCreate.nombre[0]}}</li>
              <li v-if="errorsCreate.telefono">{{errorsCreate.telefono[0]}}</li>
              <li v-if="errorsCreate.tipo_moneda">{{errorsCreate.tipo_moneda[0]}}</li>
            </ul>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="form-group col">
          <label class="font-weight-bold" for="proveedor">Razón social</label>
          <input
            type="text"
            name="proveedor"
            id="proveedor"
            class="form-control form-control-sm"
            v-model="proveedor.nombre"
          />
          <span class="glyphicon glyphicon-ok form-control-feedback"></span>
        </div>
        <div class="form-group col">
          <label class="font-weight-bold" for="cuit">CUIT</label>
          <input
            type="number"
            name="cuit"
            id="cuit"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.cuit"
          />
        </div>
        <div class="w-100"></div>
        <div class="form-group col">
          <label class="font-weight-bold" for="telefono">Teléfono</label>
          <input
            type="number"
            name="telefono"
            id="telefono"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.telefono"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="direccion">Dirección</label>
          <input
            type="text"
            name="direccion"
            id="direccion"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.direccion"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="email">Correo Electrónico</label>
          <input
            type="text"
            name="email"
            id="email"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.email"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="ciudad">Ciudad</label>
          <input
            type="text"
            name="ciudad"
            id="ciudad"
            class="form-control form-control-sm"
            v-model="proveedor.ciudad"
          />
        </div>

        <div class="w-100"></div>

        <div class="form-group col">
          <label class="font-weight-bold" for="cbu">CBU</label>
          <input
            type="text"
            name="cbu"
            id="cbu"
            class="form-control form-control-sm"
            v-model="proveedor.cbu"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="banco">Banco</label>
          <input
            type="text"
            name="banco"
            id="banco"
            class="form-control form-control-sm"
            v-model="proveedor.banco"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="banco">Tipo de moneda</label>
          <select class="form-control form-control-sm" v-model="proveedor.tipo_moneda">
            <option disabled selected value>Seleccione el tipo de moneda</option>
            <option>Peso Argentino</option>
            <option>Dolar</option>
            <option>Euro</option>
          </select>
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="retenciones">Retenciones</label>
          <input
            type="text"
            name="retenciones"
            id="retenciones"
            class="form-control form-control-sm"
            v-model="proveedor.retenciones"
          />
        </div>

        <div class="w-100"></div>

        <div class="form-group col">
          <label class="font-weight-bold" for="tipo_articulos">Tipo de artículos</label>
          <textarea
            class="form-control form-control-sm"
            name="tipo_articulos"
            id="tipo_articulos"
            style="resize: none;"
            v-model="proveedor.tipo_articulos"
          ></textarea>
        </div>

        <div class="w-100"></div>

        <div class="form-group col text-center">
          <button
            type="button"
            class="btn btn-success btn-sm font-weight-bold"
            @click="createProveedor()"
          >Crear proveedor</button>
          <button
            type="button"
            class="btn btn-danger btn-sm font-weight-bold"
            @click="$bvModal.hide('modalCrearProveedor')"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <!--Editar proveedor-->
    <b-modal
      ref="modalEditarProveedor"
      id="modalEditarProveedor"
      size="xl"
      hide-footer
      title="Editar datos del proveedor"
    >
      <div v-if="proveedor.nombre == ''" class="row">
        <div class="col text-center">
          <div class="spinner-border text-dark" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
      </div>
      <div v-if="proveedor.nombre != ''" class="row">
        <div class="col">
          <div
            ref="erroresUpdate"
            hidden
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
          >
            <h6 class="font-weight-bold">Listado de errores</h6>
            <ul>
              <li v-if="errorsUpdate.ciudad">{{errorsUpdate.ciudad[0]}}</li>
              <li v-if="errorsUpdate.cuit">{{errorsUpdate.cuit[0]}}</li>
              <li v-if="errorsUpdate.direccion">{{errorsUpdate.direccion[0]}}</li>
              <li v-if="errorsUpdate.email">{{errorsUpdate.email[0]}}</li>
              <li v-if="errorsUpdate.nombre">{{errorsUpdate.nombre[0]}}</li>
              <li v-if="errorsUpdate.telefono">{{errorsUpdate.telefono[0]}}</li>
              <li v-if="errorsUpdate.tipo_moneda">{{errorsUpdate.tipo_moneda[0]}}</li>
            </ul>

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="form-group col">
          <label class="font-weight-bold" for="proveedor">Razón social</label>
          <input
            type="text"
            name="proveedor"
            id="proveedor"
            class="form-control form-control-sm"
            v-model="proveedor.nombre"
          />
          <span class="glyphicon glyphicon-ok form-control-feedback"></span>
        </div>
        <div class="form-group col">
          <label class="font-weight-bold" for="cuit">CUIT</label>
          <input
            type="number"
            name="cuit"
            id="cuit"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.cuit"
          />
        </div>
        <div class="w-100"></div>
        <div class="form-group col">
          <label class="font-weight-bold" for="telefono">Teléfono</label>
          <input
            type="number"
            name="telefono"
            id="telefono"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.telefono"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="direccion">Dirección</label>
          <input
            type="text"
            name="direccion"
            id="direccion"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.direccion"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="email">Correo Electrónico</label>
          <input
            type="text"
            name="email"
            id="email"
            class="form-control form-control-sm"
            aria-describedby="helpId"
            v-model="proveedor.email"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="ciudad">Ciudad</label>
          <input
            type="text"
            name="ciudad"
            id="ciudad"
            class="form-control form-control-sm"
            v-model="proveedor.ciudad"
          />
        </div>

        <div class="w-100"></div>

        <div class="form-group col">
          <label class="font-weight-bold" for="cbu">CBU</label>
          <input
            type="text"
            name="cbu"
            id="cbu"
            class="form-control form-control-sm"
            v-model="proveedor.cbu"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="banco">Banco</label>
          <input
            type="text"
            name="banco"
            id="banco"
            class="form-control form-control-sm"
            v-model="proveedor.banco"
          />
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="banco">Tipo de moneda</label>
          <select class="form-control form-control-sm" v-model="proveedor.tipo_moneda">
            <option disabled selected value>Seleccione el tipo de moneda</option>
            <option>Peso Argentino</option>
            <option>Dolar</option>
            <option>Euro</option>
          </select>
        </div>

        <div class="form-group col">
          <label class="font-weight-bold" for="retenciones">Retenciones</label>
          <input
            type="text"
            name="retenciones"
            id="retenciones"
            class="form-control form-control-sm"
            v-model="proveedor.retenciones"
          />
        </div>

        <div class="w-100"></div>

        <div class="form-group col">
          <label class="font-weight-bold" for="tipo_articulos">Tipo de artículos</label>
          <textarea
            class="form-control form-control-sm"
            name="tipo_articulos"
            id="tipo_articulos"
            style="resize: none;"
            v-model="proveedor.tipo_articulos"
          ></textarea>
        </div>

        <div class="w-100"></div>

        <div class="form-group col text-center">
          <button
            type="button"
            class="btn btn-success btn-sm font-weight-bold"
            @click="updateProveedor()"
          >Editar proveedor</button>
          <button
            type="button"
            class="btn btn-danger btn-sm font-weight-bold"
            @click="$bvModal.hide('modalEditarProveedor')"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <div class="row card mt-1">
      <div class="card-header text-primary bg-transparent font-weight-bold text-left">
        Listado de proveedores
        <button
          @click="proveedor = {
                nombre: '',
                cuit: '',
                telefono: '',
                direccion: '',
                ciudad: '',
                email: '',
                cbu: '',
                banco: '',
                tipo_moneda: '',
                tipo_articulos: '',
                retenciones: ''
            } "
          v-if="$auth.can('Crear Proveedores')"
          v-b-modal.modalCrearProveedor
          class="btn btn-sm btn-success float-right font-weight-bold"
        >Crear proveedor</button>
      </div>

      <div v-if="!showInfo" class="card-body row">
        <div class="input-group">
          <input
            type="text"
            :disabled="filtroBusquedaProveedor == ''"
            class="form-control form-control-sm"
            v-model="proveedorBuscado"
            placeholder="Buscar proveedores"
          />
          <div class="input-group-append">
            <select
              @change="proveedorBuscado = ''"
              v-model="filtroBusquedaProveedor"
              class="form-control form-control-sm"
            >
              <option selected value>No aplicar filtros</option>
              <option value="cuit">CUIT</option>
              <option value="nombre">Nombre</option>
            </select>
          </div>
          <button
            class="btn btn-sm btn-primary btn-block mt-1"
            v-on:click="buscarProveedor()"
            type="button"
          >Buscar</button>
        </div>

        <b-table
          v-if="listadoProveedores.total > 0"
          id="listado-ventas"
          class="mt-1"
          style="font-size:12px;"
          outlined
          small
          hover
          fixed
          no-border-collapse
          head-variant="dark"
          :items="listadoProveedores.data"
          :fields="fieldsListadoProveedores"
          :tbody-transition-props="{name:'fade'}"
        >
          <template v-slot:cell(acciones)="row">
            <div class="text-center">
              <b-button
                v-if="$auth.can('Ver Info Proveedores')"
                @click="verInfoEspecifica(row.item.id)"
                size="sm"
                variant="info"
                title="Ver datos completos del proveedor"
                style=" box-shadow: none !important;"
              >
                <i class="fas fa-info-circle"></i>
              </b-button>
              <b-button
                v-if="$auth.can('Editar Proveedores')"
                v-b-modal.modalEditarProveedor
                @click="verInfo(row.item.id)"
                size="sm"
                variant="success"
                title="Editar datos del proveedor"
                style=" box-shadow: none !important;"
              >
                <i class="fas fa-pen"></i>
              </b-button>
              <b-button
                v-if="$auth.can('Eliminar Proveedores')"
                @click="deleteProveedor(row.item.id)"
                size="sm"
                variant="danger"
                title="Eliminar proveedor"
                style=" box-shadow: none !important;"
              >
                <i class="fas fa-trash-alt"></i>
              </b-button>
            </div>
          </template>
        </b-table>

        <div
          v-if="listadoProveedores.total != undefined && listadoProveedores.total > 10"
          @click="buscarProveedor"
        >
          <b-pagination
            aria-controls="listado-proveedores"
            size="sm"
            style="font-size:12px;"
            v-model="paginaActualListadoProveedores"
            :total-rows="listadoProveedores.total"
            :per-page="10"
            first-text="Primera"
            prev-text="Anterior"
            next-text="Siguiente"
            last-text="Última"
            align="center"
          ></b-pagination>
        </div>
      </div>

      <div v-if="showInfo" class="card-body">
        <div v-if="!loaded" class="row">
          <div class="col text-center">
            <div class="spinner-border text-dark" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
        <div v-if="loaded" class="row">
          <div class="col col-md-auto">
            <div class="card">
              <div class="card-header text-white font-weight-bold bg-dark">Datos del proveedor</div>
              <ul class="list-group">
                <li class="list-group-item">
                  <span class="font-weight-bold">Razón social:</span>
                  {{proveedor.nombre}}
                </li>
                <li class="list-group-item">
                  <span class="font-weight-bold">CUIT:</span>
                  {{proveedor.cuit}}
                </li>
                <li class="list-group-item">
                  <span class="font-weight-bold">Teléfono:</span>
                  {{proveedor.telefono}}
                </li>
                <li class="list-group-item">
                  <span class="font-weight-bold">Direccion:</span>
                  {{proveedor.direccion}}
                </li>
                <li class="list-group-item">
                  <span class="font-weight-bold">Correo Electrónico:</span>
                  {{proveedor.email}}
                </li>
                <li class="list-group-item">
                  <span class="font-weight-bold">Ciudad:</span>
                  {{proveedor.ciudad}}
                </li>
                <li v-show="proveedor.cbu != null" class="list-group-item">
                  <span class="font-weight-bold">CBU:</span>
                  {{proveedor.cbu}}
                </li>
                <li v-show="proveedor.banco != null" class="list-group-item">
                  <span class="font-weight-bold">Banco:</span>
                  {{proveedor.banco}}
                </li>
                <li v-show="proveedor.tipo_moneda != null" class="list-group-item">
                  <span class="font-weight-bold">Tipo de moneda:</span>
                  {{proveedor.tipo_moneda}}
                </li>
                <li v-show="proveedor.retenciones != null" class="list-group-item">
                  <span class="font-weight-bold">Retenciones:</span>
                  {{proveedor.retenciones}}
                </li>
                <li v-show="proveedor.tipo_articulos != null" class="list-group-item">
                  <span class="font-weight-bold">Tipos de artículos que comercia el proveedor:</span>
                  {{proveedor.tipo_articulos}}
                </li>
              </ul>
            </div>
          </div>
          <div class="col col-lg my-auto">
            <chart v-if="loaded" :chartdata="chartdata" :options="options" />
          </div>
          <div class="w-100"></div>
          <div class="col col-lg">
            <button
              @click="showInfo = false;proveedor =  {};loaded=false"
              class="btn btn-sm btn-primary"
            >Volver</button>
          </div>
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
      loaded: false,
      chartdata: null,
      options: null,

      filtroBusquedaProveedor: "",
      listadoProveedores: [],
      fieldsListadoProveedores: [
        { key: "nombre", label: "Nombre" },
        { key: "cuit", label: "Cuit" },
        { key: "telefono", label: "Teléfono" },
        { key: "email", label: "Correo Electrónico" },
        "acciones"
      ],
      paginaActualListadoProveedores: 1,
      proveedorBuscado: "",
      errorsCreate: [],

      proveedor: {
        nombre: "",
        cuit: "",
        telefono: "",
        direccion: "",
        ciudad: "",
        email: "",
        cbu: "",
        banco: "",
        tipo_moneda: "",
        tipo_articulos: "",
        retenciones: ""
      },

      errorsUpdate: [],
      showInfo: false
    };
  },

  methods: {
    volverInfo: function() {
      this.showInfo = false;
      this.proveedor = {
        nombre: "",
        cuit: "",
        telefono: "",
        direccion: "",
        ciudad: "",
        email: "",
        cbu: "",
        banco: "",
        tipo_moneda: "",
        tipo_articulos: "",
        retenciones: ""
      };
    },
    buscarProveedor: function(page) {
      this.proveedorEspecificoInfo = false;
      axios
        .post("/api/buscarProveedores", {
          page: this.paginaActualListadoProveedores,
          filtro: this.filtroBusquedaProveedor,
          proveedorBuscado: this.proveedorBuscado
        })
        .then(response => {
          if (response.status == 200) {
            this.listadoProveedores = response.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    verInfoEspecifica: async function(id) {
      this.showInfo = true;
      this.proveedor = {
        nombre: "",
        cuit: "",
        telefono: "",
        direccion: "",
        ciudad: "",
        email: "",
        cbu: "",
        banco: "",
        tipo_moneda: "",
        tipo_articulos: "",
        retenciones: ""
      };

      const resInfo = await axios.post("/api/infoProveedorEspecifica", {
        id: id
      });

      this.proveedor = resInfo.data.datosProveedor[0];

      if (resInfo.data.deudasProveedor >= 0) {
        this.chartdata = {
          labels: ["Saldo con el proveedor"],
          datasets: [
            {
              label: "Saldo con el proveedor",
              backgroundColor: "#159933",
              data: [resInfo.data.deudasProveedor]
            }
          ]
        };
      } else {
        this.chartdata = {
          labels: ["Saldo con el proveedor"],
          datasets: [
            {
              label: "Saldo con el proveedor",
              backgroundColor: "#f87979",
              data: [resInfo.data.deudasProveedor.toString().slice(1)]
            }
          ]
        };
      }

      this.options = {
        maintainAspectRatio: false
      };
      this.loaded = true;
    },
    verInfo: function(id) {
      this.proveedor = {
        nombre: "",
        cuit: "",
        telefono: "",
        direccion: "",
        ciudad: "",
        email: "",
        cbu: "",
        banco: "",
        tipo_moneda: "",
        tipo_articulos: "",
        retenciones: ""
      };
      axios
        .post("/api/infoProveedor", {
          id: id
        })
        .then(response => {
          if (response.status == 200) {
            this.proveedor = response.data;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    updateProveedor: function() {
      this.errorsUpdate = [];
      axios
        .post("/api/updateProveedor", {
          nombre: this.proveedor.nombre,
          cuit: this.proveedor.cuit,
          telefono: this.proveedor.telefono,
          direccion: this.proveedor.direccion,
          ciudad: this.proveedor.ciudad,
          email: this.proveedor.email,
          cbu: this.proveedor.cbu,
          banco: this.proveedor.banco,
          tipo_moneda: this.proveedor.tipo_moneda,
          tipo_articulos: this.proveedor.tipo_articulos,
          retenciones: this.proveedor.retenciones,
          userid: window.user.user.id
        })
        .then(response => {
          if (response.data == 1) {
            this.proveedor = {
              nombre: "",
              cuit: "",
              telefono: "",
              direccion: "",
              ciudad: "",
              email: "",
              cbu: "",
              banco: "",
              tipo_moneda: "",
              tipo_articulos: "",
              retenciones: ""
            };
            this.errorsUpdate = [];
            alertify.success("Datos del proveedor actualizados correctamente");
            this.$refs["modalEditarProveedor"].hide();
          } else {
            alertify.error(
              "Ha ocurrido un error al intentar guardar el proveedor en la base de datos"
            );
          }
        })
        .catch(error => {
          this.$refs["erroresUpdate"].removeAttribute("hidden");
          this.errorsUpdate = error.response.data.errors;
        });
    },
    deleteProveedor: function(id) {
      alertify.confirm(
        "Eliminar el proveedor",
        "¿Esta segur@ que desea elminar el proveedor?",
        function() {
          axios
            .delete("api/deleteProveedor", {
              id: id,
              userid: window.user.user.id
            })
            .then(response => {
              alertify.success("Proveedor eliminado correctamente");
            })
            .catch(error => {
              alertify.error("Ha ocurrido un error al eliminar el proveedor");
              console.log(error);
            });
        },
        function() {
          alertify.error("Se ha cancelado");
        }
      );
    },
    createProveedor: function() {
      axios
        .post("/api/createProveedor", {
          nombre: this.proveedor.nombre,
          cuit: this.proveedor.cuit,
          telefono: this.proveedor.telefono,
          direccion: this.proveedor.direccion,
          ciudad: this.proveedor.ciudad,
          email: this.proveedor.email,
          cbu: this.proveedor.cbu,
          banco: this.proveedor.banco,
          tipo_moneda: this.proveedor.tipo_moneda,
          tipo_articulos: this.proveedor.tipo_articulos,
          retenciones: this.proveedor.retenciones,
          userid: window.user.user.id
        })
        .then(response => {
          if (response.data == 1) {
            this.proveedor = {
              nombre: "",
              cuit: "",
              telefono: "",
              direccion: "",
              ciudad: "",
              email: "",
              cbu: "",
              banco: "",
              tipo_moneda: "",
              tipo_articulos: "",
              retenciones: ""
            };
            this.$refs["modalCrearProveedor"].hide();
            this.errorsCreate = [];

            alertify.success("Proveedor creado correctamente");
          } else {
            alertify.error(
              "Ha ocurrido un error al intentar guardar el proveedor en la base de datos"
            );
          }
        })
        .catch(error => {
          this.$refs["erroresCreate"].removeAttribute("hidden");
          this.errorsCreate = [];
          this.errorsCreate = error.response.data.errors;
        });
    }
  },
  beforeMount() {
    window.Echo.channel("channel-ProveedorTable").listen(
      "ProveedorTable",
      e => {
        this.filtroBusquedaProveedor = "";
        this.listadoProveedores = e.proveedor;
      }
    );
  }
};
</script>
