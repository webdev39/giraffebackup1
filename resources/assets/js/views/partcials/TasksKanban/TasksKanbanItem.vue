<template>
    <div class="drag-item"
         :class="{'drag-item__modal_show': kanbanItemModalShow.length}">
        <div
            class="drag-item-content"
            :style="{borderLeftColor: getPriorityColor }"
            @click="showTaskDetails(task, $event)"
        >

            <button class="kanban-task__button-move-task button__icon">
                <i class="icon-move">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-move">
                        </use>
                    </svg>
                </i>
            </button>
            <div class="kanban-task-content">
                <div class="checkbox-wrapper" style="display: inline-block;">
                    <input type="checkbox" v-model="getTaskIsCompleted" @click.stop>
                    <span class="checkbox checkbox_theme_blue" :style="{ 'background-color': `${getPriorityColor} !important` }"></span> 
                </div>
                <span :style="{'text-decoration': getTaskIsCompleted ? 'line-through': 'none'}">{{ task.name }}</span>
                <small v-if="debug">{{ task.sort_weight }}</small>
            </div>
            <div class="kanban-task-footer clearfix" >
                <div class="kanban-task-details">
                    <div class="kanban-task-details__list">
                        <task-item-warning-budget
                            class="kanban-task-details-timer"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                        />
                        <task-item-time-deadline
                            class="kanban-task-time-deadline"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                        />
                        <div class="kanban-task-details__item"
                            :title="$t('title_done_subtask')"
                             v-if="task.count.done_sub_task && task.count.open_sub_task"
                        >
                            <i class="fa fa-list-ul"></i>
                            {{task.count.done_sub_task}}/{{task.count.open_sub_task}}
                        </div>
                        <div class="kanban-task-details__item"
                            :title="$t('title_attachments')"
                             v-if="task.count.attachment"
                        >
                            <i class="fa fa-paperclip"></i>
                            {{task.count.attachment}}
                        </div>
                        <div class="kanban-task-details__item"
                            :title="$t('title_comments')"
                             v-if="task.count.comment"
                        >
                            <i class="fa fa-commenting-o"></i>
                            {{task.count.comment}}
                        </div>
                        <div class="kanban-task-details__item">
                            <task-unread-notification :task="task" class="kanban-task-unread-notification" />
                        </div>
                    </div>
                </div>
                <subscribers-task class="kanban-subscribers" :task="task" />
                <div class="control-btns-wrapper">
                    <div class="control-btns">
                        <task-item-timer :task="task" />
                        <task-item-subscribe
                            class="inline-blocked task-item-subscribe-kanban"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                            isModalOuter
                            selectorScroll=".drag-inner-list"
                        />
                        <task-item-deadline
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import permissionsMixin from '@mixins/permissions'

    import task from '@helpers/task'
    import TaskItemSubscribe from '@views/elements/TaskItemSubscribe/TaskItemSubscribe'
    import TaskItemDeadline from '@views/elements/TaskItemDeadline/TaskItemDeadline'
    import TaskItemTimer from '@views/elements/TaskItemTimer/TaskItemTimer'
    import TaskItemWarningBudget from '@views/elements/TaskItemWarningBudget/TaskItemWarningBudget'
    import TaskItemTimeDeadline from '@views/elements/TaskItemTimeDeadline/TaskItemTimeDeadline'
    import subscribersTask from '@views/components/subscribers/subscribers-task'
    import TaskUnreadNotification from '@views/partcials/TaskUndreadNotification/TaskUnreadNotification'

    export default {
        components: {
            subscribersTask,
            TaskUnreadNotification,
            TaskItemDeadline,
            TaskItemSubscribe,
            TaskItemTimer,
            TaskItemWarningBudget,
            TaskItemTimeDeadline,
        },
        props: {
            task: {
                type: Object
            }
        },
        data(){
            return {
                taskIsCompleted: this.task.done_by,
                kanbanItemModalShow: [],
            }
        },
        computed: {
            ...mapGetters({
                getMembers: 'members/getMembers',
                getPriorities: 'priorities/getPriorities',
                getUserId: 'user/getUserId',
            }),
            getMembersByTask() {

                let members = [];

                this.getMembers.map(member => {
                    this.task.subscribers.task.map(userTaskId => {
                        if (userTaskId === member.id) {
                            members.push(member);
                        }
                    });
                });

                return members;
            },
            getTaskIsCompleted: {
                get() {
                    return this.task.done_by;
                },
                set(complete) {
                    this.taskIsCompleted = complete;
                    this.changeTaskWorkflow(this.task.id);
                    return this.taskIsCompleted
                }
            },
            getPriorityColor () {
                return this.getPriorities.find(item => item.id === this.task.priority_id).color
            },
            doneSubtask(){

                let sum;

                sum = this.task.sub_tasks.filter(item => {
                    return item.is_completed
                });

                return sum.length;
            },
            groupId() {
                return Number(this.$route.params.group_id);
            },
        },
        methods: {
            onKanbanItemModalShow(eventItem) {
                this.kanbanItemModalShow.push(eventItem);
            },
            onKanbanItemModalHide(eventItem) {
                this.kanbanItemModalShow = this.kanbanItemModalShow.filter(item => item !== eventItem);
            },
            changeTaskWorkflow(taskId) {
                this.$api.task.changeWorkflowTask({
                    task_id: taskId,
                    is_done: this.taskIsCompleted
                }).then(() => {
                    if (this.$route.name === 'filter') {
                        this.$api.task
                        .getTasksByFilterId(this.$route.params.id)
                        .catch(err => {
                            this.$notify({type:'error', text: err.response.data.message});
                        })
                    }
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            showTaskDetails(task, event) {
                if (event.target.closest(".btn")) {
                    return;
                }
                if (task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', task.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                this.$router.replace({query: {taskId: task.id}});
            },
        }
    }
</script>

<style lang="scss">
    .kanban-task-time-deadline{
        .btn{
            background-color: transparent;
            padding: 2px 6px 2px 2px;
        }
        .task-time-deadline-drop-down{
            left: 0;
            right: auto;
        }
    }
    .kanban-task-details-timer{
        .btn{
            background-color: transparent;
            padding: 2px;
        }
        .task-time-status-drop-down{
            left: 0;
        }
    }

    .kanban-task__container-button-move{
        display: flex;
        justify-content: flex-end;
    }
    .kanban-task-content{
        padding-right: 30px;
    }
    .kanban-task__button-move-task {
        display: none;
    }
    .is_touch {
        .kanban-task__button-move-task{
            display: block;
            height: 30px;
            width: 24px;
            padding: 2px;
            right: 13px;
            position: absolute;
            top: 8px;
        }
    }

    .kanban-subscribers{
        float: right;
    }

    .kanban-tasks-list{
        .drag-item__modal_show{
            .drag-item-content{
                overflow: visible;
                .control-btns-wrapper {
                    bottom:0;
                    .control-btns {
                        .subscribers-btn {
                            top: 0;
                        }
                        .calendar-btn {
                            top: 0;
                        }
                    }
                }
            }
        }
        .drag-item-content{
            background:#fff;
            padding: 10px;
            border-left-width: 3px;
            border-left-style: solid;
            box-shadow: 0 0px 1px rgba(0,0,0,0.1), 0 2px 2px rgba(0,0,0,0.23);
            margin:0 4px 10px;
            border-radius:2px;
            overflow: hidden;
            cursor:pointer;
            position: relative;
            /*.checkbox-wrapper input{*/
                /*display: none;*/
            /*}*/
            &:hover {
                background: #f5f5f5;
                @media (min-width: 768px) {
                    
                    .control-btns-wrapper {
                        bottom:0px;
                        
                        .control-btns {
                            .subscribers-btn {
                                top: 0px;
                                
                            }
                            .calendar-btn {
                                top: 0px;
                                
                            }
                        }
                    }
                }    
            }
        }
        .sortable-ghost{
            .drag-item-content{
                opacity: 0.5;
                background: #c8ebfb;
            }
        }
    }

    .kanban-task-details{
        margin-right: auto;
    }

    .kanban-task-details__list{
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .kanban-task-details__item{
        margin-right: 6px;
        color: #909090;
        font-size: 14px;
        display: flex;
        align-items: center;
        .fa{
            margin-right: 2px;
        }
        i {
            width: 16px;
            height: 16px;
            display: flex;
                .icon {
                    fill:#909090;
                }
        }
    }

    .budget-over {
        .icon-dollar {
            .icon {
                fill:#f13539;
            }
        }
    }

    @-webkit-keyframes blinker {
        from {opacity: 1.0;}
        to {opacity: 0.0;}
        }
        .budget-over .icon-dollar{
        text-decoration: blink;
        -webkit-animation-name: blinker;
        -webkit-animation-duration: 0.6s;
        -webkit-animation-iteration-count:infinite;
        -webkit-animation-timing-function:ease-in-out;
        -webkit-animation-direction: alternate;
    }
    .kanban-task-footer{
        display: flex;
        align-items: center;
        position: relative;
            .control-btns-wrapper {
                position: absolute;
                bottom: -49px;
                right: -6px;
                z-index: 2;
                transition: all .2s;
                .control-btns {
                    display: flex;
                    position: relative;
                        .btn {
                            background: #ececec;
                            border: 4px solid #f5f5f5;
                            padding: 7px;
                                i .icon {
                                    width: 16px;
                                    height: 16px;
                                }
                        }
                        
                        .subscribers-btn { 
                            position: relative;
                            top:30px;
                            transition: all .3s;
                        }
                         .calendar-btn {
                            position: relative;
                            top:60px;
                            transition: all .4s;
                        }
                             .btn_details-modal_close {
                                position: absolute;
                                right: 0;
                                top: 0;
                                background-color: transparent;
                                border: none;
                                padding: 0;
                                padding-bottom: 0;
                                -webkit-box-shadow: none;
                                box-shadow: none;
                                .icon-close {
                                    // display: block;
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
                }
            }
            
    }
</style>
