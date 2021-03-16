<template>
    <div class="loader-bar pos-fixed width-100" v-if="requests > 0">
        <div class="loader-bar__streak"></div>
    </div>
</template>


<script>
    export default {

        name: 'working-slider',

        data() {
            return {
                shared : App.state,
                requests : 0 // the number of ajax requests currently outstanding
            };
        },

        created() {
            // each time we see a "working" event increase our outstanding requests by one
            // as long as we have at least one outstanding request display our working slider
            App.event.on( 'working', () => {
                this.requests = this.requests + 1;
            } );

            // each time we see a "done" event reduce our outstanding requests by one
            App.event.on('done', () => {
                this.requests--;
                if( this.requests < 0 ) this.requests = 0;
            } );
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

.loader-bar {
    position: fixed !important;
    z-index: 100000;
    height: 5px;
    width: 100%;
    position: relative;
}

.loader-bar__streak {
    position: relative;
    height: 100%;
    animation-name: loader-animation;
    animation-duration: 3s;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}

@keyframes loader-animation {
    0% {
        width: 0%;
        background-color: #ff00fa;
    }
    25% {
        width: 100%;
        left: 0%
    }
    50% {
        left: 100%;
        width: 0;
        background-color: gold;
    }
    75% {
        left: 0%;
        width: 100%
    }
    100% {
        left: 0%;
        width: 0%;
        background-color: #ff00fa;
    }
}
</style>

