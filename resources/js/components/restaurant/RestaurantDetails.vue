<template>
    <transition name="right">
        <div v-if="restaurant" class="details pos-fixed width-100 height-100 top-0 left-0 right-0 bottom-0 z-50">
            <button @click="index = null" class="toggle minimize-toggle top-0 right-0">
                <i class="icon-x"></i>
            </button>

            <!-- main content -->
            <div class="width-100 height-100 overflow-auto">
                <!-- restaurant interest -->
                <restaurant-interest :restaurant="restaurant"></restaurant-interest>

                <!-- name -->
                <div class="details__name d-flex overflow-hidden">
                    <span class="pr-6">{{ restaurant.name }}</span>
                    <!-- categories -->
                    <div v-if="restaurant.categories.length" class="details__categories d-flex align-center">
                        <category-item v-for="category in restaurant.categories"
                                       :key="category.name"
                                       :category="category">
                        </category-item>
                    </div>

                    <restaurant-rating :restaurant="restaurant" class="details__rating"></restaurant-rating>
                </div>

              <details-photos :photos="restaurant.photos"></details-photos>

                <div class="details__secondary-wrap d-flex p-6">
                    <!-- left nav button -->
                    <button v-if="index > 0" @click="updateIndex( -1 )" class="details__nav-button left">
                        <i class="icon-prev"></i>
                    </button>

                    <!-- secondary content -->
                    <details-posts :restaurant="restaurant"></details-posts>
                    <details-locations :restaurant="restaurant"></details-locations>

                    <!-- right nav button -->
                    <button v-if="index < maxIndex" @click="updateIndex( 1 )" class="details__nav-button right">
                        <i class="icon-next"></i>
                    </button>
                </div>

            </div>
        </div>
    </transition>
</template>


<script>

    export default {

        name: 'restaurant-details',
        data() {
            return {
                shared : App.state,
                index : null,
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

        computed : {
            restaurant(){
                if( this.index === null ) return null;
                return this.shared.restaurants[this.index];
            },

            maxIndex(){
                if( !this.shared.restaurants ) return 0;
                return this.shared.restaurants.length - 1;
            }
        },

        methods : {
            updateIndex( val ){
                let index = this.index;
                index += val;
                if( index < 0 ) index = 0;
                if( index > (this.maxIndex) ) index = this.maxIndex;
                this.index = index;
            }
        },

        created(){
            App.event.on( 'viewRestaurant', index => this.index = index );
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

    .details__nav-button {
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        top: 60%;
        padding: .5rem;
        border-radius: 50%;
        background-color: rgba(0,0,0,.5);
        color: white;
        font-size: 1.5rem;
        z-index: 50;
        box-shadow: 0 0 5px rgba(0,0,0,.5);

        &:hover { color : var(--highlight-color) }
        &.left { left: 1rem }
        &.right { right: 1rem }
    }


</style>

