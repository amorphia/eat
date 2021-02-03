<template>
    <div class="friend-match nav__item pos-relative" title="Set Friend Match">

        <div class="nav__select">
            <i class="nav__icon icon-user" :class="{ active : shared.match !== this.default }"></i>

            <select class="rated-filter__select nav__input  mobile-cover" v-model="shared.match" @change="loadRestaurants">
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
                users : [],
                default : null
            };
        },

        created(){
            // set sort
            this.shared.init( 'match', this.default );

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

