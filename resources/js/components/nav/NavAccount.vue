<template>
    <div class="nav-account pull-right pos-relative">

        <!-- settings button -->
        <div class="nav-account__button primary" @click="dropdown = !dropdown" :class="{active : dropdown}">
            <i class="icon-settings"></i>
        </div>

        <!-- settings panel -->
        <transition name="collapse">
            <div class="nav-account__dropdown pos-absolute top-100" v-if="dropdown">
                <div class="nav-account__dropdown-content p-4 d-flex flex-column">

                    <!-- close button -->
                    <button class="toggle mobile-only pos-absolute" @click="dropdown = false"><i class="icon-x"></i></button>

                    <!-- logout -->
                    <a class="nav-account__dropdown-item" href="/logout">logout</a>

                    <!-- reset filters -->
                    <button class="nav-account__dropdown-item left-text" @click="resetFilters">
                        reset filters
                    </button>

                    <!-- reset filters -->
                    <button class="nav-account__dropdown-item left-text" @click="startTour">
                        take tour
                    </button>

                    <!-- add location -->
                    <button class="nav-account__dropdown-item left-text" @click="openAddPage = true">
                        add location
                    </button>

                    <!-- edit mode -->
                    <button v-if="shared.user.admin"
                            class="nav-account__dropdown-item left-text"
                            :class="{active: shared.editMode}"
                            @click="toggleEditMode"
                    >edit mode</button>
                </div>

            </div>
        </transition>

        <!-- add new restaurant modal -->
        <modal-wrap
            :open="openAddPage"
            @closed="openAddPage = false"
            classes="width-50"
            focus=".should-focus">
            <vue-form
                method="post"
                :action="pageAction"
                submitclass="width-100"
                @success="addedNewRestaurant"
                :schema="pageSchema"></vue-form>
        </modal-wrap>

    </div>
</template>


<script>
    export default {

        name: 'nav-account',
        props: [],

        data() {
            return {
                shared : App.state,
                dropdown : false,
                openAddPage : false,
                pageAction : '/api/locations/create/yelp/page', // add page form action
            };
        },

        created(){

        },

        methods : {

            /**
             * Reset all list filters
             */
            resetFilters(){
                // clear query string
                this.$router.push( { name: this.$route.name, query: {} } );

                // close panel
                this.dropdown = false;

                // emit reset to other components
                App.event.emit( 'resetFilters' );

                // load restaurants
                App.event.emit( 'loadRestaurants' );
            },


            /**
             * Show newly added restaurant
             *
             * @param response
             */
            addedNewRestaurant( response ){
                this.openAddPage = false;
                this.shared.forcedRestaurant = response;
            },


            /**
             * Manually start a tour
             */
            startTour(){
                this.dropdown = false;
                this.$tours['myTour'].start();
            },


            /**
             * Toggle edit mode
             */
            toggleEditMode(){
                this.shared.editMode = !this.shared.editMode;
                this.dropdown = false;
            }
        },

        computed : {

            /**
             * Add restaurant form schema
             * @returns {object}
             */
            pageSchema(){
                return [
                    { name: 'yelp_id', label : 'yelp url', focus : true },
                ]
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .nav-account__button {
        font-size: 1.5rem;

        @include mobile {
            padding-left: .5rem;
        }

        &.active {
            color: var(--highlight-color);
        }
    }

    .nav-account__dropdown {
        background-color: rgba(0,0,0,.9);
        right: 0;

        @include mobile {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            font-size: 2rem;
            padding: 2rem;

            .toggle {
                font-size: 2.5rem;
                top: 1rem;
                right: 1rem;
            }

        }

    }

    .nav-account__dropdown-item {
        white-space: nowrap;
        padding : .5rem .5rem;

        @include mobile {
            margin-top: 1rem;
        }

    }
</style>

