<template>
    <button class="category__item mx-3"
            :class="{active : active}"
            @click="toggleCategory"
    >
        {{ category.name }}
    </button>
</template>


<script>
    export default {

        name: 'category-item',

        props: [ 'category' ],

        data() {
            return {
                shared : App.state
            };
        },

        computed: {
            active(){
                return false;
            }
        },

        methods: {
            toggleCategory(){
                App.event.emit( 'filterCategory', this.category.name );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .category__item {
        border: 1px solid;
        border-radius: .2em;
        letter-spacing: 1px;
        font-size: 1.2rem;
        color: var(--highlight-color);
        text-transform: uppercase;
        padding: .2rem .6rem;
        font-weight: bold;

        &.active {
            color: var(--primary-dark);
            background-color: var(--highlight-color);
        }
    }
</style>

