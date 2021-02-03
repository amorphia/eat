<template>
    <div class="restaurant-list overflow-hidden">
            <section>
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
                    <infinite-loading :distance="500" ref="infinite" @infinite="nextPage">
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
            };
        },

        created(){
            this.initPageManager();
        },

        mounted(){
            this.shared.page.state = this.$refs.infinite.stateChanger;
            this.loadRestaurants();
            App.event.on( 'loadRestaurants', this.loadRestaurants );
            App.event.on( 'nextPage', this.nextPage );
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
                    current : 0,
                    last: 0,
                    state : null,
                    complete : false,
                });
            },


            loadRestaurants(){
                this.waiting = true;
                //setTimeout( () => this.waiting = false, this.waitingDelay * 1000 );
                this.resetPage();
                this.nextPage();
            },


            resetPage(){
                this.shared.page.current = 1;
                this.shared.page.last = 0;
                this.shared.restaurants = [];
                this.shared.page.state.reset();
                this.shared.page.complete = false;

                App.event.emit( 'viewRestaurant', null );
            },


            nextPage(){
                if( this.shared.page.complete ) return;

                let data = this.getPageParamData();
                App.ajax.get( `/api/restaurants`, false, data )
                    .then( ({ data }) => this.processPageResults( data ) );
            },


            getPageParamData(){
                let data = { page : this.shared.page.current };

                // add sort parameters
                Object.assign( data, this.shared.sort );

                // add rated filter
                data.rated = this.shared.rated;

                // category
                data.category = this.shared.category;

                // match
                data.match = this.shared.match;

                return data;
            },


            processPageResults( data ){
                this.shared.page.current++;

                // if we are on or past our last page, stop loading
                if( data.current_page >= data.last_page ){
                    this.shared.page.complete = true;
                    this.shared.page.state.complete();
                }

                this.$nextTick( () => this.waiting = false );

                // if we didn't have any results we are done here
                if ( !data.data.length ) return;

                // add results to our shared restaurants array
                if( !this.shared.restaurants.length ){
                    // if we have none already just plop the whole array in
                    this.shared.restaurants = data.data;
                } else {
                    // otherwise push each result individually
                    data.data.forEach(obj => this.shared.restaurants.push(obj));
                }

                this.shared.page.state.loaded();
            },
        },
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant-list {
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
