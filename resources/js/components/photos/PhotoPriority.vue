<template>
    <i class="corner-tag icon-corner photo-priority"
       :class="`corner-tag--${photo.priority}`"
       @click="updatePriority"
    >
    </i>
</template>


<script>
    export default {

        name: 'photo-priority',
        props: [ 'photo' ],

        data() {
            return {
                shared : App.state
            };
        },

        methods : {
            updatePriority(){
                if( this.photo.priority === null ) this.photo.priority = 0;
                
                let priority = this.photo.priority + 1;
                if( priority > 2 ) priority = 0;
                App.event.emit( 'updatePhoto', this.photo, { column : 'priority', value : priority } );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .photo-priority.corner-tag {
        font-size: 1em;
    }
</style>

