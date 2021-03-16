<template>
    <div class="horizontal-scroll d-flex justify-center height-100">
        <button v-if="addButtons" class="flipper" @click="scroll( -1 )"><i class="icon-left"></i></button>
        <div class="width-100 horizontal-scroll d-flex overflow-auto" ref="container" :class="classes" @wheel="wheel">
        <slot></slot>
        </div>
        <button v-if="addButtons" class="flipper" @click="scroll( 1 )"><i class="icon-right"></i></button>
    </div>
</template>


<script>
    export default {

        name: 'horizontal-scroll',
        props : ['classes', 'buttons'],

        data() {
            return {
                isMounted: false,
                wheelScroll : 100,
                buttonScrollCards : 3
            };
        },

        mounted(){
            // flag us as mounted
            this.isMounted = true;

            // set our easing
            Math.easeInOutQuad = function (t, b, c, d) {
                t /= d/2;
                if (t < 1) return c/2*t*t + b;
                t--;
                return -c/2 * (t*(t-2) - 1) + b;
            };
        },

        methods : {
            /**
             * Is our content overflowing the container (and thus even possibly in need of scrolling)?
             * @returns {boolean}
             */
            contentOverflowing(){
                return this.$refs.container.scrollWidth > this.$refs.container.clientWidth;
            },

            /**
             * Handle mouse wheel scrolling
             *
             * @param e
             */
            wheel( e ){
                // if our content is overflowing then capture the event and don't apply the usual down page scroll
                if( this.contentOverflowing() ) e.preventDefault();

                // instead scroll our horizontal element left or right depending on our wheel direction
                if (e.deltaY > 0) this.$refs.container.scrollLeft += this.wheelScroll;
                else this.$refs.container.scrollLeft -= this.wheelScroll;
            },


            /**
             * Reset our container scroll to the left most edge
             */
            resetScroll(){
                this.$refs.container.scrollLeft = 0;
            },


            /**
             * manually scroll a fixed distance left or right
             *
             * @param sign
             */
            scroll( sign ){
                let buttonScroll = this.$refs.container.firstChild.clientWidth * this.buttonScrollCards;
                this.scrollTo( this.$refs.container, sign * buttonScroll );
            },


            /**
             * Scroll an element horizontally
             *
             * @param element - the element to scroll
             * @param shift - how far to scroll in px
             * @param duration - how long to animate the scroll
             */
            scrollTo( element, shift, duration = 250 ) {
                let start = element.scrollLeft,
                    change = shift,
                    currentTime = 0,
                    increment = 20;

                let animateScroll = function(){
                    currentTime += increment;
                    let val = Math.easeInOutQuad( currentTime, start, change, duration );
                    element.scrollLeft = val;
                    if(currentTime < duration) {
                        setTimeout( animateScroll, increment );
                    }
                };

                animateScroll();
            },

        },


    computed : {
            // should we add manual scroll buttons?
            addButtons(){
                if (!this.isMounted ) return;
                return this.buttons && this.$refs.container.scrollWidth > this.$refs.container.clientWidth;
            },
        }
    }
</script>


<style>
    .horizontal-scroll {
        max-width: 100%;
        scroll-behavior: smooth;
    }
</style>

