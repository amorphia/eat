/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./dependencies');

/*
* Import Vue and Vue Router
*/
import Vue from 'vue';
import VueRouter from 'vue-router';
import VueConfirmDialog from 'vue-confirm-dialog';
import VueLazyLoad from 'vue-lazyload'
import vSelect from 'vue-select'

window.Vue = Vue;
Vue.use( VueRouter );
Vue.use( VueConfirmDialog );
Vue.use( VueLazyLoad );

Vue.component('v-select', vSelect);
Vue.component('InfiniteLoading', require('vue-infinite-loading'));
Vue.component('vue-confirm-dialog', VueConfirmDialog.default);
Vue.config.devtools = true;




/*
* Import day JS
*/
window.Day = require('dayjs');

// add custom parse format
var customParseFormat = require('dayjs/plugin/customParseFormat');
Day.extend(customParseFormat);

// add is between
var isBetween = require('dayjs/plugin/isBetween');
Day.extend(isBetween);
/*
*
*  Register vue components globally
*
*/
const files = require.context( './components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));


/*
*
* Create global App object
*
*/
window.App = window.App || {};


/**
 *
 * Import partials
 */
require( './partials/_event' ); // Event emitter
require( './partials/_ajax' ); // ajax helper
require( './partials/_onload' ); // onload helper
require( './partials/_helpers' ); // set up shared state
require( './partials/_state' ); // set up shared state
require( './partials/_cookies' ); // Cookie handler
require( './partials/_confirm' ); // Confirm dialog handler
require( './partials/_date' ); // Date handler
//require( './partials/_filters' ); // filters
//require( './partials/_drag' ); // drag directive


import routes from './routes';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router: new VueRouter( routes ),
});
