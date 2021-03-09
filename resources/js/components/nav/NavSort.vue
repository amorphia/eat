<template>
    <div id="sort" class="sort nav__item pos-relative" title="Switch Sort" :class="{ disabled : $route.query.match }">

        <div class="nav__select">
            <i class="nav__icon icon-sort" :class="{ active : isSortActive }"></i>

            <select class="sort__select nav__input mobile-cover"
                    v-model="sort"
                    @change="setSort"
                    :disabled="$route.query.match">
                <option v-for="sort in sortOptions" class="sort__option" :value='sort.column' v-text="sort.name"></option>
            </select>

            <button @click="toggleDirection" class="desktop-only"><i class="primary sort__direction" :class="sortClasses"></i></button>
        </div>

    </div>
</template>


<script>
    export default {

        name: 'nav-sort',
        props: [],

        data() {
            return {
                shared : App.state,
                sortOptions : [
                    { name : 'interest', column : 'interest', direction : 'desc' },
                    { name : 'rating', column : 'rating', direction : 'desc' },
                    { name : 'name', column : 'name', direction : 'asc' },
                    { name : 'newest', column : 'created_at', direction : 'desc' },
                    { name : 'distance', column : 'distance', direction : 'asc' },
                ],
                sort : null,
                direction : null,
                default : 'interest',
            };
        },

        created(){
            // set initial sort
            if( this.$route.query.sort ) this.sort = this.$route.query.sort;
            else this.sort = this.default;

            // set initial direction
            if( this.$route.query.direction ) this.direction = this.$route.query.direction;
            else this.direction = this.getDefaultDirection( this.sort );

            // reset filter
            App.event.on( 'resetFilters', () => {
                this.sort = this.default;
                this.direction = this.getDefaultDirection( this.sort );
            });
        },

        methods : {

            getDefaultDirection( col ){
                let sort = this.sortOptions.find( obj => obj.column === col );
                return sort.direction;
            },

            setSort(){

                let sortVal = this.default !== this.sort ? this.sort : null;
                this.direction = this.getDefaultDirection( this.sort );

                let directionValue = this.direction;
                if( directionValue === 'desc' ) directionValue = null;

                App.query.set([
                    { name : 'sort', value : sortVal },
                    { name : 'direction', value : directionValue }
                ]);

                App.event.emit( 'loadRestaurants' );
            },

            toggleDirection(){
                this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                let directionValue = this.direction !== 'desc' ? this.direction : null;

                App.query.set( 'direction', directionValue );
                App.event.emit( 'loadRestaurants' );
            }
        },

        computed : {
            isSortActive(){
                return this.sort !== this.default;
            },

            defaultDirection(){
                return this.getDefaultDirection( this.sort );
            },

            isDirectionActive(){
                return this.direction !== this.getDefaultDirection( this.sort );
            },

            sortClasses(){
                let classes = this.sortIcon;

                if( this.isDirectionActive ) classes += ' active';

                return classes;

            },

            sortIcon(){
                return this.direction === 'asc' ? 'icon-sort_asc' : 'icon-sort_desc';
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .sort__select {
        direction: rtl;

        & option {
          direction: initial;
        }
    }

    .sort__direction.active {
        color: var(--highlight-color);
    }


</style>

