<template>
    <div class="nav-account pull-right pos-relative">
        <div class="nav-account__button primary" @click="dropdown = !dropdown" :class="{active : dropdown}">
            <i class="icon-settings"></i>
        </div>
        <transition name="collapse">
            <div class="nav-account__dropdown pos-absolute top-100" v-if="dropdown">
                <div class="nav-account__dropdown-content p-4">
                    <a class="d-block nav-account__dropdown-item" href="/logout">logout</a>
                    <a class="d-block nav-account__dropdown-item" href="/account">account</a>
                    <button v-if="shared.user.admin"
                            class="d-block nav-account__dropdown-item"
                            :class="{active: shared.editMode}"
                            @click="toggleEditMode"
                    >edit mode</button>
                </div>

            </div>
        </transition>
    </div>
</template>


<script>
    export default {

        name: 'nav-account',
        props: [],

        data() {
            return {
                shared : App.state,
                dropdown : false,
            };
        },

        created(){

        },

        methods : {
            toggleEditMode(){
                this.shared.editMode = !this.shared.editMode;
                this.dropdown = false;
            }
        },

        computed : {

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .nav-account__button {
        font-size: 1.5rem;
        &.active {
            color: var(--highlight-color);
        }
    }

    .nav-account__dropdown {
        background-color: rgba(0,0,0,.9);
        right: 0;
    }

    .nav-account__dropdown-item {
        white-space: nowrap;
        padding : .5rem .5rem;
    }
</style>

