
import Vue from 'vue';


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue' //Añadido
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'
import 'bootstrap-vue/dist/bootstrap-vue.css' //Añadido
import Auth from './auth'

Vue.use(BootstrapVue) //Añadido
Vue.use(IconsPlugin) //Añadido

require('./bootstrap');
window.Vue = require('vue');

Vue.prototype.$auth = new Auth(window.user);

Vue.component('anula-component', require('./components/AnulaComponent.vue').default);
Vue.component('reporte-component', require('./components/ReportesComponent.vue').default);
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('transfer-component', require('./components/TransferenciasComponent.vue').default);
Vue.component('client-component', require('./components/ClienteComponent.vue').default);
Vue.component('listransfer-component', require('./components/ListadoTransferenciasComponent.vue').default);
Vue.component('recepciones-component', require('./components/RecepcionesComponent.vue').default);
Vue.component('dash-component', require('./components/DashboardComponent.vue').default);
Vue.component('chart', require('./components/Chart.vue').default);
//Componentes nuevos
//Proveeedores
Vue.component('proveedor-component', require('./components/ProveedorComponent.vue').default);
//Presupuesto
Vue.component('presupuesto-component', require('./components/presupuestoComponent.vue').default); //Añadido
//Pedidos
Vue.component('pedido-component', require('./components/pedidosComponent.vue').default); //Añadido
//Pedidos
Vue.component('gestion-pedidos-component', require('./components/GestionPedidosProveedoresComponent.vue').default);

Vue.component('pedidoproveedor-component', require('./components/PedidoProveedorComponent.vue').default);

//Productos
Vue.component('producto-component', require('./components/ProductoComponent.vue').default);

Vue.component('autorizar-venta-cc', require('./components/AutorizarVentaCCComponent.vue').default); //Añadido

Vue.component('ventas-component', require('./components/VentasComponent.vue').default); //Añadido
Vue.component('caja-component', require('./components/CajaComponent.vue').default); //Añadido
Vue.component('ingreso-cobranza-component', require('./components/IngresoCobranzaComponent.vue').default); //Añadido

///Agrego Componente para probar webservice
Vue.component('service-component', require('./components/webServiceComponent.vue').default);
Vue.component('notas-credito-component', require('./components/NotasCreditoComponent.vue').default);
Vue.component('usuarios-component', require('./components/UsuariosComponent.vue').default); //Añadido
Vue.component('gestion-usuarios-component', require('./components/GestionUsuariosComponent.vue').default); //Añadido
//console.log("User object from vue",window.user);
const app = new Vue({
    el: '#app',
});
