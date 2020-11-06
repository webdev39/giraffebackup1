<template>
    <div>
        <div
            v-if="tasks.length > 0 && accordionTitle"
            class="row task-wrapper accordion-title"
            @click.stop="handleAccordionClick"
        >
            {{ shownTask ? $t('hide') : $t('show') }} {{ accordionTitle }} [ {{ tasks.length }} ]
        </div>

        <draggable
            v-model="tasks"
            :handle="getDragClass"
            :disabled="isDisabled || ['person', 'sort_weight'].indexOf(getSelectSortType.name) === -1"
            @sort="onSortCallback"
            class="drag-list"
        >
            <div
                v-if="!accordionTitle || shownTask"
                v-for="(task, index) in tasks"
                :key="task.id"
                :class="{'last': index === tasks.length - 1, 'first': index === 0}"
                class="row task-wrapper"
            >
                <drag
                    :transferData="[task.id, task, task.board_id]"
                    @dragstart="onDragStart(task)"
                    :hideImageHtml="false"
                    @dragend="onDragEnd"
                >
                    <task-list-item
                        :task="task"
                        :listlimit="listlimit"
                    />
                </drag>
            </div>
        </draggable>
    </div>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import { Drag, Drop }       from 'vue-drag-drop'
    import draggable            from 'vuedraggable'

    import tasksSorting         from '@mixins/tasksSorting'
    import TaskListItem         from '@views/partcials/TaskList/TaskListItem'

    export default {
        name: "task-list-loop",
		components: {
			TaskListItem,
			Drag,
			draggable
		},
		mixins: [
			tasksSorting,
		],
        props: {
            accordionTitle: String,
            order: String,
            listlimit: {
                type: Number,
                default: 5
            },
            isDisabled: {
                type: Boolean,
                default: false
            },
            filterFor: {
                type: String
            },
            tasksByType: {
                type: Array
            }
        },
        data() {
            return {
                shownTask: false,
            }
        },
        computed: {
            ...mapGetters({
                getSelectSortType:  'groups/getSelectSortType',
                getPriorities:      'priorities/getPriorities',
                getMembers:         'members/getMembers',
            }),
            getDragClass() {
                return /is_touch/.test(document.getElementsByTagName('html')[0].className) ? '.icon-move' : '.task-wrapper'
            },
            // getBodyWidth() {
            //   return document.body.clientWidth
            // },
            tasks: {
                get () {
                    let priorityA, priorityB;

                    this.defaultSorting();

                    return this.tasksByType.sort((a, b) => {

                        /*for page Task-List*/
                        // if (this.$route.name !== 'filter') {

                            switch (this.getSelectSortType.name) {
                                case 'sort_weight':
                                    return b.sort_weight - a.sort_weight;
                                case 'person':
                                    /*if (!a.sort_order || !a.sort_order.list) {
                                        const diff = new Date(a.created_at) - new Date(b.created_at);
                                        return diff > 0 ? 1 : -1;
                                    } else {
                                        return a.sort_order.list - b.sort_order.list;
                                    }*/

                                    if (a.sort_order.list) {
                                        return a.sort_order.list - b.sort_order.list;
                                    } else {
                                        const diff = new Date(b.created_at) - new Date(a.created_at);
                                        return diff > 0 ? 1 : -1;
                                    }

                                case 'priority':
                                    priorityA = this.getPriorities.find(item => a.priority_id === item.id);
                                    priorityB = this.getPriorities.find(item => b.priority_id === item.id);

                                    if (priorityA.sort_order) {
                                        return priorityA.sort_order - priorityB.sort_order;
                                    } else {
                                        return priorityA.id - priorityB.id;
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
                                    return new Date(b.created_at) - new Date(a.created_at);
                            }
                        // }

                        // return b.sort_weight - a.sort_weight;
                    });
                },
                set(value) {

                    if (this.isLoading) {
                        return
                    }

                    let data = {'order': value.map(task => task.id)};

                    switch (this.filterFor) {
                        case 'filter':
                            data.sort_type = "filter";
                            data.filter_id = Number(this.$route.params.id);
                            break;
                        case 'today':
                            data.sort_type = "today";
                            break;
                        case 'week':
                            data.sort_type = "week";
                            break;
                        default:
                            data.sort_type = "list";
                            data.board_id  = Number(this.$route.params.board_id);
                            data.group_id  = Number(this.$route.params.group_id);
                    }

                    let tasksIds = {};

                    data.order.map((item, index) => {
                        tasksIds[item] = index + 1
                    });

                    this.$store.dispatch('groups/changeTasksIds', tasksIds);
                    this.$store.dispatch('groups/changeTaskSortOrder', data);

                    this.$api.task.changeOrderTasks(data)
                        .catch((err) => {
                            this.$notify({type:'error', text: err.response.data.message});
                        });
                }
            },
        },
        methods: {
			/**
             * Method for default sorting (on name)
			 */
			defaultSorting() {
				this.tasksByType.sort((a, b) => {
					return sorter(a.name, b.name);
				})
            },

            onDragStart(task){
                if (this.isLoading) {
                    return
                }

                this.$event.$emit('task-calendar-drag-start', task);
            },
            onDragEnd(){
                this.$event.$emit('task-calendar-drag-end');
            },
            handleAccordionClick(e) {
                this.shownTask = !this.shownTask;
            },

            onSortCallback (evt) {
                /**
                 * Mixin tasksSorting
                 */
                this.sortTasks(this.tasks, evt);
            },
        },
    }
</script>
