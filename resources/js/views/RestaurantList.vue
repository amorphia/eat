<template>
    <div class="restaurant-list overflow-hidden">

            <section>
                <button v-if="$route.query.match"
                        class="p-4 center-text primary-light-bg pull-center my-4 d-block restaurant-list__load-previous"
                        @click="openMatch"
                >Change Match View</button>

                <button v-if="shared.page.initial > 0"
                        class="p-4 center-text primary-light-bg  pull-center my-4 d-block restaurant-list__load-previous"
                        @click="loadPreviousPage"
                >Load Previous Page</button>

                <div class="restaurant-block width-100 pos-relative">

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

                    <infinite-loading :distance="1000" ref="infinite" @infinite="nextPage">
                        <div slot="spinner"></div>
                        <div slot="no-more"></div>
                        <div slot="no-results"></div>
                    </infinite-loading>
                </div>
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
                waiting : true,
                waitingDelay : 1, // in seconds
                initRestaurantLoad  : false,
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
            hasRestaurants(){
                return this.shared.restaurants && this.shared.restaurants.length > 0;
            }
        },

        methods : {
            openMatch(){
                console.log( 'emit change match type' );
                App.event.emit( 'changeMatchType' );
            },

            initPageManager(){
                // set restaurants
                this.shared.init( 'restaurants', [] );

                // set page obj
                this.shared.init( 'page', {
                    current : this.$route.query.page ? +this.$route.query.page : 1,
                    last: this.$route.query.page ? +this.$route.query.page : 1,
                    state : null,
                    complete : false,
                    initial : this.$route.query.page > 1 ? this.$route.query.page - 1 : 0
                });
            },


            loadPreviousPage(){
                let data = this.getPageParamData();
                data.page = this.shared.page.initial;

                return App.ajax.get( `/api/restaurants`, '', data )
                    .then( ({ data }) => this.processPageResultsTop( data ) );

            },


            processPageResultsTop( data ){
                let restaurants = data.data.reverse();

                restaurants.forEach( restaurant => {
                    this.shared.restaurants.unshift( restaurant );
                });

                this.shared.page.initial--;
            },


            loadRestaurants(){
                this.waiting = true;
                this.resetPage();
                this.loadPage();
            },


            resetPage(){
                this.shared.page.current = 1;
                this.shared.page.last = 1;
                this.shared.restaurants = [];
                this.shared.page.state.reset();
                this.shared.page.complete = false;
                this.shared.page.initial = 0;

                if( this.$route.query.page ) App.query.set( 'page', null );
                App.event.emit( 'viewRestaurant', null );
            },


            nextPage(){
                if( this.shared.page.complete ) return;

                this.shared.page.current++;
                this.loadPage();
            },


            async loadPage( options = {} ){
                if( this.shared.page.complete ) return;

                let data = await this.getPageParamData();

                console.log( 'hit restaurants api', data );
                return App.ajax.get( `/api/restaurants`, false, data )
                    .then( ({ data }) => this.processPageResults( data, options ) );
            },


            setNextPageParam(){
                if( this.shared.page.current === 1
                    || this.shared.page.current === this.shared.page.initial + 1
                ) return console.log( 'no need to push' );
                App.query.set( 'page', this.shared.page.current );
            },


            async getPageParamData(){
                // clear page from our query, since we will use our internal counter not the current param
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

                console.log( 'param data', data );
                return data;
            },


            processPageResults( data, options = {} ){

                // update our query string
                this.setNextPageParam();

                // if we are on or past our last page, stop loading
                if( data.current_page >= data.last_page ){
                    this.shared.page.complete = true;
                    this.shared.page.state.complete();
                }

                // turn off the waiting spinner next tick
                this.$nextTick( () => this.waiting = false );

                // if we didn't have any results we are done here
                if ( !data.data.length ) return;

                // add results to our shared restaurants array
                if( !this.shared.restaurants.length ){
                    // if we have none already just plop the whole array in
                    this.shared.restaurants = data.data;
                } else {
                    // otherwise push each result individually
                    this.shared.restaurants.push( ...data.data );
                }

                // tell our infinite loader we are ready
                this.shared.page.state.loaded();

                // emit that we've loaded our restaurants, if this is our first time
                if( !this.initRestaurantLoad ){
                    this.initRestaurantLoad = true;
                    App.event.emit( 'initRestaurantLoad' );
                }
            },

            checkForTour(){
                if( this.shared.user.tour ) return;

                App.ajax.get( `/api/user/${this.shared.user.id}/tour`, false );
                this.shared.user.tour = true;

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
