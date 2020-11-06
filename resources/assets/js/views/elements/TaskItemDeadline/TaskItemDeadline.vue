<template>
    <div class="task-item-deadline">
        <v-popover
            class="task-deadline"
            :open="showModal"
            style="position: relative;"
            placement="auto"
        >
                <div v-if="time" @click="toggleShowModalDeadline" class="task-item-deadline-text">
                    {{ time }}
                </div>
                <button v-else
                        type="button"
                        class="btn control-btns-hide"
                        :title="$t('todo')"
                        @click="toggleShowModalDeadline"
                >
                    <i class="icon-calendar">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-calendar">
                            </use>
                        </svg>
                    </i>
                </button>
                <template slot="popover" >
                    <div class="task-deadline-drop-down">
                        <modal-deadline
                                v-if="showModal"
                                @hide="hideModalDeadline"
                                :task="task"
                                :short="short"
                                :type="type"
                        />
                    </div>
                </template>
        </v-popover>
    </div>
</template>


<script>
    import ModalDeadline from '@views/partcials/TaskDetail/ModalDeadline'
    import clickOutside from 'v-click-outside'

    export default {
        name: 'taskItemDeadline',
        components: {
            ModalDeadline,
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        props: {
            task: {
                type: Object
            },
            short: {
                type: Boolean,
                default: true,
            },
            type: {
                type: String,
                default: 'planned_deadline'
            },
            time: {
                type: String,
            },
        },
        data() {
            return {
               showModal: false,
            }
        },
        methods: {
            hideModalDeadline() {
                this.showModal = false;
                this.$emit('onModalHide', 'taskItemDeadline');
            },
            showModalDeadline() {
                this.showModal = true;
                this.$emit('onModalShow', 'taskItemDeadline');
            },
            toggleShowModalDeadline() {
                if (this.showModal) {
                    return this.hideModalDeadline();
                }

                return this.showModalDeadline();
            },
        },
    }

</script>

<style lang="scss">
    .task-deadline-drop-down {
        .details-modal{
            top: 0;
            right: 0;
        }
    }
    .task-item-deadline-text{
        cursor: pointer;
    }
    .btn_details-modal_close {
        position: absolute;
        right: 0;
        top: 0;
        background-color: transparent;
        border: none;
        padding: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
        .icon-close {
            padding: 5px 10px;
            .icon {
                width: 11px;
                height: 11px;
                fill:#b2b2b2;
            }
        }
        &:hover {
            .icon-close {
                .icon {
                    fill: #62a8ea;
                }
            }
        }
    }
</style>

