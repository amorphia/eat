<template>
    <div></div>
</template>

<script>
    export default {

        name: 'restaurant-manager',

        data() {
            return {
                shared : App.state
            };
        },

        created() {
            // register event listeners
            App.event.on( 'updateRating', this.updateRating );
            App.event.on( 'updatePhoto', this.updatePhoto );
            App.event.on( 'editRestaurants', this.editRestaurants );
            App.event.on( 'updateRestaurant', this.updateRestaurant );
            App.event.on( 'deleteLocation', this.deleteLocation );
        },

        methods : {

            /**
             * Grab a restaurant from our shared restaurants array
             *
             * @param {object|int} restaurant - a restaurant object, or an id
             * @returns {object|null} - the restaurant if we find one
             */
            findRestaurant( restaurant ){
                let id;

                // get our id
                if( typeof restaurant === 'object' && restaurant !== null ){
                    id = restaurant.id;
                } else if( typeof restaurant === 'number' ) {
                    id = restaurant;
                } else {
                    // if we don't have a valid ID then abort
                    return;
                }

                // if the restaurant doesn't exist in our shared.restaurants list, is must be a forced restaurant
                // provided by the search function
                return this.shared.restaurants.find( obj => obj.id === id ) ?? this.shared.forcedRestaurant;
            },


            /**
             * Delete a restaurant location
             *
             * @param location
             */
            deleteLocation( location ){
                // find the location's parent restaurant
                let restaurant = this.findRestaurant( location.restaurant_id );

                // make a delete request for the location
                App.ajax.delete( `/api/locations/${location.id}` ).then( result => {
                    // remove location from restaurant locations
                    restaurant.locations = restaurant.locations.filter( obj => obj.id !== location.id );
                });

            },


            /**
             * Update a restaurant's rating relation
             *
             * @param rest - the restaurant
             * @param params - the ratings column to update (interest, rating, viewed) and its new value
             */
            updateRating( rest, params ){

                // get the restaurant in our shared list
                let restaurant = this.findRestaurant( rest );

                // update the restaurant rating locally
                restaurant[params.column] = params.value;

                // update the restaurant on the back end
                App.ajax.patch( `/api/ratings/${restaurant.id}`, {
                        rating : restaurant.rating,
                        interest : restaurant.interest,
                        viewed : restaurant.viewed
                    },
                    params.message
                ).then( response => {
                    // delete restaurant from array if we set interest to -1
                    if( params.column === 'interest' && params.value === -1 ) {
                        this.shared.restaurants = this.shared.restaurants.filter( obj => obj.id !== restaurant.id );
                    }
                });
            },


            /**
             * update a photo
             *
             * @param p - the photo to update
             * @param params - the column to update and its value
             */
            updatePhoto( p, params ){

                // get the restaurant in our shared list
                let restaurant = this.findRestaurant( p.restaurant_id );

                let photo = restaurant.photos.find( obj => obj.id === p.id );

                // delete photo if we set active to false
                if( params.column === 'active' && !params.value ) {
                    App.ajax.delete( `/api/photos/${photo.id}` ).then( result => {
                        // remove photo from restaurant photos array
                        let index = _.findIndex( restaurant.photos, [ 'id', photo.id ] );
                        this.$delete( restaurant.photos, index );
                    });
                    return;
                }

                // otherwise we update its properties
                photo[params.column] = params.value;
                App.ajax.patch( `/api/photos/${photo.id}`, photo );
            },


            /**
             * Merge or delete one or more restaurants
             *
             * @param mode - (merge|delete)
             * @param restaurants
             */
            editRestaurants( mode, restaurants ){
                let ids = restaurants.map( obj => obj.id );

                App.ajax.post( `/api/restaurants/${mode}`, { ids : ids }).then( response => {
                    this.clearRestaurants( response );
                });
            },


            /**
             * remove the specified restaurants from our shared restaurant list
             *
             * @param response
             */
            clearRestaurants( response ){
                // get our restaurant ids
                let ids = response.data.ids;
                if( !Array.isArray( ids ) ) ids = _.values( ids );

                // filter out the specified restaurants
                this.shared.restaurants = this.shared.restaurants.filter( obj => !ids.includes( obj.id ) );

                // uncheck any checked restaurants we still have
                this.shared.restaurants.forEach( obj => obj.checked = false );
            },


            /**
             * Update a restaurant
             *
             * @param rest - the restaurant to update
             * @param params - the column to update and its value
             */
            updateRestaurant( rest, params ){
                let restaurant = this.findRestaurant( rest );

                // update the restaurant locally
                restaurant[params.column] = params.value;

                // update the restaurant on the back end
                App.ajax.patch( `/api/restaurants/${restaurant.id}`, restaurant ).then( response => {
                    // delete restaurant from array if we set active to false
                    if( params.column === 'active' && !params.value ) {
                        this.shared.restaurants = this.shared.restaurants.filter( obj => obj.id !== restaurant.id );
                    }
                });
            },

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>

