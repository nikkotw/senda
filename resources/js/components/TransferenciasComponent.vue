<template>
  <div>
    <div class="row">
      <div class="col">
        <h2 class="text-center">Envio A Sucursales</h2>
        <button class="btn btn-success" v-b-modal.modal-1 @click="search()">
          Cargar Articulos
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col col-md-4" v-for="item in empresas" :key="item.id">
        <div v-if="filtrado(item.id).length > 0">
          <div class="card-body mb-n3" style="font-size: 12px">
            <b-table
              class="mt-2"
              id="transferencias"
              :items="filtrado(item.id)"
              :fields="fieldsTransferencia"
              outlined
              responsive
              hover
              no-border-collapse
              head-variant="dark"
              :tbody-transition-props="{ name: 'fade' }"
            >
              <template v-slot:thead-top>
                <b-tr>
                  <b-th variant="success" class="text-center" colspan="3">
                    {{ item.sucursal }}
                    <button
                      class="btn btn-sm btn-primary float-right"
                      @click="transferencia(item.id)"
                    >
                      Transferir
                    </button>
                  </b-th>
                </b-tr>
              </template>
              <template v-slot:cell(cantidad)="row">
                <div class="col-xs-2">
                  <b-button
                    size="sm"
                    variant="danger"
                    style="box-shadow: none !important; line-heigth: 50%"
                    @click="sumaResta(row.item.idproducto, item.id, -1)"
                    >-</b-button
                  >
                  <input
                    type="number"
                    class="text-center"
                    disabled
                    :value="row.item.cantidad"
                  />
                  <b-button
                    size="sm"
                    variant="success"
                    style="box-shadow: none !important; line-heigth: 50%:margin:15px"
                    @click="sumaResta(row.item.idproducto, item.id, +1)"
                    >+</b-button
                  >
                </div>
              </template>
              <template v-slot:cell(acciones)="row" colspan="1">
                <div class="text-center">
                  <b-button
                    size="sm"
                    variant="danger"
                    style="box-shadow: none !important"
                    @click="elimina(row.item.id)"
                  >
                    <i class="fa fa-trash" aria-hidden="true"></i>
                  </b-button>
                </div>
              </template>
            </b-table>
          </div>
        </div>
      </div>

      <b-modal id="modal-1" title="Realizar Transferencia" size="xl">
        <div class="card-body mb-n3" style="font-size: 12px">
          <div class="form-group">
            <label for="Empresas">Seleccione una Sucursal</label>
            <b-form-select
              class="form-control"
              id="Empresas"
              v-model="sucursal"
            >
              <b-form-select-option
                v-for="item in empresas"
                :key="item.id"
                :value="item.id"
                >{{ item.sucursal }}</b-form-select-option
              >
            </b-form-select>
          </div>

          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              placeholder="Buscar Articulo"
              aria-label="Recipient's username"
              v-model="busqueda"
              aria-describedby="button-addon2"
              v-on:keyup.enter="search"
            />
            <div class="input-group-append">
              <button
                class="btn btn-outline-primary"
                @click="search()"
                type="button"
                id="button-addon2"
              >
                Buscar
              </button>
            </div>
          </div>
          <b-table
            id="productos"
            :items="productos.data"
            :fields="fieldsItemsStock"
            outlined
            responsive
            hover
            text-center
            no-border-collapse
            head-variant="dark"
            :tbody-transition-props="{ name: 'fade' }"
          >
         
            <template v-slot:cell(cantidad)="row" colspan="1">
              
               
                   <input
                    style="font-size:12px;"
                    type="number"
                    v-model="row.item.cantidad"
                    class="my-auto bg-transparent text-center font-weight-bold border-top-0 border-left-0 border-right-0 border-bottom-0 form-control form-control-sm"
                  />
              
            </template>
            <template v-slot:cell(acciones)="row" colspan="1">
              
                
           
                      <b-button
                          @click="agregaProducto(row.item.IdProducto,row.item.cantidad)"
                          size="sm"
                          variant="success"
                          style=" box-shadow: none !important;"
                        >Añadir producto</b-button>
                
            </template>
          </b-table>
        </div>
        <div
          v-if="productos.total != undefined && productos.total > 5"
          @click="search()"
        >
          <b-pagination
            aria-controls="productos"
            size="sm"
            style="font-size: 12px"
            v-model="paginaActualListadoProductos"
            :total-rows="productos.total"
            :per-page="5"
            first-text="Primera"
            prev-text="Anterior"
            next-text="Siguiente"
            last-text="Última"
            align="right"
          ></b-pagination>
        </div>
      </b-modal>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cantidad: "",
      sucursal: "",
      busqueda: "",
      transferencias: [],
      productos: [],
      empresas: [],
      modifica: false,
      paginaActualListadoProductos: 1,
      fieldsItemsStock: [
        { key: "Descripcion", label: "Producto" },
        { key: "stock", label: "Stock" },
        "Cantidad",
        "Acciones",
      ],
      fieldsTransferencia: [
        { key: "Descripcion", label: "Producto" },
        { key: "cantidad", label: "cantidad" },
        "acciones",
      ],
    };
  },
  created: function () {},
  mounted() {
    console.log(window.user.user.id);
    window.Echo.channel("channel-CarritoTransfer").listen(
      "CarritoTransfer",
      (e) => {
        console.log(e.productos);
        this.transferencias = e.productos;
      }
    );

    axios
      .get("/api/sucursales")
      .then((res) => {
        if (res.status == 200) {
          this.empresas = res.data;
          this.empresas = this.empresas.filter(function (item) {
            return item.estado !== "P";
          });
        }
      })
      .catch((err) => {
        console.log(err);
      });

    axios
      .get("/api/transferAll")
      .then((res) => {
        if (res.status == 200) {
          this.transferencias = res.data;
          console.log(this.transferencias);
        }
      })
      .catch((err) => {
        console.log(err);
      });
  },
  methods: {
    elimina: function (id) {
      axios
        .post("/api/eliminaProductoTransfer", {
          id: id,
          userid: window.user.id,
        })
        .then((res) => {
          if (res.status == 200) {
            this.detalle = res.data;
          }
        })
        .catch((err) => {
          console.log(err);
        });

    },
    transferencia: function (id) {
      axios
        .post("realiza_transferencia/", { id: id, userid: window.user.user.id })
        .then((res) => {
          console.log("esto es res:" + res);
          console.log("esto es res.data:" + res.data);
          axios({
            url: res.data,
            method: "GET",
            responseType: "blob",
          }).then((response) => {
            var fileURL = window.URL.createObjectURL(new Blob([response.data]));
            var fileLink = document.createElement("a");

            fileLink.href = fileURL;
            fileLink.setAttribute("download", "venta.pdf");
            document.body.appendChild(fileLink);

            fileLink.click();
          });

          console.log("Transferencia Exitosa");
        });
    },

    search: function () {
      axios
        .post("api/getProductosStock", {
          producto: this.busqueda,
          page: this.paginaActualListadoProductos,
          userid: window.user.user.id,
        })
        .then((res) => {
          this.productos = res.data;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    agregaProducto: function (producto, data) {
      this.cantidad = data;
      console.log(this.cantidad);
      if (this.sucursal != "") {
        
        if (this.cantidad == "" || this.cantidad == undefined) {
          this.cantidad = 1;
        }
        axios
          .post("api/carritoAddStock", {
            producto: producto,
            sucursal: this.sucursal,
            cantidad: this.cantidad,
            userid: window.user.user.id,
          })
          .then((res) => {
            if (res.data == 1) {
              alertify.success("Producto Agregado Correctamente");
              this.cantidad = "";
            }

            if (res.data == 2) {
              alertify.error("No hay suficiente Stock");
            }
          })
          .catch((err) => {
            console.log(err);
          });
      } else {
        alertify.error("Debe Seleccionar una sucursal");
      }
    },
    filtrado: function (filtrado) {
      let temporal;
      temporal = this.transferencias.filter(function (item) {
        return item.idsucursal == filtrado;
      });
      return temporal;
    },
    sumaResta: function (producto, sucursal, cantidad) {
      axios
        .post("api/carritoAddStock", {
          producto: producto,
          sucursal: sucursal,
          cantidad: cantidad,
        })
        .then((res) => {
          if (res.data == 1) {
            alertify.success("Producto Agregado Correctamente");
          }

          if (res.data == 2) {
            alertify.error("No hay suficiente Stock");
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
</script> 
