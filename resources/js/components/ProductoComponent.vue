<template>
  <div class="ml-1 mr-1">
    <b-modal
      ref="modalAumentarPrecios"
      id="modalAumentarPrecios"
      size="xl"
      hide-footer
      title="Aumentar precios de productos"
    >
      <div class="row" ref="aumentarPrecios">
        <div class="col col-lg">
          <div class="input-group form-group">
            <input
              v-model="proveedorSeleccionado"
              placeholder="Seleccione el proveedor al que corresponde el producto"
              onkeypress="return false;"
              type="text"
              class="bg-transparent form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                v-b-modal.modalSeleccionarProveedor
                @click="
                proveedorBuscado = '';
                listadoProveedores = [];
                paginaActualListadoProveedores = 1;
                "
                class="btn btn-sm btn-primary"
                style=" box-shadow: none !important;"
                type="button"
              >Seleccionar</button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Porcentaje</label>
          <input
            v-model="porcentaje"
            type="number"
            step="0.01"
            id="codigoBarras"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center">
          <button @click="aumentarPrecio()" class="btn btn-sm btn-success">Aumentar precios</button>
          <button @click="bajarPrecio()" class="btn btn-sm btn-danger">Bajar precios</button>
        </div>
      </div>
    </b-modal>

    <b-modal
      ref="modalAumentarStock"
      id="modalAumentarStock"
      size="sm"
      hide-footer
      title="Incrementar el stock"
    >
      <div class="row" ref="incrementarStock">
        <div class="col col-lg form-group">
          <label
            class="font-weight-bold"
            style="font-size:12px;"
            required
          >Ingrese la cantidad de productos que ingresan</label>
          <input
            v-model="stockIncrement.stock"
            type="number"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center mt-1">
          <button @click="incrementarStock()" class="btn btn-sm btn-success">Incrementar Stock</button>
          <button
            @click="stockIncrement= {stock : '',id : ''}; $bvModal.hide('modalAumentarStock')"
            class="btn btn-sm btn-danger"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <b-modal
      ref="modalSeleccionarProveedor"
      id="modalSeleccionarProveedor"
      size="lg"
      hide-footer
      title="Seleccionar proveedor"
    >
      <div class="row">
        <div class="col col-lg input-group">
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
          <transition name="fade">
            <b-table
              v-if="listadoProveedores.total > 0"
              class="mt-1"
              style="font-size:12px;"
              outlined
              small
              hover
              fixed
              no-border-collapse
              head-variant="dark"
              :tbody-transition-props="{name:'fade'}"
              :items="listadoProveedores.data"
              :fields="fieldsListadoProveedores"
            >
              <template v-slot:cell(acciones)="row">
                <div class="text-center">
                  <button
                    @click="selectProveedor(row.item.id, row.item.nombre)"
                    class="btn btn-sm btn-success"
                  >Seleccionar</button>
                </div>
              </template>
            </b-table>
          </transition>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
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
      </div>
    </b-modal>

    <b-modal
      ref="modalEditarProducto"
      id="modalEditarProducto"
      size="xl"
      hide-footer
      title="Editar información del producto"
    >
      <div class="row" ref="editarProducto">
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" required>Codigo de barras</label>
          <input
            v-model="productoNuevo.codigoBarras"
            type="text"
            id="codigoBarras"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Descripción</label>
          <input
            v-model="productoNuevo.descripcion"
            type="text"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Stock</label>
          <input
            v-model="productoNuevo.stock"
            type="number"
            id="telefono"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">
            IVA
            <span class="text-danger">*</span>
          </label>
          <b-form-group required>
            <b-form-radio-group
              id="btn-radios-2"
              v-model="productoNuevo.iva"
              :options="optionsIva"
              buttons
              class="btn-block font-weight-bold"
              button-variant="outline-primary"
              size="sm"
              name="radio-btn-outline"
              required
            ></b-form-radio-group>
          </b-form-group>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de costo</label>
          <input
            v-model="productoNuevo.precio_costo"
            step="0.01"
            type="number"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 1</label>
          <input
            v-model="productoNuevo.precio_venta_1"
            type="number"
            step="0.01"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 2</label>
          <input
            v-model="productoNuevo.precio_venta_2"
            type="number"
            step="0.01"
            class="form-control form-control-sm"
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 3</label>
          <input
            v-model="productoNuevo.precio_venta_3"
            type="number"
            step="0.01"
            class="form-control form-control-sm"
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
          <div class="input-group form-group">
            <input
              v-model="proveedorSeleccionado"
              placeholder="Seleccione el proveedor al que corresponde el producto"
              onkeypress="return false;"
              type="text"
              class="bg-transparent form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                v-b-modal.modalSeleccionarProveedor
                @click="
                proveedorBuscado = '';
                listadoProveedores = [];
                paginaActualListadoProveedores = 1;
                "
                class="btn btn-sm btn-primary"
                style=" box-shadow: none !important;"
                type="button"
              >Seleccionar</button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center mt-1">
          <button @click="editProducto()" class="btn btn-sm btn-success">Editar producto</button>
          <button
            @click="productoNuevo = {
                codigoBarras: '',
                descripcion: '',
                stock: '',
                iva: '',
                precio_costo: '',
                precio_venta_1: '',
                precio_venta_2: '',
                precio_venta_3: '',
                id_proveedor: '',
                };
                proveedorSeleccionado = '';
                $bvModal.hide('modalEditarProducto')"
            class="btn btn-sm btn-danger"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <b-modal
      ref="modalCrearProducto"
      id="modalCrearProducto"
      size="xl"
      hide-footer
      title="Registrar un nuevo producto"
    >
      <div class="row" ref="registroProducto">
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;" required>Codigo de barras</label>
          <input
            v-model="productoNuevo.codigoBarras"
            type="text"
            id="codigoBarras"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Descripción</label>
          <input
            v-model="productoNuevo.descripcion"
            type="text"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Stock</label>
          <input
            v-model="productoNuevo.stock"
            type="number"
            id="telefono"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">
            IVA
            <span class="text-danger">*</span>
          </label>
          <b-form-group required>
            <b-form-radio-group
              id="btn-radios-2"
              v-model="productoNuevo.iva"
              :options="optionsIva"
              buttons
              class="btn-block font-weight-bold"
              button-variant="outline-primary"
              size="sm"
              name="radio-btn-outline"
              required
            ></b-form-radio-group>
          </b-form-group>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de costo</label>
          <input
            v-model="productoNuevo.precio_costo"
            step="0.01"
            type="number"
            class="form-control form-control-sm"
            required
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 1</label>
          <input
            v-model="productoNuevo.precio_venta_1"
            type="number"
            class="form-control form-control-sm"
            step="0.01"
            required
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 2</label>
          <input
            v-model="productoNuevo.precio_venta_2"
            type="number"
            step="0.01"
            class="form-control form-control-sm"
          />
        </div>
        <div class="col col-lg form-group">
          <label class="font-weight-bold" style="font-size:12px;">Precio de venta de la lista 3</label>
          <input
            v-model="productoNuevo.precio_venta_3"
            type="number"
            step="0.01"
            class="form-control form-control-sm"
          />
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
          <div class="input-group form-group">
            <input
              v-model="proveedorSeleccionado"
              placeholder="Seleccione el proveedor al que corresponde el producto"
              onkeypress="return false;"
              type="text"
              class="bg-transparent form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                v-b-modal.modalSeleccionarProveedor
                @click="
                proveedorBuscado = '';
                listadoProveedores = [];
                paginaActualListadoProveedores = 1;
                "
                class="btn btn-sm btn-primary"
                style=" box-shadow: none !important;"
                type="button"
              >Seleccionar</button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg text-center mt-1">
          <button @click="saveProducto()" class="btn btn-sm btn-success">Registrar producto</button>
          <button
            @click="productoNuevo = {
                codigoBarras: '',
                descripcion: '',
                stock: '',
                iva: '',
                precio_costo: '',
                precio_venta_1: '',
                precio_venta_2: '',
                precio_venta_3: '',
                id_proveedor: '',
                };
                proveedorSeleccionado = '';
                $bvModal.hide('modalCrearProducto')"
            class="btn btn-sm btn-danger"
          >Cancelar</button>
        </div>
      </div>
    </b-modal>

    <div class="row card mt-1">
      <div class="card-header text-primary bg-transparent font-weight-bold text-left">
        Productos
        <button
          v-if="$auth.can('Crear Productos')"
          v-b-modal.modalCrearProducto
          @click="productoNuevo = {
                codigoBarras: '',
                descripcion: '',
                stock: '',
                iva: '',
                precio_costo: '',
                precio_venta_1: '',
                precio_venta_2: '',
                precio_venta_3: '',
                id_proveedor: '',
                };
                proveedorSeleccionado = '';"
          class="font-weight-bold float-right btn btn-sm btn-success ml-1"
        >Añadir producto</button>
        <button
          v-if="$auth.can('Aumentar Bajar Precios Productos')"
          v-b-modal.modalAumentarPrecios
          class="font-weight-bold float-right btn btn-sm btn-info"
        >Aumentar y bajar precios</button>
      </div>

      <div class="card-body row">
        <div class="col col-lg text-center">
          <div class="input-group">
            <input
              v-model="productoBuscado"
              type="text"
              class="form-control form-control-sm"
              placeholder="Ingrese el código de barra o el nombre del producto"
              v-on:keyup.enter="getProductos"
            />
            <div class="input-group-append">
              <button
                @click="getProductos()"
                class="btn btn-sm btn-primary"
                style=" box-shadow: none !important;"
                type="button"
              >Buscar producto</button>
            </div>
          </div>
        </div>
        <div class="w-100"></div>
        <transition name="fade">
          <div class="col col-lg" v-if="listadoProductos != ''">
            <b-table
              id="table-transition-example"
              :items="listadoProductos.data"
              :fields="fieldsListadoProductos"
              class="mt-1"
              style="font-size:12px;"
              outlined
              small
              hover
              fixed
              no-border-collapse
              head-variant="dark"
              :tbody-transition-props="{name:'fade'}"
            >
              <template v-slot:cell(descripcion)="row">
                <span class="font-weight-bold">{{row.item.Descripcion}}</span>
              </template>

              <template v-slot:cell(acciones)="row">
                <button
                  @click="getInfoEditProducto(row.item.IdProducto)"
                  class="btn btn-sm btn-success"
                  v-if="$auth.can('Editar Productos')"
                >
                  <i class="fas fa-pen"></i>
                </button>
                <button
                  v-if="$auth.can('Aumentar Stock')"
                  @click="stockIncrement.id = row.item.IdProducto"
                  v-b-modal.modalAumentarStock
                  class="btn btn-sm btn-info"
                >
                  <i class="fa fa-plus"></i>
                </button>
                <button
                  v-if="$auth.can('Eliminar Productos')"
                  @click="deleteProducto(row.item.IdProducto)"
                  class="btn btn-sm btn-danger"
                >
                  <i class="fas fa-trash-alt"></i>
                </button>
                <button
                  v-if="$auth.can('Imprimir Etiquetas')"
                  @click="printer(row.item.IdProducto)"
                  class="btn btn-sm btn-dark"
                >
                  <i class="fa fa-print"></i>
                </button>
              </template>

              <template v-slot:cell(precio_venta_1)="row">
                <span>${{row.item.precio_venta_1}}</span>
              </template>

              <template v-slot:cell(precio_venta_2)="row">
                <span>${{row.item.precio_venta_2}}</span>
              </template>

              <template v-slot:cell(precio_venta_3)="row">
                <span>${{row.item.precio_venta_3}}</span>
              </template>

              <template v-slot:cell(IVA)="row">
                <span>{{row.item.IVA}}%</span>
              </template>

              <template v-slot:cell(precio_costo)="row">
                <span>${{row.item.precio_costo}}</span>
              </template>

              <template v-slot:cell(cantidad)="row">
                <input
                  style="font-size:12px;"
                  @change="validarCantidad(row.index)"
                  type="number"
                  v-model="row.item.cantidad"
                  class="my-auto bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                />
              </template>

              <template v-slot:cell(subtotal)="row" class="text-center my-auto">
                <span class="text-success font-weight-bold">${{(row.item.subtotal).toFixed(2)}}</span>
              </template>
            </b-table>
          </div>
        </transition>
        <div class="w-100"></div>
        <transition name="fade">
          <div class="col col-lg">
            <div
              v-if="listadoProductos.total != undefined && listadoProductos.total > 10"
              @click="getProductos()"
            >
              <b-pagination
                aria-controls="listado-productos"
                size="sm"
                style="font-size:12px;"
                v-model="paginaActualListadoProductos"
                :total-rows="listadoProductos.total"
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
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      productoBuscado: "",
      listadoProductos: [],
      fieldsListadoProductos: [
        { key: "CodBarras", label: "Código de barras" },
        { key: "Descripcion", label: "Descripción" },
        { key: "stock", label: "Stock" },
        { key: "precio_costo", label: "Precio de costo" },
        { key: "precio_venta_1", label: "Precio 1" },
        { key: "precio_venta_2", label: "Precio 2" },
        { key: "precio_venta_3", label: "Precio 3" },
        { key: "IVA", label: "IVA" },
        { key: "modif_precio", label: "Última modif. precio" },
        "acciones"
      ],
      paginaActualListadoProductos: 1,
      productoNuevo: {
        codigoBarras: "",
        descripcion: "",
        stock: "",
        iva: "",
        precio_costo: "",
        precio_venta_1: "",
        precio_venta_2: "",
        precio_venta_3: "",
        id_proveedor: ""
      },
      optionsIva: [
        { text: "21%", value: 21 },
        { text: "10,5%", value: 10.5 }
      ],
      create: false,
      listadoProveedores: [],
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
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
      proveedorSeleccionado: null,
      stockIncrement: {
        stock: "",
        id: ""
      },
      porcentaje: ""
    };
  },
  methods: {
    getProductos: function() {
      axios
        .post("api/getProductos", {
          producto: this.productoBuscado,
          page: this.paginaActualListadoProductos
        })
        .then(res => {
          this.listadoProductos = res.data;
        })
        .catch(err => {
          console.log(err);
          alertify.error("No se han podido cargar los productos");
        });
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
    printer(item){
      console.log("Mandamos a Imprimir " + item);
    },
    selectProveedor: function(id, nombre) {
      this.productoNuevo.id_proveedor = id;
      this.proveedorSeleccionado = nombre;
      alertify.success("Proveedor seleccionado correctamente");
      this.$refs["modalSeleccionarProveedor"].hide();
    },
    saveProducto: async function() {
      var errors = true;

      if (
        this.productoNuevo.codigoBarras == "" ||
        this.productoNuevo.descripcion == "" ||
        this.productoNuevo.stock == "" ||
        this.productoNuevo.iva == "" ||
        this.productoNuevo.precio_costo == "" ||
        this.productoNuevo.precio_venta_1 == ""
      ) {
        errors = false;
      }

      if (errors) {
        axios
          .post("api/saveProducto", {
            CodBarras: this.productoNuevo.codigoBarras,
            descripcion: this.productoNuevo.descripcion,
            stock: this.productoNuevo.stock,
            iva: this.productoNuevo.iva,
            precio_costo: this.productoNuevo.precio_costo,
            precio_venta_1: this.productoNuevo.precio_venta_1,
            precio_venta_2: this.productoNuevo.precio_venta_2,
            precio_venta_3: this.productoNuevo.precio_venta_3,
            id_proveedor: this.productoNuevo.id_proveedor,
            userid: window.user.user.id
          })
          .then(response => {
            console.log(response.data);
            alertify.success("Producto cargado correctamente");
            this.$refs["modalCrearProducto"].hide();
            this.productoNuevo = {
              codigoBarras: "",
              descripcion: "",
              stock: "",
              iva: "",
              precio_costo: "",
              precio_venta_1: "",
              precio_venta_2: "",
              precio_venta_3: "",
              id_proveedor: ""
            };
            this.proveedorSeleccionado = "";
            this.getProductos();
          })
          .catch(error => {
            if (error.response.data.errors.CodBarras) {
              this.productoNuevo.codigoBarras = "";
              this.$refs["registroProducto"].classList.add("was-validated");
              alertify.error(
                "El codigo de barras ingresado ya corresponde a un producto en el sistema"
              );
              errors = false;
            }
          });
      } else {
        this.$refs["registroProducto"].classList.remove("needs-validation");
        this.$refs["registroProducto"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    getInfoEditProducto: async function(id) {
      console.log(id);
      try {
        const response = await axios.post("api/getInfoEditProducto", {
          id: id
        });
        console.log(response);
        this.productoNuevo = {
          id: id,
          codigoBarras: response.data.CodBarras,
          descripcion: response.data.Descripcion,
          stock: response.data.stock,
          iva: response.data.IVA,
          precio_costo: response.data.precio_costo,
          precio_venta_1: response.data.precio_venta_1,
          precio_venta_2: response.data.precio_venta_2,
          precio_venta_3: response.data.precio_venta_3,
          id_proveedor: response.data.id_proveedor
        };

        this.proveedorSeleccionado = response.data.Nombre;

        this.$refs["modalEditarProducto"].show();
      } catch (error) {}
    },
    editProducto: async function() {
      var errors = true;

      if (        
        this.productoNuevo.codigoBarras == "" ||
        this.productoNuevo.descripcion == "" ||
        this.productoNuevo.precio_costo == "" ||
        this.productoNuevo.precio_venta_1 == ""
      ) {
        errors = false;
        console.log("ENtra en el IF?");
        console.log(this.productoNuevo);
      }
      //console.log(this.productoNuevo);
      //console.log(errors);

      if (errors) {
        axios
          .post("api/editProducto", {
            IdProducto: this.productoNuevo.id,
            CodBarras: this.productoNuevo.codigoBarras,
            descripcion: this.productoNuevo.descripcion,
            stock: this.productoNuevo.stock,
            iva: this.productoNuevo.iva,
            precio_costo: this.productoNuevo.precio_costo,
            precio_venta_1: this.productoNuevo.precio_venta_1,
            precio_venta_2: this.productoNuevo.precio_venta_2,
            precio_venta_3: this.productoNuevo.precio_venta_3,
            id_proveedor: this.productoNuevo.id_proveedor
          })
          .then(response => {
            console.log(response.data);
            alertify.success("Datos del producto actualizados correctamente");
            this.$refs["modalEditarProducto"].hide();
            this.productoNuevo = {
              codigoBarras: "",
              descripcion: "",
              stock: "",
              iva: "",
              precio_costo: "",
              precio_venta_1: "",
              precio_venta_2: "",
              precio_venta_3: "",
              id_proveedor: ""
            };
            this.proveedorSeleccionado = "";
            this.getProductos();
          })
          .catch(error => {
            if (error.response.data.errors.CodBarras) {
              this.productoNuevo.codigoBarras = "";
              this.$refs["editarProducto"].classList.add("was-validated");
              alertify.error(
                "El codigo de barras ingresado ya corresponde a un producto en el sistema"
              );
              errors = false;
            }
          });
      } else {
        
        this.$refs["editarProducto"].classList.remove("needs-validation");
        this.$refs["editarProducto"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    incrementarStock: async function() {
      var errors = true;

      if (this.stockIncrement.stock == "" || this.stockIncrement.id == "") {
        errors = false;
      }

      if (errors) {
        try {
          const response = await axios.post("api/incrementarStock", {
            stock: this.stockIncrement.stock,
            id: this.stockIncrement.id,
            userid: window.user.user.id
          });
          this.getProductos();
          this.stockIncrement = {
            stock: "",
            id: ""
          };
          this.$refs["modalAumentarStock"].hide();
          alertify.success("Stock incrementado correctamente");
        } catch (error) {
          alertify.error("Error al incrementar stock");
        }
      } else {
        this.$refs["incrementarStock"].classList.remove("needs-validation");
        this.$refs["incrementarStock"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    deleteProducto: function(id) {
      alertify.confirm(
        "Eliminar producto",
        "¿Esta segur@ que desea elminar el producto?",
        () => {
          axios
            .post("api/deleteProducto", {
              id: id,
              userid: window.user.user.id
            })
            .then(response => {
              alertify.success("Producto eliminado");
              this.getProductos();
            })
            .catch(error => {
              alertify.error("Ha ocurrido un error al eliminar el producto");
              console.log(error);
            });
        },
        function() {
          alertify.error("Se ha cancelado");
        }
      );
    },
    aumentarPrecio: function() {
      var errors = true;

      if (this.porcentaje == "" || this.productoNuevo.id_proveedor == "") {
        errors = false;
      }

      if (errors) {
        axios
          .post("/api/aumentoProducto", {
            idProveedor: this.productoNuevo.id_proveedor,
            porcentaje: this.porcentaje,
            userid: window.user.user.id
          })
          .then(response => {
            this.productoNuevo.id_proveedor = "";
            this.proveedorSeleccionado = "";
            this.porcentaje = "";
            this.$refs["modalAumentarPrecios"].hide();
            alertify.success(
              "Los productos se han aumentado de precio correctamente"
            );
            this.getProductos();
          })
          .catch(error => {
            alertify.error(
              "Ha ocurrido un error a la hora de realizar el aumento"
            );
          });
      } else {
        this.$refs["aumentarPrecios"].classList.remove("needs-validation");
        this.$refs["aumentarPrecios"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    bajarPrecio: function() {
      var errors = true;

      if (this.porcentaje == "" || this.productoNuevo.id_proveedor == "") {
        errors = false;
      }

      if (errors) {
        axios
          .post("/api/bajarProducto", {
            idProveedor: this.productoNuevo.id_proveedor,
            porcentaje: this.porcentaje,
            userid: window.user.user.id
          })
          .then(response => {
            this.productoNuevo.id_proveedor = "";
            this.proveedorSeleccionado = "";
            this.porcentaje = "";
            this.$refs["modalAumentarPrecios"].hide();
            alertify.success(
              "Los productos se han bajado de precio correctamente"
            );
            this.getProductos();
          })
          .catch(error => {
            alertify.error(
              "Ha ocurrido un error a la hora de realizar la baja de precios"
            );
          });
      } else {
        this.$refs["aumentarPrecios"].classList.remove("needs-validation");
        this.$refs["aumentarPrecios"].classList.add("was-validated");
        alertify.error(
          "Por favor complete los datos que estan resaltados en rojo"
        );
      }
    },
    generarPDF: function() {
      let url = "api/pdfProductos/" + this.proveedorFiltro;

      axios
        .get(url, {
          responseType: "blob"
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "productos.pdf");
          document.body.appendChild(link);
          link.click();
        });
    },
    listarProveedores: function() {
      axios
        .get("/api/readProveedores")
        .then(response => {
          this.listadoProveedores = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    mostrarFormularioCreate: function() {
      this.create = true;
      this.listarProveedores();
    },
    ocultarFormularioCreate: function() {
      this.create = false;
      //Resetear cada uno de los campos del formulario
      this.productoNuevo = {
        descripcion: "",
        stock: "",
        precio_costo: "",
        precio_venta_1: "",
        precio_venta_2: "",
        precio_venta_3: "",
        id_proveedor: ""
      };
    },
    crearNuevoProducto: function() {
      axios
        .post("/api/createProducto", {
          descripcion: this.productoNuevo.descripcion,
          stock: this.productoNuevo.stock,
          precio_costo: this.productoNuevo.precio_costo,
          precio_venta_1: this.productoNuevo.precio_venta_1,
          precio_venta_2: this.productoNuevo.precio_venta_2,
          precio_venta_3: this.productoNuevo.precio_venta_3,
          id_proveedor: this.productoNuevo.id_proveedor,
          userid: window.user.user.id
        })
        .then(response => {
          //Resetear cada uno de los campos del formulario
          this.productoNuevo = {
            descripcion: "",
            stock: "",
            precio_costo: "",
            precio_venta_1: "",
            precio_venta_2: "",
            precio_venta_3: "",
            id_proveedor: ""
          };

          alertify.success("Producto creado correctamente");
        })
        .catch(error => {
          alertify.error(
            "Ha ocurrido un error a la hora de guardar el producto en la base de datos"
          );
        });
    },
    listarProductos: function(page) {
      if (
        this.proveedorFiltroBool == false ||
        this.proveedorFiltro == "nofilter" ||
        this.proveedorFiltro == ""
      ) {
        this.proveedorFiltroBool = false;

        axios
          .post("/api/readProductos", {
            page: page,
            idProveedor: this.proveedorFiltro,
            productoEspecifico: this.productoEspecifico
          })
          .then(response => {
            this.listadoProductos = response.data.productos.data;
            this.pagination = response.data.pagination;
            console.log(response.data.productos.data);
          })
          .catch(error => {
            console.log(error);
          });
      } else {
        this.proveedorFiltroBool = true;

        axios
          .post("/api/readProductosEspecificos", {
            page: page,
            idProveedor: this.proveedorFiltro,
            productoEspecifico: this.productoEspecifico
          })
          .then(response => {
            this.listadoProductos = response.data.productos.data;
            this.pagination = response.data.pagination;
            console.log(response.data.productos.data);
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    changePage: function(page) {
      this.pagination.current_page = page;
      this.listarProductos(page);
    }
  }
};
</script>
