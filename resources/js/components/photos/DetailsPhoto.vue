<template>
    <div class="details__photo pos-relative"
         :style="`background-image:url(${photo.url})`">

        <!-- click handler -->
        <div class="pos-cover z-1" @click="$emit( 'clicked', index )"></div>

        <!-- photo priority tag -->
        <photo-priority :photo="photo"></photo-priority>

        <!-- delete button -->
        <div v-if="canEdit" class="edit-panel pos-absolute top-0 right-0">
            <button class="edit-panel__button"><i class="icon-trash" @click="deletePhoto"></i></button>
        </div>

    </div>
</template>


<script>
    export default {

        name: 'details-photo',
        props: ['photo', 'index'],

        data() {
            return {
                shared : App.state
            };
        },

        methods : {
            /**
             * Delete this photo
             */
            deletePhoto(){
                App.confirm( () => App.event.emit( 'updatePhoto', this.photo, { column : 'active', value : false } ),
                    {
                        message: 'Are you sure you want to delete this photo?'
                    });
            }
        },

        computed : {
            // can we edit this photo?
            canEdit(){
                return this.shared.user.admin || this.shared.user.id === this.photo.user_id;
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .details__photo {
        min-width: 33.3333%;
        flex-grow: 1;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        box-shadow: inset 0 0 0px 3px rgba(0,0,0,.2);
        overflow: hidden;

        .edit-panel { opacity: 0; }
        &:hover .edit-panel { opacity: 1; }

        @include mobile {
            min-width: 95%;
            font-size: 2rem;
        }
    }




</style>

