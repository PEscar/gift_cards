/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

window.Vue = require('vue')

import {ServerTable, Event} from 'vue-tables-2';
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.use(ServerTable)
Vue.component('date-range-picker', DateRangePicker)

Vue.component('gc-validation-item', require('./components/ValidarGiftcardComponent.vue').default)
Vue.component('nueva-venta-item', require('./components/NuevaVentaComponent.vue').default)
Vue.component('nuevo-producto-item', require('./components/NuevoProductoComponent.vue').default)
Vue.component('nueva-empresa-item', require('./components/NuevaEmpresaComponent.vue').default)
Vue.component('informe-ventas-component', require('./components/InformeVentaComponent.vue').default)
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});