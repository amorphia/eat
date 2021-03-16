<template>
    <div class="d-flex align-stretch">
        <!-- nav menu -->
        <nav-menu></nav-menu>

        <!-- main content -->
        <div class="main-content">
            <router-view></router-view>
        </div>

        <!-- restaurant details -->
        <restaurant-details></restaurant-details>

        <!-- nutz and boltz -->
        <working-slider></working-slider>
        <notify-queue></notify-queue>
        <vue-confirm-dialog class="confirm-popup"></vue-confirm-dialog>
        <my-tour></my-tour>
        <restaurant-manager></restaurant-manager>
    </div>
</template>


<script>

    export default {
        name: 'app-core',
        data() {
            return {
                shared : App.state
            };
        },
        created(){
            this.loadUser();

            // make getRestaurant available via the state
            this.shared.init( 'getRestaurant', this.getRestaurant );
        },

        methods : {
            /**
             * Load our user object
             */
            loadUser(){
                App.ajax.get( 'api/users/null', false )
                    .then( ({ data }) => this.shared.init( 'user', data ) );
            },

            /**
             * Get a restaurant out of the shared.restaurants admin,
             * or alternatively the shared.forcedRestaurant property
             *
             * @param {object|number} identifier
             */
            getRestaurant( identifier ){
                let id;

                // if our identifier is an object with an id use that as our ID
                if( typeof identifier === 'object'
                        && identifier !== null
                        && identifier.hasOwnProperty( 'id' )
                ){
                    id = identifier.id;
                // if our identifier is a number use that as the id
                } else if( _.isNumeric( identifier ) ) {
                    id = +identifier; // coerce strings into numeric values
                } else {
                    // if we don't have a valid ID then abort
                    return;
                }

                // if the restaurant doesn't exist in our shared.restaurants list, is might be
                // a forced restaurant provided by the search function or query string or something
                // if neither exist then we'll just return null
                let restaurant = this.shared.restaurants.find( obj => obj.id === id ) ?? this.shared.forcedRestaurant;
                if( !restaurant ) {
                    console.log( 'No restaurant found' );
                    return;
                }

                return restaurant;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .main-content {
        flex-grow: 1;
        flex-shrink: 1;
        padding-top: 4.1rem;
    }

    .confirm-popup {
        z-index: 1000;
    }
</style>

