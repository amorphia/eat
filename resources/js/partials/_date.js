window.App.date = new class {
    format( time, format = 'YYYY-MM-DD HH:mm:ss' ){
        let date = Day( time );
        return date.format( format );
    }

    parseTime( time, options = {} ){
        // set defaults and overwrite with options
        let defaults = {
            format : 'h:mma',
            input : 'HHmm',
            overnight : false
        };

        options = Object.assign( defaults, options );

        // process time using today as a stand in for the date
        let now = Day();
        if( options.overnight ) now.add('1', 'day' );


        let dummyDate = now.format( "MM-DD-YYYY" );
        let dummyDateTime = `${dummyDate} ${time}`;
        let DateTime = Day( dummyDateTime, `MM-DD-YYYY ${options.input}` );
        return DateTime.format(options.format);
    }
};
