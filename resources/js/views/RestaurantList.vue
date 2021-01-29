<template>
    <div class="restaurant-list overflow-hidden">
        <transition name="slide-down" tag="div">
            <section class="restaurant-block width-100 pos-relative">
                <restaurant-item
                    v-for="(restaurant, index) in shared.restaurants"
                    :key="restaurant.id"
                    :index="index"
                    :restaurant.sync="restaurant"
                    :selected="selectedRestaurant"
                    @selected="obj => {
                        selectedRestaurant = obj;
                        selectedIndex = index;
                    }"
                ></restaurant-item>
            </section>
        </transition>
    </div>
</template>


<script>
    export default {
        name: 'restaurant-list',
        data() {
            return {
                shared : App.state,
                selectedRestaurant : null,
                sort : {},
            };
        },
        created(){
            this.loadRestaurants();
        },
        computed : {

        },
        methods : {
            loadRestaurants(){
                App.ajax.get( 'api/restaurants', false )
                    .then( ({ data }) => this.shared.init( 'restaurants', data ) );
            }
        },
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant-list {
        padding: 1.5rem 2.5rem;

    @include mobile {
        padding: 6.4rem .1rem .75rem;
    }
    }

    .restaurant-block {
        display: flex;
        flex-wrap: wrap;
    }

</style>
