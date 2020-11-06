<template>
    <div class="kanban-column-controls">

        <theme-sidebar class="kanban__button-show-task" @click.native="showModal" :style="{'background-color': primaryColor}">

            <i class="icon-plus specialsize">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                    xlink:href="#icon-plus">
                    </use>
                </svg>
            </i>
            <span>{{ $t("add_task") }}</span>

        </theme-sidebar>

        <transition name="fade">

            <div
                v-if="show"
                class="kanban-modal-addtask"
            >
                <label for="task-name">Task name:</label>
                <input
                    id="task-name"
                    v-model="newTask"
                    type="text"
                    class="kanban-modal-addtask__input-new-task form-control"
                    @keyup.enter="addTask"
                >

                <div class="kanban-modal-list-button">
                     <theme-button-close class="btn btn-close-modal kanban-modal-addtask__button" @click.native="hideModal">
                        <i class="icon-close">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                        </i>
                    </theme-button-close>

                    <div class="control-btns-wrapper create-task-display-control-btns">

                        <div class="control-btns">

                            <button
                                :title="$t('task_setting')"
                                :disabled="!currentBoard"
                                class="btn"
                                type="button"
                                @click="_showDraftDetail"
                            >
                                <i class="icon-settings">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                    xlink:href="#icon-settings">
                                    </use>
                                </svg>
                                </i>
                            </button>

                            <button
                                :title="$t('title_subscribers')"
                                :disabled="!currentBoard"
                                type="button"
                                class="btn"
                                @click="_showModalSubscriber"
                            >
                                <i class="icon-user">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                    xlink:href="#icon-user">
                                    </use>
                                </svg>
                                </i>
                            </button>

                            <button
                                :title="$t('deadline')"
                                :disabled="!currentBoard"
                                type="button"
                                class="btn"
                                @click="_showModalDeadline"
                            >
                                <i class="icon-calendar">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-calendar">
                                        </use>
                                    </svg>
                                </i>
                            </button>

                            <theme-button-success class="button__theme_default button__size_s kanban-modal-addtask__button" @click.native="addTask">
                                {{ $t("ok") }}
                            </theme-button-success>

                            <modal-subscriber
                                v-if="showUser"
                                :task="_getCurrentDraftTask"
                                @hide="_hideModalSubscriber"
                            />

                            <modal-deadline
                                v-if="showDeadline"
                                :task="_getCurrentDraftTask"
                                :short="true"
                                @hide="_hideModalDeadline"
                            />

                        </div>

                    </div>

                </div>
            </div>

        </transition>

    </div>
</template>

<script>
    import {mapGetters}                     from 'vuex';

    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import createTaskMixin      from '@mixins/createTask'

    import ModalSubscriber      from '@views/partcials/TaskDetail/ModalSubscriber'
    import ModalDeadline        from '@views/partcials/TaskDetail/ModalDeadline'
    import ThemeSidebar         from '@views/layouts/theme/ThemeSidebar'

    export default {
        computed: {
            ...mapGetters({
                primaryColor: 'user/getPrimaryColor',
			}),
        },
        components: {
          ThemeButtonSuccess,
          ThemeButtonClose,
          ModalSubscriber,
          ModalDeadline,
          ThemeSidebar,
        },
        mixins:[
            createTaskMixin
        ],
        props: {
            priorityId:   {type: Number},
            currentBoard: {type:Object}
        },
        data() {
            return {
                show:   false,
                newTask: null,
            };
        },
        methods: {
            showModal(){
                this.show = true;
            },
            addTask(){
                let taskInput = this.newTask.trim();
                this.$emit('addTask', this.priorityId, taskInput);
                this.newTask = null;
            },
            hideModal(){
                this.newTask = null;
                this.show = false;
            }
        },
    }
</script>

<style lang="scss">
.kanban-column-controls {
    cursor:auto;
    .kanban__button-show-task {
        position:absolute;
        top:9px;
        right:10px;
        display:flex;
        align-items:center;
        border-style: none;
        overflow:hidden;
        border-radius: 25px;
        box-shadow:none;
        width:30px;
        height:30px;
        padding:0;
        -webkit-transition: width .3s;
        transition: width .3s;
        cursor: pointer;
        &:before {
        content: '';
            position: absolute;
            top: 0;
            left: 100px;
            width: 0;
            height: 100%;
            background-color: rgba(255,255,255,0.7);
            -webkit-transition: none;
            -moz-transition: none;
	        transition: none;
        }
            &:hover {
            width:100px;
            box-shadow:none;
                &:before {
                    left: 0;
                    width: 120%;
                    background-color: rgba(255,255,255,0);
                    -webkit-transition: all 0.4s ease-in-out;
                    -moz-transition: all 0.4s ease-in-out;
                    transition: all 0.4s ease-in-out;
                }

        }
            .icon-plus {
                position: relative;
                left: 1px;
                top:2px;
                .icon {
                    width:15px;
                    height:15px;
                    margin:0 2px 0 7px;
            }
        }

         span {
         white-space:nowrap;
         padding-left:6px;
     }
    }

    .kanban-modal-list-button {
        margin: 10px 0;
        .btn-close-modal {
                position:absolute;
                top:52px;
                right:-12px;
                border:none;
                background-color: transparent;
                box-shadow:none;
                padding: 6px 10px;
                display: -webkit-box;
                display: -ms-flexbox;
                display: flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                    .icon-close {
                    display:flex;
                        .icon {
                            width:10px;
                            height:10px;
                            //fill:#fff;
                        }
                }

                &:hover {
                    .icon-close {
                        .icon {
                            fill:#376aa7;
                        }
                }
            }
        }

        .control-btns-wrapper {
            justify-content: flex-start;
            padding-left:0;

            .control-btns {
                display:flex;
                justify-content: space-between;
                width: 100%;

                .btn {
                     &:hover {
                         i .icon {
                            fill: #376aa7;
                        }
                     }
                }
            }
        }
    }

    .kanban-modal-addtask{
        position: relative;
        background: #fff;
        margin: 0 -15px;
        padding: 25px 10px 5px 10px;
        box-shadow: 0 17px 10px -15px rgba(0, 0, 0, 0.4);

        &__button {
            border-radius: 3px;
            line-height: 16px;
            font-weight: normal;

            &:first-child {
                top: 0;
                margin-right: 10px;
            }
        }
    }
}

.create-task-display-control-btns {
    background:transparent;
}
</style>
