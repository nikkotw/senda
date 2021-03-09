<template>
  <div class="ml-1 mr-1">
    <div class="row card mt-1">
      <div class="card-header text-primary bg-transparent font-weight-bold text-left">Usuarios</div>

      <div class="card-body">
        <div class="row text-center" ref="registroUsuario">
          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.nombre"
              required
              type="text"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">Nombre y apellido</label>
          </div>
          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.dni"
              required
              type="number"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">CUIL/DNI</label>
          </div>
          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.direccion"
              required
              type="text"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">Dirección</label>
          </div>
          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.telefono"
              required
              type="number"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">Teléfono</label>
          </div>

          <div class="w-100"></div>

          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.usuario"
              required
              type="text"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">Nombre de usuario</label>
          </div>

          <div style="font-size:12px;" class="col col-lg text-center form-group">
            <input
              v-model="usuario.password"
              required
              type="password"
              class="form-control form-control-sm"
            />
            <label class="font-weight-bold">Contraseña</label>
          </div>

          <div class="w-100"></div>

          <div class="col col-xl text-left">
            <div class="card">
              <div class="card-header bg-dark text-white font-weight-bold">Listado de permisos</div>
              <div class="card-body border-0 row">
                <div class="col col-xl">
                  <b-form-checkbox-group v-model="permisos" stacked switches>
                    <div class="card">
                      <div class="card-header font-weight-bold">Caja</div>
                      <div class="card-body">
                        <b-form-checkbox value="Abrir Caja" switch>Realizar apertura de caja</b-form-checkbox>
                        <b-form-checkbox value="Cerrar Caja" switch>Hacer cierre caja</b-form-checkbox>
                        <b-form-checkbox
                          value="Extraer Dinero Caja"
                          switch
                        >Extraer dinero de la caja</b-form-checkbox>
                           <b-form-checkbox
                          value="Ingreso Dinero Caja"
                          switch
                        >Ingreso de dinero en caja</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Listado Ventas"
                          switch
                        >Ver listado de las ventas a realizar</b-form-checkbox>
                        <b-form-checkbox value="Ver Detalle Ventas" switch>Ver detalle de las ventas</b-form-checkbox>
                        <b-form-checkbox value="Rechazar Ventas" switch>Rechazar ventas</b-form-checkbox>
                        <b-form-checkbox value="Facturar Venta" switch>Realizar facturación</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Listado Ventas CC"
                          switch
                        >Ver listado de la ventas a realizar por cuenta corriente</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Realizar Ventas</div>
                      <div class="card-body">
                        <b-form-checkbox value="Realizar Ventas" switch>Realizar ventas</b-form-checkbox>
                        <b-form-checkbox value="Pedidos" switch>Pedidos</b-form-checkbox>
                        <b-form-checkbox value="Presupuestos" switch>Presupuestos</b-form-checkbox>
                        <b-form-checkbox value="Escuela" switch>Ticket ESCUELA</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Notas de crédito</div>
                      <div class="card-body">
                        <b-form-checkbox value="Emitir Nota Credito" switch>Emitir nota de crédito</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Cuentas Corrientes</div>
                      <div class="card-body">
                        <b-form-checkbox
                          value="Autorizar Ventas CC"
                          switch
                        >Autorizar ventas por cuenta corriente</b-form-checkbox>
                        <b-form-checkbox
                          value="Realizar Ingreso Cobranza"
                          switch
                        >Realizar ingresos de cobranza</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Ventas CC"
                          switch
                        >Visualizar ventas por cuenta corriente de clientes</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Ingresos Cobranza"
                          switch
                        >Visualizar ingresos de cobranza de clientes</b-form-checkbox>
                      </div>
                    </div>
                  </b-form-checkbox-group>
                </div>

                <div class="col col-xl">
                  <b-form-checkbox-group v-model="permisos" stacked switches>
                    <div class="card">
                      <div class="card-header font-weight-bold">Proveedores</div>

                      <div class="card-body">
                        <b-form-checkbox value="Crear Proveedores" switch>Crear proveedores</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Info Proveedores"
                          switch
                        >Ver información de proveedores</b-form-checkbox>
                        <b-form-checkbox value="Editar Proveedores" switch>Editar proveedores</b-form-checkbox>
                        <b-form-checkbox value="Eliminar Proveedores" switch>Eliminar proveedores</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Realizar pedidos</div>
                      <div class="card-body">
                        <b-form-checkbox value="Crear Pedidos" switch>Realizar pedidos a proveedores</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Gestión de pedidos</div>
                      <div class="card-body">
                        <b-form-checkbox
                          value="Descargar Detalle Pedido"
                          switch
                        >Descargar información de pedidos</b-form-checkbox>

                        <b-form-checkbox value="Ver Info Pedidos" switch>Ver detalle de pedidos</b-form-checkbox>

                        <b-form-checkbox
                          value="Gestionar Pagos Pedido"
                          switch
                        >Gestionar pagos de pedidos</b-form-checkbox>

                        <b-form-checkbox value="Eliminar Pedidos" switch>Eliminar pedidos</b-form-checkbox>
                      </div>
                    </div>
                  </b-form-checkbox-group>
                </div>

                <div class="col col-xl">
                  <b-form-checkbox-group v-model="permisos" stacked switches>
                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Productos</div>
                      <div class="card-body">
                        <b-form-checkbox value="Crear Productos" switch>Crear productos</b-form-checkbox>
                        <b-form-checkbox
                          value="Editar Productos"
                          switch
                        >Editar información de los productos</b-form-checkbox>
                        <b-form-checkbox value="Eliminar Productos" switch>Eliminar productos</b-form-checkbox>
                        <b-form-checkbox
                          value="Aumentar Bajar Precios Productos"
                          switch
                        >Aumentar o disminuir el precio de los productos</b-form-checkbox>
                        <b-form-checkbox value="Aumentar Stock" switch>Aumentar Stock</b-form-checkbox>
                        
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Stock</div>
                      <div class="card-body">
                        <b-form-checkbox
                          value="Realizar Transferencias"
                          switch
                        >Realizar transferencias</b-form-checkbox>
                        <b-form-checkbox
                          value="Listado Transferencias"
                          switch
                        >Acceder al listado de transferencias</b-form-checkbox>
                        <b-form-checkbox value="Recepciones" switch>Recepciones</b-form-checkbox>
                      </div>
                    </div>
                  </b-form-checkbox-group>
                </div>
                <div class="col col-xl">
                  <b-form-checkbox-group v-model="permisos" stacked switches>
                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Clientes</div>
                      <div class="card-body">
                        <b-form-checkbox value="Crear Cliente" switch>Registrar clientes</b-form-checkbox>
                        <b-form-checkbox
                          value="Editar Cliente"
                          switch
                        >Editar información de clientes</b-form-checkbox>
                        <b-form-checkbox value="Eliminar Cliente" switch>Eliminar clientes</b-form-checkbox>
                        <b-form-checkbox
                          value="Ver Informacion Cliente"
                          switch
                        >Ver información de los clientes</b-form-checkbox>
                      </div>
                    </div>

                    <div class="card mt-1">
                      <div class="card-header font-weight-bold">Administración</div>
                      <div class="card-body">
                        <b-form-checkbox value="Crear Usuario" switch>Crear usuarios</b-form-checkbox>
                        <b-form-checkbox
                          value="Editar Usuario"
                          switch
                        >Editar información de usuarios</b-form-checkbox>
                        <b-form-checkbox value="Eliminar Usuario" switch>Eliminar usuarios</b-form-checkbox>
                        <b-form-checkbox
                          value="Restablecer Contraseña"
                          switch
                        >Restablecer contraseña</b-form-checkbox>
                        <b-form-checkbox value="Acceder Dashboard" switch>Acceder al dashboard</b-form-checkbox>
                      </div>
                    </div>
                     <div class="card mt-1">
                        <div class="card-header font-weight-bold">Reportes</div>
                        <div class="card-body">
                          <b-form-checkbox value="Ver Reportes" switch>Ver Reportes</b-form-checkbox>
                        <b-form-checkbox
                          value="anula-factura"
                          switch
                        >Anulacion - Refacturacion</b-form-checkbox>
                        </div>
                      </div>
                    
                  </b-form-checkbox-group>
                </div>
              </div>
            </div>
          </div>
          <div class="w-100"></div>
          <div class="col col-lg mt-1 mb-n3">
            <button @click="saveUsuario()" class="btn btn-sm btn-success">Registrar Usuario</button>
            <button
              @click="usuario = {
                nombre: '',
                dni: '',
                direccion: '',
                telefono: '',
                usuario: '',
                password: ''
                };
                permisos = [];"
              class="btn btn-sm btn-danger"
            >Cancelar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      usuario: {
        nombre: "",
        dni: "",
        direccion: "",
        telefono: "",
        usuario: "",
        password: "",
      },
      permisos: [],
    };
  },
  methods: {
    saveUsuario: async function () {
      var errors = true;

      if (
        this.usuario.nombre == "" ||
        this.usuario.dni == "" ||
        this.usuario.direccion == "" ||
        this.usuario.telefono == "" ||
        this.usuario.usuario == "" ||
        this.usuario.password == ""
      ) {
        errors = false;
      }

      if (errors) {
        try {
          const response = await axios.post("api/saveUsuario", {
            nombre: this.usuario.nombre,
            usuario: this.usuario.usuario,
            password: this.usuario.password,
            dni: this.usuario.dni,
            telefono: this.usuario.telefono,
            direccion: this.usuario.direccion,
            permisos: this.permisos,
            userid: window.user.user.id,
          });
          alertify.success("Usuario Creado");
          this.usuario = {
            nombre: "",
            dni: "",
            direccion: "",
            telefono: "",
            usuario: "",
            password: "",
          };
          this.permisos = [];
        } catch (error) {
          if (error.response.data.errors.usuario) {
            this.usuario.usuario = "";
            alertify.error(
              "El nombre de usuario ya se encuentra registrado en el sistema"
            );
          }
          if (error.response.data.errors.dni) {
            this.usuario.dni = "";
            alertify.error(
              "El DNI/CUIL ya corresponde a un usuario registrado en el sistema"
            );
          }
          errors = false;
          this.$refs["registroUsuario"].classList.add("was-validated");
        }
      } else {
        this.$refs["registroUsuario"].classList.remove("needs-validation");
        this.$refs["registroUsuario"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
  },
};
</script>
