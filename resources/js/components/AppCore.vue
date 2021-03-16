<template>
    <div class="d-flex align-stretch">
        <!-- nav menu -->
        <nav-menu></nav-menu>

        <!-- main content -->
        <div class="main-content">
            <router-view></router-view>
        </div>

        <!-- restaurant details -->
        <restaurant-details></restaurant-details>

        <!-- nutz and boltz -->
        <working-slider></working-slider>
        <notify-queue></notify-queue>
        <vue-confirm-dialog class="confirm-popup"></vue-confirm-dialog>
        <my-tour></my-tour>
        <restaurant-manager></restaurant-manager>
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
            // load our user object
            loadUser(){
                App.ajax.get( 'api/users/null', false )
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

