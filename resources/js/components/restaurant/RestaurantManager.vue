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
        },

        methods : {
            findRestaurant( restaurant ){
                let id;
                if( typeof restaurant === 'object' && restaurant !== null ) id = restaurant.id;
                else id = restaurant;

                return this.shared.restaurants.find( obj => obj.id === id );
            },

            updateRating( rest, params ){

                let restaurant = this.findRestaurant( rest );
                restaurant[params.column] = params.value;

                App.ajax.patch( `/api/ratings/${restaurant.id}`, {
                    rating : restaurant.rating,
                    interest : restaurant.interest
                });
            },

            updatePhoto( p, params ){
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
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>

