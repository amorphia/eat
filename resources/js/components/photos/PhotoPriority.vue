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
            /**
             * Update this photo's priority
             */
            updatePriority(){
                if( !Number.isInteger( this.photo.priority ) ) this.photo.priority = 0;

                // increment this photo's priority, if it goes above two then reset back to 0
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
        font-size: 1.5em;

        @include mobile {
            font-size: 2.5rem;
        }
    }
</style>

