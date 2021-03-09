<template>
    <div class="tour">
        <v-tour name="myTour" :steps="steps" :callbacks="callbacks"></v-tour>
    </div>
</template>

<script>
    export default {
        name: 'my-tour',
        data () {
            return {
                callbacks: {
                    onStart: this.onStartCallback,
                    onNextStep: this.nextStepCallback
                },
                steps: [
                    {
                        target: '.corner-tag',
                        header: {
                            title: 'Show Your Interest'
                        },
                        content: `
                        <p>Click this tag in the corner to flag it as somewhere you'd like to visit soon.</p>
                        <ul>
                            <li>One click flags it as <span style="color:var(--primary-light)">interested</span>.</li>
                            <li>A second flags it as <span style="color:var(--highlight-color)">very interested</span>.</li>
                            <li>A third starts you over</li>
                        </ul>
                        `,
                        params: {
                            enableScrolling: false
                        }
                    },
                    {
                        target: '.restaurant__rating',
                        header: {
                            title: 'Rate Restaurants'
                        },
                        content: `
                            Click here (or swipe left on mobile) to rate a restaurant on a scale of 1-10.
                        `,
                        params: {
                            enableScrolling: false,
                            placement: 'left'
                        }
                    },
                    {
                        target: '#sort',
                        header: {
                            title: 'Sort Restaurants'
                        },
                        content: `
                            <p>Want to find the places closest to you? Just sort by distance!</p>
                            <p>Use this dropdown to sort the restaurants however you like, by default it sorts by interest, then rating, then alphabetically.</p>

                        `,
                        params: {
                            enableScrolling: false
                        }
                    },
                    {
                        target: '#filter',
                        header: {
                            title: 'Filter Restaurants'
                        },
                        content: `
                           <p>Want to see only the restaurants you haven't rated (and presumably have never been to)?</p>
                           <p>Use this dropdown to filter the restaurants by rated, or unrated, interested, or unseen.</p>
                          `,
                        params: {
                            enableScrolling: false
                        }

                    },
                    {
                        target: '#category',
                        header: {
                            title: 'Choose a category'
                        },
                        content: `
                            <p>Got an itch for tacos? Use this dropdown to filter the restaurants by category.</p>
                        `,
                        params: {
                            enableScrolling: false
                        }
                    },
                    {
                        target: '#match',
                        header: {
                            title: 'Check out other users'
                        },
                        content: `
                            <p>You can view other user's ratings or interests using this dropdown. Even better, you can combine your scores together to figure out what places you are mutually interested in</p>
                        `,
                        params: {
                            enableScrolling: false
                        }
                    },
                    {
                        target: '.restaurant__name',
                        header: {
                            title: 'View a restaurant details'
                        },
                        content: `
                            <p>Click on a restaurant's name to view photos, locations, and leave yourself notes</p>
                        `,
                        params: {
                            enableScrolling: false
                        }
                    },
                ]
            }
        },
        methods: {
            onStartCallback () {
                this.$nextTick( () => {
                    console.log( 'scroll top' );
                    window.scrollTo({ top: 10, behavior: 'smooth' });
                });
            },
            nextStepCallback (currentStep) {
                this.$nextTick( () => {
                    let scroll = currentStep % 2 ? -1 : 1;
                    window.scrollTo({ top: scroll + 10, behavior: 'smooth' });
                });
            }
        }
    }
</script>

<style>

    .tour {
        max-width: 100vw;
        overflow: hidden;
    }

    .tour p, .tour li {
        margin-bottom: .65rem;
    }

    .tour p:last-child, .tour li:last-child  {
        margin-bottom: 0;
    }

</style>
