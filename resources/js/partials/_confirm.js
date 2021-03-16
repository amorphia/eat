/**
 *
 *  Vue.confirm helper class to set sensible defaults
 *
 */
window.App.confirm =  ( callback, options ) => {

    let defaults = {
        message: 'Are you sure you want to delete this?',
        button: {
            yes: 'Yes',
            no: 'Cancel'
        },
        callback: confirm => {
            if( confirm ) callback();
        }
    };

    Object.assign( defaults, options );
    Vue.$confirm( defaults );
};
