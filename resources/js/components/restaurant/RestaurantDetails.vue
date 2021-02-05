<template>
    <transition name="right">
        <div v-if="restaurant" class="details pos-fixed width-100 height-100 top-0 left-0 right-0 bottom-0 z-50">

            <!-- edit name modal -->
            <modal-wrap
                :open="editName"
                @closed="editName = false"
                classes="width-50"
                focus=".should-focus">
               <input type="text"
                      v-model="restaurant.name"
                      class="form-control should-focus"
                      @change="nameChanged"
                      @keydown.enter="e => e.target.blur()">
            </modal-wrap>

            <!-- main content -->
            <div class="width-100 height-100 overflow-auto pos-relative">
                <!-- restaurant interest -->
                <restaurant-interest :restaurant="restaurant"></restaurant-interest>

                <!-- name -->
                <div class="details__name d-flex overflow-hidden">

                    <div class="details__name-input pr-6 d-flex align-center ellipses">
                        <button v-if="shared.user.admin"
                                class="details__reload-restaurant details__edit-button mr-3"
                                @click="loadRestaurantData">
                            <i class="icon-upload"></i>
                        </button>

                        <button v-if="shared.user.admin"
                                class="details__delete-restaurant details__edit-button mr-3"
                                @click="confirmDeleteRestaurant">
                            <i class="icon-trash"></i>
                        </button>

                        <button class="details__delete-restaurant details__edit-button mr-3"
                                @click="confirmBlockRestaurant">
                            <i class="icon-block"></i>
                        </button>

                        <div class="details__name-name ellipses" @click="openEditName" v-text="restaurant.name"></div>
                    </div>

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
                    <div class="details__rating-container pos-relative ml-auto"
                         v-touch:swipe.left="() => ratingOpen = true"
                         v-touch:swipe.right="() => ratingOpen = false">
                        <restaurant-rating :restaurant="restaurant"
                                           class="details__rating"
                                           :open="ratingOpen"
                                           @opened="ratingOpen = true"
                                           @closed="ratingOpen = false"
                        ></restaurant-rating>
                    </div>


                    <!-- close button -->
                    <button @click="closeDetails" class="toggle details__toggle">
                        <i class="icon-x"></i>
                    </button>
                </div>

              <details-photos :restaurant="restaurant"></details-photos>

                <div class="details__secondary-wrap d-flex p-6"
                     v-touch:swipe.left="() => updateIndex( 1 )"
                     v-touch:swipe.right="() => updateIndex( -1 )">

                    <div v-if="!shared.forcedRestaurant && (index > 0 || index < maxIndex)" class="details__nav-buttons">
                        <!-- left nav button -->
                        <button v-if="index > 0 && !shared.forcedRestaurant" @click="updateIndex( -1 )" class="details__nav-button left">
                            <i class="icon-prev"></i>
                        </button>

                        <div class="grow-1"></div>

                        <!-- right nav button -->
                        <button v-if="index < maxIndex  && !shared.forcedRestaurant" @click="updateIndex( 1 )" class="details__nav-button right">
                            <i class="icon-next"></i>
                        </button>
                    </div>


                    <!-- secondary content -->
                    <details-posts :restaurant="restaurant" class="details__secondary"></details-posts>
                    <details-locations :restaurant="restaurant" class="details__secondary"></details-locations>


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
                editName : false,
                restaurantsLoaded : false,
                ratingOpen : false,
            };
        },

        created(){
            this.shared.init( 'forcedRestaurant', null );

            App.event.on( 'viewRestaurant', this.viewRestaurant );
            App.event.on( 'forceViewRestaurant', this.forceRestaurant );
            App.event.on( 'initRestaurantLoad', this.loadDetailsFromParam );
        },


        watch : {
            restaurant( val ){
                // lock the scrollbars on the underlying page
                this.lockPage( val );

                // ad query parameters
                this.setDetailsParam( val );

                if( val) App.event.emit( 'detailsChanged' );
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

            loadDetailsFromParam(){
                if( !this.$route.query.details )  return;

                let restaurantIndex = _.findIndex( this.shared.restaurants, [ 'id', +this.$route.query.details ] );

                // if we already have this restaurant in our restaurants array, set the index
                if( restaurantIndex !== -1 ){
                    this.viewRestaurant( restaurantIndex );
                    return;
                }

                // otherwise fetch restaurant from api if not already in our list
                App.ajax.get( `/api/restaurants/${this.$route.query.details}`, false )
                    .then( response =>  this.shared.forcedRestaurant = response.data );
            },

            setDetailsParam( val ){
                let paramVal = null;
                if( val ) paramVal = val.id;

                if( +this.$route.query.details === paramVal ) return;
                App.query.set( 'details', paramVal );
            },

            loadRestaurantData(){
                // abort if we are using a forced restaurant, or it we've already loaded (unless we force)
                if( this.shared.forcedRestaurant ) return;

                App.ajax.get( `/api/restaurants/${this.restaurant.id}`, false )
                        .then( response => {
                            let restaurant = this.shared.restaurants.find( obj => obj.id === this.restaurant.id );
                            if( restaurant ){
                                Object.entries( response.data ).forEach( ([ key, val ]) => this.$set( restaurant, key, val ) );
                                this.$set( restaurant, 'loaded', true );
                            }
                        });

            },

            lockPage( val ){
                let body = document.getElementById( 'top' );

                if( val ){
                    setTimeout( () => body.classList.add( 'overflow-hidden' ), 200 );
                } else {
                    body.classList.remove( 'overflow-hidden' );
                }
            },

            openEditName(){
                if( this.shared.user.admin ) this.editName = true;
            },

            viewRestaurant( index ){
                this.index = index;
            },

            forceRestaurant( rest ){
                let listRestaurant = this.shared.restaurants.find( obj => obj.id === rest.id );
                this.shared.forcedRestaurant = listRestaurant ? listRestaurant : rest;
            },

            nameChanged(){
                App.event.emit( 'updateRestaurant', this.restaurant, { column : 'name', value : this.restaurant.name });
                this.editName = false;
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
    @import 'resources/sass/utilities/_mq.scss';

    .details {
        background-image : url('/images/main-background.jpg');
        background-position: center;
        background-size: cover;
        font-size: 1.4rem;
        color: var(--primary-light-color);
        transition: transform .3s;

        @include mobile {
        }
    }

    .details__rating {
        font-size: 1.4rem;
        padding-right: 1em;
        position: relative;
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

        @include mobile {
            flex-wrap: wrap;
            height: auto;
            padding: 0;
            background-color: var(--primary-darkest);
        }
    }

    .details__name-name {
        @include mobile {
            flex-grow: 1;
            order: 1;
        }
    }

    .details__edit-button {
        font-size: 1.2rem;
        color: var(--primary-color);
        padding: 0 .5rem 0 0;

        @include mobile {
            font-size: 1.4rem;
            order: 2;
            padding: .5rem;
        }
    }

    .details__toggle {
        position: relative;
        background-color: var(--primary-darkest);

        @include mobile {
            padding: .5rem 1rem;
            height: 3.8rem;
        }
    }


    .details__name-input {
        background-color: transparent;
        color: white;
        border: 0;
        display: block;
        text-align: left;
        display: flex;
        align-items: center;

        &:focus {
            outline: none;
        }

        @include mobile {
            width: 100%;
            height: auto;
            padding: 1.25rem .75rem;
            font-size: 3.5rem;
            border-top: 1px solid var(--primary-darkest);
            box-shadow: 0 0 5px rgb(9, 28, 29);
            background-color: var(--primary-dark);
        }
    }

    .details__categories {
        @include mobile {
            width: 100%;
            overflow: auto;
            white-space: nowrap;
            padding: .5rem;
            background-color: var(--primary-darkest);
        }
    }

    .details__rating-container {
        @include mobile {
            overflow: hidden;
            flex-grow: 1;
            height: 3.8rem;
        }
    }

    .details__rating {
        @include mobile {
            font-size: .8rem;
            padding-right: 0;
        }
    }


    .details .action-button.shift-down {
        @include mobile {
            transform: translate(10%, 10%);
        }
    }

    .details__nav-buttons {
        @include mobile {
            width: 100%;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 0 5px rgba(0,0,0,.5);
            padding: 1.5rem;
            font-size: 2rem;
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

        @include mobile {
            position: static;
        }
    }

    .details__edit-button {
        position: initial;
    }

    .details__secondary-wrap {
        @include mobile {
            flex-wrap: wrap;
            padding: 0;
        }
    }

    .details__secondary {
        @include mobile {
            width: 100%;
            padding: 2rem;
        }
    }

    // Ordering
    @include mobile {
        .details__edit-button { order : 2 }
        .details__rating-container { order : 1 }
        .details__toggle { order : 2 }
        .details__name-input { order : 3 }
        .details__categories { order : 4 }
    }


    .details__combined-container {
        position: relative;
        margin-left: 1.5rem;

        @include mobile {
            margin-left: 0rem;
            width: 40%;
            overflow: hidden;
            position: absolute;
            height: 3.8rem;
        }
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

