<template>
    <div class="drag-container kanban-tasks-list">
        <div style="position: relative; height: 100%;">
            <draggable
                v-model="getPriority"
                :tag="'ul'"
                handle=".kanban-column-header"
                :disabled="isDisableDraggablePriority"
                class="drag-list"
            >
                <li
                    v-for="(stage, index) in getPriority"
                    :key="stage.name"
                    :class="{['drag-column-' + stage.name]: true}"
                    class="drag-column"
                >
                    <tasks-kanban-collapse>
                        <template
                            slot="collapse-header"
                        >
                            <span
                                :style="{color: stage.color}"
                                class="kanban-column-header"
                                @click="showPrioritySettingModal(stage)"
                            >
                                {{ stage.name }} {{ tasksGroupByPriorities[stage.id]['current_task'] }} / {{ tasksGroupByPriorities[stage.id]['all_task'] }}
                            </span>
                            
                            <modal-add-task-kanban
                                :currentBoard="getCurrentBoard"
                                :ref="'add-task'"
                                :priorityId="stage.id"
                                @addTask="addTask"
                            />
                        </template>

                        <template slot="collapse-content">
<!--                             <modal-add-task-kanban
                                :ref="'add-task'"
                                :priorityId="stage.id"
                                @addTask="addTask"
                            /> -->
                            <tasks-kanban-priority-list
                                :tasks="tasksGroupByPriorities"
                                :priority="stage"
                                @change="changeTask"
                            />
                        </template>
                    </tasks-kanban-collapse>
                </li>
            </draggable>
        </div>
    </div>
</template>

<script>
    import { mapGetters }           from 'vuex'
    import draggable                from 'vuedraggable'

    import TasksKanbanCollapse      from './TasksKanbanCollapse'
    import TasksKanbanPriorityList  from './TasksKanbanPriorityList'
    import ModalAddTaskKanban       from './ModalAddTaskKanban'
    import find                     from '@helpers/findInGroups'

    export default {
        name: 'Kanban',
        components: {
            TasksKanbanCollapse,
            ModalAddTaskKanban,
            TasksKanbanPriorityList,
            draggable
        },
        props: {
            currentBoard: {
            	type: Object, default: null
            },
        },
        data() {
            return {
                isFetching:         false,
                listInterval:       '',
                dragTaskPriority:   '',
                tasks: [],
            };
        },
        computed: {
            ...mapGetters({
                getBoards:          'groups/getBoards',
                getGroups:          'groups/getStateGroups',
                getPriorities:      'priorities/getPriorities',
                getFilters:         'filters/getFilters',
				getTasksList:       'task/getTasksList',
                getSelectSortType:  'groups/getSelectSortType',
                getSelectMembers:   'members/getSelectMembers',
                getMembers:         'members/getMembers',
                getCurrentDraftTask: 'groups/getCurrentDraftTask',
            }),
            isDisableDraggablePriority() {
                if (this.isPermissionsUpdateBoard && this.isDesktop) {
                    return false
                }

                return true;
            },
            isDesktop() {
                return this.getBodyWidth > 991
            },
            isPermissionsUpdateBoard() {
                return this.handlePermissionByBoardId('update-board', this.getCurrentBoardId)
            },
            getBodyWidth() {
                return document.body.clientWidth
            },
            getCurrentBoard() {
                return this.currentBoard || this.$store.getters['groups/getCurrentBoard']
            },
            getCurrentFilter() {
                if (this.$route.name === 'filter') {
                    return this.getFilters.find(filter => filter.id === +this.$route.params.id)
                }
            },
            getTasks () {
                if (this.$route.name === 'filter') {
                    return this.getTasksList.filter(task => {
						if (this.getCurrentFilter) {
							if (this.getCurrentFilter.status === 0) {
								return !task.done_by && !task.draft && task.board_id === this.getCurrentBoard.id;
							}
							if (this.getCurrentFilter.status === 1) {
								return task.done_by && !task.draft && task.board_id === this.getCurrentBoard.id;
							}
						}

                        return !task.draft &&
                               !task.is_archive &&
                                task.board_id === this.getCurrentBoard.id
                    });
                } else {
                    return this.getCurrentBoard.tasks.filter(task => {
                        if (this.getCurrentBoard.hide_done_tasks) {
                            return !task.done_by && !task.draft;
                        }

                        return !task.draft;

                    });
                }
            },
            getCurrentBoardId () {
                // return Number(this.$route.params.board_id)
                return this.getCurrentBoard.id
            },
            getCurrentGroupId () {
                //return Number(this.$route.params.group_id)
                return this.getCurrentBoard.group_id
            },
            getPriority: {
                get() {
                    if (Object.keys(this.getPriorities).length) {

                        return this.getPriorities
                            .filter(item => {

                                if (item.is_invisible) {
                                    return false;
                                }

                                if (this.getCurrentFilter && this.getCurrentFilter.priority_ids) {
                                    return item.board_id === this.getCurrentBoardId && this.getCurrentFilter.priority_ids.some(filter => filter === item.id)
                                }

                                return item.board_id === this.getCurrentBoardId

                            }).sort((a,b) => {
                                if (!a.sort_order) {
                                    return a.id - b.id;
                                }

                                return sorter(a.sort_order, b.sort_order);
                            });
                    }
                },
                set(value) {
                    this.handleSortPriority(value);
                }
            },
            tasksGroupByPriorities () {

                let tasksGroupByPriorities  = {};

                this.getPriority.forEach(priority => {
                    tasksGroupByPriorities[priority.id]                 = {};
                    tasksGroupByPriorities[priority.id]['all_task']     = 0;
                    tasksGroupByPriorities[priority.id]['current_task'] = 0;

                    tasksGroupByPriorities[priority.id].tasks = this.getTasks
                        .filter(task => {
                            if (task.priority_id !== priority.id) {
                                return false;
                            }

                            tasksGroupByPriorities[priority.id]['all_task'] += 1;

                            if (!this.getSelectMembers.length) {
                                tasksGroupByPriorities[priority.id]['current_task'] = tasksGroupByPriorities[priority.id]['all_task'];
                                return true
                            }

                            if (!task.subscribers.task.some(taskSubscribers => this.getSelectMembers.some(selectMembers => selectMembers === taskSubscribers ))) {
                                return false;
                            }

                            tasksGroupByPriorities[priority.id]['current_task'] += 1;
                            return true;
                        })
                        .sort((a,b) => {
                            switch (this.getSelectSortType.name) {
                                case 'person':
                                    if (a.sort_order.kanban) {
                                        return a.sort_order.kanban - b.sort_order.kanban;
                                    } else {
                                        const diff = new Date(b.created_at) - new Date(a.created_at);
                                        return diff > 0 ? 1 : -1;
                                    }

                                case 'a-z':
                                    return sorter(a.name, b.name);

                                case 'todo':
                                    if (a.planned_deadline && b.planned_deadline) {
                                        const diff = new Date(a.planned_deadline) - new Date(b.planned_deadline);
                                        return diff > 0 ? 1 : -1;
                                    } else if (!a.planned_deadline && b.planned_deadline) {
                                        return 1
                                    } else if (a.planned_deadline && !b.planned_deadline) {
                                        return -1
                                    } else {
                                        const diff = new Date(b.created_at) - new Date(a.created_at);
                                        return diff > 0 ? 1 : -1;
                                    }

                                case 'deadline':
                                    if (a.deadline && b.deadline) {
                                        const diff = new Date(a.deadline) - new Date(b.deadline);
                                        return diff > 0 ? 1 : -1;
                                    } else if (!a.deadline && b.deadline) {
                                        return 1
                                    } else if (a.deadline && !b.deadline) {
                                        return -1
                                    } else {
                                        const diff = new Date(b.created_at) - new Date(a.created_at);
                                        return diff > 0 ? 1 : -1;
                                    }
                                case 'assignee':
                                    const userALenght = a.subscribers.task.length;
                                    const userBLenght = b.subscribers.task.length;

                                    if (userALenght && userBLenght) {

                                        if (userALenght === 1 && userBLenght === 1) {
                                            const userA = this.getMembers.find(item => item.id === a.subscribers.task[0]);
                                            const userB = this.getMembers.find(item => item.id === b.subscribers.task[0]);

                                            return sorter(userA.user.nickname, userB.user.nickname)
                                        }

                                        if (userALenght < userBLenght) {
                                            return -1
                                        }

                                        if (userALenght > userBLenght) {
                                            return 1
                                        }

                                    } else if (!userALenght && userBLenght) {
                                        return 1
                                    } else if (userALenght && !userBLenght) {
                                        return -1
                                    } else {
                                        const diff = new Date(b.created_at) - new Date(a.created_at);
                                        return diff > 0 ? 1 : -1;
                                    }

                                case 'date':
                                    return new Date(b.created_at) - new Date(a.created_at)
                            }
                    })
                });

                return tasksGroupByPriorities;
            }
        },
        methods: {
            showPrioritySettingModal(priority) {
                this.$modal.show(
                    'priority-setting-modal',
                    {priority, boardId: this.getCurrentBoardId},
                );
            },
            handleSortPriority(priorities) {
                const data = priorities.map(item => item.id);

                this.$store.dispatch('priorities/updateSortOrderPriorities', data);

                this.$api.priorities.updateSortOrderCustomPriority(data);
            },
            changeTask (data) {
                if (data.type === "moved") {
                    this.saveTasksOrder(this.changeTaskByOrder(data));
                } else if (data.type === "added") {
                    this.getPriorityUpdate(data);
                }
            },
            changeTaskByOrder (data) {

                let orders = [];

                for (let id in this.tasksGroupByPriorities) {
                    if (+id === data.priority_id) {
                        data.tasks.forEach(item => {
                            orders.push(item.id)
                        })
                    } else {
                        this.tasksGroupByPriorities[id].tasks.forEach(item => {
                            orders.push(item.id)
                        })
                    }
                }

                return orders
            },
            getFirstSortWeightByPriorityId(priority_id) {
                const { tasks } = this.tasksGroupByPriorities[priority_id];
                if (tasks.length) {
                    return tasks[0].sort_weight + 103;
                }
                return null;
            },

            addTask (priority_id, taskInput) {
                if (!this.handlePermissionByGroupId('create-task', this.getCurrentGroupId)) {
                    return this.sendNotifyPermissionInfo('create-task');
                }

                if (this.isFetching) {
                    return
                }

                this.isFetching = true;

                let subscribersForNewTask = {};

				find.search(this.getGroups, {id: this.getCurrentDraftTask.id}, 'task', (tasks, task, index, board, group) => {
					subscribersForNewTask = task.subscribers;
				});

                let data = {
                    board_id:       this.getCurrentBoardId,
                    draft_task_id:  this.getCurrentDraftTask.id,
                    name:           taskInput,
                    is_draft:       0,
                    priority_id,
					subscribers: subscribersForNewTask
                };

                const sort_weight = this.getFirstSortWeightByPriorityId(priority_id);

                if (sort_weight) {
                    data.sort_weight = sort_weight;
                }
                
                this.$api.task.createTask(data)
                    .then(res => {
                        if (this.$route.name === 'filter') {
                            this.$api.task.getTasksByFilterId(this.$route.params.id).catch(err => {
                                this.$notify({type:'error', text: err.response.data.message});
                            })
                        }
                    })
                    .finally(() => {
                        this.isFetching = false;
                    });
            },
            getPriorityUpdate (data) {
                if (!this.isLoading) {

                    let newTask            = this.copyObject(data.task);
                    newTask.priority_id    = data.priority_id;
                    newTask.task_id        = data.task.id;

                    this.$store.dispatch('groups/changeTask', newTask);
                    this.$store.dispatch('task/changeTaskList', newTask);
                    this.saveTasksOrder(this.changeTaskByOrder(data));

                    this.$api.task.updateTask(newTask, null, false)
                        .then(res => {})
                        .catch(err => {
                            this.$notify({type:'error', text: err.response.data.message});
                        })
                }
            },
            saveTasksOrder (tasks) {
                let data = {
                    'order':        tasks,
                    'board_id':     this.getCurrentBoardId,
                    'group_id':     this.getCurrentGroupId,
                    'sort_type':    'kanban'
                };

                if (!this.isLoading) {
                    this.$store.dispatch('groups/changeTaskSortOrder', data);
                }
            },
        },
    };
</script>

<style lang="scss" scoped>
    $ease-out: all .3s cubic-bezier(0.23, 1, 0.32, 1);

    .drag-container {
        height:100%;
        display:flex;
        position: relative;
        top: -14px;
    }

    .drag-list{
        height:100%;
        /*overflow-y: hidden; */
        @media (min-width: 768px) {
            margin-left:0px;
            margin-right:0px;
        }
    }

    .kanban-column-controls{
        padding: 0px 15px;
    }

    .drag-inner-list {
        min-height: 101px;
        height: 100%;
        padding-left: 0;
        list-style: none;
        overflow-y: auto;
        margin-top:10px;
    }

    .drag-list {
        display: flex;
        padding-left: 0;
        list-style: none;
        margin-bottom:0;
    }

    .drag-column {
        flex: 1;
        margin: 0 10px 5px 0px;
        position: relative;
        // min-width: calc(100vw - 70px);
        min-width: 265px;
        padding:0;
    }

//     .drag-column:last-child {
//     padding-right:10px;

// }


    .kanban-column-header {
        display: block;
        font-size: 16px;
        margin: 0;
        text-transform: uppercase;
        font-weight: 600;
        padding: 15px;
        padding-right: 33px;
        line-height: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        background-color:#fff;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .kanban-collapse-open{
        .kanban-column-header {
            border-bottom: 1px solid #e2e2e2;
        }
    }

    .drag-item {
        border-top: 1px solid rgb(226, 226, 226);
        transition: $ease-out;
        cursor: pointer;
        &:first-child{
            border-top: 0;
        }
        &.is-moving {
            transform: scale(1);
            background: #fff;
        }
    }

    .drag-header-more {
        cursor: pointer;
    }

    .drag-options {
        position: absolute;
        top: 44px;
        left: 0;
        width: 100%;
        height: 100%;
        padding: 10px;
        transform: translateX(100%);
        opacity: 0;
        transition: $ease-out;

        &.active {
            transform: translateX(0);
            opacity: 1;
        }

        &-label {
            display: block;
            margin: 0 0 5px 0;

            input {
                opacity: 0.6;
            }

            span {
                display: inline-block;
                font-size: 0.9rem;
                font-weight: 400;
                margin-left: 5px;
            }
        }
    }

    /* Dragula CSS  */

    .gu-mirror {
        position: fixed !important;
        margin: 0 !important;
        z-index: 9999 !important;
        opacity: 0.8;
        list-style-type: none;
    }

    .gu-hide {
        display: none !important;
    }

    .gu-unselectable {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }

    .gu-transit {
        opacity: 0.2;
    }

    .drag-item-content{
        border-left-width: 3px;
        border-left-style: solid;
        border-left-color: transparent;
        padding: 15px;
    }

    .reports-spinner-root-wrapper{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin-top: 0;
        z-index:  1;
    }

    .reports-spinner-wrapper{
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -90px;
        margin-left: -90px;
        z-index: 1;
    }
    .hide-bottom-controls{
        visibility: hidden;
    }
</style>
