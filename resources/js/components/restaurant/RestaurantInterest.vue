<template>
    <i class="corner-tag icon-corner"
       :class="`corner-tag--${restaurant.interest}`"
       @click="updateInterest"
    >
    </i>
</template>


<script>
    export default {

        name: 'restaurant-interest',
        props: [ 'restaurant' ],

        data() {
            return {
                shared : App.state
            };
        },

        methods : {
            updateInterest(){
                let interest = this.restaurant.interest + 1;
                if( interest > 2 ) interest = 0;
                App.event.emit( 'updateRating', this.restaurant, { column : 'interest', value : interest } );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .corner-tag {
        position: absolute;
        top: 0;
        left: 0;
        color: #1e393a;
        font-size: 2em;
        z-index: 1;
        cursor: pointer;

        &.corner-tag--1 {
            color: var(--primary-light);
        }
        &.corner-tag--2 {
            color: orange;
        }
    }
</style>

