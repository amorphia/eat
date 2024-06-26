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
             * Delete a restaurant location
             *
             * @param location
             */
            deleteLocation( location ){
                // make a delete request for the location
                App.ajax.delete( `/api/locations/${location.id}` ).then( result => {
                    // remove location from restaurant locations
                    let restaurant = this.shared.getRestaurant( location.restaurant_id );
                    if( restaurant) restaurant.locations = restaurant.locations.filter( obj => obj.id !== location.id );
                });
            },


            /**
             * Update a restaurant's rating relation
             *
             * @param restaurant - the restaurant
             * @param params - the ratings column to update (interest, rating, viewed) and its new value
             */
            updateRating( restaurant, params ){

                // get the restaurant in our shared list
                restaurant = this.shared.getRestaurant( restaurant );
                if( ! restaurant ) return;

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
             * @param photo - the photo to update
             * @param params - the column to update and its value
             */
            updatePhoto( photo, params ){

                let restaurant = this.shared.getRestaurant( photo.restaurant_id );
                photo = restaurant.photos.find( obj => obj.id === photo.id );

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
             * @param restaurant - the restaurant to update
             * @param params - the column to update and its value
             */
            updateRestaurant( restaurant, params ){
                restaurant = this.shared.getRestaurant( restaurant );

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



