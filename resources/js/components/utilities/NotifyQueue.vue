<template>
    <div class="notify-queue overflow-hidden">
        <transition-group name="slide" tag="ul">

            <!-- notification items -->
            <li class="notify-item"
                 v-for="note in queue"
                :key="note.id"
                :class="note.class"
                 @click="removeNote( note )"
            >
                <i class="notify-item__icon" :class="note.class === 'error' ? 'icon-block' : 'icon-check'"></i>
                <span v-if="note.message" class="notify-item__message" v-html="note.message"></span>
            </li>

        </transition-group>
    </div>
</template>


<script>
    export default {

        name: 'notify-queue',

        data() {
            return {
                shared : App.state,
                queue : [],
                persist : 3,
            };
        },

        methods : {

            /**
             * Create a notification item
             *
             * @param {object} note
             *      [error] - is this note an error notification?
             *      [persist] - should this note persist until clicked?
             *      [message] - the message to add to our note
             */
            createNote( note = {} ){

                // generate an id from a timestamp
                note.id = new Date().getTime();

                if( note.error ){
                    note.class = "error";
                    note.persist = true;
                } else {
                    note.class = "success";
                }

                // add new note to front of queue
                this.queue.unshift( note );

                // if we don't tell this note to persist then remove it after x seconds
                if( ! note.persist ) {
                    setTimeout( () => this.removeNote( note ), this.persist * 1000 );
                }
            },


            /**
             *  remove a note
             *
             * @param note - a note's ID
             */
            removeNote( note ){
                this.queue = this.queue.filter( item => item.id !== note.id );
            }

        },

        created() {
            App.event.on( 'notify', this.createNote );
        }

    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .notify-queue {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        max-width: 18vw;
        text-align: center;
        z-index: 10001;

        @include mobile {
            max-width: 40vw;
        }
    }

    .notify-item {
        background-color: #3fb13f;
        padding: 1rem;
        color: white;
        width: 100%;
        font-family: 'open sans', sans-serif;
        letter-spacing: 1px;
        border-radius: 3px;
        margin-top: .5rem;
        display: flex;
        align-items: center;
    }

    .notify-item__message {
        @include mobile {
            display: none;
        }
    }

    .notify-item.error {
        background-color: #c40c0c;
    }

    .notify-item__icon {
        font-size: 1.5rem;
    }

    .notify-item__message {
        padding: 0 .5rem;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

</style>

