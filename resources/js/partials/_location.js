

window.App.location = new class {

    constructor() {
        this._coordinates = navigator.geolocation ? null : false;
    }

    async get(){
        console.log( 'coordinates', this._coordinates );
        if( this._coordinates !== null ) return this._coordinates;

        console.log( 'await coordinates' );
        await this._loadCoordinates();

        console.log( 'return coordinates', this._coordinates );
        return this._coordinates;
    }


    async _loadCoordinates(){
        console.log( 'await geolocation' );
        const position = await this._getCoordinates();
        this._coordinates = position.coords;
    }


    async _getCoordinates() {
        let _handleError = this._handleError;
        return new Promise(function( resolve, _handleError ) {
            navigator.geolocation.getCurrentPosition( resolve, _handleError );
        });
    }


    _handleError( error ){
        console.log( 'Geolocation error', error.code );
    }
};
