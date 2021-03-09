<template>
    <div class="d-flex align-stretch">
        <nav-menu></nav-menu>
        <div class="main-content">
            <router-view></router-view>
        </div>
        <restaurant-details></restaurant-details>
        <restaurant-manager></restaurant-manager>
        <working-slider></working-slider>
        <notify-queue></notify-queue>
        <vue-confirm-dialog class="confirm-popup"></vue-confirm-dialog>
        <my-tour></my-tour>
    </div>
</template>


<script>

    export default {
        name: 'app-core',
        data() {
            return {
                shared : App.state
            };
        },
        created(){
            this.loadUser();

        },

        methods : {
            loadUser(){
                App.ajax.get( 'api/user', false )
                    .then( ({ data }) => this.shared.init( 'user', data ) );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .main-content {
        flex-grow: 1;
        flex-shrink: 1;
        padding-top: 4.1rem;
    }

    .confirm-popup {
        z-index: 1000;
    }
</style>

