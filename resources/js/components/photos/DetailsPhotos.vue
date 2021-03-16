<template>
    <div class="details__photos-container pos-relative">
        <div v-if="photos.length" class="details__photos">

            <!-- photos horizontal scroll -->
            <horizontal-scroll classes="height-100" ref="container">
                <details-photo v-for="(photo, index) in photos"
                               :photo="photo"
                               :key="photo.id"
                               :index="index"
                               @clicked="openLightbox"
                ></details-photo>
            </horizontal-scroll>

        </div>
        <!-- no photos -->
        <div v-else class="primary-darkest-bg p-5 light-text">
            No Photos
        </div>

        <!-- add photo button -->
        <button class="action-button shift-down" title="Add Photo" @click="openAddPhoto = true">
            <i class="icon-add_photo"></i>
        </button>

        <!-- lightbox -->
        <LightBox v-if="media.length" :media="media"
                  :showLightBox="false"
                  :showThumbs="false"
                  :showCaption="true"
                  ref="lightbox"
        ></LightBox>

        <!-- add photo modal -->
        <modal-wrap
            :open="openAddPhoto"
            @closed="openAddPhoto = false"
            classes="width-50">
            <div class="photos__add-container" :class="addType">
                <div class="d-flex px-4 pb-4">
                    <button class="photos__type-button"
                            :class="{active: addType ==='file'}"
                            @click="addType = 'file'">
                        FILE</button>
                    <button class="photos__type-button"
                            :class="{active: addType ==='url'}"
                            @click="addType = 'url'">
                        URL</button>
                </div>
                <vue-form
                    id="new-post"
                    method="post"
                    :action="photoAction"
                    submitclass="width-100"
                    @success="photoAdded"
                    :schema="photoSchema"></vue-form>
            </div>
        </modal-wrap>

    </div>
</template>


<script>
    import LightBox from 'vue-image-lightbox'

    export default {

        name: 'details-photos',
        props: ['restaurant'],
        components: {LightBox},

        data() {
            return {
                shared : App.state,
                openAddPhoto : false,
                photoAction: '/api/photos',
                addType : 'file',
            };
        },

        created(){
            App.event.on( 'detailsChanged', this.resetPhotoScroll );
        },

        methods : {

            /**
             * Open our lightbox to zoom in on our images
             *
             * @param index - image index to start with
             */
            openLightbox( index ){
                this.$refs.lightbox.showImage( index );
            },


            /**
             * Scroll our photos slider back to the first photo
             */
            resetPhotoScroll(){
                if( this.$refs.container ) this.$refs.container.resetScroll();
            },


            /**
             * Process a newly added photo
             *
             * @param response
             */
            photoAdded( response ){
                // close add photo modal
                this.openAddPhoto = false;

                // get our parent restaurant
                let restaurant = this.shared.restaurants.find( obj => obj.id === this.restaurant.id );
                restaurant = restaurant ? restaurant : this.shared.forcedRestaurant;

                // add this photo to our restaurant's photos
                restaurant.photos.push( response );
            }
        },

        computed: {
            // this restaurants photos
            photos(){
                return this.restaurant.photos;
            },

            // return a formatted array of our photos
            media(){
                let photos = [];
                this.photos.forEach( photo => {
                    photos.push({
                        src: photo.url,
                        caption : photo.body
                    });
                });
                return photos;
            },

            // schema for our add restaurants form
            photoSchema(){
                return [
                    { name: 'restaurant_id', value: this.restaurant.id, type: 'hidden' },
                    { name: 'url', class: 'photo__upload-url', optional : true, hideOptionalLabel : true },
                    { name: 'image', class: 'photo__upload-file', type: 'file', accept : 'image/*;capture=camera', max : 15, optional : true, label : 'file', hideOptionalLabel : true  },
                    { name: 'body', label: 'caption', type : 'textarea', optional : true },
                ]
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';
    @import '/node_modules/vue-image-lightbox/dist/vue-image-lightbox.min.css';

    .details__photos {
        height: 20rem;
        overflow: auto;
    }

    .photos__add-container.file .photo__upload-url, .photos__add-container.url .photo__upload-file {
        display: none;
    }

    .photos__type-button {

        padding: 1rem;
        width: 50%;
        background-color: rgba(235,235,235,.4);

        &.active {
            background-color: var(--highlight-color);
        }
    }

    .vue-lb-info:empty {
        display: none;
    }

    .vue-lb-info {
        font-size: 1rem;
        padding: .5rem;
        height: auto;
    }


</style>

