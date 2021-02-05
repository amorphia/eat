<template>
<nav class="nav d-flex flex-wrap">

    <nav class="nav__core d-flex align-center">

        <!-- logo -->
        <a href="/" class="nav-logo desktop-only pull-left">
            <img class="nav-logo__full" src="/images/svg/logo.svg">
        </a>

        <!-- sort select -->
        <nav-sort></nav-sort>

        <!-- rated filters -->
        <nav-rated></nav-rated>

        <!-- category filters -->
        <nav-category></nav-category>

        <!-- user match -->
        <nav-match></nav-match>

        <!-- search -->
        <restaurant-search></restaurant-search>

        <!-- account -->
        <nav-account></nav-account>
    </nav>

    <edit-mode></edit-mode>
</nav>
</template>


<script>
    export default {
        name: 'nav-menu',

        data() {
            return {
                shared : App.state,
                recentOpen : false,
                searchInput : '',
            };
        },

        created(){

        },

        methods : {

        },

        computed: {

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';


    .nav {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        z-index: 7;
        background-color: var(--primary-extra-dark);
        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.01);
        border-bottom: 1px solid rgba(0,0,0,.1);
    }

    .nav__core {
        padding: 1.2rem 3.2rem;
        width: 100%;

        @include mobile {
            padding: 1rem .5rem;
        }
    }

    .nav-logo__full {
        width: 4em;
    }

    .nav__item {
        position: relative;
        margin: 0 .5rem;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        max-width: 30%;

        &.disabled {
            opacity: .3;
            filter: saturate( .3 );
        }

        @include mobile {
            flex-grow: 1;
            justify-content: center;
        }

    }

    .nav__icon {
        color: var(--primary-light);
        font-size: 1.4rem;

        &.active {
            color: var(--highlight-color);
        }

        @include mobile {
            font-size: 1.8rem;
        }

    }

    .nav__select {
        display: inline-flex;
        align-items: center;
        padding: .35rem .55rem;
        border-radius: .2em;
        border: 1px solid  rgba(101, 155, 145, .42 );

        @include mobile {
            width: 100%;
            justify-content: center;
            padding: .65rem .35rem;
        }
    }

    .nav__input {
        outline: none;
        border: 0;
        color: #ededed;
        padding: 0 .5rem;
        position: relative;
        background: transparent;
        line-height: 1.3em;

        option {
            background-color: var(--primary-color);
            color: white;
        }

        @include mobile {
            &.mobile-cover {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                opacity: 0;
                padding: 0;
            }
        }
    }

</style>

