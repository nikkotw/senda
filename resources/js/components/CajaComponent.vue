<template>
  <div class="ml-1 mr-1">
    <!--Modal abrir caja-->
    <b-modal ref="modalAbrirCaja" id="modalAbrirCaja" size="xl" hide-footer title="Abrir caja">
      <div class="row">
        <div class="col col-lg text-center">
          <b-form-group label="Seleccione con el monto que desea iniciar la caja del día de hoy">
            <b-form-radio-group
              id="btn-radios-1"
              v-model="selected"
              :options="options"
              buttons
              button-variant="outline-success"
              name="radios-btn-default"
            ></b-form-radio-group>
          </b-form-group>
        </div>
        <div class="w-100"></div>
        <div v-if="selected == 'radio1'" class="col col-lg text-center">
          <label v-if="cajaSeleccionada != 99999">
            La caja iniciara con un total de:
            <span
              class="font-weight-bold"
            >${{listadoCajas[cajaSeleccionada]['total']}}</span>
          </label>
        </div>
        <div v-if="selected == 'radio2'" class="col col-lg text-center form-group">
          <label class="font-weight-bold">Ingrese el monto con el que desea iniciar la caja</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input v-model="monto_abrir" type="number" class="form-control" />
          </div>
        </div>
      </div>

      <div class="w-100"></div>

      <div class="col col-lg text-center">
        <button
          @click="abrirCaja(listadoCajas[cajaSeleccionada]['id'])"
          class="btn btn-sm btn-success"
        >Abrir caja</button>
      </div>
    </b-modal>

    <!--Extracción de caja-->
    <b-modal
      ref="modalExtraccionCaja"
      id="modalExtraccionCaja"
      size="xl"
      hide-footer
      title="Extracciones de caja"
    >
      <div class="row">
        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Ingrese el monto que se va a extraer de la caja</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input v-model="montoExtraccion" type="number" class="form-control" />
          </div>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Motivo por el que se retira el efectivo</label>
          <textarea v-model="motivoExtraccion" class="form-control"></textarea>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <button
            @click="realizarExtraccion(listadoCajas[cajaSeleccionada]['id'])"
            class="btn btn-sm btn-warning"
          >Realizar extracción</button>
          
        </div>
      </div>
    </b-modal>

    <b-modal
      ref="modalIngresoCaja"
      id="modalIngresoCaja"
      size="xl"
      hide-footer
      title="Ingreso Cobro a caja"
    >
      <div class="row">
        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Ingrese el monto que se va a Ingresar en la caja</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input v-model="montoIngreso" type="number" class="form-control" />
          </div>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Detalle del ingreso de plata</label>
          <textarea v-model="motivoIngreso" class="form-control"></textarea>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <button
            @click="IngresarDinero(listadoCajas[cajaSeleccionada]['id'])"
            class="btn btn-sm btn-success"
          >Ingresar Dinero</button>
        </div>
      </div>
    </b-modal>

    <!--Cierre de caja-->
    <b-modal
      ref="modalCierreCaja"
      id="modalCierreCaja"
      size="xl"
      hide-footer
      title="Cierre de caja"
    >
      <div class="row">
        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Ingrese el monto que hay en caja</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input v-model="montoCajaCerrar" type="number" class="form-control" />
          </div>
        </div>
        <div class="w-100"></div>
        <div class="col col-lg">
          <div class="card">
            <div class="card-header bg-dark text-white">Información de la caja</div>
            <div class="card-body">
              <p>
                Monto inicial de la caja:
                <span
                  class="font-weight-bold text-info"
                >${{datosCajaCerrar.monto_inicio}}</span>
              </p>
              <p>
                Total en caja según el sistema:
                <span
                  class="font-weight-bold text-info"
                >${{datosCajaCerrar.total}}</span>
              </p>
              <p v-if="(datosCajaCerrar.total - montoCajaCerrar) == 0">
                Diferencia:
                <span
                  class="font-weight-bold text-success"
                >${{datosCajaCerrar.total - montoCajaCerrar}}</span>
              </p>
              <p v-if="(datosCajaCerrar.total - montoCajaCerrar) > 0">
                Diferencia:
                <span
                  class="font-weight-bold text-danger"
                >${{datosCajaCerrar.total - montoCajaCerrar}}</span>
              </p>
              <p v-if="(datosCajaCerrar.total - montoCajaCerrar) < 0">
                Diferencia:
                <span
                  class="font-weight-bold text-warning"
                >${{(datosCajaCerrar.total - montoCajaCerrar).toFixed(2).toString().slice(1)}}</span>
              </p>
            </div>
          </div>
        </div>
        <div class="col col-lg">
          <div class="card">
            <div class="card-header bg-dark text-white">Referencias</div>
            <div class="card-body">
              <p>
                <span class="badge badge-danger">Falta dinero</span>
              </p>
              <p>
                <span class="badge badge-success">No hay diferencia</span>
              </p>
              <p>
                <span class="badge badge-warning">Hay dinero extra</span>
              </p>
            </div>
          </div>
        </div>
        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Observaciones</label>
          <textarea v-model="obsCierre" class="form-control"></textarea>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center form-group">
          <label class="font-weight-bold">Ingrese el monto que queda en caja luego del cierre</label>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">$</span>
            </div>
            <input v-model="montoNuevo" type="number" class="form-control" />
          </div>
        </div>

        <div class="w-100"></div>

        <div class="col col-lg text-center">
          <button @click="cerrarCaja(datosCajaCerrar.id)" class="btn btn-sm btn-success">Cerrar caja</button>
        </div>
      </div>
    </b-modal>

    <div class="card mt-1">
      <div class="card-header text-primary bg-transparent font-weight-bold text-left">
        Caja
        <button
          @click="showVentas = false; resetVariables();"
          v-if="showVentas == true"
          class="btn btn-sm btn-danger float-right font-weight-bold"
        >Volver</button>
      </div>

      <!--Seleccionar caja para abrir y cerrar-->
      <div v-if="!showVentas" class="row">
        <div class="form-group col ml-1 mr-1">
          <label style="font-size:12px" class="font-weight-bold">Listado de cajas</label>
          <select v-model="cajaSeleccionada" class="form-control form-control-sm font-weight-bold">
            <option
              class="font-weight-bold"
              value="99999"
              selected
              disabled
            >Seleccione una caja para operar</option>
            <option
              v-for="(listado,index) in listadoCajas"
              :key="index"
              class="font-weight-bold"
              :value="index"
            >Caja {{listado.descripcion}}</option>
          </select>
        </div>

        <div class="w-100"></div>
        <div class="col ml-1 mr-1 mb-1">
          <div v-if="cajaSeleccionada !=  99999">
            <button
              v-b-modal.modalAbrirCaja
              v-if="$auth.can('Abrir Caja') && listadoCajas[cajaSeleccionada]['estado'] == 0"
              class="btn btn-sm btn-success"
            >Abrir caja</button>
            <button
              @click="showVentas = true"
              v-if="($auth.can('Ver Listado Ventas') || $auth.can('Ver Listado Ventas CC')) && listadoCajas[cajaSeleccionada]['estado'] == 1"
              class="btn btn-sm btn-primary"
            >Continuar operaciones</button>
            <button
              v-b-modal.modalCierreCaja
              @click="datosCierreCaja(listadoCajas[cajaSeleccionada]['id'])"
              v-if="$auth.can('Cerrar Caja') && listadoCajas[cajaSeleccionada]['estado'] == 1"
              class="btn btn-sm btn-danger"
            >Cierre de caja</button>
            <button
              v-b-modal.modalExtraccionCaja
              v-if="listadoCajas[cajaSeleccionada]['estado'] == 1 && $auth.can('Extraer Dinero Caja')"
              class="btn btn-sm btn-warning"
            >Extracciones</button>
              <button
              v-b-modal.modalIngresoCaja
              v-if="listadoCajas[cajaSeleccionada]['estado'] == 1 && $auth.can('Ingreso Dinero Caja') "
              class="btn btn-sm btn-success"
            >Ingreso Dinero</button>
          </div>
        </div>
      </div>

      <!--Proceso de ventas-->
      <div class="col col-lg" v-if="showVentas">
        <!-- Listado de ventas -->
        <div
          class="card mt-1"
          v-if="$auth.can('Ver Listado Ventas') && listadoVentas != '' && detalleVenta == '' && !facturar"
        >
          <div
            class="card-header text-primary bg-transparent font-weight-bold text-left"
          >Listado de ventas</div>
          <div class="card-body mb-n2">
            <b-table
              id="listado-ventas"
              class="mt-1"
              style="font-size:12px;"
              outlined
              small
              hover
              fixed
              no-border-collapse
              head-variant="dark"
              :items="listadoVentas"
              :fields="fieldsListadoVentas"
            >
              <template v-slot:cell(estado)="row">
                <p v-if="row.item.estado == 0" style="font-size:16px;">
                  <span class="badge badge-warning">Pendiente</span>
                </p>
              </template>

              <template v-slot:cell(tipo_de_facturación)="row">
                <p
                  v-if="row.item.tipo_venta == 'Factura_A'"
                  class="font-weight-bold text-danger"
                >Factura A</p>
                <p
                  v-if="row.item.tipo_venta == 'Factura_B'"
                  class="font-weight-bold text-danger"
                >Factura B</p>
                <p
                  v-if="row.item.tipo_venta == 'Consumidor_Final'"
                  class="font-weight-bold text-danger"
                >Consumidor Final</p>
                <p
                  v-if="row.item.tipo_venta == 'No_Facturar'"
                  class="font-weight-bold text-danger"
                >No se requiere facturación</p>
              </template>

              <template v-slot:cell(total)="row">
                <p class="font-weight-bold text-success">${{row.item.total}}</p>
              </template>

              <template v-slot:cell(acciones)="row">
                <button
                  @click="getDetalleVenta(row.item.id,row.index)"
                  title="Detalle de la venta"
                  class="btn btn-sm btn-primary"
                >
                  <b-icon-document-text></b-icon-document-text>
                </button>
                <button
                  @click="deleteVenta(row.item.id)"
                  title="Eliminar venta"
                  class="btn btn-sm btn-danger"
                >
                  <b-icon-trash></b-icon-trash>
                </button>
                <button
                  @click="confirmacionVenta(row.index)"
                  title="Proceder a la facturación"
                  class="btn btn-sm btn-success"
                >
                  <b-icon-check></b-icon-check>
                </button>
              </template>
            </b-table>
          </div>
        </div>

        <h5
          v-if="listadoVentas == '' && listadoVentasAutorizadas == ''"
        >En este momento no hay ventas que requieran facturación</h5>

        <!--Card header de ventas autorizadas por cuenta corriente-->
        <div
          class="card mt-1 mb-1"
          v-if="listadoVentasAutorizadas != '' &&  detalleVenta == '' && !facturar && $auth.can('Ver Listado Ventas CC')"
        >
          <div
            class="card-header text-primary bg-transparent font-weight-bold text-left"
          >Listado de ventas autorizadas por Cuenta Corriente</div>

          <!-- Listado de ventas autorizadas -->
          <div
            class="card-body mb-n2"
            v-if="listadoVentasAutorizadas != '' && detalleVenta == '' && !facturar"
          >
            <b-table
              id="listado-ventas"
              class="mt-1"
              style="font-size:12px;"
              :outlined="true"
              :small="true"
              :hover="true"
              :fixed="true"
              :no-border-collapse="true"
              head-variant="dark"
              :items="listadoVentasAutorizadas"
              :fields="fieldsListadoVentasAutorizadas"
            >
              <template v-slot:cell(estado)="row">
                <p v-if="row.item.estado == 2" style="font-size:16px;">
                  <span class="badge badge-primary">Autorizada</span>
                </p>
              </template>
              <template v-slot:cell(tipo_de_facturación)="row">
                <p
                  v-if="row.item.tipo_venta == 'Factura_A'"
                  class="font-weight-bold text-danger"
                >Factura A</p>
                <p
                  v-if="row.item.tipo_venta == 'Factura_B'"
                  class="font-weight-bold text-danger"
                >Factura B</p>
                <p
                  v-if="row.item.tipo_venta == 'Consumidor_Final'"
                  class="font-weight-bold text-danger"
                >Consumidor Final</p>
                <p
                  v-if="row.item.tipo_venta == 'No_Facturar'"
                  class="font-weight-bold text-danger"
                >No se requiere facturación</p>
              </template>

              <template v-slot:cell(total)="row">
                <p class="font-weight-bold text-success">${{row.item.total}}</p>
              </template>

              <template v-slot:cell(acciones)="row">
                <button
                  @click="getDetalleVentaAutorizada(row.item.id,row.index);isVentaAutorizada=true;"
                  title="Detalle de la venta"
                  class="btn btn-sm btn-primary"
                >
                  <b-icon-document-text></b-icon-document-text>
                </button>
                <button
                  @click="deleteVenta(row.item.id)"
                  title="Eliminar venta"
                  class="btn btn-sm btn-danger"
                >
                  <b-icon-trash></b-icon-trash>
                </button>
                <button
                  @click="isVentaAutorizada=true;confirmacionVenta(row.index);"
                  title="Confirmar venta"
                  class="btn btn-sm btn-success"
                >
                  <b-icon-check></b-icon-check>
                </button>
              </template>

              <template v-slot:cell(cuit)="row">
                <p v-if="row.item.cuit != null">{{row.item.cuit}}</p>
                <p v-else>-</p>
              </template>
            </b-table>
          </div>
        </div>

        <!-- Detalle de la venta -->
        <div class="card-body" v-if="detalleVenta != '' && !facturar">
          <!--Datos de la venta autorizada en el detalle-->
          <b-table
            id="listado-ventas"
            class="mt-1"
            style="font-size:12px;"
            :outlined="true"
            :small="true"
            :hover="true"
            :fixed="true"
            responsive
            :no-border-collapse="true"
            head-variant="dark"
            :items="datosVentaEspecifica"
            :fields="fieldsDatosVentaEspecificaAutorizada"
            v-if="isVentaAutorizada"
          >
            <template v-slot:cell(estado)="row">
              <p v-if="row.item.estado == 2" style="font-size:16px;">
                <span class="badge badge-primary">Autorizada</span>
              </p>
            </template>

            <template v-slot:cell(total)="row">
              <p class="font-weight-bold text-success">${{row.item.total}}</p>
            </template>

            <template v-slot:cell(tipo_de_facturación)="row">
              <p
                v-if="row.item.tipo_venta == 'Factura_A'"
                class="font-weight-bold text-danger"
              >Factura A</p>
              <p
                v-if="row.item.tipo_venta == 'Factura_B'"
                class="font-weight-bold text-danger"
              >Factura B</p>
              <p
                v-if="row.item.tipo_venta == 'Consumidor_Final'"
                class="font-weight-bold text-danger"
              >Consumidor Final</p>
              <p
                v-if="row.item.tipo_venta == 'No_Facturar'"
                class="font-weight-bold text-danger"
              >No se requiere facturación</p>
            </template>

            <template v-slot:cell(cuit)="row">
              <p v-if="row.item.cuit != null">{{row.item.cuit}}</p>
              <p v-else>-</p>
            </template>
          </b-table>

          <!--Datos de la venta en el detalle-->
          <b-table
            id="listado-ventas"
            class="mt-1"
            style="font-size:12px;"
            :outlined="true"
            :small="true"
            :hover="true"
            responsive
            :fixed="true"
            :no-border-collapse="true"
            head-variant="dark"
            :items="datosVentaEspecifica"
            :fields="fieldsDatosVentaEspecifica"
            v-if="!isVentaAutorizada"
          >
            <template v-slot:cell(estado)="row">
              <p v-if="row.item.estado == 0" style="font-size:16px;">
                <span class="badge badge-warning">Pendiente</span>
              </p>
            </template>

            <template v-slot:cell(total)="row">
              <p class="font-weight-bold text-success">${{row.item.total}}</p>
            </template>
          </b-table>

          <!--Header detalle de la venta-->
          <div class="card-header border my-auto bg-dark text-center text-white">
            <h5 class="font-weight-bold mb-n2">Detalle de la venta</h5>
          </div>

          <!--Detalle de la venta-->
          <b-table
            style="font-size:12px;"
            :outlined="true"
            :small="true"
            :fixed="true"
            :no-border-collapse="true"
            head-variant="dark"
            :items="detalleVenta.data"
            :fields="fieldsDetalleVenta"
          >
            <template v-slot:cell(precio_venta)="row">
              <p class="font-weight-bold text-success">${{row.item.precio_venta}}</p>
            </template>

            <template v-slot:cell(subtotal)="row">
              <p class="font-weight-bold text-success">${{row.item.subtotal}}</p>
            </template>
          </b-table>

          <!--Botones de acciones-->

          <div class="mb-1">
            <button
              title="Volver al listado de ventas"
              class="btn btn-sm btn-primary"
              @click="resetVariables()"
            >Volver</button>
            <button
              title="Proceder a la facturación"
              class="btn btn-sm btn-success"
              @click="confirmacionVenta(0)"
            >Continuar</button>
            <button
              title="Rechazar venta"
              class="btn btn-sm btn-danger"
              @click="deleteVenta(datosVentaEspecifica[0]['id'])"
            >Rechazar venta</button>
          </div>
        </div>

        <!-- Proceso de facturación -->
        <div v-if="facturar">
          <!--Si no es una venta autorizada-->
          <div class="card-body" v-if="!isVentaAutorizada">
            <h5 class="font-weight-bold">Seleccione la condición de venta</h5>
            <b-form-group>
              <b-form-radio-group
                id="condicion_venta"
                v-model="condicion_venta"
                :options="options_condicion_venta"
                button-variant="outline-success"
                class="btn-block"
                size="sm"
                buttons
                name="radios-btn-condicion-venta"
              ></b-form-radio-group>
            </b-form-group>

            <div v-if="condicion_venta == 'Efectivo'" class="row">
              <div class="col col-lg">
                <label class="font-weight-bold">Descuento efectivo:</label>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">$</span>
                  </div>
                  <input
                    v-model="descuentoEfectivo"
                    @change="aplicarDescuentos()"
                    type="number"
                    class="form-control"
                    step="0.01"
                  />
                </div>
              </div>
              <div class="col col-lg">
                <label class="font-weight-bold">Descuento procentual:</label>
                <div class="input-group">
                  <input
                    v-model="descuentoProcentual"
                    @change="aplicarDescuentos()"
                    type="number"
                    class="form-control"
                    step="0.01"
                  />
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Si es una venta autorizada -->
          <div v-if="isVentaAutorizada && clienteRegistrado != '' && datosVentaEspecifica != ''">
            <!--Datos de la venta autorizada en el detalle-->
            <b-table
              id="listado-ventas"
              class="mt-1"
              style="font-size:12px;"
              :outlined="true"
              :small="true"
              :hover="true"
              :fixed="true"
              :no-border-collapse="true"
              head-variant="dark"
              :items="datosVentaEspecifica"
              :fields="fieldsDatosVentaEspecificaAutorizada"
            >
              <template v-slot:cell(estado)="row">
                <p v-if="row.item.estado == 2" style="font-size:16px;">
                  <span class="badge badge-primary">Autorizada</span>
                </p>
              </template>

              <template v-slot:cell(total)="row">
                <p class="font-weight-bold text-success">${{row.item.total}}</p>
              </template>

              <template v-slot:cell(tipo_de_facturación)="row">
                <p
                  v-if="row.item.tipo_venta == 'Factura_A'"
                  class="font-weight-bold text-danger"
                >Factura A</p>
                <p
                  v-if="row.item.tipo_venta == 'Factura_B'"
                  class="font-weight-bold text-danger"
                >Factura B</p>
                <p
                  v-if="row.item.tipo_venta == 'Consumidor_Final'"
                  class="font-weight-bold text-danger"
                >Consumidor Final</p>
                <p
                  v-if="row.item.tipo_venta == 'No_Facturar'"
                  class="font-weight-bold text-danger"
                >No se requiere facturación</p>
              </template>

              <template v-slot:cell(cuit)="row">
                <p v-if="row.item.cuit != null">{{row.item.cuit}}</p>
                <p v-else>-</p>
              </template>
            </b-table>
          </div>

          <!-- Total de la venta y datos importantes -->
          <div v-if="datosVentaEspecifica != ''" class="row">
            <div class="col col-lg form-group mt-1">
              <!--Saldo en cuenta corriente en caso que sea 0-->
              <h5
                v-if="(isVentaAutorizada || condicion_venta == 'Cuenta_Corriente')
                                && clienteRegistrado != ''
                                && clienteRegistrado[0]['total_cc'] == 0"
                class="font-weight-bold"
              >
                Saldo en cuenta corriente:
                <span
                  class="text-warning"
                >${{clienteRegistrado[0]['total_cc']}}</span>
              </h5>

              <!--Saldo en cuenta corriente en caso que el cliente deba-->
              <h5
                v-if="(isVentaAutorizada || condicion_venta == 'Cuenta_Corriente')
                                && clienteRegistrado != ''
                                && clienteRegistrado[0]['total_cc'] > 0"
                class="font-weight-bold"
              >
                Saldo en cuenta corriente:
                <span
                  class="text-danger"
                >${{clienteRegistrado[0]['total_cc']}}</span>
              </h5>

              <!--Saldo en cuenta corriente en caso que el cliente tenga saldo a favor-->
              <h5
                v-if="(isVentaAutorizada || condicion_venta == 'Cuenta_Corriente')
                                && clienteRegistrado != ''
                                && clienteRegistrado[0]['total_cc'] < 0"
                class="font-weight-bold"
              >
                Saldo en cuenta corriente:
                <span
                  class="text-success"
                >${{clienteRegistrado[0]['total_cc'].toString().slice(1)}}</span>
              </h5>

              <!--Monto máximo que el cliente puede retirar sin pedir autorización-->
              <h5
                v-if="(isVentaAutorizada || condicion_venta == 'Cuenta_Corriente')
                                && clienteRegistrado != ''"
                class="font-weight-bold"
              >
                Monto máximo en cuenta corriente:
                <span
                  class="text-info"
                >${{clienteRegistrado[0]['monto_maximo_cc']}}</span>
              </h5>

              <!--Total de la venta realizada-->
              <h5 v-if="datosVentaEspecifica != ''" class="font-weight-bold">
                Total de la venta:
                <span class="text-primary">${{datosVentaEspecifica[0]['total']}}</span>
              </h5>
            </div>
          </div>

          <!-- Botones de operaciones -->
          <div class="row mb-2">
            <div class="col col-lg">
              <!--Botón que se muestra en caso que la venta requiera autorización-->
              <button
                @click="autorizarVentaCuentaCorriente(datosVentaEspecifica[0]['id'])"
                v-if="(clienteRegistrado != '' && datosVentaEspecifica != '')
                                && (condicion_venta == 'Cuenta_Corriente' && !isVentaAutorizada)
                                && (clienteRegistrado[0]['monto_maximo_cc']-clienteRegistrado[0]['total_cc'] < datosVentaEspecifica[0]['total'])"
                title="Solicitar autoriación para realizar la venta por Cuenta Corriente"
                class="btn btn-sm btn-warning"
              >Solicitar autorización</button>

              <!--Botón que realiza una venta de cuenta corriente si no requiere autorización-->
              <button
                @click="facturarCuentaCorriente(datosVentaEspecifica[0]['id'],clienteRegistrado[0]['id'])"
                v-if="(clienteRegistrado != '' && datosVentaEspecifica != '' && condicion_venta == 'Cuenta_Corriente')
                                && (clienteRegistrado[0]['monto_maximo_cc']-clienteRegistrado[0]['total_cc'] > datosVentaEspecifica[0]['total'])"
                title="Realizar venta por cuenta corriente"
                class="btn btn-sm btn-success"
              >Realizar venta</button>

              <!--Botón que realiza una venta de cuenta corriente si ya está autorizada-->
              <button
                @click="facturarCuentaCorriente(datosVentaEspecifica[0]['id'],clienteRegistrado[0]['id'])"
                v-if="clienteRegistrado != '' && datosVentaEspecifica != '' && isVentaAutorizada"
                title="Realizar venta por cuenta corriente"
                class="btn btn-sm btn-success"
              >Realizar venta</button>

              <!--Botón que realiza una venta que no es del tipo cuenta corriente-->
              <button
                @click="facturarVenta(datosVentaEspecifica[0]['id'])"
                v-if="condicion_venta != 'Cuenta_Corriente'"
                title="Realizar venta"
                class="btn btn-sm btn-success"
              >Realizar venta</button>

              <!--Botón que elimina o rechaza una venta-->
              <button
                @click="deleteVenta(datosVentaEspecifica[0]['id'])"
                title="Rechazar venta"
                class="btn btn-sm btn-danger"
              >Rechazar</button>
              <button
                title="Volver al listado de ventas"
                class="btn btn-sm btn-primary"
                @click="resetVariables()"
              >Volver</button>
            </div>
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
  disableStats: true,
});

export default {
  data() {
    return {
      descuentoEfectivo: 0,
      descuentoProcentual: 0,
      //LISTADO DE CAJAS
      listadoCajas: [], //ARRAY con el listado de las cajas que hay en la sucursal
      cajaSeleccionada: 99999, //Caja que fue seleccionada para operar
      showVentas: false, //Boolean para permitir ver las ventas luego de seleccionar la caja

      //ABRIR CAJA
      options: [
        { text: "Abrir caja con el último monto registrado", value: "radio1" },
        { text: "Abrir caja con otro monto", value: "radio2" },
      ],
      selected: "radio1",
      monto_abrir: "",

      //EXTRACCIONES
      montoExtraccion: "",
      motivoExtraccion: "",
      motivoIngreso : "",
      montoIngreso:"",

      //CIERRE DE CAJA
      datosCajaCerrar: [],
      montoCajaCerrar: "",
      obsCierre: "",
      montoNuevo: "",

      //LISTADO DE VENTAS PARA PROCESAR
      listadoVentas: [], //ARRAY con el listado de las ventas
      fieldsListadoVentas: [
        { key: "estado", label: "Estado" },
        { key: "cliente", label: "Cliente" },
        { key: "cuit", label: "CUIT" },
        { key: "domicilio", label: "Domicilio" },
        { key: "fecha", label: "Fecha de la venta" },
        "tipo_de_facturación",
        "total",
        "acciones",
      ], //COLUMNAS de la tabla de las ventas

      //DETALLES DE LA VENTA
      detalleVenta: [], //ARRAY con el detalle de la venta
      fieldsDetalleVenta: [
        { key: "Descripcion", label: "Producto" },
        { key: "precio_venta", label: "Precio de venta" },
        { key: "cantidad", label: "Cantidad" },
        { key: "subtotal", label: "Subtotal" },
      ], //COLUMNAS de la tabla del detalle de la venta
      datosVentaEspecifica: [], //ARRAY con los datos del cliente y la venta
      fieldsDatosVentaEspecifica: [
        { key: "estado", label: "Estado" },
        { key: "fecha", label: "Fecha de la venta" },
        "total",
      ], //COLUMNAS de la tabla con los datos del cliente y la venta
      fieldsDatosVentaEspecificaAutorizada: [
        { key: "estado", label: "Estado" },
        { key: "cliente", label: "Cliente" },
        { key: "cuit", label: "CUIT" },
        { key: "domicilio", label: "Domicilio" },
        { key: "fecha", label: "Fecha de la venta" },
        "tipo_de_facturación",
        "total",
      ], //COLUMNAS de la tabla con los datos del cliente y la venta de una venta que fue autorizada y es por cuenta corriente

      //PROCESO DE FACTURACIÓN
      facturar: false, //Confirmar si se factura o no
      options_condicion_venta: [
        { text: "Efectivo", value: "Efectivo" },
        { text: "Tarjeta de débito", value: "Débito" },
        { text: "Tarjeta de crédito", value: "Crédito" },
      ],
      datosRequeridos: false, //Seleccionar si se desean insertar datos de un cliente específico
      tipo_venta: "", //Tipo de venta (Tipo de facturación)
      condicion_venta: "", //Condición de venta (Medios de pago)
      cuit: "", //CUIT que se guarda al autorizar o facturar
      domicilio: "", //DOMICILIO que se guarda al autorizar o facturar
      cliente: "", //NOMBRE DEL CLIENTE que se guarda al autorizar o facturar
      clienteRegistrado: [], //Aca se almacena si el cliente se encuentra registrado en la base de datos
      clienteNoRegistrado: true,
      isVentaAutorizada: false,
      cuentaCorriente: false,
    

      //LISTADO DE VENTAS AUTORIZADAS
      listadoVentasAutorizadas: [], //ARRAY con el listado de las ventas
      fieldsListadoVentasAutorizadas: [
        { key: "estado", label: "Estado" },
        { key: "cliente", label: "Cliente" },
        { key: "cuit", label: "CUIT" },
        { key: "domicilio", label: "Domicilio" },
        { key: "fecha", label: "Fecha de la venta" },
        "tipo_de_facturación",
        "total",
        "acciones",
      ], //COLUMNAS de la tabla de las ventas
    };
  },
  methods: {
    //Facturar ventas en efectivo, tarjetas de debito y crédito
    facturarVenta(id, id_cliente) {
      if (
        this.condicion_venta != "" &&
        this.condicion_venta != "Cuenta_Corriente"
      ) {
        axios
          .post("api/finalizarVenta", {
            id_venta: this.datosVentaEspecifica[0]["id"],
            cajaSeleccionada: this.listadoCajas[this.cajaSeleccionada]["id"],
            condicion_venta: this.condicion_venta,
            userid: window.user.user.id,
          })
          .then((response) => {
            if (response.data == 0) {
              this.resetVariables();
              alertify.success("Venta realizada correctamente");
            } else {
              alertify.error(
                "Ha ocurrido un error al intentar realizar la venta"
              );
            }
          })
          .catch((error) => {
            alertify.error(
              "Ha ocurrido un error al intentar realizar la venta"
            );
          });
      } else {
        alertify.error("Por favor seleccione una condición de venta");
      }
    },
    //Facturar cuenta corriente venta directa
    facturarCuentaCorriente(id, id_cliente) {
      axios
        .post("api/finalizarVentaCuentaCorriente", {
          id: id,
          id_cliente: id_cliente,
          cuit: this.cuit,
          cliente: this.cliente,
          domicilio: this.domicilio,
          tipo_venta: this.tipo_venta,
          condicion_venta: this.condicion_venta,
          userid: window.user.user.id,
        })
        .then((response) => {
          if (response.data == 1) {
            this.resetVariables();
            alertify.success("Venta finalizada correctamente");
          } else {
            alertify.error("Ha ocurrido un error al finalizar la venta");
          }
        })
        .catch((error) => {
          alertify.error("Ha ocurrido un error al finalizar la venta");
        });
    },
    //Confirmar el proceso de venta
    confirmacionVenta(index) {
      if (!this.$auth.can("Facturar Venta")) {
        alertify.error(
          "Usted no esta autorizado a realizar la facturación de las ventas"
        );
      } else {
        this.datosVentaEspecifica = [];

        if (this.isVentaAutorizada) {
          this.datosVentaEspecifica.push(this.listadoVentasAutorizadas[index]);
          this.cuit = this.listadoVentasAutorizadas[index]["cuit"];
          this.condicion_venta = "Cuenta_Corriente";
          this.getDatosClienteRegistradoVenta();
        } else {
          this.datosVentaEspecifica.push(this.listadoVentas[index]);
        }

        this.facturar = true;
      }
    },
    //Obtener los datos del cliente en caso de que este registrado
    getDatosClienteRegistradoVenta() {
      if (this.cuit.toString().length == 11) {
        let cuitEditado =
          this.cuit.toString().slice(0, 2) +
          "-" +
          this.cuit.toString().slice(2, 10) +
          "-" +
          this.cuit.toString().slice(10 - 11);

        axios
          .post("api/getDatosClienteRegistradoVenta", {
            cuitEditado: cuitEditado,
            cuit: this.cuit,
          })
          .then((response) => {
            if (response.data.length > 0) {
              this.clienteRegistrado = response.data;
              this.cliente = this.clienteRegistrado[0]["Nombre"];
              this.domicilio = this.clienteRegistrado[0]["Domicilio"];
              this.clienteNoRegistrado = false;
              this.verificarCuentaCorriente(this.cuit, cuitEditado);
            }
          })
          .catch((error) => {
            alertify.error(
              "Ha ocurrido un error al enviar el CUIT a la base de datos"
            );
          });
      } else {
        this.cliente = "";
        this.domicilio = "";
        this.clienteRegistrado = [];
        this.clienteNoRegistrado = true;
        this.cuentaCorriente = false;
        this.condicion_venta = "";
      }
    },
    //Verifica que el cliente tenga cuenta corriente
    verificarCuentaCorriente(cuit, cuitEditado) {
      axios
        .post("api/verificarCuentaCorriente", {
          cuit: cuit,
          cuitEditado: cuitEditado,
          userid: window.user.user.id,
        })
        .then((response) => {
          if (response.data["cuenta_corriente"] == 1) {
            this.cuentaCorriente = true;
          } else {
            this.cuentaCorriente = false;
          }
        })
        .catch((error) => {
          alertify.error(
            "Ha ocurrido un error al intentar enviar la verificación de cuenta corriente"
          );
        });
    },
    datosCierreCaja(id) {
      axios
        .post("api/datosCierreCaja", {
          id: id,
          userid: window.user.user.id,
        })
        .then((response) => {
          this.datosCajaCerrar = response.data;
        });
    },
    //Realizar extracciones
    realizarExtraccion(id) {
      let errors = true;

      if (this.montoExtraccion == "") {
        alertify.error("Debe ingresar el monto de la extracción");
        errors = false;
      }

      if (this.motivoExtraccion == "") {
        alertify.error("Debe ingresar el motivo de la extracción");
        errors = false;
      }

      if (errors) {
        axios
          .post("api/realizarExtraccion", {
            id: id,
            montoExtraccion: this.montoExtraccion,
            motivoExtraccion: this.motivoExtraccion,
            userid: window.user.user.id,
          })
          .then((response) => {
            if (response.data == 0) {
              this.$refs["modalExtraccionCaja"].hide();
              this.montoExtraccion = "";
              this.motivoExtraccion = "";
              alertify.success("Extracción realizada correctamente");
            } else {
              alertify.error("Ha ocurrido un error al realizar la extracción");
            }
          })
          .catch((error) => {
            alertify.error("Ha ocurrido un error al realizar la extracción");
          });
      }
    },


    IngresarDinero(id){
          let errors = true;

      if (this.montoIngreso== "") {
        alertify.error("Debe ingresar el monto del ingreso de dinero");
        errors = false;
      }

      if (this.motivoIngreso == "") {
        alertify.error("Debe ingresar el motivo del ingreso de dinero");
        errors = false;
      }

      if (errors) {
        axios
          .post("api/realizarIngreso", {
            id: id,
            montoIngreso: this.montoIngreso,
            motivoIngreso: this.motivoIngreso,
            userid: window.user.user.id,
          })
          .then((response) => {
            if (response.data == 0) {
              this.$refs["modalExtraccionCaja"].hide();
              this.montoIngreso = "";
              this.motivoIngreso = "";
              alertify.success("Ha ingresado Correctamente");
            } else {
              alertify.error("Ha ocurrido un error al realizar la extracción");
            }
          })
          .catch((error) => {
            alertify.error("Ha ocurrido un error al realizar la extracción");
          });
      }
    },
    //Abre la caja
    abrirCaja(id) {
      axios
        .post("api/abrirCaja", {
          id: id,
          selected: this.selected,
          monto_abrir: this.monto_abrir,
          userid: window.user.user.id,
        })
        .then((response) => {
          if (response.data == 1) {
            this.listadoCajas[this.cajaSeleccionada]["estado"] = 1;
            this.$refs["modalAbrirCaja"].hide();
            if ($auth.can("Ver Listado Ventas")) {
              this.showVentas = true;
            }
            alertify.success("Caja abierta correctamente");
          } else {
            alertify.error("Ha ocurrido un error a la hora de abrir la caja");
          }
        })
        .catch((error) => {
          console.log("ERROR al realizar apertura de caja");
        });
    },
    cerrarCaja(id) {
      let errors = true;

      if (this.montoCajaCerrar == "") {
        alertify.error("Ingrese el monto que hay en caja");
        errors = false;
      }

      if (this.montoNuevo == "") {
        alertify.error("Ingrese el monto que queda en caja luego del cierre");
        errors = false;
      }

      if (errors) {
        alertify
          .confirm(
            "Cerrar Caja",
            "¿Esta seguro de cerrar la caja?",
            () => {
              axios
                .post("api/cerrarCaja", {
                  id: id,
                  monto: this.montoCajaCerrar,
                  obs: this.obsCierre,
                  monto_nuevo: this.montoNuevo,
                  userid: window.user.user.id,
                })
                .then((response) => {
                  if (response.data == 0) {
                    this.cajaSeleccionada = 99999;
                    this.listadoCajas = [];
                    this.getCajas();
                    this.montoCajaCerrar = "";
                    this.obsCierre = "";
                    this.montoNuevo = "";
                    this.$refs["modalCierreCaja"].hide();
                    alertify.success("Caja cerrada correctamente");
                  } else {
                    alertify.error(
                      "Ha ocurrido un error al intentar cerrar la caja"
                    );
                  }
                });
            },
            () => {
              alertify.error("Se ha cancelado la operación");
            }
          )
          .set("labels", { ok: "Cerrar la caja", cancel: "Cancelar" });
      }
    },
    //Obtiene el listado de cajas disponibles en el locas
    getCajas() {
      axios
        .post("api/getCajas")
        .then((response) => {
          this.listadoCajas = response.data;
        })
        .catch((error) => {
          console.log("ERROR al cargar las cajas");
        });
    },
    //Obtiene el listado de ventas
    getVentas() {
      axios
        .post("api/getVentas")
        .then((response) => {
          this.listadoVentas = response.data;
        })
        .catch((error) => {
          console.log("ERROR al cargar las ventas");
        });
    },
    //Obtiene el listado de ventas por cuenta corriente que han sido autorizadas
    getVentasAutorizadas() {
      axios
        .post("api/getVentasAutorizadas")
        .then((response) => {
          this.listadoVentasAutorizadas = response.data;
        })
        .catch((error) => {
          console.log("ERROR al cargar las ventas autorizadas");
        });
    },
    //Obtiene el detalle de una venta específica
    getDetalleVenta(id_venta, index) {
      if (!this.$auth.can("Ver Detalle Ventas")) {
        alertify.error("Usted no está autorizado a ver el detalle de la venta");
      } else {
        this.datosVentaEspecifica = [];
        this.detalleVenta = [];
        axios
          .post("api/getDetalleVenta", {
            id: id_venta,
          })
          .then((response) => {
            this.detalleVenta = response;
            this.datosVentaEspecifica.push(this.listadoVentas[index]);
          })
          .catch((error) => {
            console.log("Error al obtner el detalle de venta");
          });
      }
    },
    //Obtiene el detalle de una venta autorizada específica
    getDetalleVentaAutorizada(id_venta, index) {
      if (!this.$auth.can("Ver Detalle Ventas")) {
        alertify.error("Usted no está autorizado a ver el detalle de la venta");
      } else {
        this.datosVentaEspecifica = [];
        this.detalleVenta = [];

        axios
          .post("api/getDetalleVenta", {
            id: id_venta,
          })
          .then((response) => {
            this.detalleVenta = response;
            this.datosVentaEspecifica.push(
              this.listadoVentasAutorizadas[index]
            );
          })
          .catch((error) => {
            console.log("Error al obtener el detalle de venta");
          });
      }
    },
    //Elimina una venta
    deleteVenta(id) {
      if (!this.$auth.can("Rechazar Ventas")) {
        alertify.error("Usted no está autorizado a rechazar ventas");
      } else {
        alertify
          .confirm(
            "Rechazar venta",
            "¿Esta seguro de rechazar la venta?",
            () => {
              axios
                .post("api/deleteVentas", {
                  id: id,
                  userid: window.user.user.id,
                })
                .then((response) => {
                  if (response.data == 1) {
                    this.resetVariables();
                    alertify.success("Venta rechazada correctamente");
                  } else {
                    alertify.error(
                      "Ha ocurrido un error al intentar eliminar la venta"
                    );
                  }
                })
                .catch((error) => {
                  alertify.error(
                    "Ha ocurrido un error al intentar eliminar la venta"
                  );
                });
            },
            () => {
              alertify.error("Se ha cancelado la operación");
            }
          )
          .set("labels", { ok: "Rechazar venta", cancel: "Cancelar" });
      }
    },
    //Volver atras
    resetVariables() {
      this.facturar = false;
      this.condicionVenta = "";
      this.datosCliente = [];
      this.cuentaCorriente = false;
      this.datosVentaEspecifica = [];
      this.detalleVenta = [];
      this.isVentaAutorizada = false;
      this.datosRequeridos = false;
      this.tipo_venta = "";
      this.condicion_venta = "";
      this.cuit = "";
      this.domicilio = "";
      this.cliente = "";
      this.clienteRegistrado = [];
      this.clienteNoRegistrado = true;
    },
    //Envía una venta por cuenta corriente a autorización
    autorizarVentaCuentaCorriente(id) {
      axios
        .post("api/autorizarVentaCuentaCorriente", {
          id: id,
          cuit: this.cuit,
          domicilio: this.domicilio,
          cliente: this.cliente,
          condicion_venta: this.condicion_venta,
          tipo_venta: this.tipo_venta,
        })
        .then((response) => {
          if (response.data == 1) {
            this.facturar = false;
            this.resetVariables();
            alertify.success("La venta se ha enviado a autorización");
          } else {
            alertify.error("No se ha podido autorizar la venta");
          }
        })
        .catch((error) => {
          alertify.error(
            "Se ha producido un error al intentar enviar la autorización"
          );
        });
    },
  },
  beforeMount() {
    console.log(window.user.user.id);

    this.getCajas();
    this.getVentas();
    this.getVentasAutorizadas();

    window.Echo.channel("channel-VentaCreada").listen("VentaCreada", (e) => {
      this.listadoVentas = e.ventas;
    });

    window.Echo.channel("channel-VentaEliminada").listen(
      "VentaEliminada",
      (e) => {
        this.listadoVentas = e.ventas;
        this.listadoVentasAutorizadas = e.ventasAutorizadas;
      }
    );

    window.Echo.channel("channel-VentaAutorizada").listen(
      "VentaAutorizada",
      (e) => {
        this.listadoVentas = e.ventas;
        this.listadoVentasAutorizadas = e.ventasAutorizadas;
      }
    );

    window.Echo.channel("channel-VentaFinalizada").listen(
      "VentaFinalizada",
      (e) => {
        this.listadoVentas = e.ventas;
        this.listadoVentasAutorizadas = e.ventasAutorizadas;
      }
    );
  },
};
</script>
