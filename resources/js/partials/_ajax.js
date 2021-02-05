window.App.ajax = new class {

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

    post( url, data, message ){
        return this.axios( 'post', url, data, message );
    }

    get( url, message, data = {} ){
        return this.axios( 'get', url, { params : data }, message );
    }

    patch( url, data, message ){
        data = data || {};
        data._method = 'patch';
        return this.axios( 'post', url, data, message );
    }

    delete( url, data, message ){
        data = data || {};
        data._method = 'delete';
        return this.axios( 'post', url, data, message );
    }

    axios( type, url, data, message, headers ){

        // start working slider
        if( message !== false ) App.event.emit('working' );

        // return an axios call wrapped in a promise
        return new Promise(function( resolve, reject ) {
            axios[type]( url, data )
                .then( response => {
                    // notify success

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
                            break;
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

