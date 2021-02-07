<template>
    <div class="friend-match nav__item pos-relative" title="Set Friend Match">

        <modal-wrap
            :open="openChooseMatchType"
            @closed="closeChooseTypeModal"
            classes="width-50">

            <div class="choose-match width-50">

                <div class="choose-match__title pb-4">View {{ currentMatchName }}'s list</div>

                <button v-for="type in matchTypes"
                        class="choose-match__button"
                        @click="setMatch( type.type)">{{ type.label }}</button>

            </div>

        </modal-wrap>

        <div class="nav__select">
            <i class="nav__icon icon-user" :class="{ active : isActive }"></i>

            <select class="rated-filter__select nav__input  mobile-cover" v-model="match" @change="matchChanged">
                <option :value="null">None</option>
                <option
                    v-for="user in users"
                    class="sort__option"
                    :value='user.uuid'
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
                type : null,
                openChooseMatchType : false,
                matchTypes : [
                    { label : 'View your overlap', type : null },
                    { label : 'View their most interested', type : 'interest' },
                    { label : 'View their favorites', type : 'ratings' },
                ]
            };
        },

        created(){
            // set sort
            if( this.$route.query.match ) this.match = this.$route.query.match;
            else this.match = this.default;

            // reset filter
            App.event.on( 'resetFilters', () => this.match = this.default );

            // get users list
            App.ajax.get( '/api/users', false ).then( response => this.users = response.data );
        },

        methods : {
            closeChooseTypeModal(){
                this.openChooseMatchType = false;
                this.match = this.default;
            },

            matchChanged( e ){
                // if we changed to the default, then reset
                if( this.default === this.match ){
                    App.query.set([
                        {name : 'match', value : null },
                        {name : 'type', value : null }
                    ]);
                    App.event.emit( 'loadRestaurants' );
                    return;
                }

                //otherwise open our select match type
                this.openChooseMatchType = true;
            },

            setMatch( type ){
                this.openChooseMatchType = false;

                App.query.set([
                    {name : 'match', value : this.match  },
                    {name : 'type', value : type }
                ]);

                App.event.emit( 'loadRestaurants' );
            },
        },

        computed : {
            isActive(){
                return this.match !== this.default;
            },

            currentMatchName(){
                if( !this.match ) return;

                let user = this.users.find( obj => obj.uuid === this.match );
                if( user ) return user.name;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';


    .choose-match {
        margin: auto;
        text-align: center;
        font-size: 1.5rem;
        border: 1px solid rgba(255,255,255,.5);
        border-radius: .25rem;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        box-shadow: 0 0 10px rgba(0,0,0,.5);

        @include mobile {
            width: 90%;
        }
    }

    .choose-match__title {
        @include mobile {
            margin-bottom: 1rem;
        }
    }

    .choose-match__button  {
        display: block;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: .25rem;
        background-color: var(--primary-light);

        &:last-child {
            margin-bottom: 0;
        }
    }
</style>

