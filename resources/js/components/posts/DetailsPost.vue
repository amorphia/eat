<template>
    <div class="post">
        <div class="post__body">
            {{ post.body }}
        </div>
        <div class="post__actions d-flex">
            <span class="grow-1">{{ date }}</span>
            <button class="icon-edit" title="Edit post" @click="editingPost = true"></button>
            <button class="icon-trash" title="Delete post" @click="confirmDeletePost"></button>
        </div>

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
            date(){
                return App.date.format( this.post.created_at, "MMM D, YYYY - h:mma" );
            },

            editPostAction(){
                return `/api/posts/${this.post.id}`;
            },

            editSchema(){
                return [
                    { name: 'body', type: 'textarea', focus : true, value : this.post.body },
                ]
            }
        },

        methods : {
            confirmDeletePost(){
                App.confirm( () => this.deletePost(),
                    { message: 'Are you sure you want to delete this note?' });
            },


            postEdited( editedPost ){
                let restaurant = this.getRestaurant();
                let post = restaurant.posts.find( obj => obj.id === +editedPost.id );
                post.body = editedPost.body;
                this.editingPost = false;
            },


            getRestaurant( id ){
                let restaurant =  this.shared.restaurants.find( obj => obj.id === +this.post.restaurant_id );
                return restaurant;
            },


            deletePost(){
                let restaurant = this.getRestaurant();
                App.ajax.delete( `/api/posts/${this.post.id}` ).then( response => {
                    // remove photo from restaurant photos array
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
</style>

