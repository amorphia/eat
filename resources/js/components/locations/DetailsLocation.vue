<template>
    <div class="location py-4 subtle">
        <div class="location__address mb-3 ellipses location__line">
            <a v-if="addressText" :href="addressLink" target="_blank">{{ addressText }}</a>
        </div>

        <div v-if="location.phone" class="location__phone mb-3 ellipses location__line">
            <a :href="`tel:${location.phone}`" class="d-block">{{ formattedPhone }}</a>
        </div>

        <transition name="collapse">
            <div v-if="openHours" class="location__hours flex-column mb-3 py-3">
                <location-hours v-for="hour in hours" :hour="hour" :key="hour.index" ></location-hours>
            </div>
        </transition>

        <div class="location__actions d-flex location__line">
            <a :href="location.yelp_url" target="_blank" class="icon-clip mr-3 location__actions-icon" title="View on Yelp"></a>
            <button v-if="hours && hours.length" class="icon-clock location__actions-icon" @click="openHours = !openHours" title="View hours"></button>
            <button v-if="shared.user.admin" class="icon-trash location__actions-icon" @click="confirmDeleteLocation" title="Delete Location"></button>

        </div>

    </div>
</template>


<script>
    export default {

        name: 'details-location',
        props: ['location'],

        data() {
            return {
                shared : App.state,
                openHours : false,
            };
        },

        methods : {
            confirmDeleteLocation(){
                App.confirm( () => App.event.emit( 'deleteLocation', this.location ),
                    {
                        message: 'Are you sure you want to delete this location?'
                    });
            }
        },

        computed : {
            formattedPhone(){
                return `(${this.location.phone.slice(2,5)})-${this.location.phone.slice(5,8)}-${this.location.phone.slice(8,12)}`;
            },

            addressText(){
                if( !this.location.street || !this.location.city || !this.location.zip ) return false;
                return `${this.location.street}. ${this.location.city}, ${this.location.zip}`;
            },

            addressLink(){
                return `https://maps.google.com/?q=${this.addressText}`;
            },

            hours(){
                let hours = JSON.parse( this.location.hours );
                if( ! hours ) return [];

                return hours.map( (obj, index) => {
                    obj.index = index;
                    return obj;
                });
            },
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .location {

    }

    .location__actions {
        padding-top: .5rem;
        font-size: 1rem;
        color: var(--subtle-text);
    }

    .location__line {
        margin-bottom: .5rem;
        line-height: 1.4;
    }

    .location__actions-icon {
        display: inline-block;
        margin-right: .5rem;

        @include mobile {
            font-size: 1.5rem;
        }
    }
</style>

