<template>
    <div class="category nav__item pos-relative" title="Set Category Filter" :class="{ 'opacity-3' : shared.match }">

        <div class="nav__select">
            <i class="nav__icon icon-food"></i>

            <select class="rated-filter__select nav__input"
                    v-model="shared.category"
                    @change="setFilter"
                    :disabled="shared.match">
                <option class="sort__option" value="all">all</option>
                <option v-for="category in categories"
                        class="sort__option"
                        :value='category.name'
                        v-text="category.label"></option>
            </select>
        </div>

    </div>
</template>


<script>
    export default {

        name: 'nav-category',

        data() {
            return {
                shared : App.state,
                categories : []
            };
        },

        created(){
            // set selected category
            this.shared.init( 'category', 'all' );

            // setCategory event
            App.event.on( 'setCategory', cat => {
                this.shared.category = cat;
                this.setFilter();
            });

            // get category list
            App.ajax.get( '/api/categories', false ).then( response => this.categories = response.data );
        },

        methods : {
            setFilter(){
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

