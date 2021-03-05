<template>
    <transition name="modalfade" mode="out-in">
        <div class="modal-wrap pos-fixed" v-if="open">
            <button @click="$emit('closed')" class="pos-absolute p-4 modal-wrap__close"><i class="icon-x"></i></button>
            <div class="modal-wrap__content pos-absolute-center" :class="classes" @click="clickedIn">
                <slot></slot>
            </div>
            <div class="modal-wrap__bg pos-cover" @click.self="$emit('closed')"></div>
        </div>
    </transition>
</template>


<script>
    export default {

        name: 'modal-wrap',
        props : ['classes', 'open', 'focus' ],
        data() {
            return {

            };
        },
        watch : {
            open( val ){
                if( val && this.focus ){
                    this.$nextTick( () => {
                        let el = document.querySelector( this.focus ).focus();
                    });
                }
            }
        },
        methods : {
            // if we click inside the modal, sniff for a close modal button, otherwise just report there was
            // a click, pass the event target and let the parent decide what to do with it (if anything)
            clickedIn( event ){

                if( event.target.getAttribute( 'data-close-modal' ) ){
                    this.$emit( 'modalClose' );
                    return false;
                }

                this.$emit( 'modalClickedIn', event.target );
            }
        }
    }
</script>


<style lang="scss">
    @import 'resources/sass/utilities/_mq.scss';

    .modal-wrap {
        top: 0; left: 0; bottom: 0; right: 0;
        z-index: 11000;
    }

    .modal-wrap__bg {
        z-index: -1;
        transition: opacity .2s;
        background-color: rgba( 15, 47, 52, .8 );
        backdrop-filter: blur(4px);
    }


    .modalfade-enter .modal-wrap__bg,
    .modalfade-leave-to .modal-wrap__bg {
        opacity: 0;
    }

    .modalfade-enter .modal-wrap__content,
    .modalfade-leave-to .modal-wrap__content {
        transform: translate3d( -50%, -55%, 0 );
    }

    .modal-wrap__content {
        max-width: calc(100vw - 7em);
        max-height: 100vh;
        overflow: auto;
        min-width: 150px;
        transition: all .3s ease-out;

        @include mobile {
            width: 90vw;
            max-width: 90vw;
        }
    }

    .modal-wrap__button {
        display: block;
        color: #fff;
        padding: 1rem;
        font-size: 1.25rem;
        text-transform: uppercase;
        box-shadow: 2px 2px 0 rgba(0,0,0,.2);
        font-family: var(--secondary-font);
    }

    .modal-wrap__close {
        top: 0;
        right: 0;
        font-size: 4rem;

        @include mobile {
            font-size: 3rem;
        }

    }

    .modal-wrap__link {
        text-align: center;
        margin-top: 1.5rem;
        display: block;
        color: var(--mid-grey);
        font-family: var(--primary-font);
    }

    /*
    .modal-wrap__content .icon-x {
        font-size: 4rem;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        top: -1em;
        right: 0;
    }
*/
</style>

