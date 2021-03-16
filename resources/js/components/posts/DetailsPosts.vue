<template>
    <div class="posts pos-relative width-60">

        <!-- header -->
        <h2 class="posts__headline width-100 d-flex align-center">
            <span class="mr-5">Your Notes</span>

            <!-- add note button -->
            <button class="action-button posts__add-button" title="Add Post" @click="openAddPost = true">
                <i class="icon-add_post"></i>
            </button>
        </h2>

        <!-- display notes -->
        <div v-if="restaurant.posts && restaurant.posts.length" class="posts__container">
            <details-post v-for="post in restaurant.posts" :post="post" :key="post.id"></details-post>
        </div>
        <div v-else class="posts__empty subtle p-4 py-6">You haven't made any notes yet</div>

        <!-- add note modal -->
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
            /**
             * process a newly added note
             *
             * @param response - ajax response
             */
            postAdded( response ){
                // close add post modal
                this.openAddPost = false;

                // get our restaurant from the shared array
                let restaurant = this.shared.getRestaurant( this.restaurant.id );

                // add the new note to the top of the restaurants notes list
                if( restaurant ) restaurant.posts.unshift( response );
            },
        },

        computed : {
            /**
             * The form schema for the add a post modal form
             *
             * @returns {*[]}
             */
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

        @include mobile {
            padding: 1rem 1.5rem 4rem 1.5rem;
            order: 2;
            margin-bottom: 4rem;
        }
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

