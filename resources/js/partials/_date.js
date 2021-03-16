/**
 *
 *  The date class handles some tricky data-time calculations we use when
 *  formatting our location hours
 *
 */

window.App.date = new class {

    /**
     * format the given time string
     *
     * @param {string} time - a string time that is processable by dayJs
     * @param {string} format - the date-time format we wish to return
     * @returns {string} - our formatted date-time
     */
    format( time, format = 'YYYY-MM-DD HH:mm:ss' ){
        let date = DayJs( time );
        return date.format( format );
    }

    /**
     * Parse restaurant location hours into readable format
     *
     * @param time
     * @param options
     * @returns {string}
     */
    parseTime( time, options = {} ){
        // set defaults and overwrite with options
        let defaults = {
            format : 'h:mma',
            input : 'HHmm',
            overnight : false
        };

        options = Object.assign( defaults, options );

        // process time using today as a stand in for the date
        let now = DayJs();

        // if we have an overnight close time shift the dat to tomorrow
        if( options.overnight ) now.add( '1', 'day' );


        let dummyDate = now.format( "MM-DD-YYYY" );
        let dummyDateTime = `${dummyDate} ${time}`;
        let DateTime = DayJs( dummyDateTime, `MM-DD-YYYY ${options.input}` );

        return DateTime.format( options.format );
    }

};
