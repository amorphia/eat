<template>
    <div class="category nav__item pos-relative" title="Set Category Filter" :class="{ disabled  : $route.query.match }">

        <div class="nav__select">
            <i class="nav__icon icon-food" :class="{ active : isActive }"></i>

            <select class="rated-filter__select nav__input  mobile-cover"
                    v-model="category"
                    @change="setFilter"
                    :disabled="$route.query.match">
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
                categories : [],
                default : 'all',
                category : 'all',
            };
        },

        created(){
            // set selected category
            if( this.$route.query.category ) this.category = this.$route.query.category;
            else this.category = this.default;

            // setCategory event
            App.event.on( 'setCategory', cat => {
                this.category = cat;
                this.setFilter();
            });

            // get category list
            App.ajax.get( '/api/categories', false ).then( response => this.categories = response.data );
        },

        methods : {
            setFilter(){
                let val = this.default !== this.category ? this.category : null;
                App.query.set( 'category', val );
                App.event.emit( 'loadRestaurants' );
            },
        },

        computed : {
            isActive(){
                return this.category !== this.default;
            }

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';


</style>

