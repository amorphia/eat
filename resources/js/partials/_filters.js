/**
 * Apply lodash Start case
 */
Vue.filter( 'startCase', function ( value ) {
    if (!value) return '';
    return _.startCase( value );
});

/**
 * Replace hyphens with spaces
 */
Vue.filter( 'clearHyphens', function ( value ) {
    if (!value) return '';
    return value.replace( '-', ' ' );
});
