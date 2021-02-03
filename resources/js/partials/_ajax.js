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
        App.event.emit('working' );

        // return an axios call wrapped in a promise
        return new Promise(function( resolve, reject ) {
            axios[type]( url, data )
                .then( response => {
                    // notify success

                    if( message !== false ){
                        let successMessage = message ? message : 'Success';
                        App.event.emit( 'notify', { message : successMessage });
                    }

                    // resolve
                    resolve( response );
                } )
                .catch( error => {
                    console.log(error.response);

                    // refresh if our session has times out
                    if( error?.response?.status === 419 ){
                        window.location.reload()
                    }

                    let message = error.response.data?.error ?? error.response.statusText;

                    // notify error
                    App.event.emit( 'notify', { message : message, error : true });

                    // reject
                    reject( error );
                } )
                .then( () => App.event.emit('done') ); // end working slider

        });

    }

};

