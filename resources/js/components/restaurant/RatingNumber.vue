<template>
    <div class="rating-number"
         :class="{active : active}"
         @click="updateRating"
    >{{ ratingText }}</div>
</template>


<script>
    export default {

        name: 'rating-number',
        props :[ 'value', 'open', 'restaurant' ],
        data() {
            return {
                shared : App.state
            };
        },
        computed : {
            // is this rating value the restaurant's current rating?
            active(){
                return this.value === this.restaurant.rating;
            },

            // if our rating is "0" replace the "0" with a dash "-"
            ratingText(){
                return this.value > 0 ? this.value : '-';
            },
        },
        methods : {

            /**
             * update the restaurant's rating
             */
            updateRating(){
                this.$emit( 'clicked' );

                // if we click the active rating, then do nothing
                if( !this.active ){
                    App.event.emit( 'updateRating', this.restaurant, { column : 'rating', value : this.value } );
                }
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant_ratings.open .rating-number {
        max-width: 100px;
        margin: 0 .1em;

        &.active {
            color: var(--highlight-color);
        }
    }

    .rating-number.active {
        margin: 0 .1em;
        max-width: 100px;
    }

    .rating-number {
        max-width: 0px;
        overflow: hidden;
        font-family: 'teko';
        font-size: 4em;
        cursor: pointer;

        @include tablet-up {
            transition: all .3s;

            &:hover{
                color: var(--highlight-color);
            }
        }
    }
</style>

