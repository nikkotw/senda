<template>
  <div class="ml-1 mr-1">
    <transition name="fade">
      <div v-if="show" class="row">
        <div class="col">
          <h2>Presupuesto</h2>
          <div class="card">
            <div class="card-header text-primary bg-transparent font-weight-bold text-left">
              Cliente
              <input
                style="width:30%;display:inline"
                type="Text"
                v-model="cliente"
                id="cliente"
                ref="inputCuit"
                v-on:keyup.enter="abreModal"
                class="form-control form-control-sm"
              />
            </div>
          </div>
        </div>

        <b-modal
          ref="buscaPresu"
          id="buscaPresu"
          size="xs"
          title="Buscar Presupuesto"
          ok-title="Buscar Presupuesto"
          @ok="buscarPresu()"
        >
          <div class="form-group label-floating mt-3">
            <label class="font-weight-bold" for="nroPresu">Presupuesto Nro</label>
            <input
              type="Text"
              v-model="buscaPresu"
              id="nroPresu"
              ref="nroPresu"
              class="form-control form-control-sm"
            />
          </div>
        </b-modal>

        <b-modal ref="my-modal" id="my-modal" size="xl" hide-footer>
          <h2 style="text-align:center;">Busqueda Clientes</h2>
          <div class="input-group">
            <input
              type="text"
              ref="clienteSearch"
              class="form-control form-control-sm"
              v-model="searchCliente"
              placeholder="Buscar clientes"
              v-focus
            />
            <div class="input-group-append"></div>
            <button
              class="btn btn-sm btn-primary btn-block mt-1"
              v-on:click="getClientes()"
              type="button"
            >Buscar</button>
          </div>
          <div>
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
                <button @click="setInfoCliente(row.item.id)" class="btn btn-sm btn-success">
                  <i class="fa fa-check"></i>
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
        </b-modal>

        <div class="w-100"></div>

        <div class="col col-xl mt-1">
          <div class="card">
            <div class="card-header text-primary bg-transparent font-weight-bold text-left">
              Detalle Presupuesto
              <button
                class="btn btn-primary float-right"
                v-b-modal.buscaPresu
              >Buscar Presupuesto</button>
              <span
                v-if="cantidadProductos>0"
                style="float:right;margin-right:50%"
              >Cantidad Productos : {{cantidadProductos}}</span>
            </div>

            <div class="col col-xl float-right mt-1 mb-n3" style="font-size:12px">
              <button
                v-b-modal.modal-add-producto
                @click="getProductosStock()"
                class="btn btn-sm btn-success float-right mr-1"
              >Añadir producto</button>
            </div>

            <div class="w-100"></div>

            <div class="card-body mb-n3" style="font-size:12px">
              <b-table
                id="table-transition-example"
                :items="detalleVenta"
                :fields="fieldsDetalleVenta"
                outlined
                small
                responsive
                hover
                fixed
                no-border-collapse
                head-variant="dark"
                :tbody-transition-props="{ name: 'fade' }"
              >
                <template v-slot:head(descripción)="data">
                  <span class="text-white font-weight-bold">
                    {{
                    data.label
                    }}
                  </span>
                </template>

                <template v-slot:head(precio_de_venta)="data">
                  <span class="text-white font-weight-bold">
                    {{
                    data.label
                    }}
                  </span>
                </template>

                <template v-slot:head(cantidad)="data">
                  <span class="text-white font-weight-bold">
                    {{
                    data.label
                    }}
                  </span>
                </template>

                <template v-slot:head(subtotal)="data">
                  <span class="text-white font-weight-bold">
                    {{
                    data.label
                    }}
                  </span>
                </template>

                <template v-slot:head(acciones)="data">
                  <span class="text-white font-weight-bold">
                    {{
                    data.label
                    }}
                  </span>
                </template>

                <template v-slot:cell(descripción)="row">
                  <span class="font-weight-bold">
                    {{
                    row.item.Descripcion
                    }}
                  </span>
                </template>

                <template v-slot:cell(acciones)="row">
                  <button
                    @click="quitarDetalleVenta(row.index)"
                    class="btn btn-sm btn-danger"
                    size="sm"
                    style=" box-shadow: none !important;"
                  >Quitar producto</button>
                </template>

                <template v-slot:cell(precio_de_venta)="row">
                  <select
                    @change="calcularTotal(row.index)"
                    style="font-size:12px;"
                    v-model="row.item.precio_venta"
                    class="bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                  >
                    <option :value="row.item.precio_venta_1">
                      Precio de venta 1: ${{
                      row.item.precio_venta_1
                      }}
                    </option>
                    <option v-if="row.item.precio_venta_2 != 0" :value="row.item.precio_venta_2">
                      Precio de venta 2: ${{
                      row.item.precio_venta_2
                      }}
                    </option>
                    <option v-if="row.item.precio_venta_3 != 0" :value="row.item.precio_venta_3">
                      Precio de venta 3: ${{
                      row.item.precio_venta_3
                      }}
                    </option>
                  </select>
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
                  <span class="text-success font-weight-bold">
                    ${{
                    row.item.subtotal.toFixed(2)
                    }}
                  </span>
                </template>
              </b-table>

              <div class="row">
                <div class="col col-xl">
                  <h6 class="float-right">
                    Subtotal:
                    <span
                      class="text-success font-weight-bold mt-1"
                    >${{ total.toFixed(2) }}</span>
                  </h6>
                </div>

                <div class="w-100"></div>

                <div class="col col-xl text-left"></div>

                <div class="col col-xl">
                  <h6 class="float-right">
                    Total:
                    <span class="text-success font-weight-bold mt-1">
                      ${{
                      total.toFixed(2)
                      }}
                    </span>
                  </h6>
                </div>

                <div class="w-100"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="w-100"></div>
        <div class="col col-xl mt-1">
          <div v-if="detalleVenta.length > 0 " class="form-group col">
            <button class="btn btn-sm btn-primary" @click="savePresu()">Guardar Presupuesto</button>

            <button class="btn btn-sm btn-danger" @click="cancelarPresu()">Cancelar Presupuesto</button>
          </div>
        </div>

        <b-modal id="modal-add-producto" size="xl" hide-footer title="Añadir producto a la compra">
          <div class="container-fluid">
            <div class="card">
              <div
                class="card-header text-primary bg-transparent font-weight-bold text-left"
              >Listado de productos</div>

              <div class="card-body">
                <div class="form-group">
                  <div class="form-group mt-1 text-center">
                    <div class="input-group">
                      <input
                        v-model="productoBuscado"
                        type="text"
                        ref="busqueda"
                        class="form-control form-control-sm"
                        placeholder="Ingrese el código de barra o el nombre del producto"
                        v-on:keyup.enter="getProductosStock()"
                        v-focus
                      />
                      <div class="input-group-append"></div>
                    </div>
                  </div>

                  <b-table
                    class="mt-1"
                    style="font-size:12px;"
                    id="listado-productos"
                    outlined
                    small
                    hover
                    responsive
                    no-border-collapse
                    head-variant="dark"
                    :items="listadoProductos.data"
                    :fields="fieldsListadoProductos"
                    :tbody-transition-props="{
                                            name: 'fade'
                                        }"
                  >
                    <template v-slot:head(CodBarras)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(Descripcion)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(stock)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(precio_venta_1)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(precio_venta_2)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(precio_venta_3)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:head(acciones)="data">
                      <span class="text-white font-weight-bold">{{ data.label }}</span>
                    </template>

                    <template v-slot:cell(CodBarras)="row">
                      <p class="font-weight-bold">{{ row.item.CodBarras }}</p>
                    </template>

                    <template v-slot:cell(Descripcion)="row">
                      <p class="font-weight-bold">{{ row.item.Descripcion }}</p>
                    </template>

                    <template v-slot:cell(stock)="row">
                      <p class="font-weight-bold text-success">{{ row.item.stock }}</p>
                    </template>

                    <template v-slot:cell(precio_venta_1)="row">
                      <p class="font-weight-bold text-info">${{ row.item.precio_venta_1 }}</p>
                    </template>

                    <template v-slot:cell(precio_venta_2)="row">
                      <p class="font-weight-bold text-info">${{ row.item.precio_venta_2 }}</p>
                    </template>

                    <template v-slot:cell(precio_venta_3)="row">
                      <p class="font-weight-bold text-info">${{ row.item.precio_venta_3 }}</p>
                    </template>

                    <template v-slot:cell(modif_precio)="row">
                      <span
                        v-if="
                                                    row.item.modif_precio !=
                                                        null
                                                "
                      >
                        {{
                        new Date(
                        row.item.modif_precio
                        ).toLocaleDateString(
                        "en-GB",
                        { timeZone: "UTC" }
                        )
                        }}
                      </span>
                      <span v-else>Sin registrar</span>
                    </template>

                    <template v-slot:cell(cantidad)="row">
                      <input
                        style="font-size:12px;"
                        @change="validarStock(row.data,row.index)"
                        type="number"
                        v-model="row.data"
                        class="my-auto bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                        :ref="row.index"
                      />
                    </template>

                    <template v-slot:cell(acciones)="row">
                      <div class="text-center">
                        <b-button
                          @click="
                                                        cargarDetalleVenta(row.index,row.data);
                                                        calcularTotal;
                                                    "
                          size="sm"
                          variant="success"
                          style=" box-shadow: none !important;"
                        >Añadir producto</b-button>
                      </div>
                    </template>
                  </b-table>

                  <div
                    v-if="
                                            listadoProductos.total !=
                                                undefined &&
                                                listadoProductos.total > 5
                                        "
                    @click="getProductosStock"
                  >
                    <b-pagination
                      aria-controls="listado-productos"
                      size="sm"
                      style="font-size:12px;"
                      v-model="
                                                paginaActualListadoProductos
                                            "
                      :total-rows="listadoProductos.total"
                      :per-page="5"
                      first-text="Primera"
                      prev-text="Anterior"
                      next-text="Siguiente"
                      last-text="Última"
                      align="center"
                    ></b-pagination>
                  </div>

                  <b-button
                    class="mt-3"
                    variant="primary"
                    block
                    @click="
                                            $bvModal.hide('modal-add-producto')
                                        "
                  >
                    Volver al detalle de la
                    compra
                  </b-button>
                </div>
              </div>
            </div>
          </div>
        </b-modal>
      </div>
    </transition>
  </div>
</template>
<script>
Vue.directive("focus", {
  inserted: function(el) {
    el.focus();
  },
  update: function(el) {
    Vue.nextTick(function() {
      el.focus();
    });
  }
});
export default {
  data() {
    return {
      show: false,
      cliente: "",
      presupuestos: [],
      clienteRegistrado: [],
      clienteNoRegistrado: false,
      idCliente: 0,
      searchCliente: "",
      listadoClientes: [],
      cantidadProductos: 0,
      nroPresu: 0,
      fieldsListadoClientes: [
        { key: "Nombre", label: "Cliente" },
        { key: "Domicilio", label: "Domicilio" },
        { key: "CUIT", label: "CUIT/CUIL/DNI" },
        { key: "Localidad", label: "Ciudad" },
        { key: "Telefono", label: "Teléfono" },
        { key: "Mail", label: "Correo Electrónico" },
        "acciones"
      ],
      fieldsPresupuestos: [
        { key: "Nombre", label: "Fecha" },
        { key: "Domicilio", label: "Cliente" },
        { key: "CUIT", label: "Total" },
        "acciones"
      ],
      paginaActualListadoClientes: 1,
      //Listado de productos a añadir
      listadoProductos: [], //Listado de los productos que tienen stock
      fieldsListadoProductos: [
        { key: "CodBarras", label: "Código de barras" },
        { key: "Descripcion", label: "Descripción" },
        { key: "stock", label: "Stock" },
        { key: "precio_venta_1", label: "Precio 1" },
        { key: "precio_venta_2", label: "Precio 2" },
        { key: "precio_venta_3", label: "Precio 3" },
        { key: "modif_precio", label: "Ultima modif." },
        "Cantidad",
        "acciones"
      ], //Columnas de la tabla del listado de produtos
      paginaActualListadoProductos: 1,
      productoBuscado: "", //Producto buscado por nombre
      //Detalle de la venta
      detalleVenta: [], //Listado detalle de venta
      fieldsDetalleVenta: [
        "descripción",
        "precio_de_venta",
        "cantidad",
        "subtotal",
        "acciones"
      ], //Columnas de la tabla del detalla de venta
      paginaActualDetalleVenta: 1, //Pagina actual detalle de venta
      errorStock: false, //Error para controlar la cantidad
      total: 0, //Total de la venta
      buscaPresu: 0,
      edita: false
    };
  },
  methods: {
    abreModal() {
      this.$refs["my-modal"].show();
    },
    getClientes: async function() {
      try {
        const response = await axios.post("api/getClientesVenta", {
          busqueda: this.searchCliente,
          page: this.paginaActualListadoClientes
        });

        this.listadoClientes = response.data;
      } catch (error) {
        alertify.error("Error al buscar clientes");
      }
    },

    buscarPresu() {
      this.detalleVenta = [];
      axios
        .post("api/buscaPresu", { presupuesto: this.buscaPresu })
        .then(res => {
          if (res.data != 20) {
            let datosPresu;
            datosPresu = res.data.detalle;

            datosPresu.forEach(element => {
              this.detalleVenta.push({
                IdProducto: element.IdProducto,
                Descripcion: element.Descripcion,
                precio_venta_3: element.precio_venta_3,
                precio_venta_2: element.precio_venta_2,
                precio_venta_1: element.precio_venta_1,
                cantidad: element.cantidad,
                subtotal: element.precio_venta_1 * element.cantidad,
                stock: element.stock,
                precio_venta: element.precio_venta_1
              });
            });
            this.cliente = res.data.presupuesto.cliente;
            this.edita = true;
            this.calcularTotal();
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    resetDatosCliente() {
      console.log(this.tipo_venta);
      if (this.cuit != "") {
        this.$refs["inputNombre"].setAttribute("disabled", true);
        this.$refs["inputNombre"].setAttribute("readonly", true);
        this.$refs["inputDomicilio"].setAttribute("disabled", true);
        this.$refs["inputDomicilio"].setAttribute("readonly", true);
      }
      this.clienteNoRegistrado = false;
      this.ventaCC = false;
      this.clienteRegistrado = [];
      this.cliente = "";
      this.domicilio = "";
      this.cuit = "";
    },
    getProductosStock() {
      axios
        .post("api/getProductosStock", {
          producto: this.productoBuscado,
          page: this.paginaActualListadoProductos
        })
        .then(response => {
          this.listadoProductos = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    cargarDetalleVenta(index) {
      if (this.errorStock != true) {
        let error = 0;
        let cantidad = 1;
        if (this.$refs[index].value != "") {
          cantidad = this.$refs[index].value;
        } else {
          cantidad = 1;
        }

        this.detalleVenta.forEach(element => {
          if (
            this.listadoProductos.data[index].IdProducto == element.IdProducto
          ) {
            alertify.error("No se puede ingresar el mismo producto dos veces");
            error++;
          }
        });

        if (error == 0) {
          this.detalleVenta.push({
            IdProducto: this.listadoProductos.data[index].IdProducto,
            Descripcion: this.listadoProductos.data[index].Descripcion,
            precio_costo: this.listadoProductos.data[index].precio_costo,
            precio_venta_1: this.listadoProductos.data[index].precio_venta_1,
            precio_venta_2: this.listadoProductos.data[index].precio_venta_2,
            precio_venta_3: this.listadoProductos.data[index].precio_venta_3,
            stock: this.listadoProductos.data[index].stock,
            precio_venta: this.listadoProductos.data[index].precio_venta_1,
            cantidad: cantidad,
            subtotal:
              this.listadoProductos.data[index].precio_venta_1 * cantidad
          });

          this.total = 0;
          this.cantidadProductos = 0;

          this.detalleVenta.forEach(element => {
            this.total = this.total + element.subtotal;
            this.cantidadProductos =
              this.cantidadProductos + parseInt(element.cantidad);
          });
          if (this.ventaCC) {
            this.total = this.total + (this.total * this.intereses) / 100;
          }

          alertify.success("Producto agregado correctamente");
          this.$refs[index].value = "";
          this.productoBuscado = "";
        }
      } else {
        alertify.error("La cantidad Supera el stock");
      }
    },
    setInfoCliente(id) {
      console.log(id);
      let selected;
      selected = this.listadoClientes.data.filter(res => {
        if (res.id == id) {
          return res;
        }
      });
      this.cliente = selected[0].Nombre;
      this.idCliente = selected[0].id;
      this.searchCliente = "";
      this.$refs["my-modal"].hide();
    },
    quitarDetalleVenta(index) {
      this.detalleVenta.splice(index, 1);

      alertify.error("Producto quitado correctamente");

      this.total = 0;
      this.cantidadProductos = 0;
      this.detalleVenta.forEach(element => {
        this.cantidadProductos =
          this.cantidadProductos + parseInt(element.cantidad);
        this.total = this.total + element.subtotal;
      });
    },
    aumentarCantidad(index) {
      if (this.detalleVenta[index].cantidad == this.detalleVenta[index].stock) {
        alertify.error("La cantidad no puede superar el stock");
      } else {
        this.detalleVenta[index].cantidad++;
        this.detalleVenta[index].subtotal =
          this.detalleVenta[index].cantidad *
          this.detalleVenta[index].precio_venta;

        this.total = 0;
        this.cantidadProductos = 0;
        this.detalleVenta.forEach(element => {
          this.cantidadProductos =
            this.cantidadProductos + parseInt(element.cantidad);
          this.total = this.total + element.subtotal;
        });
      }
    },
    disminuirCantidad(index) {
      if (this.detalleVenta[index].cantidad == 1) {
        alertify.error("La cantidad no puede ser 0");
      } else {
        this.detalleVenta[index].cantidad--;
        this.detalleVenta[index].subtotal =
          this.detalleVenta[index].cantidad *
          this.detalleVenta[index].precio_venta;

        this.total = 0;
        this.cantidadProductos = 0;
        this.detalleVenta.forEach(element => {
          this.total = this.total + element.subtotal;
          this.cantidadProductos =
            this.cantidadProductos + parseInt(element.cantidad);
        });
      }
    },
    validarCantidad(index) {
      console.log("cantidad recibida es:" + this.$refs[index]);
      if (this.detalleVenta[index].cantidad < 1) {
        this.detalleVenta[index].cantidad = 1;
        alertify.error("La cantidad no puede ser 0");
      } else {
        if (
          this.detalleVenta[index].cantidad > this.detalleVenta[index].stock
        ) {
          this.detalleVenta[index].cantidad = 1;
          alertify.error("La cantidad no puede superar el stock");
        }
      }

      this.detalleVenta[index].subtotal =
        this.detalleVenta[index].cantidad *
        this.detalleVenta[index].precio_venta;

      this.total = 0;
      this.cantidadProductos = 0;
      this.detalleVenta.forEach(element => {
        this.cantidadProductos =
          this.cantidadProductos + parseInt(element.cantidad);
        this.total = this.total + element.subtotal;
      });
      if (this.ventaCC) {
        this.total = this.total + (this.total * this.descuento) / 100;
      }
    },
    validarStock(cantidad, index) {
      if (this.listadoProductos.data[0].stock < cantidad) {
        alertify.error("Cantidad Supera El Stock");
        this.errorStock = true;
      } else {
        this.errorStock = false;
      }
    },
    calcularTotal(index) {
      //this.detalleVenta[index].subtotal =
      //this.detalleVenta[index].cantidad *
      //this.detalleVenta[index].precio_venta;

      this.total = 0;

      this.detalleVenta.forEach(element => {
        this.total = this.total + element.subtotal;
      });
      console.log(this.ventaCC);
      if (this.ventaCC) {
        this.total = this.total + (this.total * this.intereses) / 100;
      }
    },
    savePresu(autorizar) {
      if (this.edita != true) {
        if (this.cliente != "" && this.detalleVenta.length > 0) {
          let filename = "Presupuesto - " + this.cliente + ".pdf";

          let url = "api/savePresupuesto";
          axios
            .post(url, {
              cliente: this.cliente,
              detalleVenta: this.detalleVenta,
              idCliente: this.idCliente,
              total: this.total,
              userid: window.user.user.id
            })
            .then(response => {
              if (response.data != 20) {
                axios({
                  url: response.data,
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
                  // let filename = "presupuesto - "+ this.cliente +".pdf";
                  console.log(filename);
                  fileLink.setAttribute("download", filename);
                  document.body.appendChild(fileLink);
                  fileLink.click();
                });
                this.limpia();
              } else {
                alertify.error(
                  "Ha ocurrido un error al momento de cargar la venta"
                );
                this.limpia();
              }
            });
        } else {
          alertify.error("Faltan Datos Para Cargar");
        }
      } else {
        this.editaPresu();
      }
    },
    cancelarPresu() {
      this.cantidadProductos = 0;
      this.total = 0;
      this.datosRequeridos = false;
      this.clienteRegistrado = [];
      this.clienteNoRegistrado = false;
      this.detalleVenta = [];

      this.cliente = "";

      this.total = 0;
      this.nroPresu = 0;
      this.edita = false;
      this.detalleVenta = [];
      this.paginaActualListadoProductos = 1;
      alertify.error("Presupuesto cancelado correctamente");
    },

    editaPresu() {
      console.log(this.cliente);
      if (this.cliente != "") {
        axios
          .post("api/actualizaPresu", {
            cliente: this.cliente,
            presupuesto: this.buscaPresu,
            detalleVenta: this.detalleVenta,
            idCliente: this.idCliente,
            total: this.total,
            userid: window.user.user.id
          })
          .then(res => {
               if (res.data != 20) {
                 let filename = "Presupuesto - " + this.cliente + ".pdf";
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
                  fileLink.setAttribute("download", filename);
                  document.body.appendChild(fileLink);
                  fileLink.click();
                });
                this.limpia();
                 alertify.success("Presupuesto Actualizado Correctamente");
              }  else {
              alertify.error("Error Al actualizar Presupuesto");
              this.limpia();
            }
          });
      } else {
        alertify.error("Faltan completar Datos");
      }
    },

    limpia() {
      this.edita = false;

      this.total = 0;
      this.datosRequeridos = false;
      this.clienteRegistrado = [];
      this.clienteNoRegistrado = false;
      this.detalleVenta = [];
      this.cliente = "";
      this.detalleVenta = [];
      this.nroPresu = 0;

      this.idCliente = 0;
      this.cantidadProductos = 0;
    }
  },

  mounted() {
    this.show = true;
  }
};
</script>
