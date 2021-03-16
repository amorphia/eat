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
window.Vue = Vue;

import VueRouter from 'vue-router';
Vue.use( VueRouter );


/*
* Import other plugins
 */
import VueConfirmDialog from 'vue-confirm-dialog';
Vue.use( VueConfirmDialog );
Vue.component('vue-confirm-dialog', VueConfirmDialog.default);

import VueLazyLoad from 'vue-lazyload';
Vue.use( VueLazyLoad );
Vue.component('InfiniteLoading', require('vue-infinite-loading'));

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import Vue2TouchEvents from 'vue2-touch-events';
Vue.use( Vue2TouchEvents );

import VueTour from 'vue-tour'
Vue.use(VueTour);

Vue.config.devtools = true;

/*
* Import day JS
*/
window.DayJs = require('dayjs');

// add custom parse format
const customParseFormat = require('dayjs/plugin/customParseFormat');
DayJs.extend(customParseFormat);

// add is between
const isBetween = require('dayjs/plugin/isBetween');
DayJs.extend(isBetween);


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
require( './partials/_state' ); // set up shared state
require( './partials/_cookies' ); // Cookie handler
require( './partials/_confirm' ); // Confirm dialog handler
require( './partials/_filters' ); // vue text filters
require( './partials/_date' ); // Date handler
require( './partials/_location' ); // Geolocation handler

// import our site vue router routes
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


window.vueApp = app;

require( './partials/_query' ); // Query string handler
