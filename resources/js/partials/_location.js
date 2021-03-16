/**
 *
 *  The location class handles requesting and passing geolocation data from the browser
 *
 */
window.App.location = new class {

    constructor() {
        // if we don't even have a geolocator to work with set _coordinates to false
        this._coordinates = navigator.geolocation ? null : false;
    }


    /**
     * Get our current coordinates
     *
     * @returns {promise|object}
     */
    async get(){
        // if we don't have a navigator.geolocation to work with, abort
        if( this._coordinates === false ) return this._handleError( 'No navigator.geolocation to access' );

        await this._loadCoordinates();
        return this._coordinates;
    }


    /**
     * sets geolocation coordinates data
     *
     * @returns {Promise<void>}
     * @private
     */
    async _loadCoordinates(){
        const position = await this._getCoordinates();
        this._coordinates = position.coords;
    }


    /**
     * get the current geolocation dat from navigator.geolocation
     *
     * @returns {Promise<unknown>}
     * @private
     */
    async _getCoordinates() {
        let _handleError = this._handleError;
        return new Promise(function( resolve, _handleError ) {
            navigator.geolocation.getCurrentPosition( resolve, _handleError );
        });
    }


    /**
     * report any error with connecting
     *
     * @param error
     * @private
     */
    _handleError( error ){
        console.log( 'Geolocation error', error.code );
    }
};
