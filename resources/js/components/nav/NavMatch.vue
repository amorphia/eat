<template>
    <div class="friend-match nav__item pos-relative" title="Set Friend Match">

        <div class="nav__select">
            <i class="nav__icon icon-user" :class="{ active : isActive }"></i>

            <select class="rated-filter__select nav__input  mobile-cover" v-model="match" @change="changeMatch">
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
                default : null,
                match : null,
            };
        },

        created(){
            // set sort
            if( this.$route.query.match ) this.match = this.$route.query.match;
            else this.match = this.default;

            // get users list
            App.ajax.get( '/api/users', false ).then( response => this.users = response.data );
        },

        methods : {
            changeMatch(){
                let val = this.default !== this.match ? this.match : null;
                App.query.set( 'match', val );
                App.event.emit( 'loadRestaurants' );
            },
        },

        computed : {
            isActive(){
                return this.match !== this.default;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
</style>

