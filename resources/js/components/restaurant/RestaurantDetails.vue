<template>
    <transition name="right">
        <div v-if="restaurant" class="details pos-fixed width-100 height-100 top-0 left-0 right-0 bottom-0 z-50">

            <!-- edit name modal -->
            <modal-wrap
                :open="openEditName"
                @closed="openEditName = false"
                classes="width-50"
                focus=".should-focus">
               <input type="text"
                      v-model="restaurant.name"
                      class="form-control should-focus"
                      @change="nameChanged"
                      @keydown.enter="e => e.target.blur()">
            </modal-wrap>

            <!-- main content -->
            <div class="width-100 height-100 overflow-auto">
                <!-- restaurant interest -->
                <restaurant-interest :restaurant="restaurant"></restaurant-interest>

                <!-- name -->
                <div class="details__name d-flex overflow-hidden">

                    <button v-if="shared.user.admin"
                            class="details__delete-restaurant mr-3"
                            @click="confirmDeleteRestaurant">
                        <i class="icon-trash"></i>
                    </button>

                    <button class="details__delete-restaurant mr-3"
                            @click="confirmBlockRestaurant">
                        <i class="icon-block"></i>
                    </button>

                    <button v-if="shared.user.admin" class="details__name-input pr-6 d-flex align-center"
                        @click="openEditName = true"
                        v-text="restaurant.name"></button>
                    <div v-else class="details__name-input pr-6 d-flex align-center" v-text="restaurant.name"></div>

                    <!-- categories -->
                    <div v-if="restaurant.categories.length" class="details__categories d-flex align-center">
                        <category-item v-for="category in restaurant.categories"
                                       :key="category.name"
                                       :category="category">
                        </category-item>
                    </div>

                    <!-- combined -->
                    <div v-if="restaurant.combined_rating" class="details__combined-container" >
                        <span class="details__combined-rating">{{ restaurant.combined_rating }}</span>
                    </div>

                    <!-- rating -->
                    <restaurant-rating :restaurant="restaurant" class="details__rating ml-auto"></restaurant-rating>

                    <!-- close button -->
                    <button @click="closeDetails" class="toggle">
                        <i class="icon-x"></i>
                    </button>
                </div>

              <details-photos :restaurant="restaurant"></details-photos>

                <div class="details__secondary-wrap d-flex p-6">
                    <!-- left nav button -->
                    <button v-if="index > 0 && !shared.forcedRestaurant" @click="updateIndex( -1 )" class="details__nav-button left">
                        <i class="icon-prev"></i>
                    </button>

                    <!-- secondary content -->
                    <details-posts :restaurant="restaurant"></details-posts>
                    <details-locations :restaurant="restaurant"></details-locations>

                    <!-- right nav button -->
                    <button v-if="index < maxIndex  && !shared.forcedRestaurant" @click="updateIndex( 1 )" class="details__nav-button right">
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
                openEditName : false,
            };
        },

        created(){
            this.shared.init( 'forcedRestaurant', null );

            App.event.on( 'viewRestaurant', index => this.index = index );
            App.event.on( 'forceViewRestaurant', this.forceRestaurant );
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
                if( this.shared.forcedRestaurant ) return this.shared.forcedRestaurant;
                if( this.index === null ) return null;
                return this.shared.restaurants[this.index];
            },

            maxIndex(){
                if( !this.shared.restaurants ) return 0;
                return this.shared.restaurants.length - 1;
            }
        },

        methods : {

            forceRestaurant( rest ){
                let listRestaurant = this.shared.restaurants.find( obj => obj.id === rest.id );
                this.shared.forcedRestaurant = listRestaurant ? listRestaurant : rest;
            },

            nameChanged(){
                App.event.emit( 'updateRestaurant', this.restaurant, { column : 'name', value : this.restaurant.name });
                this.openEditName = false;
            },

            closeDetails(){
                if( this.shared.forcedRestaurant ){
                    this.shared.forcedRestaurant = null;
                    return;
                }

                this.index = null;
            },

            confirmBlockRestaurant(){
                App.confirm( () => App.event.emit( 'updateRating', this.restaurant, { column : 'interest', value : -1 } ) ,{
                    message: 'Are you sure you want to block this restaurant?' });
            },

            confirmDeleteRestaurant(){
                App.confirm( () => App.event.emit( 'updateRestaurant',
                                    this.restaurant,
                                    { column : 'active', value : false }),{
                                    message: 'Are you sure you want to delete this restaurant?' });
            },

            updateIndex( val ){
                let index = this.index;
                index += val;
                if( index < 0 ) index = 0;
                if( index > (this.maxIndex) ) index = this.maxIndex;
                this.index = index;

                // see if we should load the next pages
                this.checkForPageLoad();
            },

            checkForPageLoad(){
                // if we have less than 5 restaurants left, and we are not done loading
                if( this.index > this.shared.restaurants.length - 5 && !this.shared.page.complete ){
                    App.event.emit( 'nextPage' );
                }
            }
        },


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

         .details__rating {
             font-size: 1.4rem;
             padding-right: 1em;
             position: relative;
         }
    }

    .details__name {
        padding-left: 4rem;
        height: 4.75rem;
        font-size: 2.5rem;
        color: white;
        letter-spacing: 2px;
        font-family: var(--secondary-font);
        font-weight: 300;
        position: relative;
        box-shadow: 0px 3px 0px 0px rgba( 0, 0, 0, .39 );
        background-color: var(--primary-dark);

        .details__name-input {
            background-color: transparent;
            color: white;
            border: 0;

            &:focus {
                outline: none;
            }
        }

        & .toggle {
            position: relative;
            background-color: var(--primary-darkest);
        }
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

    .details__delete-restaurant {
        font-size: .5em;
        color: rgba(255,255,255,.2);
    }


    .details__combined-container {
        position: relative;
        margin-left: 1.5rem;
    }

    .details__combined-rating {
        position: absolute;
        background-color: var(--orange);
        border-radius: 50%;
        width: 10rem;
        height: 10rem;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: var(--accent-font);
        font-size: 5rem;
        top: 56%;
        left: 0;
        transform: translateY(-50%);
    }
</style>

