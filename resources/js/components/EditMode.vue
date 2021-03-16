<template>
    <transition name="collapse">
        <div v-if="checked.length > 0" class="edit-mode p-4 width-100 justify-center d-flex">
            <button class="edit-mode__button mx-3"
                    @click="confirmDelete">delete</button>
            <button :disabled="checked.length < 2"
                    class="edit-mode__button"
                    @click="confirmMerge">merge</button>
        </div>
    </transition>
</template>


<script>
    export default {

        name: 'edit-mode',
        props: [],

        data() {
            return {
                shared : App.state
            };
        },

        watch : {
            // watch when we leave edit mode and uncheck any checked restaurants
            'shared.editMode' : function( val ) {
                if( !val ) this.checked.forEach( obj => obj.checked = false );
            }
        },

        created(){
            // if we are an admin and hit the escape key then toggle edit mode
            document.addEventListener( 'keyup', (e) => {
                if( this.shared.user.admin && e.key === 'Escape') this.shared.editMode = !this.shared.editMode;
            });
        },

        methods : {
            confirmDelete(){
                App.confirm( () => App.event.emit( 'editRestaurants', 'delete', this.checked ), {
                    message: 'Are you sure you want to delete these restaurants?'
                });
            },

            confirmMerge(){
                App.confirm( () => App.event.emit( 'editRestaurants', 'merge', this.checked ), {
                    message: 'Are you sure you want to merge these restaurants?'
                });
            },

        },

        computed : {
            checked(){
                if( ! this.shared.restaurants ) return [];
                return this.shared.restaurants.filter( obj => obj.checked );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .edit-mode {
        background-color: var(--primary-darkest);
    }

</style>

