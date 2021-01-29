<template>
    <div class="details__photos-container pos-relative">
        <div v-if="photos.length" class="details__photos">
            <horizontal-scroll classes="height-100">
                <details-photo v-for="(photo, index) in photos"
                               :photo="photo"
                               :key="photo.id"
                               :index="index"
                               @clicked="openLightbox"
                ></details-photo>
            </horizontal-scroll>
        </div>
        <div v-else class="primary-darkest-bg p-5 light-text">
            No Photos
        </div>

        <button class="action-button shift-down" title="Add Photo">
            <i class="icon-add_photo"></i>
        </button>

        <LightBox v-if="media.length" :media="media"
                  :showLightBox="false"
                  :showThumbs="false"
                  :showCaption="true"
                  ref="lightbox"
        ></LightBox>
    </div>
</template>


<script>
    import LightBox from 'vue-image-lightbox'

    export default {

        name: 'details-photos',
        props: ['photos'],
        components: {LightBox},

        data() {
            return {
                shared : App.state,
            };
        },

        methods : {
            openLightbox( index ){
                this.$refs.lightbox.showImage( index );
            }
        },
        computed: {
            media(){
                let photos = [];
                this.photos.forEach( photo => {
                    photos.push({
                        src: photo.url,
                        caption : photo.body
                    });
                });
                return photos;
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



    .vue-lb-info:empty {
        display: none;
    }

    .vue-lb-info {
        font-size: 1rem;
        padding: .5rem;
        height: auto;
    }

</style>

