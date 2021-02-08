<template>
    <transition name="right">
        <div v-if="open" class="select-match pos-fixed d-flex width-100 height-100 top-0 left-0 right-0 bottom-0 z-50 primary-darkest-bg">

            <button @click="$emit('close')" class="toggle top-0 right-0"><i class="icon-x"></i></button>

            <div class="width-50 overflow-auto match-select__panel z-2 primary-darkest-bg d-flex justify-end align-center">
                <ul class="match-list right-text">
                    <li v-for="user in users"
                        class="match-list__item"
                        :class="{ active : user === currentMatch}"
                        @click="currentMatch = user"
                    >{{ user.name }}</li>
                </ul>
            </div>


            <transition name="right">
                <div v-if="currentMatch" class="width-50 match-select__panel z-1 d-flex flex-column justify-center">
                    <div class="">View {{ currentMatchName }}'s list</div>
                    <ul class="match-list">
                        <li v-for="type in matchTypes"
                            class="match-list__item"
                            :class="{ active : type === currentType}"
                            @click="setType( type )"
                        >{{ type.label }}</li>
                    </ul>
                </div>
            </transition>
        </div>
    </transition>
</template>


<script>
    export default {

        name: 'select-match',
        props: [
            'open',
            'matchTypes',
            'match',
            'type',
            'users'
        ],

        data() {
            return {
                shared : App.state,
                currentMatch : null,
                currentType : null,
            };
        },

        created(){

        },

        watch : {
            open( val ){
                if( val ){
                    this.currentMatch = this.match;
                    this.currentType = this.type;
                }
            }
        },


        methods : {
            setType( type ){
                this.currentType = type;

                this.$emit( 'changed', {
                   match : this.currentMatch,
                    type : this.currentType,
                });
            }
        },

        computed : {
            currentMatchName(){
                if( !this.currentMatch ) return;

                let user = this.users.find( obj => obj.uuid === this.currentMatch );
                if( user ) return user.name;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .select-match {
        padding: 2rem;
    }

    .match-select__panel {
        position: relative;
    }

    .match-list__item.active {
        color: var(--highlight-color);
    }
</style>

