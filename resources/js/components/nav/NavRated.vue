<template>
    <div class="rated-filter nav__item pos-relative" title="Set Rated Filter">

        <div class="nav__select">
            <i class="nav__icon icon-star_half"></i>

            <select class="rated-filter__select nav__input" v-model="shared.rated" @change="setRatedFilter">
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
                    'rated'
                ]
            };
        },

        created(){
            // set sort
            this.shared.init( 'rated', 'all' );
        },

        methods : {
            setRatedFilter(){
                App.event.emit( 'loadRestaurants' );
            },
        },

        computed : {

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>

