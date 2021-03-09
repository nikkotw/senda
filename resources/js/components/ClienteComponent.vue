<template>
  <div class="ml-1 mr-1">
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
        <div class="col col-lg form-group">
          <label
            class="font-weight-bold"
            style="font-size:12px;"
            for="cp"
          >Descuento habitual (ingresar el valor del porcentaje)</label>
          <input
            v-model="datosRegistrarCliente.descuento_habitual"
            type="number"
            class="form-control form-control-sm"
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" for="tipo_iva">Condicion Iva</label>

          <b-form-select class="mt-t" id="iva_tipo" required v-model="datosRegistrarCliente.tipo_iva">
            <option value="0">Resp Inscripto</option>
            <option value="1">No Inscripto</option>
            <option value="2">Exento</option>
            <option value="3">Consumidor Final</option>
            <option value="4">Resp. Monotributo</option>
            <option value="5">Sujeto No Categorizado</option>
          </b-form-select>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" for="cp">Observaciones</label>
          <textarea
            style="resize:none;"
            type="text"
            v-model="datosRegistrarCliente.obs"
            class="form-control form-control-sm bg-transparent"
          ></textarea>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
          <b-form-checkbox
            v-model="datosRegistrarCliente.cuenta_corriente"
            @change="datosRegistrarCliente.monto_maximo_cc = ''"
            id="checkbox-1"
            name="checkbox-1"
            class="font-weight-bold"
            value="accepted"
            unchecked-value="not_accepted"
          >Cuenta corriente</b-form-checkbox>
        </div>
        <div class="w-100"></div>
        <div v-if="datosRegistrarCliente.cuenta_corriente == 'accepted'" class="col col-lg">
          <label
            class="font-weight-bold"
            style="font-size:12px;"
            for="cp"
          >Monto máximo en cuenta corriente</label>
          <input
            v-model="datosRegistrarCliente.monto_maximo_cc"
            type="number"
            class="form-control form-control-sm"
            ref="monto_maximo"
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center mt-1">
          <button @click="registrarCliente()" class="btn btn-sm btn-success">Registrar cliente</button>
          <button
            @click="datosRegistrarCliente={razonSocial: '',
            cuit: '',
            telefono: '',
            email: '',
            direccion: '',
            ciudad: '',
            cp: '',
            obs: '',
            cuenta_corriente: '',
            monto_maximo_cc: '',
            descuento_habitual: ''};
            $bvModal.hide('modalCrearCliente')"
            class="btn btn-sm btn-danger"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <b-modal
      ref="modalEditarCliente"
      id="modalEditarCliente"
      size="xl"
      hide-footer
      title="Editar datos del cliente"
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
                    <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" for="iva_tipo">Condicion Iva</label>

             <b-form-select
              id="iva_tipo"
            class="form-control form-control-sm"
          
          :options="options"
          value-field="item"
          text-field="name"
          v-model="datosRegistrarCliente.tipo_iva"
        ></b-form-select>
        </div>
          
        <div class="col col-lg form-group">
          <label
            class="font-weight-bold"
            style="font-size:12px;"
            for="cp"
          >Descuento habitual (ingresar el valor del porcentaje)</label>
          <input
            v-model="datosRegistrarCliente.descuento_habitual"
            type="number"
            class="form-control form-control-sm"
          />
        
      
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" for="cp">Observaciones</label>
          <textarea
            style="resize:none;"
            type="text"
            v-model="datosRegistrarCliente.obs"
            class="form-control form-control-sm bg-transparent"
          ></textarea>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
          <b-form-checkbox
            v-model="datosRegistrarCliente.cuenta_corriente"
            @change="datosRegistrarCliente.monto_maximo_cc = ''"
            id="checkbox-1"
            name="checkbox-1"
            class="font-weight-bold"
            value="accepted"
            unchecked-value="not_accepted"
          >Cuenta corriente</b-form-checkbox>
        </div>
        <div class="w-100"></div>
        <div v-if="datosRegistrarCliente.cuenta_corriente == 'accepted'" class="col col-lg">
          <label
            class="font-weight-bold"
            style="font-size:12px;"
            for="cp"
          >Monto máximo en cuenta corriente</label>
          <input
            v-model="datosRegistrarCliente.monto_maximo_cc"
            type="number"
            class="form-control form-control-sm"
            ref="monto_maximo"
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center mt-1">
          <button @click="editarCliente()" class="btn btn-sm btn-success">Editar cliente</button>
          <button
            @click="datosRegistrarCliente={razonSocial: '',
            cuit: '',
            telefono: '',
            email: '',
            direccion: '',
            ciudad: '',
            cp: '',
            obs: '',
            cuenta_corriente: '',
            monto_maximo_cc: '',
            descuento_habitual: ''};
            $bvModal.hide('modalEditarCliente')"
            class="btn btn-sm btn-danger"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <div class="row card mt-1">
      <div class="card-header text-primary bg-transparent font-weight-bold text-left">
        Clientes
        <button
          v-b-modal.modalCrearCliente
          v-if="$auth.can('Crear Cliente')"
          @click="datosRegistrarCliente={razonSocial: '',
                    cuit: '',
                    telefono: '',
                    email: '',
                    direccion: '',
                    ciudad: '',
                    cp: '',
                    obs: '',
                    cuenta_corriente: '',
                    monto_maximo_cc: '',
                    descuento_habitual: ''};"
          class="btn btn-sm btn-success float-right font-weight-bold"
        >Registrar cliente</button>
      </div>

      <transition name="fade">
        <div v-if="infoCliente == ''" class="row card-body">
          <div class="input-group">
            <input
              type="text"
              :disabled="filter == ''"
              class="form-control form-control-sm"
              v-model="search"
              placeholder="Buscar clientes"
            />
            <div class="input-group-append">
              <select @change="search = ''" v-model="filter" class="form-control form-control-sm">
                <option selected value>No aplicar filtros</option>
                <option value="cuit">CUIT/CUIL/DNI</option>
                <option value="nombre">Nombre</option>
              </select>
            </div>
            <button
              class="btn btn-sm btn-primary btn-block mt-1"
              v-on:click="getClientes()"
              type="button"
            >Buscar</button>
          </div>

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
            <template v-slot:cell(Nombre)="row">
              <span class="font-weight-bold">{{row.item.Nombre}}</span>
            </template>

            <template v-slot:cell(CUIT)="row">
              <span class="font-weight-bold">{{row.item.CUIT}}</span>
            </template>

            <template v-slot:cell(Telefono)="row">
              <span v-if="row.item.Telefono != 0">{{row.item.Telefono}}</span>
              <span v-else>Sin especificar</span>
            </template>

            <template v-slot:cell(Mail)="row">
              <span v-if="row.item.Mail != null">{{row.item.Mail}}</span>
              <span v-else>Sin especificar</span>
            </template>

            <template v-slot:cell(acciones)="row">
              <button
                v-if="$auth.can('Ver Informacion Cliente')"
                @click="getInfoCliente(row.item.id);
                datosRegistrarCliente={razonSocial: '',
                cuit: '',
                telefono: '',
                email: '',
                direccion: '',
                ciudad: '',
                cp: '',
                obs: '',
                cuenta_corriente: '',
                monto_maximo_cc: '',
                descuento_habitual: ''};"
                class="btn btn-sm btn-info"
              >
                <i class="fas fa-info-circle"></i>
              </button>
              <button
                v-if="$auth.can('Editar Cliente')"
                @click="getClientEditInfo(row.index)"
                class="btn btn-sm btn-success"
              >
                <i class="fas fa-pen"></i>
              </button>
              <button
                v-if="$auth.can('Eliminar Cliente')"
                @click="eliminarCliente(row.item.id)"
                class="btn btn-sm btn-danger"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </template>
          </b-table>

          <div
            class="col text-center"
            v-if="listadoClientes.total != undefined && listadoClientes.total > 10"
            @click="getClientes()"
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
      </transition>

      <transition name="fade">
        <div v-if="infoCliente != ''" class="row card-body">
          <div class="col col-xl">
            <div class="card">
              <div
                class="card-header text-white bg-dark font-weight-bold text-left"
              >Información del cliente</div>
              <div class="card-body row font-weight-bold" style="font-size:12px;">
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      :value="infoCliente.Nombre"
                      type="text"
                      class="form-control form-control-sm bg-transparent"
                    />
                    <label class="active" for="cliente">Cliente</label>
                  </div>
                </div>
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      :value="infoCliente.Domicilio"
                      type="text"
                      class="form-control form-control-sm bg-transparent"
                    />
                    <label class="active" for="domicilio">Dirección</label>
                  </div>
                </div>
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      :value="infoCliente.CUIT"
                      type="text"
                      class="form-control form-control-sm bg-transparent"
                    />
                    <label class="active" for="cuit">CUIT/CUIL/DNI</label>
                  </div>
                </div>
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      :value="infoCliente.Mail"
                      type="text"
                      class="form-control form-control-sm bg-transparent"
                    />
                    <label class="active" for="tipo_facturacion">E-Mail</label>
                  </div>
                </div>
                <div class="w-100"></div>
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      :value="infoCliente.Telefono"
                      type="text"
                      class="form-control form-control-sm bg-transparent"
                    />
                    <label class="active" for="factura">Teléfono</label>
                  </div>
                </div>
                <div class="col col-lg">
                  <div class="md-form">
                    <input
                      readonly
                      v-if="infoCliente.cuenta_corriente == 0"
                      value="El cliente no cuenta con cuenta corriente"
                      type="text"
                      class="form-control form-control-sm text-danger font-weight-bold bg-transparent"
                    />
                    <input
                      readonly
                      v-if="infoCliente.cuenta_corriente != 0 && infoCliente.total_cc < 0"
                      :value="infoCliente.total_cc.toString().slice(1)"
                      type="text"
                      class="form-control form-control-sm text-danger font-weight-bold bg-transparent"
                    />
                    <input
                      readonly
                      v-if="infoCliente.cuenta_corriente != 0 && infoCliente.total_cc == 0"
                      :value="infoCliente.total_cc"
                      type="text"
                      class="form-control form-control-sm text-warning font-weight-bold bg-transparent"
                    />
                    <input
                      readonly
                      v-if="infoCliente.cuenta_corriente != 0 && infoCliente.total_cc > 0"
                      :value="infoCliente.total_cc"
                      type="text"
                      class="form-control form-control-sm text-success font-weight-bold bg-transparent"
                    />
                    <label class="active" for="cliente">Saldo en cuenta corriente</label>
                  </div>
                </div>
                <div class="w-100"></div>
                <div class="col col-lg">
                  <div class="md-form">
                    <textarea
                      readonly
                      style="resize:none;"
                      type="text"
                      :value="infoCliente.Obs"
                      class="form-control form-control-sm bg-transparent"
                    ></textarea>
                    <label class="active" for="factura">Observaciones</label>
                  </div>
                </div>
                <div class="w-100"></div>
                <div
                  v-if="selected == 'movimientos_cc' && infoCliente.cuenta_corriente == 1"
                  class="col col-lg my-auto"
                >
                  <div v-if="!loaded" class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <transition name="fade">
                    <chart v-if="loaded" :chartdata="chartdataCC" :options="optionsCC" />
                  </transition>
                </div>
                <div
                  v-if="selected == 'ventas' || selectedWithoutCC == 'ventas'"
                  class="col col-lg my-auto"
                >
                  <div v-if="!loaded" class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
                  <transition name="fade">
                    <chart v-if="loaded" :chartdata="chartdataVentas" :options="optionsVentas" />
                  </transition>
                </div>
              </div>
            </div>
          </div>
          <div class="w-100"></div>
          <transition name="fade">
            <div v-if="infoCliente != ''" class="col col-xl mt-1">
              <button @click="volver()" class="btn btn-sm btn-primary">Volver</button>
            </div>
          </transition>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import Chart from "chart.js";
export default {
  data() {
    return {
      selected: "movimientos_cc",
      selectedWithoutCC: "ventas",

      chartdataCC: null,
      chartdataVentas: null,
      optionsCC: null,
      optionsVentas: null,
      fechasCC: [],
      fechasVentas: [],
      egresosCC: [],
      ingresosCC: [],
      ventasMensuales: [],
     
         options: [
          { item: '0', name: 'Resp Inscripto' },
          { item: '1', name: 'No Inscripto' },
          { item: '2', name: 'Exento' },
          { item: '3', name: 'Consumidor Final' },
          { item: '4', name: 'Resp Monotributo' },
          { item: '5', name: 'Sujeto No Categorizado' },
        ],
      loaded: false,

      search: "",
      filter: "",

      listadoClientes: [],
      fieldsListadoClientes: [
        { key: "Nombre", label: "Cliente" },
        { key: "Domicilio", label: "Domicilio" },
        { key: "CUIT", label: "CUIT/CUIL/DNI" },
        { key: "Localidad", label: "Ciudad" },
        { key: "Telefono", label: "Teléfono" },
        { key: "Mail", label: "Correo Electrónico" },
        "acciones"
      ],
      paginaActualListadoClientes: 1,
      infoCliente: [],
      datosRegistrarCliente: {
        razonSocial: "",
        cuit: "",
        telefono: "",
        email: "",
        direccion: "",
        ciudad: "",
        cp: "",
        obs: "",
        cuenta_corriente: "",
        monto_maximo_cc: "",
        descuento_habitual: ""
      } //Datos para registrar al cliente
    };
  },
  created: function() {},
  mounted() {},
  methods: {
    getClientes: async function() {
      try {
        const response = await axios.post("api/getClientes", {
          busqueda: this.search,
          filtro: this.filter,
          page: this.paginaActualListadoClientes
        });

        this.listadoClientes = response.data;
      } catch (error) {
        alertify.error("Error al buscar clientes");
      }
    },
    getInfoCliente: async function(id) {
      try {
        const response = await axios.post("api/getInfoCliente", {
          id: id
        });
        this.infoCliente = response.data;
      } catch (error) {
        alertify.error(
          "Ha ocurrido un error al obtener la información del cliente"
        );
      }
      if (this.infoCliente.cuenta_corriente == 1) {
        this.getInfoChartsClientes(id, this.selected);
      } else {
        this.getInfoChartsClientes(id, this.selectedWithoutCC);
      }
    },
    getInfoChartsClientes: function(id, selected) {
      this.loaded = false;
      this.fechasCC = [];
      this.egresosCC = [];
      this.ingresosCC = [];
      this.ventasMensuales = [];
      this.fechasVentas = [];
      axios
        .post("api/getClienteInfoCharts", {
          id: id,
          selected: selected
        })
        .then(response => {
          response.data.cc.forEach(element => {
            this.fechasCC.push(element.mes);
            this.egresosCC.push(element.egreso);
            this.ingresosCC.push(element.ingreso);
          });

          response.data.ventas.forEach(element => {
            this.ventasMensuales.push(element.ventasMensuales);
            this.fechasVentas.push(element.mes);
          });

          this.chartdataCC = {
            labels: this.fechasCC.slice().reverse(),
            type: "bar",
            datasets: [
              {
                label: "Egresos en cuenta corriente",
                backgroundColor: "#159933",
                data: this.egresosCC.slice().reverse()
              },
              {
                label: "Ingresos en cuenta corriente",
                backgroundColor: "#FF0000",
                data: this.ingresosCC.slice().reverse()
              }
            ]
          };

          this.optionsCC = {
            maintainAspectRatio: false
          };

          this.chartdataVentas = {
            labels: this.fechasVentas.slice().reverse(),
            type: "bar",
            datasets: [
              {
                label: "Cantidad de ventas realizadas",
                backgroundColor: "#159933",
                data: this.ventasMensuales.slice().reverse()
              }
            ]
          };

          this.optionsVentas = {
            maintainAspectRatio: false
          };

          this.loaded = true;
        });
    },
    volver: function() {
      this.infoCliente = [];
    },
    registrarCliente: function() {
      var errors = true;
      if (this.datosRegistrarCliente.cuenta_corriente == "accepted") {
        this.$refs["monto_maximo"].setAttribute("required", true);
      }

      if (
        this.datosRegistrarCliente.razonSocial == "" ||
        this.datosRegistrarCliente.cuit == "" ||
        this.datosRegistrarCliente.telefono == "" ||
        this.datosRegistrarCliente.email == "" ||
        this.datosRegistrarCliente.direccion == "" ||
        this.datosRegistrarCliente.ciudad == "" ||
        this.datosRegistrarCliente.cp == "" ||
        (this.datosRegistrarCliente.cuenta_corriente == "accepted" &&
          this.datosRegistrarCliente.monto_maximo_cc == "")
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
          .post("api/saveCliente", {
            razonSocial: this.datosRegistrarCliente.razonSocial,
            cuit: this.datosRegistrarCliente.cuit,
            telefono: this.datosRegistrarCliente.telefono,
            email: this.datosRegistrarCliente.email,
            direccion: this.datosRegistrarCliente.direccion,
            ciudad: this.datosRegistrarCliente.ciudad,
            cp: this.datosRegistrarCliente.cp,
            obs: this.datosRegistrarCliente.obs,
            descuento_habitual: this.datosRegistrarCliente.descuento_habitual,
            cuenta_corriente: this.datosRegistrarCliente.cuenta_corriente,
            monto_maximo_cc: this.datosRegistrarCliente.monto_maximo_cc,
            tipo_iva: this.datosRegistrarCliente.tipo_iva,
            userid: window.user.user.id
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
                cp: "",
                obs: "",
                descuento_habitual: "",
                cuenta_corriente: "",
                monto_maximo_cc: ""
              };

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
              if (error.response.data.errors.iva_tipo) {
              this.datosRegistrarCliente.iva_tipo = "";
              this.$refs["registroCliente"].classList.add("was-validated");
              alertify.error(
                "Debe registrar una condicion de IVA"
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
    getClientEditInfo: function(index) {
      this.datosRegistrarCliente = {
        id: "",
        razonSocial: "",
        cuit: "",
        telefono: "",
        email: "",
        direccion: "",
        ciudad: "",
        cp: "",
        obs: "",
        descuento_habitual: "",
        cuenta_corriente: "",
        monto_maximo_cc: "",
        tipo_iva: 99
      };
      this.datosRegistrarCliente.tipo_iva = this.listadoClientes.data[index].IVA_Tipo;
      this.datosRegistrarCliente.id = this.listadoClientes.data[index].id;
      this.datosRegistrarCliente.razonSocial = this.listadoClientes.data[
        index
      ].Nombre;
      this.datosRegistrarCliente.cuit = this.listadoClientes.data[index].CUIT;
      this.datosRegistrarCliente.telefono = this.listadoClientes.data[
        index
      ].Telefono;
      this.datosRegistrarCliente.direccion = this.listadoClientes.data[
        index
      ].Domicilio;
      this.datosRegistrarCliente.email = this.listadoClientes.data[index].Mail;
      this.datosRegistrarCliente.ciudad = this.listadoClientes.data[
        index
      ].Localidad;
      this.datosRegistrarCliente.cp = this.listadoClientes.data[index].CP;
      this.datosRegistrarCliente.obs = this.listadoClientes.data[index].Obs;
      if (this.listadoClientes.data[index].cuenta_corriente == 1) {
        this.datosRegistrarCliente.cuenta_corriente = "accepted";
        this.datosRegistrarCliente.monto_maximo_cc = this.listadoClientes.data[
          index
        ].monto_maximo_cc;
      } else {
        this.datosRegistrarCliente.cuenta_corriente = "not-accepted";
      }
      this.datosRegistrarCliente.descuento_habitual = this.listadoClientes.data[
        index
      ].DescuentoHabitual;

      this.$refs["modalEditarCliente"].show();
    },
    editarCliente: function() {
      console.log(this.datosRegistrarCliente);

      var errors = true;
      if (this.datosRegistrarCliente.cuenta_corriente == "accepted") {
        this.$refs["monto_maximo"].setAttribute("required", true);
      }

      if (
        this.datosRegistrarCliente.razonSocial == "" ||
        this.datosRegistrarCliente.cuit == "" ||
        this.datosRegistrarCliente.telefono == "" ||
        this.datosRegistrarCliente.email == "" ||
        this.datosRegistrarCliente.direccion == "" ||
        this.datosRegistrarCliente.ciudad == "" ||
        this.datosRegistrarCliente.cp == "" ||
        (this.datosRegistrarCliente.cuenta_corriente == "accepted" &&
          this.datosRegistrarCliente.monto_maximo_cc == "")
      ) {
        errors = false;
      }

      if (
        this.datosRegistrarCliente.cuit.toString().length == 11 ||
        this.datosRegistrarCliente.cuit.toString().length == 8
      ) {
      } else {
        this.datosRegistrarCliente.cuit = "";
        errors = false;
      }

      if (errors) {
        axios
          .post("api/editCliente", {
            id: this.datosRegistrarCliente.id,
            razonSocial: this.datosRegistrarCliente.razonSocial,
            cuit: this.datosRegistrarCliente.cuit,
            telefono: this.datosRegistrarCliente.telefono,
            email: this.datosRegistrarCliente.email,
            direccion: this.datosRegistrarCliente.direccion,
            ciudad: this.datosRegistrarCliente.ciudad,
            cp: this.datosRegistrarCliente.cp,
            obs: this.datosRegistrarCliente.obs,
            descuento_habitual: this.datosRegistrarCliente.descuento_habitual,
            cuenta_corriente: this.datosRegistrarCliente.cuenta_corriente,
            monto_maximo_cc: this.datosRegistrarCliente.monto_maximo_cc,
            userid: window.user.user.id
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
                cp: "",
                obs: "",
                descuento_habitual: "",
                cuenta_corriente: "",
                monto_maximo_cc: ""
              };

              alertify.success("Datos del cliente actualizados correctamente");
              this.$refs["modalEditarCliente"].hide();
              this.listadoClientes = [];
              this.paginaActualListadoClientes = 1;
            } else {
              alertify.error(
                "Hay errores en el formulario de edición del cleinte"
              );
            }
          })
          .catch(error => {
            if (error.response.data.errors.cuit) {
              this.datosRegistrarCliente.cuit = "";
              this.$refs["registroCliente"].classList.add("was-validated");
              alertify.error("El cuit ya corresponde a un cliente registrado");
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
    eliminarCliente: function(id) {
      alertify.confirm(
        "Eliminar el cliente",
        "¿Esta segur@ que desea elminar el cliente?",
        () => {
          axios
            .post("api/eliminarCliente", {
              id: id,
              userid: window.user.user.id
            })
            .then(response => {
              alertify.success("Cliente eliminado correctamente");
              this.getClientes();
            })
            .catch(error => {
              alertify.error("Ha ocurrido un error al eliminar al cliente");
              console.log(error);
            });
        },
        function() {
          alertify.error("Se ha cancelado");
        }
      );
    }
  }
};
</script>
