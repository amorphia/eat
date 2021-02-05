<template>
    <div class="restaurant__rating">
        <div class="restaurant_ratings"
             :class="{ open : open  }"
        >
            <rating-number
                v-for="val in values"
                :value="val"
                :key="val"
                :open="open"
                :restaurant="restaurant"
                @clicked="numberClicked"
            ></rating-number>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'restaurant-rating',
        props : [ 'restaurant', 'open' ],

        data() {
            return {
                values: [0,1,2,3,4,5,6,7,8,9,10],
                shared : App.state
            };
        },

        created(){
            App.event.on( 'detailsChanged', () => {
                this.$emit( 'closed' );
            });
        },

        methods : {
            numberClicked( rating ){
                if( this.open ) this.$emit( 'closed' );
                else this.$emit( 'opened' );
            },
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant__rating {
        background-color: #1b5b58;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        z-index: 4;

        &:after{
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            background-color: #1b5b58;
            width: 6em;
            height: 6em;
            border-radius: 50%;
            transform: translate(-25%, -50%);
        }
    }

    .restaurant_ratings {
        height: 100%;
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
        top: .2em;
        padding-right: .5em;
    }
</style>

