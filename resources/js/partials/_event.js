window.App.event = new class {

    constructor() {
        this._vue = new Vue();
        this.debug = false;
    }

    emit( event, ...args ){
        if( this.debug ) console.log( 'emit', event );
        this._vue.$emit( event, ...args );
    }

    on( event, callback ){
        if( this.debug ) console.log( 'on', event );
        this._vue.$on( event, callback );
    }

};
