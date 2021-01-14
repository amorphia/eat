/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./dependencies');

/*
*
*  Register vue components globally
*
*/
const files = require.context('./components', true, /\.vue$/i);
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
 *

require( './partials/_ajax' ); // ajax helper
require( './partials/_onload' ); // onload helper
require( './partials/_ifCsrf' ); // set ifCsrf handler
require( './partials/_helpers' ); // set up shared state
require( './partials/_state' ); // set up shared state
require( './partials/_event' ); // Event emitter
require( './partials/_cookies' ); // Cookie handler
require( './partials/_filters' ); // filters
require( './partials/_drag' ); // drag directive
require( './partials/_preload' ); // image preloader
 */

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
