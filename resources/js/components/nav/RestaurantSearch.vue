<template>

    <div class="nav-search nav__item" title="Search movies">

        <div class="nav__select">
            <i class="nav__icon icon-search" @click="setSearchOpen"></i>

            <div class="nav-search-wrap width-100 d-flex align-center" :class="{ open : openSearch }">
                <input class="nav__input search-input"
                       v-model="query"
                       placeholder="Search..."
                       type="text"
                       @input="searchChange"
                       @keydown.down="navigateList( +1 )"
                       @keydown.up="navigateList( -1 )"
                       @keyup.enter="viewRestaurant( 'selected' )"
                       ref="search"
                        >

                <i v-if="openSearch || query" class="nav-search-close icon-x pointer" @click="reset"></i>

                <div v-if="results" class="autocomplete pos-absolute width-100">
                    <div v-for="( restaurant, index ) in results"
                         class="autocomplete__item ellipses"
                         :class="{ selected : selectedItem === index }"
                         v-html="restaurant.name"
                         @click="viewRestaurant( restaurant )"
                        ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        name: 'restaurant-search',

        data() {
            return {
                query : '',
                selectedItem : -1,
                results : null,
                axiosCancelToken : null,
                axiosSource : null,
                openSearch : false
            }
        },

        created() {
            // throttle searches
            this.searchChange = _.throttle( () => {
                var search = this.makeRequestCreator();
                search();
            }, 200 );

        },

        computed : {

        },

        methods : {

            setSearchOpen(){
                this.openSearch = true;

                this.$nextTick( () => {
                    this.$refs.search.focus();
                });

            },

            reset(){
                this.query = '';
                this.results = null;
                this.selectedItem = -1;
                this.openSearch = false;
            },

            navigateList( increment ){
                this.selectedItem += increment;
                if( this.selectedItem < -1 ){
                    this.selectedItem = -1;
                }

                if( this.results ){
                    if( this.selectedItem > this.results.length - 1 ){
                        this.selectedItem = this.results.length -1 ;
                    }
                } else {
                    this.selectedItem = -1;
                }
            },

            viewRestaurant( restaurant ){

                if( restaurant === 'selected'){
                    restaurant = this.results[ this.selectedItem ];
                }

                App.event.emit( 'forceViewRestaurant', restaurant );
                this.reset();

            },

            makeRequestCreator() {
                var call;
                return () => {

                    if (call) { call.cancel(); }
                    call = axios.CancelToken.source();

                    return axios.post(
                        '/api/restaurants/search',
                        { searchTerm : this.query },
                        { cancelToken : call.token }
                    )
                        .then( response => {
                            this.results = response.data;
                            this.navigateList( 0 );
                        })
                        .catch( errors => {
                            console.log( errors );
                        });
                }
            },

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>
