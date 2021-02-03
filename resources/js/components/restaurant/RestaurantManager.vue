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
            App.event.on( 'updateRating', this.updateRating );
            App.event.on( 'updatePhoto', this.updatePhoto );
            App.event.on( 'editRestaurants', this.editRestaurants );
            App.event.on( 'updateRestaurant', this.updateRestaurant );
        },

        methods : {
            findRestaurant( restaurant ){
                let id;
                if( typeof restaurant === 'object' && restaurant !== null ) id = restaurant.id;
                else id = restaurant;

                return this.shared.restaurants.find( obj => obj.id === id );
            },

            updateRating( rest, params ){

                console.log( rest, params );

                // get and update the restaurant in our list
                let restaurant = this.findRestaurant( rest );
                restaurant = restaurant ? restaurant : this.shared.forcedRestaurant;
                restaurant[params.column] = params.value;

                App.ajax.patch( `/api/ratings/${restaurant.id}`, {
                    rating : restaurant.rating,
                    interest : restaurant.interest
                }).then( response => {
                    // delete restaurant from array if we set interest to -1
                    if( params.column === 'interest' && params.value === -1 ) {
                        this.shared.restaurants = this.shared.restaurants.filter( obj => obj.id !== restaurant.id );
                    }
                });
            },

            updatePhoto( p, params ){
                let restaurant = this.findRestaurant( p.restaurant_id );
                restaurant = restaurant ? restaurant : this.shared.forcedRestaurant;

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


            editRestaurants( mode, restaurants ){
                let ids = restaurants.map( obj => obj.id );

                App.ajax.post( `/api/restaurants/${mode}`, { ids : ids }).then( response => {
                    this.clearRestaurants( response );
                });
            },

            clearRestaurants( response ){
                let ids = response.data.ids;
                if( !Array.isArray( ids ) ) ids = _.values( ids );
                this.shared.restaurants = this.shared.restaurants.filter( obj => !ids.includes( obj.id ) );
                this.shared.restaurants.forEach( obj => obj.selected = false );
            },

            updateRestaurant( rest, params ){
                let restaurant = this.findRestaurant( rest );
                restaurant = restaurant ? restaurant : this.shared.forcedRestaurant;

                restaurant[params.column] = params.value;

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

