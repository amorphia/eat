<template>
    <transition name="right">
        <div v-if="restaurant" class="details pos-fixed width-100 height-100 top-0 left-0 right-0 bottom-0 z-50">
            <button @click="restaurant = null" class="toggle minimize-toggle top-0 right-0">
                <i class="icon-x"></i>
            </button>

            <!-- main content -->
            <div class="width-100 height-100 overflow-auto">
                <!-- restaurant interest -->
                <restaurant-interest :restaurant="restaurant"></restaurant-interest>

                <!-- name -->
                <div class="details__name d-flex">
                    <span class="pr-6">{{ restaurant.name }}</span>
                    <!-- categories -->
                    <div v-if="restaurant.categories.length" class="details__categories d-flex align-center">
                        <category-item v-for="category in restaurant.categories"
                                       :key="category.name"
                                       :category="category">
                        </category-item>
                    </div>
                </div>

              <details-photos :photos="restaurant.photos"></details-photos>

                <div class="details__secondary-wrap d-flex py-6">
                    <details-posts :restaurant="restaurant"></details-posts>
                    <details-locations :restaurant="restaurant"></details-locations>
                </div>

            </div>
        </div>
    </transition>
</template>


<script>
    import DetailsPosts from "../posts/DetailsPosts";
    import DetailsLocations from "../locations/DetailsLocations";
    export default {

        name: 'restaurant-details',
        components: {DetailsLocations, DetailsPosts},
        data() {
            return {
                shared : App.state,
                restaurant : null,
            };
        },

        watch : {
            restaurant( val ){
                let body = document.getElementById( 'top' );

                if( val ){
                    setTimeout( () => body.classList.add( 'overflow-hidden' ), 200 );
                } else {
                    body.classList.remove( 'overflow-hidden' );
                }
            }

        },

        created(){
            App.event.on( 'viewRestaurant', restaurant => this.restaurant = restaurant );
        }
    }
</script>


<style lang="scss">

    .details {
        background-image : url('/images/main-background.jpg');
        background-position: center;
        background-size: cover;
        font-size: 1.4rem;
        color: var(--primary-light-color);
        transition: transform .3s;
    }

    .details__name {
        padding: 1.5rem 4rem;
        font-size: 2.5rem;
        color: white;
        letter-spacing: 2px;
        font-family: var(--secondary-font);
        font-weight: 300;
        position: relative;
        box-shadow: 0px 3px 0px 0px rgba( 0, 0, 0, .39 );
        background-color: var(--primary-dark);
    }




</style>

