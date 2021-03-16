<template>
    <div class="hours mb-2">
        <span class="hours__day">{{ day }}</span>
        <span class="hours__text">{{ hoursText }}</span>
    </div>
</template>


<script>
    export default {

        name: 'location-hours',
        props: ['hour'],

        data() {
            return {
                shared : App.state,
                dayOfWeekCodes : {
                    0 : 'Mon',
                    1 : 'Tue',
                    2 : 'Wed',
                    3 : 'Thu',
                    4 : 'Fri',
                    5 : 'Sat',
                    6 : 'Sun'
                }
            };
        },

        computed : {

            day(){
                return this.dayOfWeekCodes[this.hour.day];
            },

            hoursText(){
                return `${App.date.parseTime( this.hour.start )} - ${App.date.parseTime( this.hour.end )}`;
            },
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .hours__day {
        display: inline-block;
        width: 3rem;
        text-transform: lowercase;
        font-weight: bold;
    }
</style>

