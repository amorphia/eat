<template>
<nav class="nav d-flex flex-wrap">

    <nav class="nav__core d-flex align-center">

        <a href="/" class="nav-logo desktop-only pull-left">
            <img class="nav-logo__full" src="/images/svg/logo.svg">
        </a>



        <!-- sort select -->
        <nav-sort></nav-sort>

        <!-- rated filters -->
        <nav-rated></nav-rated>

        <!-- category filters -->
        <nav-category></nav-category>

        <!-- search -->
        <restaurant-search></restaurant-search>

        <!--
        <div class="view-recents nav__item pointer" @click="recentOpen = true">
            <i class="nav__icon icon-recent"></i>
        </div>


        <div class="view-year nav__item grow-2 pos-relative" title="Switch years">
            <i class="nav__icon icon-year"></i>
            <select v-if="shared.movies" class="nav__input mobile-cover year-select" :value="shared.currentYear" @change="setYear">
                <option v-for="year in movieYears" :value='year' v-text="year"></option>
            </select>
        </div>



        <movie-search></movie-search>



        <div class="view-filter nav__item grow-1" title="Filter movies">
            <i class="nav__icon icon-show"></i>
            <select class="nav__input nav-select mobile-cover" v-model="shared.filter">
                <option value="all">all</option>
                <option value="unseen">unseen</option>
                <option value="seen">seen</option>
            </select>
        </div>

        <div class="total-movies nav__item" v-text="shared.seenTotal" @click="shared.editMode = !shared.editMode"></div>
        -->


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
            padding: 1.2rem .5rem;
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

        @include mobile {
            flex-grow: 1;
            justify-content: center;
        }

    }

    .nav__icon {
        color: var(--primary-light);
        font-size: 1.4rem;
    }

    .nav__select {
        display: inline-flex;
        align-items: center;
        padding: .35rem .55rem;
        border-radius: .2em;
        border: 1px solid  rgba(101, 155, 145, .42 );
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


    .nav-search {
        @include mobile {
            position: unset;
        }
    }

    .nav-search-wrap {
        @include mobile {
            position: absolute;
            top: 0;
            left: 0;
            background-color: var(--near-white);
            display: none;
            z-index: 5;
            padding: 1rem;

            &.open {
                display: flex;
            }
        }
    }

    .search-input {
        @include mobile {
            font-size: 1.8rem;
        }
    }

    .nav-search-close {
        color: var(--primary-color);
        font-size: 1.5em;

        @include mobile {
            font-size: 3rem;
        }
    }

    .autocomplete {
        top: 100%;
        left: 0;
        background-color: var(--primary-darkest);
        padding: 1rem;
        border-top: 0;
        color: var(--light-text);
        font-family: var( --secondary-font);
        letter-spacing: 1px;
    }

    .autocomplete__item {
        margin-bottom: .5rem;
        font-size: 1.4rem;
        cursor: pointer;

        &.selected {
            color: gold;
        }

        @include mobile {
            font-size: 1.8rem;
        }

        span {
            font-family: var(--secondary-font);
            font-size: .8em;
            opacity: .7;
        }
    }




</style>

