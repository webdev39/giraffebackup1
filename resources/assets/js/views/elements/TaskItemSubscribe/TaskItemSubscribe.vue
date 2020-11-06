<template>
    <div class="task-item-subscribe">
        <button
            :title="$t('title_subscribers')"
            type="button"
            class="btn control-btns-hide"
            @click="toggleShowModalSubscribe"
        >
            <i class="icon-user">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-user">
                    </use>
                </svg>
            </i>
        </button>

        <template v-if="isModalOuter">
            <modal-subscriber
                    v-if="showModal"
                    class="modal-subscriber__size_m"
                    :task="task"
                    @hide="hideModalSubscribe"
                    ref="modalSubscriber"
                    :style="{
                        left: getLeftContainer,
                        top: getTopContainer,
                        position: 'fixed',
                     }"
            />
        </template>
        <template v-else>
            <modal-subscriber
                    @hide="hideModalSubscribe"
                    v-if="showModal"
                    :task="task"
            />
        </template>
    </div>
</template>

<script>
    import ModalSubscriber from '@views/partcials/TaskDetail/ModalSubscriber'
    import clickOutside from 'v-click-outside'

    export default {
        name: 'taskItemSubscribe',
        components: {
            ModalSubscriber,
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        props: {
            task: {
                type: Object
            },
            isModalOuter: {
                type: Boolean,
                default: false,
            },
            selectorScroll: {
                type: String,
            },
        },
        computed: {
            getLeftContainer() {
                const commonWidthModal = this.clientRect.left + this.modalWidth;

                if (commonWidthModal > window.innerWidth) {
                    return this.clientRect.left + 35 - this.modalWidth + 'px';
                } else {
                    return this.clientRect.left + 5 + 'px';
                    
                }
            },
            getTopContainer() {
                const commonHeightModal = this.clientRect.top + this.modalHeight;

                if (commonHeightModal > window.innerHeight) {
                    return this.clientRect.top - this.modalHeight + 'px';
                } else {
                    return this.clientRect.top + 40 + 'px';
                }
            }
        },
        data() {
            return {
                showModal: false,
                modalHeight: 0,
                modalWidth: 0,
                clientRect: {},
                containerScroll: null,
                scrollTopY: null,
            }
        },
        beforeDestroy() {
            this.removeEvent();
        },
        mounted() {
            if (this.selectorScroll) {
                this.containerScroll = this.$el.closest(this.selectorScroll);
            };
        },
        methods: {
            setScrollTo() {
                this.containerScroll.scrollTo(0, this.scrollTopY);
            },
            addEvent() {
                if (this.containerScroll) {
                    this.scrollTopY = this.containerScroll.scrollTop;
                    this.containerScroll.addEventListener('scroll', this.setScrollTo);
                }
                window.addEventListener('resize', this.setModalPosition);
            },
            removeEvent() {
                if (this.containerScroll) {
                    this.containerScroll.removeEventListener('scroll', this.setScrollTo);
                }

                window.removeEventListener('resize', this.setModalPosition);
            },
            setModalPosition() {
                this.clientRect = this.$el.getBoundingClientRect();
            },
            hideModalSubscribe() {
                this.showModal = false;
                if (this.isModalOuter) {
                    this.removeEvent();
                }
                this.$emit('onModalHide', 'taskItemSubscribe');
            },
            showModalSubscribe() {
                this.showModal = true;

                if (this.isModalOuter) {
                    setTimeout(() => {
                      this.setModalPosition();
                      this.modalHeight = this.$refs['modalSubscriber'].$el.clientHeight;
                      this.modalWidth = this.$refs['modalSubscriber'].$el.clientWidth;
                    }, 10);
                    this.setModalPosition();
                    this.addEvent();
                }
                this.$emit('onModalShow', 'taskItemSubscribe');
            },
            toggleShowModalSubscribe() {
              if (this.showModal) {
                return this.hideModalSubscribe();
              }
              return this.showModalSubscribe();
            },
        },
    }

</script>

<style lang="scss">
    .modal-subscriber__size_m{
        width: 500px;
    }
</style>

