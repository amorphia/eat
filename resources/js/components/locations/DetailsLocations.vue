<template>
    <div class="locations pos-relative width-40 pr-6">
        <h2 class="posts__headline width-100 d-flex align-center">
            <span>Locations</span>
        </h2>

        <div class="locations__container">
            <details-location
                v-for="location in restaurant.locations"
                :key="location.id"
                :location="location"
            ></details-location>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'details-locations',
        props: ['restaurant'],

        data() {
            return {
                shared : App.state,
                locations : null,
            };
        },

        created(){
            this.locations = this.restaurant.locations;
        },

        methods : {

        },

        computed : {

        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .locations {

    }
</style>

