<template>
    <div class="restaurant-wrap" :class="{ selected : isSelected, visited : restaurant.visited }">
        <div class="restaurant">

            <restaurant-interest :restaurant="restaurant"></restaurant-interest>

            <div class="restaurant__main" :id="`restaurant-${restaurant.id}`">
                <div class='pad-buffer restaurant__name' @click="openViewRestaurant">{{ restaurant.name }}</div>
                <restaurant-rating
                    :rating="restaurant.rating"
                    :restaurant="restaurant"></restaurant-rating>
            </div>

        </div>
    </div>
</template>


<script>
    export default {

        name: 'restaurant-item',
        props: [ 'restaurant', 'selected' ],
        data() {
            return {
                shared : App.state
            };
        },
        computed : {
            isSelected(){
                return this.selected
                    &&  this.selected.id === this.restaurant.id;
            }
        },
        methods : {
            openViewRestaurant(){
                App.event.emit( 'viewRestaurant', this.restaurant );
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
                padding: 1.2rem .9rem 1.2rem 3.4rem;
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

