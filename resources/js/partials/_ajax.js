/**
 *
 *  The Ajax class wraps around Axios and provides a number of helpers
 *
 */
window.App.ajax = new class {

    /**
     * Submit an ajax post request
     *
     * @param {string} url - our endpoint url
     * @param {object} data
     * @param {string} message - (optional) a message to pass to a notification upon success
     * @returns {*|Promise}
     */
    post( url, data, message ){
        return this.axios( 'post', url, data, message );
    }


    /**
     * Submit an ajax get request
     *
     * @param {string} url - our endpoint url
     * @param {string} message - (optional) a message to pass to a notification upon success
     * @param {object} data - (optional) additional data to pass with the get request
     * @returns {*|Promise}
     */
    get( url, message, data = {} ){
        return this.axios( 'get', url, { params : data }, message );
    }


    /**
     * Submit an ajax patch request
     *
     * @param {string} url - our endpoint url
     * @param {object} data
     * @param {string} message - (optional) a message to pass to a notification upon success
     * @returns {*|Promise}
     */
    patch( url, data, message ){
        data = data || {};
        data._method = 'patch';
        return this.axios( 'post', url, data, message );
    }


    /**
     * Submit an ajax delete request
     *
     * @param {string} url - our endpoint url
     * @param {object} data
     * @param {string} message - (optional) a message to pass to a notification upon success
     * @returns {*|Promise}
     */
    delete( url, data, message ){
        data = data || {};
        data._method = 'delete';
        return this.axios( 'post', url, data, message );
    }


    /**
     * Submit an ajax post request with a file
     *
     * @param url
     * @param originalData - an originalData object passed from our partials/Form class
     * @param file
     * @param message
     * @returns {*|Promise<unknown>}
     */
    file( url, originalData, file, message ){
        // set multipart form headers
        let headers = { headers: { 'Content-Type': 'multipart/form-data' } };

        // build form data manually
        let data = new FormData();

        for ( let property in originalData ) {
            data.set( property, originalData[property] );
        }

        // add file to formdata
        data.append( "image", file );

        // post request
        return this.axios( 'post', url, data, message, headers )
    }

    /**
     * Submit our axios request
     *
     * @param method
     * @param url
     * @param data
     * @param message - (optional) a message to pass to a notification upon success
     * @param headers - (optional) additional headers
     * @returns {Promise}
     */
    axios( method, url, data, message, headers ){

        // start working slider
        if( message !== false ) App.event.emit('working' );

        // return an axios call wrapped in a promise
        return new Promise(function( resolve, reject ) {
            axios[method]( url, data )
                .then( response => {

                    // notify of our success unless we specifically passed FALSE as our message
                    if( message !== false ){
                        let data = {};
                        if( message ) data.message = message;
                        App.event.emit( 'notify', data );
                    }

                    // resolve
                    resolve( response );
                } )
                .catch( error => {
                    console.log(error.response);

                    switch (error.response.status) {
                        case 401: // Not logged in
                        case 419: // Session expired
                        case 503: // Down for maintenance
                            // Bounce the user to the login screen with a redirect back
                            window.location.reload();
                            break;
                        case 500:
                            // notify error
                            let message = error.response.data.message ?? error.response.statusText;
                            App.event.emit( 'notify', { message : message, error : true });
                            break;
                        default:
                    }

                    // reject
                    reject( error );
                } )
                .then( () => {
                    // end working slider
                    if( message !== false ) App.event.emit('done')
                });

        });
    }

};

