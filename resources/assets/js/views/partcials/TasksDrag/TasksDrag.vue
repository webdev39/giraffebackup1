<template>
    <div>
        <drop
            :class="{'over': dropOpenOver }"
            class='open-drop'
            @drop="handleOpenTaskDrop(...arguments)"
            @dragover="dropOpenOver = true"
            @dragleave="dropOpenOver = false"
        >
            <task-list-loop
                :listlimit="listlimit"
                :tasks-by-type="getActiveTask"
                :filterFor="filterFor"
            />
        </drop>

        <drop
            :class="{'over': dropDoneOver }"
            class='done-drop'
            @drop="handleDoneTaskDrop(...arguments)"
            @dragover="dropDoneOver = true"
            @dragleave="dropDoneOver = false"
        >
            <task-list-loop
                v-if="$route.name !== 'deadline' && hideDoneTask"
                :listlimit="listlimit"
                :accordionTitle="$t('done_tasks_from_me')"
                :tasks-by-type="groupTask['my-done']"
                :filterFor="filterFor"
            />
        </drop>

        <task-list-loop
            v-if="$route.name !== 'deadline' && hideDoneTask"
            :listlimit="listlimit"
            :accordionTitle="$t('done_tasks_from_other')"
            :tasks-by-type="groupTask['other-done']"
            :filterFor="filterFor"
        />

        <template v-if="isSplitted">
            <task-list-loop
                v-if="$route.name !== 'deadline'"
                :accordionTitle="$t('open_tasks_from_other')"
                :listlimit="listlimit"
                :tasks-by-type="groupTask['other-active']"
                :filterFor="filterFor"
            />
        </template>

    </div>

</template>

<script>
	import { mapGetters } from 'vuex'
	import { Drop }       from 'vue-drag-drop'
	import TaskListLoop   from '@views/partcials/TaskList/TaskListLoop'
	import find           from '@helpers/findInGroups'

	export default {
		components: {
			TaskListLoop,
			Drop,
		},
		props: {
			listlimit: {
				type: Number,
				default: 5,
			},
			tasksWithoutTime: {
				type: Boolean,
				default: false
			},
			filterFor: {
				type: String
			},
		},
		data() {
			return {
				dropOpenOver: false,
				dropDoneOver: false,
			}
		},
		computed: {
			...mapGetters({
				getCurrentBoard:    'groups/getCurrentBoard',
				getSelectSplitted:  'groups/getSelectSplitted',
				getOwner:           'members/getOwner',
				getPriorities:      'priorities/getPriorities',
				getSelectMembers:   'members/getSelectMembers',
				getTasksList:       'task/getTasksList',
                getTaskDeadline:    'groups/getTasksDeadline',
				getGroups:          'groups/getStateGroups',
			}),
			isSplitted() {
				// Filter, Deadline page has always splitted tasks
				return this.getSelectSplitted.name || this.$route.name === 'filter' || this.$route.name === 'deadline';
			},
			hideDoneTask() {
				if(this.getCurrentBoard) {
					return !this.getCurrentBoard.hide_done_tasks
				}
			},
			getUserId() {
				if (this.getOwner) {
					return this.getOwner.id
				}
			},
			getTasks() {
				if (this.$route.name === 'deadline') {
					let taskListIdsDeadline = this.getTasksList.map(item => String(item.id));
					return find.searchTasksByIds(this.getGroups, taskListIdsDeadline);
                }

				if (this.$route.name === 'filter') {
					return this.getTasksList;
				}

				return this.getCurrentBoard.tasks;
			},
			groupTask() {
				let tasks = {
					'other-done':   [],
					'other-active': [],
					'my-active':    [],
					'my-done':      [],
					'all-active':   []
				};

				this.getTasks.forEach(task => {
					let subscribers = !this.getSelectMembers.length || task.subscribers.task.some(taskSubscribers => this.getSelectMembers.some(selectMembers => selectMembers === taskSubscribers)),
						priority    = this.getPriorities.find(priority => priority.id === task.priority_id),
						hiddenTask  = false;

					if (priority) {
						hiddenTask = priority.is_invisible;
					}

					if (task.draft || !subscribers || hiddenTask) {
						return;
					}

					// Show task only without time
					if (this.tasksWithoutTime && task.planned_deadline) {
						return
					}

					/* filter select splitted */
					if (this.isSplitted) {
						if (!task.done_by && this.isMyTask(task)) {
							tasks['my-active'].push(task);
							return;
						}

						if (!task.done_by && !this.isMyTask(task)) {
							tasks['other-active'].push(task);
							return;
						}
					} else {
						if (!task.done_by) {
							tasks['all-active'].push(task);
							return;
						}
					}

					if (this.$route.name === 'deadline') {
						return;
					}

					if (task.done_by && this.isMyTask(task)) {
						tasks['my-done'].push(task);
						return;
					}
					if (task.done_by && !this.isMyTask(task)) {
						tasks['other-done'].push(task);
						return;
					}

				});

				return tasks;
			},
			getActiveTask() {
				if (this.isSplitted) {
					return this.groupTask['my-active']
				}

				return this.groupTask['all-active']
			}
		},
		methods: {
			isMyTask(task) {
				if (! task) {
					return false;
                }
				if (task.subscribers.task.length) {
					return find.searchTaskByUser(task, this.getUserId);
				}

				return false;
			},
			handleOpenTaskDrop([task_id, task]) {
				if (task.done_by) {
					this.$api.task
						.changeWorkflowTask({ task_id, is_done: !task.done_by})
						.catch(err => {
							this.$notify({type:'error', text: err.response.data.message});
						});

					this.dropOpenOver = false;
				}
			},
			handleDoneTaskDrop([task_id, task]) {
				if (!task.done_by) {
					this.$api.task
						.changeWorkflowTask({ task_id, is_done: !task.done_by})
						.catch(err => {
							this.$notify({type:'error', text: err.response.data.message});
						});
					this.dropDoneOver = false;
				}
			}
		}
	}
</script>
