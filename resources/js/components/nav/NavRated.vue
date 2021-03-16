<template>
    <div id="filter" class="rated-filter nav__item pos-relative" title="Set Rated Filter" :class="{ disabled  : $route.query.match }">

        <div class="nav__select">

            <!-- icon -->
            <label class="nav__icon icon-star_half" :class="{ active : isActive }"></label>

            <!-- select -->
            <select class="rated-filter__select nav__input  mobile-cover"
                    v-model="rated"
                    @change="setRatedFilter"
                    :disabled="$route.query.match">
                <option v-for="filter in ratedFilterOptions" class="sort__option" :value='filter' v-text="filter"></option>
            </select>
        </div>

    </div>
</template>


<script>
    export default {

        name: 'nav-rated',

        data() {
            return {
                shared : App.state,
                ratedFilterOptions : [
                    'all',
                    'unrated',
                    'rated',
                    'interested',
                    'unviewed'
                ],
                default : 'all',
                rated : null
            };
        },

        created(){
            // set sort
            if( this.$route.query.rated ) this.rated = this.$route.query.rated;
            else this.rated = this.default;

            // reset filter
            App.event.on( 'resetFilters', () => this.rated = this.default );

        },

        methods : {
            setRatedFilter(){
                let val = this.default !== this.rated ? this.rated : null;

                // set query string
                App.query.set( 'rated', val );

                // load restaurants
                App.event.emit( 'loadRestaurants' );
            },
        },

        computed : {
            // is this filter active
            isActive(){
                return this.rated !== this.default;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>

