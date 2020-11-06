<template>
    <div
        data-v-step="task_0"
        class="task-create-holder"
    >
        <div class="row task-create"
             v-click-outside="handleClickOutsideHideSetting"
        >
            <task-create-quill
                v-if="controls.includes('task_add')"
                :users="users"
                :placeholder="getTaskPlaceholder"
                ref="quill"
                @send="handleSendInput"
                @click-input="handleChangeVisibilitySettings(true)"
                @set-task-name="handleSetTaskName"
            />

            <div class="col-xs-12 col-md-6 control-btns-wrapper create-task-display-control-btns" v-if="getShowSetting">
                <div class="control-btns">
                    <button
                        v-if="controls.includes('task_setting')"
                        :title="$t('task_setting')"
                        :disabled="!getCurrentBoard"
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
                        v-if="controls.includes('title_subscribers')"
                        :title="$t('title_subscribers')"
                        :disabled="!getCurrentBoard"
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
                        v-if="controls.includes('deadline')"
                        :title="$t('deadline')"
                        :disabled="!getCurrentBoard"
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

                    <button
                        v-if="controls.includes('choose_boards')"
                        :title="$t('choose_boards')"
                        id="choose-board"
                        type="button"
                        class="btn"
                        @click="showChooseBoard"
                    >
                        <i class="icon-document">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-document">
                                </use>
                            </svg>
                        </i>
                    </button>

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

</template>

<script>
    import { mapGetters }               from 'vuex'
    import clickOutside                 from 'v-click-outside'
    import config                       from '@config'

    import helpersTask                  from '@helpers/task'

    import TaskCreateQuill              from '@views/components/task/TaskCreateQuill'

    import ModalSubscriber              from '@views/partcials/TaskDetail/ModalSubscriber'
    import ModalDeadline                from '@views/partcials/TaskDetail/ModalDeadline'
    import find                         from '@helpers/findInGroups'
    import createTaskMixin              from '@mixins/createTask'

    export default {
        name: 'task-create',
        props: {
            redirect: {
                type: Boolean,
                default: true
            },
			taskInfo: {
            	type: Object,
                default: () => {
                	return {
						name: null
					}
                }
            },
			controls: {
            	type: Array,
                default: () => {
                	return ["task_add", "task_setting", "title_subscribers", "deadline", "choose_boards"]
                }
            }
        },
        data() {
            return {
                form: {
                    name:               null,
                    planned_deadline:   null,
                    mentions:           [],
                },
                board: {
                    id:         Number(this.$route.params.board_id),
                    group_id:   Number(this.$route.params.group_id)
                },
                showUser:       false,
                showDeadline:   false,
                showSetting:    false,
                windowWidth:    window.innerWidth
            }
        },
        mixins:[
            createTaskMixin
        ],
        computed: {
            ...mapGetters({
                getGroups:              'groups/getStateGroups',
                getCurrentDraftTask:    'groups/getCurrentDraftTask',
                getMembers:             'members/getMembers',
				getCurrentTour:         'getCurrentTour',
			}),
            users() {
                if (!this.getCurrentGroupId) {
                    return this.getMembers.map(member => member.user)
                }

                let group = find.searchGroupById(this.getGroups, this.getCurrentGroupId);

                if (group.members) {
                    return find.searchMembersInGroups(group, this.getMembers).map(member => member.user);
                }

                return [];
            },
            getShowSetting () {
                if (this.windowWidth < config.size.tablet) {
                    return this.showSetting;
                }

                return true;
            },
            getDraftTaskId() {
                if (this.getCurrentDraftTask) {
                    return this.getCurrentDraftTask.id
                }

                return null
            },
            getDraftTaskName: {
                get: function () {
                    if (this.getCurrentDraftTask && this.form.name === null) {
                        this.form.name = this.getCurrentDraftTask.name;
                    }

                    return this.form.name;
                },
                set: function (value) {
                    this.form.name = value;
                }
            },
            /*Board*/
            getCurrentBoardName() {
                if (this.getCurrentBoard) {
                    return this.getCurrentBoard.name
                }
            },
            getCurrentBoardId() {
                if (this.getCurrentBoard) {
                    return this.getCurrentBoard.id;
                }
            },
            getCurrentBoard() {
                if (this.getGroups) {
                    return find.searchBoardById(this.getGroups, Number(this.$route.params.board_id) || this.board.id );
                }
            },
            /*Group*/
            getCurrentGroupId() {
                if (this.getCurrentGroup) {
                    return this.getCurrentGroup.id;
                }
            },
            getCurrentGroupName() {
                if (this.getCurrentGroup) {
                    return this.getCurrentGroup.name
                }
            },
            getCurrentGroup() {
                if (this.getGroups) {
                    return find.searchGroupById(this.getGroups, Number(this.$route.params.group_id) || this.board.group_id);
                }
            },
            getPermissionCreateTask () {
                if (this.getCurrentBoard) {
                    return this.handlePermissionByBoardId('create-task', this.getCurrentBoard.id)
                }

                return false;
            },
            getTaskPlaceholder () {
                const taskPlaceholders = {
                    forName: this.$t('new_task_for'),
                    week: this.$t('new_task_for_week'),
                    day: this.$t('new_task_for_day'),
                    default: this.$t('new_task_title_here'),
                };
                const boardPeriod = this.$route.params.period;

                if (this.getBoardNameOrTaskName) {
                    return taskPlaceholders.forName + ' ' + this.getBoardNameOrTaskName;
                }

                if (boardPeriod) {
                    return taskPlaceholders[boardPeriod];
                }

                return taskPlaceholders.default;
            },
            getBoardNameOrTaskName() {
                return this.getCurrentBoardName || this.getDraftTaskName;
            }
        },
        components: {
            ModalDeadline,
            ModalSubscriber,
            TaskCreateQuill
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        watch: {
            '$route'(to, from) {
                this.getDraftTask();
            },
        },
        created() {
            this.$event.$on('update-window-screen-width', this.handleWidth);
        },
        mounted() {
            this.getDraftTask();
        },
        beforeDestroy() {
            this.$event.$off('update-window-screen-width', this.handleWidth);
            this.$store.dispatch('groups/changeTask', Object.assign({}, this.getCurrentDraftTask, this.form));
        },
        methods: {
            handleWidth(width) {
                this.windowWidth = width;
            },
            handleChangeVisibilitySettings(status) {
                if (this.windowWidth < config.size.tablet) {
                    this.showSetting = status;
                }
            },
			handleSetTaskName(name) {
            	this.form.name = name;
			},
            handleClickOutsideHideSetting() {
                if (!this.getDraftTaskName && !this.$refs.quill.form.name) {
                    this.handleChangeVisibilitySettings(false);
                }
            },
            async getDraftTask () {
                if (this.getCurrentBoard && !this.getCurrentDraftTask && this.handlePermissionByBoardId('create-task', this.getCurrentBoard.id)) {
                    await this.$api.task.createTask({is_draft : 1, board_id: this.getCurrentBoard.id, name: ""})
                }
            },
            handleSendInput(task) {
                if (task) {
                    this.form = task;
                }
                this.form.name = this.form.name || this.taskInfo.name;

                if (!this.form.name) {
                    return this.$notify({type:'error', text: this.$t('enter_task_title_please')});
                }

                if (this.getCurrentBoard) {
                    if (this.form.planned_deadline || this.form.addMentions.length) {
                       this.handleUpdateDrag();
                    } else {
                        this.createTask();
                    }

                } else {
                    this.showChooseBoard();
                }
            },
            handleSubscribe() {
                let data = {
                    user_tenant_id: this.form.addMentions,
                    task_id:        this.getDraftTaskId
                };

                return new Promise((resolve, reject) => {
                    this.$api.task.subscribeAndAttach(data).then(() => {
                        resolve();
                    }, error => {
                        reject(error);
                    })
                });
            },
            handleUpdateDrag() {
                if (!this.handlePermissionByGroupId('create-task', this.getCurrentGroupId)) {
                    return this.sendNotifyPermissionInfo('create-task');
                }

                let promiseSubscribe;
                let promiseUpdate;

                if (this.form.addMentions.length) {
                    promiseSubscribe = this.handleSubscribe()
                }

                if (this.form.planned_deadline) {
                    //this.form.planned_deadline += ":00";
                    let data = Object.assign({...this.getCurrentDraftTask}, this.form, {task_id: this.getCurrentDraftTask.id});
                    promiseUpdate = this.$api.task.updateTask(data);
                }

                Promise.all([promiseSubscribe, promiseUpdate]).then(() => {
                    this.createTask();
                }).catch(e => {
                    console.error(e.message)
                });
            },
            showChooseBoard() {
                this.$modal.show('choose-modal', {type: "board", callback: (board) => {
                    this.board = board;
                    this.$store.dispatch('groups/setCurrentBoardId', board.id);

                    this.getDraftTask().then(() => {
                        if (this.$refs.quill.form.name) {
                            this.createTask();
                        }

                        if (this.redirect) {
                            return this.$router.push({
                                name: 'board', params: {
                                    group_id: board.group_id, board_id: board.id
                                }
                            });
                        }
                    });
                    
                }});
            },
            async createTask() {
                if (!this.handlePermissionByGroupId('create-task', this.getCurrentGroupId)) {
                    return this.sendNotifyPermissionInfo('create-task');
                }
                if (!this.getCurrentDraftTask) {
                    this.getDraftTask();
                }

				let subscribersForNewTask = {};

				find.search(this.getGroups, {id: this.getCurrentDraftTask.id}, 'task', (tasks, task, index, board, group) => {
					subscribersForNewTask = task.subscribers;
				});
                this.$api.task.createTask({
                    name:           this.$refs.quill.form.name.replace(/<p>|<\/p>/gi, ""),
                    board_id:       this.getCurrentBoardId,
                    draft_task_id:  this.getCurrentDraftTask.id,
                    is_draft:       0,
                    created_at:     this.$moment().format('YYYY-MM-DD HH:mm:ss'),
                    planned_deadline:     this.form.planned_deadline,
                    deadline:     this.form.deadline,
                    soft_budget: this.form.soft_budget,
                    subscribers:    subscribersForNewTask
                }).then(res => {
                    res.task.route = this.$route.params.period;
                    this.$refs.quill.clearForm();
                    helpersTask.updateCountTask(res.task);

                    this.handleChangeVisibilitySettings(false);

                    this.$root.$emit(`add-task`, {data: res.task});

                    if (this.$route.name === 'filter') {
                        this.$api.task.getTasksByFilterId(this.$route.params.id).catch(err => {
                            this.$notify({type:'error', text: err.response.data.message});
                        })
                    }
				})
            },
        }
    }
</script>

<style lang="scss">
    .row.task-create {
        width: auto;
        margin: 0 10px;
    }
    .List, .Kanban, .Gantt {
        .row.task-create{
            display: flex;
            @media (max-width: 990px) {
                display: block; 
            }
        }
    }
    .deadline-page{
        .row.task-create{
            display: flex;
            @media (max-width: 990px) {
                display: block; 
            }
        }
    }

        .filter-page {
     .row.task-create {
        margin: 0;
           @media (min-width: 992px) {
                margin:0px;
            }
    }

         .fixed-component .task-wrapper  {
                margin-top: 0px;
                margin-bottom:10px;
                padding: 0 30px 0 10px;
          }
}
    .task-create__hint{
        background-color: #fff;
        margin: 0 10px;
        border-radius: 5px;
        margin-top: 5px;
        padding: 5px;
        font-style: italic;
        color: #666;
        font-size: 12px;
    }
    .task-headline{
        .ql-container{
            margin: 0;
        }
    }

    .Kanban .fixed-component .task-create-holder {
        display:none;
    }
    @media (min-width: 768px) {
    .row.task-create {
        margin:0;
    }
}
</style>
