/**
 *
 *  The Errors class maintains and displays any current form errors
 *
 */
class Errors {

    /**
     * Create a new Errors instance.
     */
    constructor() {
        this.errors = {};
    }


    /**
     * Determine if an errors exists for the given field.
     *
     * @param {string} field - the field name we are checking for
     */
    has( field ) {
        return this.errors.hasOwnProperty( field );
    }


    /**
     * Determine if we have any errors.
     */
    any() {
        return Object.keys( this.errors ).length > 0;
    }


    /**
     * Retrieve the error message for a field.
     *
     * @param {string} field
     */
    get( field ) {
        if ( this.errors[field] ) {
            return this.errors[field][0];
        }
    }


    /**
     * Manually set an error
     *
     * @param {string} field
     * @param {string} message
     */
    set( field, message ) {
        this.errors[field] = [message];
    }


    /**
     * Record the new errors.
     *
     * @param {object} errors - the error object passed from our http response
     */
    record( errors ) {
        this.errors = errors;
    }


    /**
     * Clear an error field.
     *
     * @param {string} field
     */
    clear( field ) {
        delete this.errors[field];
    }


    /**
     * Clear all error fields
     */
    clearAll() {
        this.errors = {};
    }

}

/**
 *
 *  The Form class handles form data and submission
 *
 */
export default class Form {


    /**
     * Create a new Form instance.
     *
     * @param {object} data - the schema and values of our form fields. Formatted:
     *          { fieldName : value }
     *     or if we have additional options
     *          { fieldName : { value : value, option : optionValue } }
     *     options:
     *          { file : true } - used when including a file form input
     *
     * @param {string} action - the url our form should submit to
     * @param {string} method - (optional) our form method, defaults to post
     */
    constructor( data, action, method = 'post' ) {

        // the original fields schema and data used when we created the form
        this._originalData = data;

        // our form action
        this._action = action;

        // our form method
        this._method = method.toLowerCase();

        // have we submitted the form successfully?
        this._success = null;

        // do we have any headers to add to the form?
        this._headers = {};

        // initialize our field data
        this.initializeFields();

        // set our errors class
        this.errors = new Errors();
    }


    /**
     *  Use our _originalData to populate our form fields
     */
    initializeFields(){

        // for each field specified in our data populate our internal record
        for ( let field in this._originalData ) {

            // our data can be supplied in the format { field : value }
            // or the format { field : { value : value, [additional properties such as "file"] : [whatever] } }
            // the following takes either format and stores it internally as property / value pairs on the form object
            if( typeof this._originalData[field] === 'object' && this._originalData[field].hasOwnProperty( 'value' ) ) {
                this[field] = { value : this._originalData[field].value };
            } else {
                this[field] = { value : this._originalData[field] };
            }

            // if we have any file inputs set our internal flag it on our internal property
            if( this._originalData[field].file ){
                this[field].file = true;
            }
        }
    }


    /**
     * Return all relevant data for the form.
     *
     * @return object // axios formData
     */
    data() {

        // instantiate axios formdata
        let data = new FormData();

        // for each property in our original data object build an appropriate property in our
        // newly minted formData
        for ( let property in this._originalData ) {

            // if we have a file append the file to out formData
            if( this[property].file ){
                data.append( property, this[property].value )
            } // if we have an array of values stringify them and add them to our formData
            else if( Array.isArray( this[property].value ) ){
                this[property].value.forEach( item => {
                    if( typeof item === 'object' ){
                        item = JSON.stringify( item );
                    }
                    data.append(`${property}[]`, item );
                });
            } // otherwise just set the appropriate property on our formData
            else {
                data.set( property, this[property].value );
            }
        }

        // set our method manually if its not the default "post" method
        if( this._method !== 'post' ){
            data.append( '_method', this._method );
        }

        // return our formData object
        return data;
    }


    /**
     * Reset the form fields.
     */
    reset() {
        // use our _originalData to repopulate our form
        this.initializeFields();

        // clear our errors
        this.errors.clearAll();
    }


    /**
     *  Flag this form as having files
     */
    hasFiles(){
        this._headers['Content-Type'] = 'multipart/form-data';
    }

    /**
     *  Return our headers in a wrapping object
     */
    getHeaders() {
        return { headers : this._headers };
    }


    /**
     * Submit the form.
     */
    submit() {
        this._success = null;

        // abort if we have any errors
        if( this.errors.any() ){
            return new Promise( (resolve, reject ) => {
                reject( 'form has errors' );
            });
        }


        // submit our form
        return new Promise(( resolve, reject ) => {

            // get our form data
            let data = this.data();

            // set our spinner a' spinning
            App.event.emit( 'working' );

            // post to our API
            axios.post( this._action, this.data(), this.getHeaders() )
                .then(response => {
                    this.onSuccess();
                    resolve( response.data );
                })
                .catch(error => {
                    this.onFail( error );
                    reject( error.response.data );
                })
                .then(() => App.event.emit('done') );
        });
    }


    /**
     * Handle a successful form submission.
     */
    onSuccess() {
        this._success = true;
        App.event.emit( 'notify' );
        this.reset();
    }


    /**
     * Handle a failed form submission.
     *
     * @param {object} error - our http error response
     */
    onFail( error ) {
        // refresh if our session has times out
        if( error.response.status === 419 ){
            window.location.reload()
        }

        // get our error message
        let message = error.response.data.error ?? error.response.statusText;

        // notify error
        App.event.emit( 'notify', { message : message, error : true });

        // record our errors to our error object
        if( error.response.data.errors ){
            this.errors.record( error.response.data.errors );
        }
    }
}
