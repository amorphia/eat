<template>
    <div class="rated-filter nav__item pos-relative" title="Set Rated Filter" :class="{ 'opacity-3' : shared.match }">

        <div class="nav__select">
            <label class="nav__icon icon-star_half" :class="{ active : shared.rated !== this.default }"></label>

            <select class="rated-filter__select nav__input  mobile-cover"
                    v-model="shared.rated"
                    @change="setRatedFilter"
                    :disabled="shared.match">
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
                ],
                default : 'all'
            };
        },

        created(){
            // set sort
            this.shared.init( 'rated', this.default );
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

