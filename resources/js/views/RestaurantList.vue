<template>
    <div class="restaurant-list overflow-hidden">
            <section>

                <button v-if="shared.page.initial > 0"
                        class="p-4 center-text primary-light-bg width-25 pull-center my-4 d-block restaurant-list__load-previous"
                        @click="loadPreviousPage"
                >Load Previous Page</button>

                <div class="restaurant-block width-100 pos-relative">

                    <restaurant-item
                        v-for="(restaurant, index) in shared.restaurants"
                        :key="restaurant.id"
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


            loadPage( options = {} ){
                if( this.shared.page.complete ) return;

                let data = this.getPageParamData();
                return App.ajax.get( `/api/restaurants`, false, data )
                    .then( ({ data }) => this.processPageResults( data, options ) );
            },


            setNextPageParam(){
                if( this.shared.page.current === 1
                    || this.shared.page.current === this.shared.page.initial + 1
                ) return console.log( 'no need to push' );
                App.query.set( 'page', this.shared.page.current );
            },


            getPageParamData(){
                // clear page from our query, since we will use our internal counter not the current param
                let query = {...this.$route.query };
                delete query['page'];

                // merge our route params to our internal page counter
                let data = { page : this.shared.page.current };
                Object.assign( data, query );

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
        },
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant-list__load-previous {
        margin-top: 2rem;
        margin-bottom: -.5rem;
    }

    .restaurant-block {
        padding: 1.5rem 2.5rem;

        @include mobile {
            padding: 1.75rem .1rem .75rem;
        }

        display: flex;
        flex-wrap: wrap;
    }

</style>
