<template>
    <div class="restaurant-wrap"
         :class="{ selected : isSelected, visited : restaurant.visited }"
         v-touch:swipe.left="() => ratingOpen = true"
         v-touch:swipe.right="() => ratingOpen = false"
    >
        <div class="restaurant">

            <restaurant-interest :restaurant="restaurant"></restaurant-interest>

            <div class="restaurant__main" :id="`restaurant-${restaurant.id}`">

                <div v-if="restaurant.combined_rating" class="restaurant__combined-rating">
                    <span class="z-6">{{ restaurant.combined_rating }}</span>
                </div>

                <div v-if="shared.editMode"
                     class="restaurant__checkbox"
                     :class="{active : restaurant.checked}"
                     @click="$emit('checked')">
                        <i :class="checkIcon"></i>
                </div>
                <div class='pad-buffer restaurant__name' @click="openViewRestaurant">{{ restaurant.name }}</div>
                <restaurant-rating :restaurant="restaurant"
                                   :open="ratingOpen"
                                   @opened="ratingOpen = true"
                                   @closed="ratingOpen = false">
                </restaurant-rating>


            </div>

        </div>
    </div>
</template>


<script>
    export default {

        name: 'restaurant-item',
        props: [ 'restaurant', 'selected', 'index' ],
        data() {
            return {
                shared : App.state,
                ratingOpen : false,
            };
        },
        computed : {
            isSelected(){
                return this.selected
                    &&  this.selected.id === this.restaurant.id;
            },

            checkIcon(){
                return this.restaurant.checked ? 'icon-checkbox' : 'icon-checkbox_unchecked';
            }
        },
        methods : {
            openViewRestaurant(){
                App.event.emit( 'viewRestaurant', this.index, );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .restaurant-wrap {
        width: 33%;
        padding: .8rem;

        @include mobile {
            width: 100%;
            padding: .5rem;
        }
    }

    .restaurant {
        position: relative;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,.3);

        .restaurant__main {
            width: 100%;
            padding: 1.2rem .9rem 1.2rem 1.8rem;
            background-color: var(--primary-dark);
            background-image: linear-gradient(45deg, var(--primary-dark), #162e34);
            display: flex;
            color: white;
            position: relative;
            transition: background-color .4s;
            overflow:hidden;
            align-items: center;

            &.placeholder {
                box-shadow: none;
            }

            @include mobile {
                box-shadow: none;
                padding: 1.2rem .9rem 1.2rem 1.6rem;
            }
        }

        .restaurant__name {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 85%;
            z-index: 2;
            font-family: var(--secondary-font);
            letter-spacing: 1px;
            padding: .5rem 0;
            font-size: 1.8rem;
            cursor: pointer;
            color: white;

            @include mobile{
            }
        }

        .restaurant__combined-rating {
            height: 6rem;
            position: relative;
            background-color: var(--orange);
            margin: -2rem 1rem -2rem 0;
            right: 2rem;
            top: .25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0 0 1.5rem;
            font-size: 3.5rem;
            font-family: var(--accent-font);

            &:after {
                content: '';
                position: absolute;
                right: 0;
                top: 50%;
                background-color: var(--orange);
                width: 2em;
                height: 2em;
                border-radius: 50%;
                transform: translate(20%, -54%);
                z-index: 0;
            }
        }

        .restaurant__link {
            color: #FB9380;
            position: absolute;
            font-size: 1.2rem;
            padding: .5rem;
            left: 0;
            background-color: rgba(0,0,0,.1);
            height: 100%;
            top: 0;
            display: flex;
            align-items: center;

            @include mobile {
                padding: .5rem .75rem;
            }
        }

        .restaurant__checkbox {
            font-size: 1.5em;
            position: relative;
            right: .5rem;

            &.active {
                color: var(--highlight-color);
            }
        }

        &:after{
            content: attr( data-rank );
            color: rgba(26, 79, 69, .64);
            font-family: var(--accent-font);
            position: absolute;
            top: 50%;
            z-index: 1;
            transform: translateY(-50%);
            padding: 0;
            right: 0;
            font-size: 7rem;
            margin-top: .5rem;

            @include mobile{
            }
        }

        .restaurant-list__delete {
            color: rgba(0,0,0,.5);
            font-size: 1.5rem;
            z-index: 6;
        }

    }

    .restaurant-wrap:not(.active) .flash {
        animation: unseenflash 1.5s ease-out;
        animation-iteration-count: 1;
    }

    .restaurant-wrap.active .flash {
        animation: seenflash 1.5s ease-out;
        animation-iteration-count: 1;
    }


    .restaurant-list.unvisited .restaurant-wrap.visited {
        display: none;
    }

    .restaurant-list.visited .restaurant-wrap:not( .visited ) {
        display: none;
    }

    .restaurant__categories {
        background-color: var(--primary-darkest);
    }
</style>

