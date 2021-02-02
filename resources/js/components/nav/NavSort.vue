<template>
    <div class="sort nav__item pos-relative" title="Switch Sort">

        <div class="nav__select">
            <i class="nav__icon icon-sort"></i>

            <select class="sort__select nav__input" v-model="shared.sort.sort" @change="setSort">
                <option v-for="sort in sortOptions" class="sort__option" :value='sort.column' v-text="sort.name"></option>
            </select>

            <button @click="toggleDirection"><i class="primary" :class="sortIcon"></i></button>
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
                ]
            };
        },

        created(){
            // set sort
            this.shared.init( 'sort', {
                sort : 'interest',
                direction : 'desc'
            });
        },

        methods : {
            setSort(){
                this.shared.sort.direction = this.sortOptions.find( obj => obj.column === this.shared.sort.sort ).direction;
                App.event.emit( 'loadRestaurants' );
            },

            toggleDirection(){
                this.shared.sort.direction = this.shared.sort.direction === 'asc' ? 'desc' : 'asc';
                App.event.emit( 'loadRestaurants' );
            }
        },

        computed : {
            sortIcon(){
                return this.shared.sort.direction === 'asc' ? 'icon-sort_asc' : 'icon-sort_desc';
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


</style>

