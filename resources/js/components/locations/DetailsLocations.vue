<template>
    <div class="locations pos-relative width-40 pr-6">

        <!-- title -->
        <h2 class="posts__headline width-100 d-flex align-center">
            <span>Locations</span>
        </h2>

        <div class="locations__container pos-relative">
            <!-- locations -->
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
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .locations {
        @include mobile {
            padding: 1rem 1.5rem;
            order: 1;
        }
    }

    .locations__container {
        @include mobile {
            padding: 1rem .5rem;
            font-size: 1.2em;
        }
    }
</style>

