<template>
    <div class="posts pos-relative width-60">
        <h2 class="posts__headline width-100 d-flex align-center">
            <span class="mr-5">Your Notes</span>
            <button class="action-button posts__add-button" title="Add Post" @click="openAddPost = true">
                <i class="icon-add_post"></i>
            </button>
        </h2>

        <div v-if="restaurant.posts.length" class="posts__container">
            <details-post v-for="post in restaurant.posts" :post="post" :key="post.id"></details-post>
        </div>
        <div v-else class="posts__empty subtle p-4 py-6">You haven't made any notes yet</div>

        <modal-wrap
            :open="openAddPost"
            @closed="openAddPost = false"
            classes="width-50"
            focus=".should-focus">
            <vue-form
                id="new-post"
                method="post"
                :action="postAction"
                submitclass="width-100"
                @success="postAdded"
                :schema="postSchema"></vue-form>
        </modal-wrap>

    </div>
</template>


<script>
    export default {

        name: 'details-posts',
        props: ['restaurant'],

        data() {
            return {
                shared : App.state,
                openAddPost : false,
                postAction : '/api/posts',
            };
        },


        methods : {
            postAdded( response ){
                console.log( response );
                this.openAddPost = false;

                let restaurant = this.shared.restaurants.find( obj => obj.id === this.restaurant.id );
                restaurant = restaurant ? restaurant : this.shared.forcedRestaurant;
                restaurant.posts.unshift( response );
            },
        },

        computed : {
            postSchema(){
                return [
                    { name: 'body', type: 'textarea', focus : true },
                    { name: 'restaurant_id', type: 'hidden', value : this.restaurant.id },
                ]
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .posts {
        padding: 0 4rem;
    }

    .posts__headline {
        border-bottom: 2px solid var(--primary-dark);
        color: var(--light-text);
        font-family: var(--secondary-font);
        letter-spacing: 1px;
        font-size: 1.4em;
        height: 3rem;
    }
</style>

