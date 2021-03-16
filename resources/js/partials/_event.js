/**
 *
 *  The event class instantiates a Vue to act as a global event dispatcher
 *
 */
window.App.event = new class {

    constructor() {
        this._vue = new Vue();
        this.debug = false;
    }

    /**
     * Emit an event
     *
     * @param {string} event - the name of our event
     * @param {*} args - zero or more argument to pass along with our event
     */
    emit( event, ...args ){
        if( this.debug ) console.log( 'emit', event );
        this._vue.$emit( event, ...args );
    }

    /**
     * Subscribe to an event
     *
     * @param {string} event - the name of our event
     * @param {function} callback - our callback
     */
    on( event, callback ){
        if( this.debug ) console.log( 'on', event );
        this._vue.$on( event, callback );
    }

};
