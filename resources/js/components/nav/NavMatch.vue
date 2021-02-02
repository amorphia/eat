<template>
    <div class="friend-match nav__item pos-relative" title="Set Friend Match">

        <div class="nav__select">
            <i class="nav__icon icon-user"></i>

            <select class="rated-filter__select nav__input" v-model="shared.match" @change="loadRestaurants">
                <option :value="null">None</option>
                <option
                    v-for="user in users"
                    class="sort__option"
                    :value='user.id'
                    v-text="user.name">
                </option>
            </select>
        </div>

    </div>
</template>


<script>
    export default {

        name: 'nav-match',

        data() {
            return {
                shared : App.state,
                users : []
            };
        },

        created(){
            // set sort
            this.shared.init( 'match', null );

            // get category list
            App.ajax.get( '/api/users', false ).then( response => this.users = response.data );
        },

        methods : {
            loadRestaurants(){
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

