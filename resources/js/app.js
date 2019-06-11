require('./bootstrap');
window.Vue = require('vue');
import Gate from './Gate'
import VueRouter from 'vue-router'
import {routes} from './Routes'
import VueProgressBar from 'vue-progressbar'
import { Form, HasError, AlertError } from 'vform'
import Swal from 'sweetalert2'
window.moment = require('moment');

Vue.prototype.$gate = new Gate(window.User);
if(APP_URL != undefined) {
    Vue.prototype.$url = APP_URL;
}


// Vue Router
Vue.use(VueRouter)
const router = new VueRouter({
    //mode: 'history',
    routes // short for `routes: routes`
})

// Vue Progressbar
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})

// Vform
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
window.Form = Form;

// Laravel Vue Pagination
Vue.component('pagination', require('laravel-vue-pagination'));

// Sweet alert
window.Swal = Swal;

window.Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

// Vue Global Filter
Vue.filter('capitalize', function(value) {
  return value.charAt(0).toUpperCase() + value.slice(1)
});

Vue.filter('formatedDate', function(date) {
  return moment(date).format('MMMM Do YYYY');
});

// Vue Event
window.Fire = new Vue();

Vue.component( 'passport-clients', require('./components/passport/Clients.vue').default );

Vue.component( 'passport-authorized-clients', require('./components/passport/AuthorizedClients.vue').default );

Vue.component( 'passport-personal-access-tokens', require('./components/passport/PersonalAccessTokens.vue').default );

Vue.component('page-not-found', require('./components/PageNotFound.vue').default);

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
});

