window.App.query = new class {

    constructor() {
        this._vue = vueApp;
        this.debug = false;
    }

    set( name, value = null ){

        let paramArray = [];

        // wrap parameters in an array if there is only one
        if( !Array.isArray( name ) ){
            paramArray.push( { name: name, value : value } );
        } else {
            paramArray = name;
        }

        // modify our parameters
        let params = this.generateParams( paramArray );

        // push the changes
        this._vue.$router.push( { name: this._vue.$route.name, query: params } );
    }

    generateParams( paramArray ){
        // grab our original query object and clone it
        let params =  {...this._vue.$route.query};

        paramArray.forEach( param => {

            // if our query didn't change, then we don't need to do anything
            if( params[param.name] !== param.value ){

                // if we passed a value change the query to the new value
                if( param.value !== null && param.value !== undefined ){
                    params[param.name] = param.value;
                    // otherwise if passed null as the value delete the parameter
                } else {
                    delete params[param.name];
                }
            }
        });

        return params;
    }

    // helper that just calls set with a null value to delete the chosen parameter
    remove( name ){
        this.set( name, null );
    }

};
