<template>
    <div class="task-list-item" :class="{'task-list-item__monthly': task.budget_type_id === 3}"  v-if="task.id">
        <div class="task-title-wrapper">
            <div class="task-progress" :class="{'task-progress_single': !progressTimeUsed || !progressSubtask}" v-if="progressTimeUsed || progressSubtask">
                <div class="task-progress__item task-progress__item_subtask" v-if="progressSubtask" :style="progressSubtask"></div>
                <div class="task-progress__item task-progress__item_timeused" v-if="progressTimeUsed && checkPermission('time-tracking')" :style="progressTimeUsed" ></div>
            </div>
            <div class="task-priority-icon pull-left">
                <task-unread-notification :task="task" class="task-list-unread-notification" />
                <input type="checkbox"
                       class="form-check-input done-task-checkbox"
                       v-model="getTaskIsCompleted"
                       :title="getCheckboxTitle"
                       :disabled="isLoading"
                >
                <span class="checkmark" :style="{ 'background-color': `${getPriorityColor} !important` }" ></span>
                <span class="checkmark-border"></span>
            </div>
            <div class="task-name" @click="showTaskDetails(task)">
                <div class="task-name-title">
                    {{task.name}}
                    <small v-if="debug">{{task.sort_weight}}</small>
                </div>
                <div v-if="this.$route.name === 'deadline' || this.$route.name === 'filter'" class="groupAndBoardFooter pull-left secColor">
                    {{getCurrentGroup.name}} - {{ getCurrentBoard.name}}
                </div>
            </div>
        </div>
        <div class="task-list-item__controls_mobile">
            <button class="task-list-item__button_mobile button__icon">
                <i class="icon-move">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-move">
                        </use>
                    </svg>
                </i>
            </button>

            <button class="task-list-item__button_mobile button__icon" @click="handleToggleControls">
                <i class="icon-dots-vertical">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-vertical-dots">
                        </use>
                    </svg>
                </i>
            </button>
        </div>

        <div class="control-btns-wrapper" :class="{ 'show-control-btns': showControlBtns.length}" v-if="getShowControls">
            <div class="control-btns">
                <!--<div class="control-btns__left">-->
                    <button
                        type="button"
                        class="btn control-btns-hide"
                        :title="$t('task_setting')"
                        @click="showTaskDetails(task)"
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
                        v-show="canDeleteTask"
                        :title="$t('title_remove_task')"
                        :disabled="isLoading"
                        type="button"
                        class="btn btn-fix-padding-icon control-btns-hide"
                        @click="removeTask"
                        >
                        <i class="icon-trash">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                xlink:href="#icon-trash">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <task-item-subscribe
                            class="inline-blocked task-list-item-subscribe"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                    />
                    <task-item-deadline
                            style="position: relative;"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                    />
                    <task-item-timer
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                    />
                    <task-item-warning-budget
                            class="task-list-item-warning-budget"
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                    />
                    <task-item-time-deadline
                            :task="task"
                            @onModalHide="onKanbanItemModalHide"
                            @onModalShow="onKanbanItemModalShow"
                    />

                    <subscribers-task class="task-subscribers" :task="task" />
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters }               from 'vuex'
    import clickOutside                 from 'v-click-outside'

    import config                       from '@config'

    import find                         from '@helpers/findInGroups'

    import helpersTime                  from '@mixins/time'
    import permissionsMixin             from '@mixins/permissions'

    import TaskItemSubscribe from '@views/elements/TaskItemSubscribe/TaskItemSubscribe'
    import TaskItemDeadline from '@views/elements/TaskItemDeadline/TaskItemDeadline'
    import TaskItemTimer from '@views/elements/TaskItemTimer/TaskItemTimer'
    import TaskItemWarningBudget from '@views/elements/TaskItemWarningBudget/TaskItemWarningBudget'
    import TaskItemTimeDeadline from '@views/elements/TaskItemTimeDeadline/TaskItemTimeDeadline'
    import SubscribersTask              from '@views/components/subscribers/subscribers-task'
    import TaskUnreadNotification       from '@views/partcials/TaskUndreadNotification/TaskUnreadNotification'


    export default {
        name: "task-list-item",
        props: {
            task: {
                type: Object
            },
            listlimit: {
                type: Number,
                default: 5,
            },
        },
        data() {
            return {
                showUser: false,
                reachSoftBudget: false,
                reachHardBudget: false,
                deadlineDays: null,
                taskTimeStatusDropDown: false,
                taskTimeDeadlineDropDown: false,
                showControls: false,
                showControlBtns: [],
                windowWidth: window.innerWidth,
                htmlEl: document.getElementsByTagName('html')[0],
            }
        },
        computed: {
            ...mapGetters({
                getGroups:              'groups/getStateGroups',
                getPriorities:          'priorities/getPriorities',
                getCurrentStartTimer:   'timers/getCurrentStartTimer',
                getUserId:              'user/getUserId',
            }),
            isQuickNavigation() {
                return this.showControls = this.getCurrentBoard.quick_nav;
            },
            getShowControls () {
                this.isQuickNavigation;

                if (this.windowWidth < config.size.tablet || this.htmlEl.className === 'is_touch') {
                    return this.showControls;
                }

                return true;
            },
            getCurrentTaskTrackedTime() {
                return this.getTrackedTimeSecondsByTask(this.task);
            },
            getCurrentTaskSoftBudget () {
                return this.getBudgetSeconds(this.task.soft_budget);
            },
            getCurrentTaskHardBudget () {
                return this.getBudgetSeconds(this.task.hard_budget);
            },
            // showControlBtns() {
            //     return this.showUser || this.taskTimeStatusDropDown || this.taskTimeDeadlineDropDown
            // },
            progressSubtask: function () {
                if (this.task.count.done_sub_task) {
                    let styleObj = {
                        width: null
                    };

                    styleObj.width = ( this.task.count.done_sub_task * 100 / (this.task.count.done_sub_task + this.task.count.open_sub_task) ) + '%';

                    return styleObj
                }
            },
            progressTimeUsed: function () {
                if (this.getCurrentTaskHardBudget && this.getCurrentTaskHardBudget) {
                    let styleObj = {
                        backgroundColor: null
                    };

                    styleObj.width = ( this.getCurrentTaskTrackedTime * 100 / this.getCurrentTaskHardBudget ) + '%';

                    return styleObj
                }
            },
            getPriority() {
                if (this.task) {
                    return this.getPriorities.find(item => item.id === this.task.priority_id);
                }
            },
            getPriorityColor () {
                return this.getPriority.color
            },
            getCheckboxTitle() {
                if (this.getTaskIsCompleted) {
                    return `Click to reopen Task. Label: ${this.getPriorityName}`
                }

                return `Click to finish Task. Label: ${this.getPriorityName}`
            },
            getPriorityName () {
                return this.getPriority.name;
            },
            canDeleteTask() {
                if (this.task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('delete-task', this.task.group_id)) {
                    return false
                }

                return true;
            },
            getCurrentBoard() {
                if (this.getGroups) {
                    return find.searchBoardById(this.getGroups, this.task.board_id);
                }
            },
            getCurrentGroup() {
                if (this.getGroups) {
                    return find.searchGroupById(this.getGroups, this.task.group_id);
                }
            },
            getTaskIsCompleted: {
                get: function () {
                    return this.task.done_by;
                },
                set: function (complete) {
                    return this.changeTaskWorkflow();
                }
            },
        },
        components: {
            TaskItemDeadline,
            TaskItemSubscribe,
            TaskItemTimer,
            TaskItemWarningBudget,
            SubscribersTask,
            TaskUnreadNotification,
            TaskItemTimeDeadline,
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        mixins: [
            permissionsMixin,
            helpersTime
        ],
        filters: {
            hours: function (value) {

                let h = value / 60 | 0;

                if (h < 10) {
                    h = '0' + h;
                }

                let  m = value % 60 | 0;

                if (m < 10) {
                    m = '0' + m;
                }

                return `${h}:${m}`
            },
        },
        created() {
            this.$event.$on('update-window-screen-width', this.handleWidth);
        },
        beforeDestroy() {
            this.$event.$off('update-window-screen-width', this.handleWidth);
        },
        methods: {
            onKanbanItemModalShow(eventItem) {
                this.showControlBtns.push(eventItem);
            },
            onKanbanItemModalHide(eventItem) {
                this.showControlBtns = this.showControlBtns.filter(item => item !== eventItem);
            },
            handleWidth(width) {
                this.windowWidth = width;
            },
            handleToggleControls() {
                if (this.windowWidth < config.size.tablet) {
                    this.showControls = !this.showControls;
                }
            },
            changeTaskWorkflow() {
                this.$api.task.changeWorkflowTask({task_id: this.task.id, is_done: !this.task.done_by}).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handleToggleDropDownTimeStatus(){
                this.taskTimeStatusDropDown = !this.taskTimeStatusDropDown;
            },
            handleToggleDropDownDeadline(){
                this.taskTimeDeadlineDropDown = !this.taskTimeDeadlineDropDown;
            },

            showTaskDetails(task) {
                if (task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', task.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                this.$router.replace({query: {taskId: task.id}});
            },
            removeTask() {
                if (this.task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('delete-task', this.task.group_id)) {
                    return this.sendNotifyPermissionInfo('delete-task');
                }

                this.$modal.show("confirm-modal", {
                    title: 'Delete this Task',
                    body: 'Are you sure you want to delete this task?',
                    confirmCallback: () => {
                        this.$api.task.removeTask(this.task)
							.then(() => {
								if (this.$route.name === 'filter') {
									this.$api.task.getTasksByFilterId(this.$route.params.id).catch(err => {
										this.$notify({type:'error', text: err.response.data.message});
									})
								}
							})
                            .catch(err => {
                                this.$notify({type:'info', text: err.response.data.message});

                                this.$modal.show("confirm-modal", {
                                    title: 'Update status a Task',
                                    body: 'You cannot delete this task. Do you want to change the status to \'done\'?',
                                    confirmCallback: () => {
                                        this.$api.task.changeWorkflowTask({task_id: this.task.id, is_done: !this.task.done_by}).catch(err => {
                                            this.$notify({type:'info', text: err.response.data.message});
                                        })
                                    },
                                });
                            })
                    },
                });
            },
        },
    }
</script>

<style lang="scss">
    .task-title-wrapper{
        @media (max-width: 991px) {
            position: relative;
        }
    }
    .task-list-item{
        position: relative;
    }
    .task-progress__item{
        height: 50%;
    }
    .task-progress{
        position: absolute;
        width: 100%;
        height: 100%;
        padding-left: 65px;
        overflow: hidden;
    }
    .task-progress_single {
        .task-progress__item{
            height: 100%;
        }
    }

    .task-progress__item_subtask{
        background-color: #edf1fe;
    }
    .task-progress__item_timeused{
        background-color: #fdeded;
    }
    .task-list-item__controls_mobile,
    .task-list-item__button-move-task {
        display: none;
    }
    .is_touch {
        .task-list-item__button_mobile{
            display: block;
            width: 20px;
            padding: 2px;
            height: 100%;
        }
        .task-list-item__controls_mobile {
            position: absolute;
            display: flex;
            align-items: center;
            right: 0;
            height: 52px;
            top: 0;
        }
    }
    .task-list-unread-notification{
        position: absolute;
        z-index: 1;
        top: 4px;
        left: 10px;
    }
    .task-list-item-warning-budget{
        .budget-warning .icon-dollar .icon{
            fill: #b2b2b2;
        }
    }
    .task-list-item-subscribe{
        position: relative;
        .modal-subscriber{
            width: 500px;
            left: auto;
        }
    }
    .task-list-item {
        .show-control-btns{
            .btn{
                visibility: visible;
            }
        }
    }
</style>
