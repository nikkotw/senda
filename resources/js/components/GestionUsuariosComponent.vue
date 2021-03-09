<template>
  <div class="ml-1 mr-1">
    <b-modal
      ref="modalNewPassword"
      id="modalNewPassword"
      size="xl"
      hide-footer
      title="Nueva contraseña generada"
    >
      <h5>
        La nueva contraseña generada para el usuario es:
        <span class="font-weight-bold">{{ newPassword }}</span>
      </h5>
    </b-modal>
    <div class="row card mt-1">
      <div
        class="card-header text-primary bg-transparent font-weight-bold text-left"
      >
        Gestión de usuarios
      </div>

      <transition name="fade">
        <div v-if="userInfo == ''" class="card-body">
          <!-- Buscador de usuarios -->
          <div class="input-group">
            <input
              type="text"
              :disabled="filter == ''"
              class="form-control form-control-sm"
              v-model="search"
              placeholder="Buscar usuarios"
            />
            <div class="input-group-append">
              <select
                @change="search = ''"
                v-model="filter"
                class="form-control form-control-sm"
              >
                <option selected value>No aplicar filtros</option>
                <option value="cuil">CUIL/DNI</option>
                <option value="nombre">Nombre</option>
              </select>
            </div>
            <button
              class="btn btn-sm btn-primary btn-block mt-1"
              v-on:click="getUsers()"
              type="button"
            >
              Buscar
            </button>
          </div>
          <!-- Fin buscador de usuarios -->

          <!--Tabla del listado de usuarios-->
          <transition name="fade">
            <b-table
              v-if="usersList.total > 0"
              id="listado-ventas"
              class="mt-1"
              style="font-size: 12px"
              outlined
              small
              hover
              fixed
              no-border-collapse
              head-variant="dark"
              :items="usersList.data"
              :fields="fieldsUsersList"
              :tbody-transition-props="{ name: 'fade' }"
            >
              <template v-slot:cell(acciones)="row">
                <button
                  @click="resetPassword(row.item.id)"
                  v-if="$auth.can('Restablecer Contraseña')"
                  title="Restablecer contraseña"
                  class="btn btn-sm btn-warning"
                >
                  <i class="fas fa-key"></i>
                </button>
                <button
                  @click="getUserInfo(row.item.id)"
                  v-if="$auth.can('Editar Usuario')"
                  title="Editar datos del usuario"
                  class="btn btn-sm btn-success"
                >
                  <i class="fas fa-pen"></i>
                </button>
                <button
                  @click="deleteUser(row.item.id)"
                  v-if="$auth.can('Eliminar Usuario')"
                  title="Eliminar usuario"
                  class="btn btn-sm btn-danger"
                >
                  <i class="fas fa-trash-alt"></i>
                </button>
              </template>

              <template v-slot:cell(estado)="row">
                <div v-if="row.item.estado == 1">
                  <button
                    @click="changeState(row.item.id)"
                    class="btn btn-sm btn-success font-weight-bold"
                  >
                    Habilitado
                  </button>
                </div>
                <div v-else>
                  <button
                    @click="changeState(row.item.id)"
                    class="btn btn-sm btn-warning font-weight-bold"
                  >
                    Inhabilitado
                  </button>
                </div>
              </template>
            </b-table>
          </transition>
          <!--Fin tabla del listado de usuarios-->

          <!--Paginación tabla de usuarios-->

          <!--Fin paginación tabla de usuarios-->
        </div>
      </transition>

      <transition name="fade">
        <!-- Editar información del usuario -->
        <div v-if="userInfo != ''" class="card-body">
          <div class="row text-center" ref="updateUser">
            <div
              style="font-size: 12px"
              class="col col-lg text-center form-group"
            >
              <input
                v-model="userInfo.nombre"
                required
                type="text"
                class="form-control form-control-sm"
              />
              <label class="font-weight-bold">Nombre y apellido</label>
            </div>
            <div
              style="font-size: 12px"
              class="col col-lg text-center form-group"
            >
              <input
                v-model="userInfo.cuil"
                required
                type="number"
                class="form-control form-control-sm"
              />
              <label class="font-weight-bold">CUIL/DNI</label>
            </div>
            <div
              style="font-size: 12px"
              class="col col-lg text-center form-group"
            >
              <input
                v-model="userInfo.domicilio"
                required
                type="text"
                class="form-control form-control-sm"
              />
              <label class="font-weight-bold">Dirección</label>
            </div>
            <div
              style="font-size: 12px"
              class="col col-lg text-center form-group"
            >
              <input
                v-model="userInfo.telefono"
                required
                type="number"
                class="form-control form-control-sm"
              />
              <label class="font-weight-bold">Teléfono</label>
            </div>
            <div class="w-100"></div>
            <div
              style="font-size: 12px"
              class="col col-lg text-center form-group"
            >
              <input
                v-model="userInfo.user"
                required
                type="text"
                class="form-control form-control-sm"
              />
              <label class="font-weight-bold">Nombre de usuario</label>
            </div>

            <div class="w-100"></div>

            <div class="col col-xl text-left">
              <div class="card">
                <div class="card-header bg-dark text-white font-weight-bold">
                  Listado de permisos
                </div>
                <div class="card-body border-0 row">
                  <div class="col col-xl">
                    <b-form-checkbox-group
                      v-model="userPermissions"
                      stacked
                      switches
                    >
                      <div class="card">
                        <div class="card-header font-weight-bold">Caja</div>
                        <div class="card-body">
                          <b-form-checkbox value="Abrir Caja" switch
                            >Realizar apertura de caja</b-form-checkbox
                          >
                          <b-form-checkbox value="Cerrar Caja" switch
                            >Hacer cierre caja</b-form-checkbox
                          >
                          <b-form-checkbox value="Extraer Dinero Caja" switch
                            >Extraer dinero de la caja</b-form-checkbox
                          >
                          <b-form-checkbox value="Ingreso Dinero Caja" switch
                            >Ingreso de dinero en caja</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Listado Ventas" switch
                            >Ver listado de las ventas a
                            realizar</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Detalle Ventas" switch
                            >Ver detalle de las ventas</b-form-checkbox
                          >
                          <b-form-checkbox value="Rechazar Ventas" switch
                            >Rechazar ventas</b-form-checkbox
                          >
                          <b-form-checkbox value="Facturar Venta" switch
                            >Realizar facturación</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Listado Ventas CC" switch
                            >Ver listado de la ventas a realizar por cuenta
                            corriente</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Realizar Ventas
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Realizar Ventas" switch
                            >Realizar ventas</b-form-checkbox
                          >
                          <b-form-checkbox value="Pedidos" switch
                            >Pedidos</b-form-checkbox
                          >
                          <b-form-checkbox value="Presupuestos" switch
                            >Presupuestos</b-form-checkbox
                          >
                          <b-form-checkbox value="Escuela" switch
                            >Ticket ESCUELA</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Notas de crédito
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Emitir Nota Credito" switch
                            >Emitir nota de crédito</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Cuentas Corrientes
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Autorizar Ventas CC" switch
                            >Autorizar ventas por cuenta
                            corriente</b-form-checkbox
                          >
                          <b-form-checkbox
                            value="Realizar Ingreso Cobranza"
                            switch
                            >Realizar ingresos de cobranza</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Ventas CC" switch
                            >Visualizar ventas por cuenta corriente de
                            clientes</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Ingresos Cobranza" switch
                            >Visualizar ingresos de cobranza de
                            clientes</b-form-checkbox
                          >
                        </div>
                      </div>
                    </b-form-checkbox-group>
                  </div>

                  <div class="col col-xl">
                    <b-form-checkbox-group
                      v-model="userPermissions"
                      stacked
                      switches
                    >
                      <div class="card">
                        <div class="card-header font-weight-bold">
                          Proveedores
                        </div>

                        <div class="card-body">
                          <b-form-checkbox value="Crear Proveedores" switch
                            >Crear proveedores</b-form-checkbox
                          >
                          <b-form-checkbox value="Ver Info Proveedores" switch
                            >Ver información de proveedores</b-form-checkbox
                          >
                          <b-form-checkbox value="Editar Proveedores" switch
                            >Editar proveedores</b-form-checkbox
                          >
                          <b-form-checkbox value="Eliminar Proveedores" switch
                            >Eliminar proveedores</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Realizar pedidos
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Crear Pedidos" switch
                            >Realizar pedidos a proveedores</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Gestión de pedidos
                        </div>
                        <div class="card-body">
                          <b-form-checkbox
                            value="Descargar Detalle Pedido"
                            switch
                            >Descargar información de pedidos</b-form-checkbox
                          >

                          <b-form-checkbox value="Ver Info Pedidos" switch
                            >Ver detalle de pedidos</b-form-checkbox
                          >

                          <b-form-checkbox value="Gestionar Pagos Pedido" switch
                            >Gestionar pagos de pedidos</b-form-checkbox
                          >

                          <b-form-checkbox value="Eliminar Pedidos" switch
                            >Eliminar pedidos</b-form-checkbox
                          >
                        </div>
                      </div>
                    </b-form-checkbox-group>
                  </div>

                  <div class="col col-xl">
                    <b-form-checkbox-group
                      v-model="userPermissions"
                      stacked
                      switches
                    >
                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Productos
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Crear Productos" switch
                            >Crear productos</b-form-checkbox
                          >
                          <b-form-checkbox value="Editar Productos" switch
                            >Editar información de los
                            productos</b-form-checkbox
                          >
                          <b-form-checkbox value="Eliminar Productos" switch
                            >Eliminar productos</b-form-checkbox
                          >
                          <b-form-checkbox
                            value="Aumentar Bajar Precios Productos"
                            switch
                            >Aumentar o disminuir el precio de los
                            productos</b-form-checkbox
                          >
                          <b-form-checkbox value="Aumentar Stock" switch
                            >Aumentar Stock</b-form-checkbox
                          >
                          <b-form-checkbox value="Imprimir Etiquetas" switch
                            >Imprimir Etiquetas</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">Stock</div>
                        <div class="card-body">
                          <b-form-checkbox
                            value="Realizar Transferencias"
                            switch
                            >Realizar transferencias</b-form-checkbox
                          >
                          <b-form-checkbox value="Listado Transferencias" switch
                            >Acceder al listado de
                            transferencias</b-form-checkbox
                          >
                          <b-form-checkbox value="Recepciones" switch
                            >Recepciones</b-form-checkbox
                          >
                        </div>
                      </div>
                    </b-form-checkbox-group>
                  </div>
                  <div class="col col-xl">
                    <b-form-checkbox-group
                      v-model="userPermissions"
                      stacked
                      switches
                    >
                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">Clientes</div>
                        <div class="card-body">
                          <b-form-checkbox value="Crear Cliente" switch
                            >Registrar clientes</b-form-checkbox
                          >
                          <b-form-checkbox value="Editar Cliente" switch
                            >Editar información de clientes</b-form-checkbox
                          >
                          <b-form-checkbox value="Eliminar Cliente" switch
                            >Eliminar clientes</b-form-checkbox
                          >
                          <b-form-checkbox
                            value="Ver Informacion Cliente"
                            switch
                            >Ver información de los clientes</b-form-checkbox
                          >
                        </div>
                      </div>

                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">
                          Administración
                        </div>
                        <div class="card-body">
                          <b-form-checkbox value="Crear Usuario" switch
                            >Crear usuarios</b-form-checkbox
                          >
                          <b-form-checkbox value="Editar Usuario" switch
                            >Editar información de usuarios</b-form-checkbox
                          >
                          <b-form-checkbox value="Eliminar Usuario" switch
                            >Eliminar usuarios</b-form-checkbox
                          >
                          <b-form-checkbox value="Restablecer Contraseña" switch
                            >Restablecer contraseña</b-form-checkbox
                          >
                          <b-form-checkbox value="Acceder Dashboard" switch
                            >Acceder al dashboard</b-form-checkbox
                          >
                        </div>
                      </div>
                      <div class="card mt-1">
                        <div class="card-header font-weight-bold">Reportes</div>
                        <div class="card-body">
                          <b-form-checkbox value="Ver Reportes" switch
                            >Ver Reportes</b-form-checkbox
                          >
                          <b-form-checkbox value="anula-factura" switch
                            >Anulacion - Refacturacion</b-form-checkbox
                          >
                        </div>
                      </div>
                    </b-form-checkbox-group>
                  </div>
                </div>
              </div>
            </div>
            <div class="w-100"></div>
            <div class="col col-lg mt-1 mb-n3">
              <button @click="updateUser()" class="btn btn-sm btn-success">
                Editar
              </button>
              <button
                @click="
                  userInfo = [];
                  userPermissions = [];
                "
                class="btn btn-sm btn-danger"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
        <!-- Fin editar información del usuario -->
      </transition>
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
      search: "",
      filter: "",
      usersList: [],
      fieldsUsersList: [
        { key: "user", label: "Usuario" },
        { key: "nombre", label: "Nombre" },
        { key: "cuil", label: "CUIL/DNI" },
        { key: "domicilio", label: "Domicilio" },
        { key: "telefono", label: "Teléfono" },
        { key: "estado", label: "Estado" },
        "acciones",
      ],
      actualPageUsersList: 1,
      userInfo: [],
      userPermissions: [],
      newPassword: "",
    };
  },
  methods: {
    getUsers: async function () {
      try {
        const response = await axios.post("api/getUsers", {
          filter: this.filter,
          search: this.search,
          page: this.actualPageUsersList,
        });
        this.usersList = response.data;
      } catch (error) {
        throw Error(error);
      }
    },
    changeState: async function (id) {
      try {
        const response = await axios.post("api/changeState", {
          id: id,
          filter: this.filter,
          search: this.search,
          page: this.actualPageUsersList,
          userid: window.user.user.id,
        });
        alertify.success("El estado del usuario se ha cambiado correctamente");
      } catch (error) {
        alertify.error("Ha ocurrido un error al cambiar el estado del usuaro");
        throw Error(error);
      }
    },
    getUserInfo: async function (id) {
      try {
        const response = await axios.post("api/getUserInfo", {
          id: id,
        });
        this.userInfo = response.data.user;
        this.userPermissions = response.data.permissions;
      } catch (error) {
        alertify.error("No se ha podido obtener la información del usuario");
      }
    },
    updateUser: async function () {
      var errors = true;

      if (
        this.userInfo.nombre == "" ||
        this.userInfo.cuil == "" ||
        this.userInfo.domicilio == "" ||
        this.userInfo.telefono == "" ||
        this.userInfo.user == ""
      ) {
        errors = false;
      }

      if (errors) {
        try {
          const response = await axios.post("api/updateUser", {
            id: this.userInfo.id,
            nombre: this.userInfo.nombre,
            usuario: this.userInfo.user,
            dni: this.userInfo.cuil,
            telefono: this.userInfo.telefono,
            direccion: this.userInfo.domicilio,
            permisos: this.userPermissions,
            filter: this.filter,
            search: this.search,
            page: this.actualPageUsersList,
            userid: window.user.user.id,
          });
          this.userInfo = [];
          this.userPermissions = [];
          alertify.success("Información del usuario actualizada correctamente");
        } catch (error) {
          alertify.error("ERRRRRRRRRRROOOOOOOOOOOORRRRRRRRRR");
        }
      } else {
        this.$refs["updateUser"].classList.remove("needs-validation");
        this.$refs["updateUser"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    deleteUser: async function (id) {
      alertify.confirm(
        "Eliminar usuario",
        "¿Esta segur@ que desea elminar el usuario?",
        () => {
          axios
            .post("api/deleteUser", {
              id: id,
              filter: this.filter,
              search: this.search,
              page: this.actualPageUsersList,
              userid: window.user.user.id,
            })
            .then((response) => {
              alertify.success("Usuario eliminado correctamente");
            })
            .catch((error) => {
              alertify.error("Ha ocurrido un error al eliminar al usuario");
            });
        },
        function () {}
      );
    },
    resetPassword: async function (id) {
      try {
        const response = await axios.post("api/resetPassword", {
          id: id,
          userid: window.user.user.id,
        });
        this.newPassword = response.data;
        this.$refs["modalNewPassword"].show();
      } catch (error) {
        alertify.error("Error al resetear la contraseña");
      }
    },
  },
  beforeMount() {
    window.Echo.channel("channel-usersTable").listen("UsersTable", (e) => {
      this.usersList = e.users;
    });
  },
};
</script>
