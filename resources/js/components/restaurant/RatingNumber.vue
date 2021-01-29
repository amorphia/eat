<template>
    <div class="rating-number"
         :class="{active : active}"
         @click="updateRating"
    >{{ ratingText }}</div>
</template>


<script>
    export default {

        name: 'rating-number',
        props :[ 'value', 'rating', 'open', 'restaurant' ],
        data() {
            return {
                shared : App.state
            };
        },
        computed : {
            active(){
                return this.value === this.rating;
            },

            ratingText(){
                return this.value > 0 ? this.value : '-';
            },
        },
        methods : {
            updateRating(){
                this.$emit( 'clicked' );
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
        transition: all .3s;
        font-family: 'teko';
        font-size: 4em;
        cursor: pointer;

        &:hover{
            color: var(--highlight-color);
        }
    }
</style>

