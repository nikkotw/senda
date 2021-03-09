<template>
  <div class="ml-1 mr-1">
    <!--Modal Seleccionar Proveedor-->
    <b-modal
      ref="modalSeleccionarProveedor"
      id="modalSeleccionarProveedor"
      size="xl"
      hide-footer
      title="Seleccionar proveedor"
    >
      <div class="row">
        <div class="col col-lg">
          <div class="input-group mt-1">
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
              v-on:click="searchProveedores()"
              type="button"
            >Buscar</button>
          </div>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center mt-1 mb-1">
          <div v-if="listadoProveedores.length == 0" class="spinner-border text-dark" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg">
          <b-table
            class="mt-1"
            style="font-size:12px;"
            id="listado-proveedores"
            outlined
            small
            hover
            responsive
            no-border-collapse
            head-variant="dark"
            :items="listadoProveedores"
            :fields="fieldsListadoProveedores"
            v-if="listadoProveedores.length != 0"
          >
            <template v-slot:cell(acciones)="row">
              <button
                @click="selectProveedor(row.item.id)"
                title="Seleccionar proveedor"
                class="btn btn-sm btn-primary"
              >Seleccionar</button>
            </template>
          </b-table>
        </div>

        <div class="w-100"></div>

        <div
          v-if="listadoProveedores.total != undefined && listadoProveedores.total > 10"
          @click="searchProveedores"
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
    </b-modal>

    <!-- Modal Añadir Productos -->
    <b-modal
      ref="modalAddProducto"
      id="modalAddProducto"
      size="xl"
      hide-footer
      title="Añadir productos"
    >
      <div class="card">
        <div
          class="card-header text-primary bg-transparent font-weight-bold text-left"
        >Listado de productos del proveedor {{proveedor.nombre}}</div>

        <div class="card-body row">
          <div class="col col-xl">
            <div class="input-group">
              <input
                v-model="productoBuscado"
                type="text"
                class="form-control form-control-sm"
                placeholder="Ingrese el código de barra o el nombre del producto"
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

          <div class="col col-xl text-center mt-1">
            <div v-if="listadoProductos.length == 0" class="spinner-border text-dark" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>

          <div class="w-100"></div>

          <div class="col col-xl mt-1">
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
              v-if="listadoProductos.length != 0"
            >
              <template v-slot:cell(acciones)="row">
                <button
                  @click="addDetalle(row.index,row.item.IdProducto)"
                  title="Añadir"
                  class="btn btn-sm btn-success"
                >Añadir</button>
              </template>

              <template v-slot:cell(precio_venta_1)="row">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    readonly
                    style="background-color:transparent;"
                    v-model="row.item.precio_venta_1"
                    @dblclick="editPrecioVenta1(row.item.precio_venta_1,row.item.IdProducto,$event)"
                    @focusout="editPrecioVenta1(row.item.precio_venta_1,row.item.IdProducto,$event)"
                  />
                </div>
              </template>

              <template v-slot:cell(precio_venta_2)="row">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    readonly
                    style="background-color:transparent"
                    v-model="row.item.precio_venta_2"
                    @dblclick="editPrecioVenta2(row.item.precio_venta_2,row.item.IdProducto,$event)"
                    @focusout="editPrecioVenta2(row.item.precio_venta_2,row.item.IdProducto,$event)"
                  />
                </div>
              </template>

              <template v-slot:cell(precio_venta_3)="row">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    readonly
                    style="background-color:transparent"
                    v-model="row.item.precio_venta_3"
                    @dblclick="editPrecioVenta3(row.item.precio_venta_3,row.item.IdProducto,$event)"
                    @focusout="editPrecioVenta3(row.item.precio_venta_3,row.item.IdProducto,$event)"
                  />
                </div>
              </template>

              <template v-slot:cell(precio_costo)="row">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    readonly
                    style="background-color:transparent"
                    v-model="row.item.precio_costo"
                  />
                </div>
              </template>
            </b-table>
          </div>
          <div class="w-100"></div>
          <div class="col col-xl">
            <div
              v-if="listadoProductos.total != undefined && listadoProductos.total > 5"
              @click="getProductos"
            >
              <b-pagination
                aria-controls="listado-productos"
                size="sm"
                style="font-size:12px;"
                v-model="paginaActualListadoProductos"
                :total-rows="listadoProductos.total"
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
    </b-modal>

    <div class="row card mt-1">
      <div
        class="card-header text-primary bg-transparent font-weight-bold text-left"
      >Realizar pedidos</div>

      <div style="font-size:12px" class="row card-body mb-n2">
        <div class="col col-xl">
          <div class="input-group">
            <input
              readonly
              v-model="proveedor.nombre"
              placeholder="Seleccione un proveedor de la lista"
              style="background-color:transparent;"
              type="text"
              class="form-control form-control-sm"
            />
            <div class="input-group-append">
              <button
                class="btn btn-sm btn-primary"
                v-b-modal.modalSeleccionarProveedor
                @click="getProveedores()"
                type="button"
              >Seleccionar proveedor</button>
            </div>
          </div>
        </div>

        <label class="w-100 font-weight-bold" for="fechaLlegada">Proveedor</label>

        <div class="w-100"></div>

        <div class="form-group col col-xl">
          <input
            v-model="fechaPedido"
            type="date"
            class="form-control form-control-sm"
            id="fechaPedidoRealizado"
          />
          <label
            class="font-weight-bold"
            for="fechaPedidoRealizado"
          >Fecha en la que se realizó el pedido</label>
        </div>

        <div class="form-group col col-xl">
          <input
            v-model="fechaArribo"
            type="date"
            class="form-control form-control-sm"
            id="fechaLlegada"
          />
          <label class="font-weight-bold" for="fechaLlegada">Fecha estimada de llegada</label>
        </div>

        <div class="w-100"></div>

        <div class="col col-xl text-right">
          <button
            v-b-modal.modalAddProducto
            v-if="proveedor != ''"
            @click="getProductos"
            class="btn btn-success btn-sm"
          >Añadir producto</button>
        </div>

        <div class="w-100"></div>

        <div class="col col-xl">
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
            :items="detallePedido"
            :fields="fieldsDetallePedido"
            v-if="detallePedido.length != 0"
          >
            <template v-slot:cell(precio_costo)="row">
              <span class="font-weight-bold text-primary">${{row.item.precio_costo}}</span>
            </template>

            <template v-slot:cell(cantidad)="row">
              <input
                type="number"
                v-model="row.item.cantidad"
                @change="modificarCantidad(row.index)"
                class="form-control form-control-sm"
              />
            </template>

            <template v-slot:cell(subtotal)="row">
              <span class="font-weight-bold text-primary">${{row.item.subtotal}}</span>
            </template>

            <template v-slot:cell(acciones)="row">
              <button @click="quitarDetalle(row.index)" class="btn btn-sm btn-danger">Quitar</button>
            </template>
          </b-table>
        </div>

        <div class="w-100"></div>

        <div v-if="total != 0" class="col col-xl">
          <b-form-checkbox
            id="checkbox-2"
            name="checkbox-2"
            value="accepted"
            unchecked-value="not_accepted"
            class="float-left"
            v-model="generarPDF"
          >
            <h6 class="mt-1 font-weight-bold">Generar PDF al finalizar el pedido</h6>
          </b-form-checkbox>
          <h6 class="float-right">
            Total:
            <span class="font-weight-bold text-primary">${{total.toFixed(2)}}</span>
          </h6>
        </div>

        <div class="w-100"></div>

        <div v-if="total != 0" class="col col-xl">
          <button @click="createPedido()" class="btn btn-success btn-sm">Confirmar</button>
          <button @click="cancelarPedido()" class="btn btn-danger btn-sm">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      fechaPedido: "",
      fechaArribo: "",
      //Proveedores
      filtroBusquedaProveedor: "",
      proveedorBuscado: "",
      listadoProveedores: [],
      fieldsListadoProveedores: [
        { key: "nombre", label: "Nombre" },
        { key: "cuit", label: "CUIT" },
        { key: "ciudad", label: "Ciudad" },
        { key: "email", label: "Correo Electrónico" },
        "acciones"
      ],
      paginaActualListadoProveedores: 1,
      proveedor: [],

      //Productos
      listadoProductos: [],
      fieldsListadoProductos: [
        { key: "CodBarras", label: "Código de barras" },
        { key: "Descripcion", label: "Descripción" },
        { key: "stock", label: "Stock" },
        { key: "precio_costo", label: "Precio de costo" },
        { key: "precio_venta_1", label: "Precio 1" },
        { key: "precio_venta_2", label: "Precio 2" },
        { key: "precio_venta_3", label: "Precio 3" },
        "acciones"
      ],
      paginaActualListadoProductos: 1,
      productoBuscado: "",

      //Detalle pedido
      detallePedido: [],
      fieldsDetallePedido: [
        { key: "CodBarras", label: "Código de barras" },
        { key: "Descripcion", label: "Descripción" },
        { key: "stock", label: "Stock" },
        { key: "precio_costo", label: "Precio de costo" },
        "cantidad",
        "subtotal",
        "acciones"
      ],
      total: 0,
      generarPDF: "not_accepted"
    };
  },
  methods: {
    getProveedores: async function() {
      try {
        const response = await axios.get("/api/readProveedores");
        this.listadoProveedores = response.data;
      } catch (error) {
        throw Error(error);
      }
    },
    searchProveedores: async function() {
      try {
        this.listadoProveedores = [];
        const response = await axios.post("/api/buscarProveedores", {
          page: this.paginaActualListadoProveedores,
          filtro: this.filtroBusquedaProveedor,
          proveedorBuscado: this.proveedorBuscado
        });
        this.listadoProveedores = response.data.data;
      } catch (error) {
        throw Error(error);
      }
    },
    selectProveedor: function(id) {
      this.fechaPedido = "";
      this.fechaArribo = "";
      this.paginaActualListadoProveedores = 1;
      this.proveedor = [];
      this.listadoProductos = [];
      this.paginaActualListadoProductos = 1;
      this.productoBuscado = "";
      this.detallePedido = [];
      this.total = 0;
      this.generarPDF = "not_accepted";
      this.proveedor = this.listadoProveedores.find(
        searched => searched.id === id
      );
      this.$refs["modalSeleccionarProveedor"].hide();
    },
    getProductos: async function() {
      this.listadoProductos = [];
      try {
        const response = await axios.post("/api/getListadoProductos", {
          page: this.paginaActualListadoProductos,
          idProveedor: this.proveedor.id,
          productoBuscado: this.productoBuscado
        });
        this.listadoProductos = response.data;
      } catch (error) {
        throw Error(error);
      }
    },
    editPrecioVenta1: async function(precio_venta_1, id, event) {
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");

        try {
          const response = axios.post("/api/editPrecioVenta1", {
            id: id,
            precio_venta_1: precio_venta_1,
            userid: window.user.user.id
          });
          alertify.success("Precio 1 editado correctamente");
        } catch (error) {
          alertify.error("El precio 1 no se pudo editar");
        }
      }
    },
    editPrecioVenta2: async function(precio_venta_2, id, event) {
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");

        try {
          const response = axios.post("/api/editPrecioVenta2", {
            id: id,
            precio_venta_2: precio_venta_2,
            userid: window.user.user.id
          });
          alertify.success("Precio 2 editado correctamente");
        } catch (error) {
          alertify.error("El precio 2 no se pudo editar");
        }
      }
    },
    editPrecioVenta3: async function(precio_venta_3, id, event) {
      console.log(id);
      if (event.target.getAttribute("readonly") == "readonly") {
        event.target.removeAttribute("readonly");
      } else {
        event.target.setAttribute("readonly", "readonly");

        try {
          const response = axios.post("/api/editPrecioVenta3", {
            id: id,
            precio_venta_3: precio_venta_3,
            userid: window.user.user.id
          });
          alertify.success("Precio 3 editado correctamente");
        } catch (error) {
          alertify.error("El precio 3 no se pudo editar");
        }
      }
    },
    addDetalle: function(index, IdProducto) {
      if (
        this.detallePedido.find(
          producto => producto.IdProducto == IdProducto
        ) != undefined
      ) {
        alertify.error("El producto ya ha sido cargado al pedido");
      } else {
        alertify.success("Producto cargado correctamente");
        this.total = 0;

        this.detallePedido.push({
          IdProducto: this.listadoProductos.data[index].IdProducto,
          CodBarras: this.listadoProductos.data[index].CodBarras,
          Descripcion: this.listadoProductos.data[index].Descripcion,
          stock: this.listadoProductos.data[index].stock,
          precio_costo: this.listadoProductos.data[index].precio_costo,
          cantidad: 1,
          subtotal: 1 * this.listadoProductos.data[index].precio_costo
        });

        this.detallePedido.forEach(element => {
          this.total += element.subtotal;
        });
      }
    },
    quitarDetalle: function(index) {
      this.detallePedido.splice(index, 1);

      this.total = 0;

      this.detallePedido.forEach(element => {
        this.total += element.subtotal;
      });

      alertify.success("Producto quitado correctamente");
    },
    modificarCantidad: function(index) {
      if (
        this.detallePedido[index].cantidad == 0 ||
        this.detallePedido[index].cantidad == ""
      ) {
        this.detallePedido[index].cantidad = 1;
      }

      this.detallePedido[index].subtotal = parseFloat(
        (
          this.detallePedido[index].cantidad *
          this.detallePedido[index].precio_costo
        ).toFixed(2)
      );

      this.total = 0;
      this.detallePedido.forEach(element => {
        this.total += element.subtotal;
      });
    },
    createPedido: async function() {
      if (this.fechaPedido == "") {
        alertify.error(
          "Por favor ingrese la fecha en la que se realizó el pedido"
        );
      } else {
        try {
          const response = await axios.post("/api/createPedidoAndDetalles", {
            idProveedor: this.proveedor.id,
            cuit: this.proveedor.cuit,
            fecha: this.fechaPedido,
            total: this.total,
            fecha_arribo: this.fechaArribo,
            detallePedido: this.detallePedido,
            userid: window.user.user.id
          });

          if (this.generarPDF === "accepted") {
            this.descargarPdf();
          }

          this.limpiarFormulario();
          alertify.success("Se ha cargado correctamente el pedido");
        } catch (error) {
          alertify.error("Ha ocurrido un error en la carga del pedido");
        }
      }
    },
    cancelarPedido: function() {
      alertify
        .confirm(
          "Cancelar pedido",
          "¿Esta segur@ que desea cancelar el pedido?",
          cancelOk => {
            this.limpiarFormulario();
            alertify.success("Pedido cancelado correctamente");
          },
          cancelNo => {
            alertify.error("No se  ha cancelado el pedido");
          }
        )
        .set("labels", { ok: "Cancelar pedido", cancel: "No cancelar" });
    },
    limpiarFormulario: function() {
      this.fechaPedido = "";
      this.fechaArribo = "";
      this.filtroBusquedaProveedor = "";
      this.proveedorBuscado = "";
      this.listadoProveedores = [];
      this.paginaActualListadoProveedores = 1;
      this.proveedor = [];
      this.listadoProductos = [];
      this.paginaActualListadoProductos = 1;
      this.productoBuscado = "";
      this.detallePedido = [];
      this.total = 0;
      this.generarPDF = "not_accepted";
    },
    descargarPdf: async function() {
      var url = "";

      if (this.fechaArribo == "") {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(this.detallePedido) +
          "/" +
          this.proveedor.id +
          "/" +
          this.fechaPedido +
          "/0-0-0/" +
          this.total;
      } else {
        url =
          "api/pdfPedidos/" +
          JSON.stringify(this.detallePedido) +
          "/" +
          this.proveedor.id +
          "/" +
          this.fechaPedido +
          "/" +
          this.fechaArribo +
          "/" +
          this.total;
      }

      await axios
        .get(url, {
          responseType: "blob"
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "pedido.pdf");
          document.body.appendChild(link);
          link.click();
        });
    }
  }
};
</script>
