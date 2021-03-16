<template>
    <div class="restaurant-list overflow-hidden">

            <section>
                <!-- change query match type button -->
                <button v-if="$route.query.match"
                        class="p-4 center-text primary-light-bg pull-center my-4 d-block restaurant-list__load-previous"
                        @click="openMatch"
                >Change Match View</button>

                <!-- load previous page button -->
                <button v-if="shared.page.initial > 0"
                        class="p-4 center-text primary-light-bg  pull-center my-4 d-block restaurant-list__load-previous"
                        @click="loadPreviousPage"
                >Load Previous Page</button>

                <!-- restaurant list -->
                <div class="restaurant-block width-100 pos-relative">

                    <!-- restaurants loop -->
                    <restaurant-item
                        v-for="(restaurant, index) in shared.restaurants"
                        :key="restaurant.yelp_id ? restaurant.yelp_id : restaurant.id"
                        :index="index"
                        :restaurant.sync="restaurant"
                        @checked="restaurant.checked = !restaurant.checked"
                        :selected="selectedRestaurant"
                        @selected="obj => {
                            selectedRestaurant = obj;
                            selectedIndex = index;
                        }"
                    ></restaurant-item>

                    <!-- infinite loader -->
                    <infinite-loading :distance="1000" ref="infinite" @infinite="nextPage">
                        <div slot="spinner"></div>
                        <div slot="no-more"></div>
                        <div slot="no-results"></div>
                    </infinite-loading>
                </div>

                <!-- no results -->
                <div v-if="!hasRestaurants" class="p-6 center-text width-100">
                    <div class="loader" v-if="waiting"></div>
                    <div v-else class="">No Results</div>
                </div>
            </section>
    </div>
</template>


<script>

    export default {
        name: 'restaurant-list',

        data() {
            return {
                shared : App.state,
                selectedRestaurant : null,
                waiting : true, // are we waiting for our API to return with our restaurants list?
                initRestaurantLoad  : false, // how we loaded our restaurants for the first time?
            };
        },


        created(){
            this.initPageManager();
        },

        mounted(){
            // set our event listeners
            App.event.on( 'loadRestaurants', this.loadRestaurants );
            App.event.on( 'nextPage', this.nextPage );
            App.event.on( 'initRestaurantLoad', this.checkForTour );

            // store our infinite loader state manager
            this.shared.page.state = this.$refs.infinite.stateChanger;

            // turn on our spinner
            this.waiting = true;

            // load our initial page data
            this.loadPage();
        },


        computed : {

            /**
             * Do we have any restaurants loaded?
             *
             * @returns boolean
             */
            hasRestaurants(){
                return this.shared.restaurants && this.shared.restaurants.length > 0;
            }

        },

        methods : {

            /**
             * Emit our change match type event
             */
            openMatch(){
                App.event.emit( 'changeMatchType' );
            },


            /**
             * Initialize some properties on our shared state object
             */
            initPageManager(){
                // initialize restaurants as an empty array
                this.shared.init( 'restaurants', [] );

                 // initialize editMode to false
                this.shared.init( 'editMode', false );

                // set pagination obj
                this.shared.init( 'page', {
                    current : this.$route.query.page ? +this.$route.query.page : 1,
                    last: this.$route.query.page ? +this.$route.query.page : 1,
                    state : null,
                    complete : false,
                    initial : this.$route.query.page > 1 ? this.$route.query.page - 1 : 0
                });
            },


            /**
             * Load a previous page's results
             *
             * @returns Promise
             */
            loadPreviousPage(){
                let data = this.getPageParamData();
                data.page = this.shared.page.initial;

                return App.ajax.get( `/api/restaurants`, '', data )
                    .then( ({ data }) => this.processPageResultsTop( data ) );
            },


            /**
             * Process a response from our index restaurants API endpoint, but insert them at the top
             * of the page in reverse order, instead of the bottom like usual. Used when loading data
             * from a previous page
             *
             * @param data
             */
            processPageResultsTop( data ){
                let restaurants = data.data.reverse();

                restaurants.forEach( restaurant => {
                    this.shared.restaurants.unshift( restaurant );
                });

                this.shared.page.initial--;
            },


            /**
             * Reset the pagination and load our restaurants from the API
             */
            loadRestaurants(){
                this.waiting = true;
                this.resetPage();
                this.loadPage();
            },


            /**
             * Reset our pagination
             */
            resetPage(){
                this.shared.page.current = 1;
                this.shared.page.last = 1;
                this.shared.restaurants = [];
                this.shared.page.state.reset();
                this.shared.page.complete = false;
                this.shared.page.initial = 0;

                if( this.$route.query.page ) App.query.set( 'page', null );

                // clear our restaurant details view
                App.event.emit( 'viewRestaurant', null );
            },


            /**
             * Load the next page of the restaurant index
             */
            nextPage(){
                // if we've loaded the final page already, then we are done here
                if( this.shared.page.complete ) return;

                // advance pagination then init call to the API
                this.shared.page.current++;
                this.loadPage();
            },


            /**
             * Load a page of our restaurants index from the API
             *
             * @returns Promise
             */
            async loadPage(){
                // if we've loaded the final page already, then we are done here
                if( this.shared.page.complete ) return;

                // get our query filters from our current page parameters
                let data = await this.getPageParamData();

                // call the API
                return App.ajax.get( `/api/restaurants`, false, data )
                    .then( ({ data }) => this.processPageResults( data ) );
            },


            /**
             *  Set the query string parameter for our router
             */
            setNextPageParam(){
                // if its the first page, no need to set the query string at all
                if( this.shared.page.current === 1 || this.shared.page.current === this.shared.page.initial + 1 ) return;

                // set the query string
                App.query.set( 'page', this.shared.page.current );
            },


            /**
             * Use our current router query string to generate the data object we will pass to our API
             * and use as query filters for our request
             *
             * @returns object
             */
            async getPageParamData(){
                // clone our query object, then clear the page number parameter from it, since
                // we will use our internal pagination object to determine the page and not the query string
                let query = {...this.$route.query };
                delete query['page'];

                // merge our route params to our internal page counter
                let data = { page : this.shared.page.current };
                Object.assign( data, query );

                // if sorting by distance get user coordinates
                if( data.sort === 'distance' ){
                    let coordinates = await App.location.get();
                    if( !coordinates ) return App.event.emit( 'notify', {
                        message : 'Geolocation failed',
                        error : true
                    });

                    data.latitude = coordinates.latitude;
                    data.longitude = coordinates.longitude;
                }

                return data;
            },


            /**
             * Take our results from the API and do whatever processing we need
             * to do, and then display the results
             *
             * @param response // our response from the API
             */
            processPageResults( response ){

                // update our query string
                this.setNextPageParam();

                // if we are on or past our last page, stop loading additional pages
                if( response.current_page >= response.last_page ){
                    this.shared.page.complete = true;
                    this.shared.page.state.complete();
                }

                // turn off the waiting spinner next tick
                this.$nextTick( () => this.waiting = false );

                // if we didn't have any results we are done here
                if ( !response.data.length ) return;

                // add results to our shared restaurants array
                if( !this.shared.restaurants.length ){
                    // if we have none already just plop the whole array in
                    this.shared.restaurants = response.data;
                } else {
                    // otherwise push each result individually
                    this.shared.restaurants.push( ...response.data );
                }

                // tell our infinite loader we are ready
                this.shared.page.state.loaded();

                // emit that we've loaded our restaurants, if this is our first time
                if( !this.initRestaurantLoad ){
                    this.initRestaurantLoad = true;
                    App.event.emit( 'initRestaurantLoad' );
                }

            },


            /**
             * Check if we should display our "List tour" to a new user, and if so load it
             */
            checkForTour(){
                // if we have already shown the list_tour don't show it again
                if( this.shared.user.list_tour ) return;

                // mark the list_tour as having been shown
                App.ajax.patch( `/api/tour`, { tour : 'list_tour' }, false );
                this.shared.user.list_tour = true;

                // wait a second, then show the tour
                setTimeout( () => this.$tours['myTour'].start(), 1000 );
            }

        },
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant-list__load-previous {
        margin-top: 2rem;
        margin-bottom: -.5rem;
        width: 30%;

        @include mobile {
            padding: 1.5rem;
            font-size: 1.3em;
            margin-top: 3rem;
            margin-bottom: -1rem;
            width: 80%
        }
    }

    .restaurant-block {
        padding: 1.5rem 2.5rem;
        max-width: 100vw;
        display: flex;
        flex-wrap: wrap;

        @include mobile {
            padding: 1.75rem .1rem .75rem;
        }

    }

</style>
