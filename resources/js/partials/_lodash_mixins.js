let mixins = {

    /**
     * Checks if a value is numeric, this includes actual numbers and strings that evaluate to numbers
     *
     * @param val - the value to test
     * @returns {boolean}
     */
    isNumeric( val ) {
        return  !isNaN( val );
    },

};

module.exports = mixins;
