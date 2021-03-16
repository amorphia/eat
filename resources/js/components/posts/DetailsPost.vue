<template>
    <div class="post">

        <!-- note body -->
        <div class="post__body">
            {{ post.body }}
        </div>

        <!-- note actions -->
        <div class="post__actions d-flex">
            <!-- date -->
            <span class="grow-1">{{ date }}</span>

            <!-- edit post -->
            <button class="icon-edit post__actions-icon" title="Edit post" @click="editingPost = true"></button>

            <!-- delete post -->
            <button class="icon-trash" title="Delete post" @click="confirmDeletePost"></button>
        </div>

        <!-- edit post modal -->
        <modal-wrap
            :open="editingPost"
            @closed="editingPost = false"
            classes="width-50"
            focus=".should-focus">
            <vue-form
                id="edit-post"
                method="patch"
                :action="editPostAction"
                submitclass="width-100"
                @success="postEdited"
                :schema="editSchema"></vue-form>
        </modal-wrap>

    </div>
</template>


<script>
    export default {

        name: 'details-post',
        props: [ 'post' ],

        data() {
            return {
                shared : App.state,
                editingPost : false,
            };
        },

        computed : {

            // formatted post created at date
            date(){
                return App.date.format( this.post.created_at, "MMM D, YYYY - h:mma" );
            },

            // the form action url to pass when editing this post
            editPostAction(){
                return `/api/posts/${this.post.id}`;
            },

            // the form schema for the edit post modal form
            editSchema(){
                return [
                    { name: 'body', type: 'textarea', focus : true, value : this.post.body },
                ]
            }
        },

        methods : {

            /**
             * Confirm if we really want to delete this note
             */
            confirmDeletePost(){
                App.confirm( () => this.deletePost(),
                    { message: 'Are you sure you want to delete this note?' });
            },


            /**
             * Apply edited note data
             *
             * @param editedPost
             */
            postEdited( editedPost ){
                let restaurant = this.shared.getRestaurant( this.post.restaurant_id );
                if( !restaurant ) return;

                let post = restaurant.posts.find( obj => obj.id === +editedPost.id );
                post.body = editedPost.body;
                this.editingPost = false;
            },


            /**
             * Delete a note
             */
            deletePost(){
                App.ajax.delete( `/api/posts/${this.post.id}` ).then( response => {
                    // remove photo from restaurant photos array
                    let restaurant = this.shared.getRestaurant( this.post.restaurant_id );
                    if( !restaurant ) return;

                    let index = _.findIndex( restaurant.posts, [ 'id', this.post.id ] );
                    this.$delete( restaurant.posts, index );
                });
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .post {
        background-color: #415d5a7a;
        margin-top: 1.5rem;
        padding: 2rem;
        color: white;
    }

    .post__body {
        border-bottom: 1px solid var(--subtle-text);
        padding-bottom: 1rem;
        line-height: 1.35;

        &::first-letter {
            text-transform: uppercase;
        }
    }

    .post__actions {
        padding-top: .5rem;
        font-size: 1rem;
        color: var(--subtle-text);
    }

    .post__actions-icon {
        @include mobile {
            margin-left: 1rem;
        }
    }
</style>

