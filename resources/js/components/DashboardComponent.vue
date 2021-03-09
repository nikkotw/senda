<template>
  <div v-if="$auth.can('Acceder Dashboard')" class="container-fluid">
    <b-modal
      ref="modalChangePassword"
      id="modalChangePassword"
      size="xl"
      hide-footer
      no-close-on-backdrop
      no-close-on-esc
      hide-header-close
      title="Ingrese su nueva contraseña"
    >
      <div class="row" ref="changePassword">
        <div class="col col-xl form-group">
          <label class="font-weight-bold" style="font-size:12px">Contraseña</label>
          <div class="input-group">
            <input
              ref="password"
              v-model="password"
              required
              type="password"
              class="broder-right form-control form-control-sm"
            />
            <div class="input-group-append">
              <button ref="noEyePassword" @click="revealPassword()" class="btn btn-sm btn-light">
                <i class="fas fa-eye-slash"></i>
              </button>
              <button
                hidden
                ref="eyePassword"
                @click="revealPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl form-group">
          <label class="font-weight-bold" style="font-size:12px">Confirme su contraseña</label>
          <div class="input-group">
            <input
              v-model="confirmPassword"
              ref="confirmPassword"
              required
              type="password"
              class="form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                ref="noEyeConfirmPassword"
                @click="revealConfirmPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye-slash"></i>
              </button>
              <button
                hidden
                ref="eyeConfirmPassword"
                @click="revealConfirmPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl text-center">
          <button
            ref="changePasswordButton"
            @click="changePassword()"
            class="btn btn-sm btn-success"
          >Cambiar contraseña</button>
          <button @click="cancelChangePassword()" class="btn btn-sm btn-danger">Cancelar</button>
        </div>
      </div>
    </b-modal>
    <div class="row justify-content-center">
      <div class="card border-left-primary shadow h-100 py-2 mt-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas De HOY</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">${{ventasDia}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-calendar fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="card border-left-primary shadow h-100 py-2 mt-2 ml-3">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div
                class="text-xs font-weight-bold text-primary text-uppercase mb-1"
              >Total Ventas Mes</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">${{ventasMes}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="card border-left-primary shadow h-100 py-2 mt-2 ml-3">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Caja</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">${{caja}}</div>
            </div>
            <div class="col-auto">
              <i class="fas fa-cash-register fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col col-md-5 mt-3">
        <canvas id="ventas" width="600" height="300"></canvas>
      </div>
        <div class="col col-md-5 mt-3">
        <canvas id="ventasMensual" width="600" height="300"></canvas>
      </div>
      <div class="col col-md-6">
        <h2>
          Productos Mas Vendidos
        </h2>
        <b-table
          id="listado-ventas"
          style="font-size:12px;"
          responsive="sm"
          small
          hover
          bordered
          head-variant="dark"
          :items="masVendido"
          :fields="fieldsMasMenosVendido"
        >
          <template v-slot:cell(cantidad)="row">
            <span class="badge badge-success">{{row.item.cantidad}}</span>
          </template>
        </b-table>
      </div>
      <div class="col col-lg">
         <h2>
          Productos Menos Vendidos
        </h2>
        <b-table
          id="listado-ventas"
          style="font-size:12px;"
          responsive="sm"
          hover
          bordered
          small
          head-variant="dark"
          :items="menosVendido"
          :fields="fieldsMasMenosVendido"
        >
          <template v-slot:cell(cantidad)="row">
            <span class="badge badge-danger">{{row.item.cantidad}}</span>
          </template>
        </b-table>
      </div>
      <div class="w-100"></div>
      <div class="col col-lg">
        <b-table
          class="text-left text-muted"
          :items="logs"
          :fields="fieldsLogs"
          :small="true"
          :fixed="true"
        >
          <template v-slot:thead-top>
            <b-tr>
              <b-th variant="success" class="text-center" colspan="3">Log Usuarios</b-th>
            </b-tr>
          </template>
        </b-table>
      </div>
    </div>
  </div>
  <div class="ml-1 mr-1" v-else>
    <b-modal
      ref="modalChangePassword"
      id="modalChangePassword"
      size="xl"
      hide-footer
      no-close-on-backdrop
      no-close-on-esc
      hide-header-close
      title="Ingrese su nueva contraseña"
    >
      <div class="row" ref="changePassword">
        <div class="col col-xl form-group">
          <label class="font-weight-bold" style="font-size:12px">Contraseña</label>
          <div class="input-group">
            <input
              ref="password"
              v-model="password"
              required
              type="password"
              class="broder-right form-control form-control-sm"
            />
            <div class="input-group-append">
              <button ref="noEyePassword" @click="revealPassword()" class="btn btn-sm btn-light">
                <i class="fas fa-eye-slash"></i>
              </button>
              <button
                hidden
                ref="eyePassword"
                @click="revealPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl form-group">
          <label class="font-weight-bold" style="font-size:12px">Confirme su contraseña</label>
          <div class="input-group">
            <input
              v-model="confirmPassword"
              ref="confirmPassword"
              required
              type="password"
              class="form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                ref="noEyeConfirmPassword"
                @click="revealConfirmPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye-slash"></i>
              </button>
              <button
                hidden
                ref="eyeConfirmPassword"
                @click="revealConfirmPassword()"
                class="btn btn-sm btn-light"
              >
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-xl text-center">
          <button
            ref="changePasswordButton"
            @click="changePassword()"
            class="btn btn-sm btn-success"
          >Cambiar contraseña</button>
          <button @click="cancelChangePassword()" class="btn btn-sm btn-danger">Cancelar</button>
        </div>
      </div>
    </b-modal>
    <transition name="fade">
      <div>
        <h5>Bienvenidos al sistema de control de stock y facturación de Senda SRL!</h5>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      diasSemana: [
        "Sabado",
        "Domingo",
        "Lunes",
        "Martes",
        "Miercoles",
        "Jueves",
        "Viernes",
      ],
       meses: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Sept",
        "Octubre",
        "Noviem",
        "Diciem",
      ],
      ventasDia: 0,
      ventasMes: 0,
      masVendido: [],
      menosVendido: [],
      ventaMensual:[],
      totalVentasMensual:[],
      caja: 0,
      totalSemana: [],
      ventasSemana: [],
      fieldsLogs: [
        { key: "user", label: "Usuario" },
        { key: "detalle", label: "Tarea" },
        { key: "hora", label: "Hora" },
      ],
      logs: [],
      password: "",
      confirmPassword: "",
      fieldsMasMenosVendido: [
        { key: "cantidad", label: "Cantidad" },
        { key: "Descripcion", label: "Producto" },
      ],
    };
  },

  created() {
    this.getDashboard();
  },

  mounted() {
    if (window.user.user.ftp == 1) {
      this.$refs["modalChangePassword"].show();
    }
  },
  methods: {
    getDashboard: async function () {
      await axios
        .get("api/getDashboard")
        .then((response) => {
          this.menosVendido = response.data.menosVendido;
          this.masVendido = response.data.masVendido;
          this.logs = response.data.log;
          console.log(response.data.caja);
          this.caja = response.data.caja;
          if (response.data.ventasDia[0].total != null) {
            this.ventasDia = response.data.ventasDia[0].total.toFixed(2);
          }
          if (response.data.ventasMonth[0].total != null) {
            this.ventasMes = response.data.ventasMonth[0].total.toFixed(2);
          }

          // this.ventasDia = this.ventasDia.toFixed(2);
          //this.ventasMes = this.ventasMes.toFixed(2);
          this.ventasSemana = response.data.ventasWeek;
          this.ventaMensual = response.data.graficoMensual;
          this.armaGrafico();
        })
        .catch((err) => {
          console.log(err);
        });
    },
    armaGrafico: async function () {
      console.log("arma Grafico");
      let semana = [];
      let months =[];
      await this.ventasSemana.forEach((element) => {
        var date = new Date(element.fecha);
        let dia = date.getUTCDate(date.toLocaleDateString(["es-es"]));
        semana.push(this.diasSemana[element.dia] + " " + dia);
        this.totalSemana.push(element.total.toFixed(2));
      });
      
      await this.ventaMensual.forEach((element) =>{
        months.push(this.meses[element.mes-1]);
        this.totalVentasMensual.push(element.total.toFixed(2));

      });

       new Chart(document.getElementById("ventasMensual"), {
        type: "horizontalBar",
        data: {
          labels: months,
          datasets: [
            {
              label: "Ventas Mensuales",
              backgroundColor: [
                "rgba(0, 255, 30, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)",
              ],
              color: [
                "rgba(0, 255, 30, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)",
              ],
              data: this.totalVentasMensual
            },
          ],
        },
      });

      new Chart(document.getElementById("ventas"), {
        type: "line",
        data: {
          labels: semana.slice().reverse(),
          datasets: [
            {
              label: "Ventas Diarias",
              backgroundColor: [
                "rgba(0, 255, 30, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)",
              ],
              color: [
                "rgba(0, 255, 30, 0.2)",
                "rgba(54, 162, 235, 0.2)",
                "rgba(255, 206, 86, 0.2)",
                "rgba(75, 192, 192, 0.2)",
                "rgba(153, 102, 255, 0.2)",
                "rgba(255, 159, 64, 0.2)",
              ],
              data: this.totalSemana.slice().reverse(),
            },
          ],
        },
      });
    },
    changePassword: async function () {
      this.$refs["changePasswordButton"].setAttribute("disabled", true);
      var errors = true;

      if (this.password == "" || this.confirmPassword == "") {
        errors = false;
      }
      if (this.password != this.confirmPassword) {
        errors = false;
        this.password = "";
        this.confirmPassword = "";
        alertify.error("Las contraseñas no coinciden");
      }

      if (errors) {
        try {
          const response = await axios.post("api/changePassword", {
            id: window.user.user.id,
            password: this.password,
          });
          this.$refs["modalChangePassword"].hide();
          this.$refs["changePasswordButton"].removeAttribute("disabled");
          alertify.success("Contraseña establecida correctamente");
        } catch (error) {
          alertify.error(
            "Ha ocurrido un error al intentar cambiar la contraseña"
          );
        }
      } else {
        this.$refs["changePassword"].classList.remove("needs-validation");
        this.$refs["changePassword"].classList.add("was-validated");
        this.$refs["changePasswordButton"].removeAttribute("disabled");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    cancelChangePassword: async function () {
      window.location.replace("/logout");
    },
    revealPassword: function () {
      if (this.$refs["password"].type == "password") {
        this.$refs["password"].type = "text";
        this.$refs["eyePassword"].removeAttribute("hidden");
        this.$refs["noEyePassword"].setAttribute("hidden", true);
      } else {
        this.$refs["password"].type = "password";
        this.$refs["noEyePassword"].removeAttribute("hidden");
        this.$refs["eyePassword"].setAttribute("hidden", true);
      }
    },
    revealConfirmPassword: function () {
      if (this.$refs["confirmPassword"].type == "password") {
        this.$refs["confirmPassword"].type = "text";
        this.$refs["eyeConfirmPassword"].removeAttribute("hidden");
        this.$refs["noEyeConfirmPassword"].setAttribute("hidden", true);
      } else {
        this.$refs["confirmPassword"].type = "password";
        this.$refs["noEyeConfirmPassword"].removeAttribute("hidden");
        this.$refs["eyeConfirmPassword"].setAttribute("hidden", true);
      }
    },
  },
};
</script>
