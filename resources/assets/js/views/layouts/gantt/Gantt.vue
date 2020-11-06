<template>
    <div class="gantt" ref="ganttWrapper" >
        <content-loading
            :absolute="true"
            :autosize="true"
            :loading="isUpdateTask"
        />
        <gantt-elastic
            ref="gantt"
            v-if="tasks.length"
            style="position: relative;"
            :tasks="tasks"
            :options="options"
        />
    </div>
</template>

<script>
    import { mapGetters }       from 'vuex';
    import GanttElastic         from '@views/components/gantt/GanttElastic';
    import find                 from '@helpers/findInGroups';

    import ThemeSubscribers     from '@views/layouts/theme/ThemeSubscribers';
    import SubscribersTask      from '@views/components/subscribers/subscribers-task';
    import ContentLoading       from "@views/components/ContentLoading";

    import store                from '@store';
    import Vue                  from 'vue';
    import {_setDocumentTitle} from "@helpers/controlDocumentTitle";

    export default {
        name: "gantt",
		components: {
			GanttElastic,
			ThemeSubscribers,
			SubscribersTask,
			ContentLoading,
		},
		data() {
			return {
				now: null,
				options:    {
					title: {
						label: 'Your project title as html (link or whatever...)',
						html: true,
						style: {
							'font-size': '20px',
							'vertical-align': 'middle',
							'font-weight': '400',
							'line-height': '35px',
							'padding-left': '22px',
							'letter-spacing': '1px'
						}
					},
                    calendar:{
                        hour: {
                            display: true,
                        }
                    },
					times: {
						timeZoom: 2.6
					},
                    locale: {
                        Now: "Now",
                        "X-Scale": "Zoom-X",
                        "Y-Scale": "Zoom-Y",
                        "Task list width": "Task list",
                        "Before/After": "Expand",
                        "Display task list": "Show task list",
                        weekdays: 'Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday'.split('_'),
                    },
					taskList: {
						columns: [
							{
								id: 1,
								label: 'ID',
								value: 'id',
								width: 30
							}, {
								id: 2,
								label: this.$t('title'),
								width: 50,
                                componentName: "TaskItemTitle"
							}, {
								id: 3,
								label: this.$t('assigned_to'),
								value: 'user',
								width:50,
								html: true
							}, {
								id: 3,
                                columnType: 'planned_deadline',
								label: this.$t('start'),
                                value: (task) => this.toLocalTime(task.start),
								width: 100,
                                html: false,
                                componentName: "TaskItemDeadline",
							},
                            {
                                id: 5,
                                columnType: 'deadline',
                                label: this.$t('deadline'),
                                value: (task) => {
                                    const time = task.deadline ? task.deadline : this.$moment().endOf('day').valueOf();
                                    return this.toLocalTime(time, 'YYYY-MM-DD HH:mm:ss');
                                },
                                width: 80,
                                html: false,
                                componentName: "TaskItemDeadline",
                            },
                            {
								id: 6,
								label: 'Budget',
								value: 'soft_budget',
								width: 70,
								styles: {
									label: {
										'text-align': 'center',
										'width': '100%'
									},
									value: {
										'text-align': 'center',
										'width': '100%'
									}
								}
							}
						]
					},
				}
				,
				cursorPos: {
					x: null,
				},
				dragTask: null,
				widthHour: 0,
				isUpdateTask: false,
			}
		},
        computed: {
            ...mapGetters({
                getMembers:         'members/getMembers',
                currentBoard:       'groups/getCurrentBoard',
                getSelectSortType:  'groups/getSelectSortType',
                getPriorities:      'priorities/getPriorities',
                getSortTypes:       'groups/getSortTypes',
                getSelectMembers:   'members/getSelectMembers',
                selectGanttTypeView: 'groups/getSelectGanttTypeView',
                rangeGanttTypeView: 'groups/getRangeGanttTypeView',
            }),
            getCurrentTasks() {
                return this.currentBoard.tasks
            },
            sortTask() {
                let priorityA, priorityB;

                return [...this.getCurrentTasks].sort((a, b) => {
                  switch (this.getSelectSortType.name) {
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
              });
            },
            tasks() {
                let tasks = [];

                if (this.now) {
                    this.sortTask.map((item, index) => {

                        if (item.draft) {
                            return;
                        }

                        if (this.currentBoard.hide_done_tasks && item.done_by) {
                            return;
                        }

                        let isSubscribers = !this.getSelectMembers.length || item.subscribers.task.some(taskSubscribers => this.getSelectMembers.some(selectMembers => selectMembers === taskSubscribers));

                        if (!isSubscribers) {
                            return;
                        }

                        let progress = 0;

                        if (item.count.done_sub_task) {
                            progress = ( item.count.done_sub_task * 100 / (item.count.done_sub_task + item.count.open_sub_task) ).toFixed(3);
                        }

                        let task = {};

                        const start = item.planned_deadline ? this.$moment(this.toLocalTime(item.planned_deadline)).valueOf() : this.$moment().startOf('day').valueOf();

                        const duration = item.deadline
                            ? this.$moment(this.toLocalTime(item.deadline)).diff(start, 'seconds')
                            : this.$moment(this.$moment().endOf('day').format()).diff(start, 'seconds');

                        task = {
                            id: item.id,
                            user: this.getMembersByTask(item),
                            start: start,
                            duration: duration * 1000,
                            progress: progress,
                            type: 'task',
                           ...item,
                        };

                        tasks.push(task);
                    });
                }

                return tasks
            },
            getMembersByTasks() {
                let taskOfMembers = {};

                this.sortTask.map((item, index) => {
                    if (item.draft) {
                        return;
                    }

                    if (this.currentBoard.hide_done_tasks && item.done_by) {
                        return;
                    }

                    let members = [];

                    this.getMembers.map(member => {
                        item.subscribers.task.map(userTaskId => {
                            if (userTaskId === member.id) {
                                members.push(member);
                            }
                        });
                    });

                    taskOfMembers[index] = members
                });
                return taskOfMembers;
            },
        },
        watch: {
            'selectGanttTypeView': function(typeView) {
                this.$root.$emit(`scale-update`, {data: typeView});
            },
            'rangeGanttTypeView': function(data) {
                this.$root.$emit(`scale-update`, {data: data});
            },
        },
        mounted() {
            this.now = this.$moment();

            this.$nextTick(function () {
                this.setSelectSortType();
                this.addTaskListener();
                this.addGanttListener();
            });
        },
        beforeDestroy() {
            this.removeGanttListener();
        },
        methods: {
            setSelectSortType() {
                let getSortTypeIndex = 0;

                this.getSortTypes.some((item,index) => {
                    if (item.name === 'a-z') {
                        getSortTypeIndex = index
                    }
                });
                this.$store.dispatch('groups/changeSelectSortType', this.getSortTypes[getSortTypeIndex]);
            },
            addTaskListener() {
                this.$el.addEventListener('click', (e) => {
                    if (e.target.dataset.role === 'gantt-label') {
                        this.showTaskDetails(+e.target.id)
                    }

                    return false;
                });
            },
            addGanttListener() {
                this.$refs.gantt.$on('chart-task-mousedown', this.handleTaskMousedown);
                this.$refs.gantt.$on('chart-task-mouseup', this.handleTaskMouseup);
                this.$refs.gantt.$on('chart-task-mousemove', this.handleTaskMove);
                this.$refs.gantt.$on('chart-task-mouseout', this.handleTaskMouseout);

                this.$refs.gantt.$on('ganttmainview-updatetask', this.updateTask);
            },
            removeGanttListener() {
                this.$refs.gantt.$off('chart-task-mousedown', this.handleTaskMousedown);
                this.$refs.gantt.$off('chart-task-mouseup', this.handleTaskMouseup);
                this.$refs.gantt.$off('chart-task-mousemove', this.handleTaskMove);
                this.$refs.gantt.$off('chart-task-mouseout', this.handleTaskMouseout);

                this.$refs.gantt.$off('ganttmainview-updatetask', this.updateTask);
            },
            handleTaskMousedown(e) {
                this.dragTask = Object.assign({},{...e.data});
                this.$refs.gantt.state.options.scroll.scrolling = false;
                this.cursorPos.x = e.event.clientX;
                e.event.stopPropagation();
            },
            handleTaskMouseout(e) {
                this.dragTask = null;
            },
            handleTaskMouseup(e) {
                this.dragTask = null;
            },
            handleTaskMove(e) {
                let diffX = this.cursorPos.x - e.event.clientX;
                this.cursorPos.x = e.event.clientX;
                if (this.dragTask && this.dragTask.id === e.data.id) {
                    e.data.x = e.data.x - diffX;
                    if(e.event.width) {
                        e.data.newWidth = e.event.width;
                    }
                }
            },
            handleTaskResizeRight(e) {
                e.data.newWidth = e.event.width;
            },
            convertDiffToTimestamp(length) {
                let diffHor = length / this.widthHour;
                return diffHor * 60 * 60 * 1000;
            },
            updateTask(e) {
                this.isUpdateTask = true;
                this.$api.task.updateTask(e.data).finally(() => this.isUpdateTask = false);
            },
            getTask(id) {
                let task = this.getCurrentTasks.find(item => item.id === id);

                if (task) {
                    return {...task}
                }

                return null;
            },
            showTaskDetails(id) {
                let task = find.searchTaskById(this.$store.getters['groups/getStateGroups'], id);

                if (task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', task.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                this.$router.replace({query: {taskId: +id}});
            },
            getMembersByTask(members) {
                let el = Vue.compile('<subscribers-task class="task-subscribers" :task="members" />');
                el = new Vue({
                    store,
                    data(){
                      return { members }
                    },
                    components: {
                        SubscribersTask
                    },
                    render: el.render,
                }).$mount();

                return el.$el.outerHTML
            },
        },

    }
</script>

<style lang="scss">
    .gantt-label{
        cursor: pointer;
    }
    .gantt{
        margin: 0px;
        position: relative;

        .waiting-spinner{
            background-color: rgba(255,255,255,0.5);
        }

        .gantt-elastic__chart-row-progress-bar-pattern{
            cursor: pointer;
        }

        .task-subscribers{
            padding: 2px;
        }
        .subscribers__list{
            padding-left: 6px;
        }
        .subscribers__list-item{
            width: 30px;
            height: 30px;
        }
        .gantt-elastic__task-list-items,
        .gantt-elastic__main-container-wrapper,
        .gant-elastic__chart-graph-container,
        .gant-elastic__chart-graph-container > div,
        .gantt-elastic__task-list-container,
        .gantt-elastic__main-container{
            height: auto !important;
        }
        .gantt-elastic__chart-scroll-container--vertical{
            display: none;
        }

    }
    .gantt-elastic__task-list-item-value-wrapper:hover > .gantt-elastic__task-list-item-value-container{
        background-color: #fff;
        display: flex;
        margin: inherit !important;
    }
    .gantt-elastic__task-list-items{
        border-left: 1px solid #f0f0f0;
    }
    .gantt-elastic__task-list-wrapper,
    .gantt-elastic__main-view-container{
        background-color: #fff;
    }
    .gantt-elastic__main-container-wrapper{
        border-top: none !important;
        border-bottom: none !important;
    }
    .gantt-elastic__main-view{
        background-color: transparent !important;
    }
    /*.gantt-elastic__task-list-items,*/
    /*.gantt-elastic__main-view-container{*/
        /*overflow: visible !important;*/
    /*}*/
    .gantt-elastic__chart-row-text-wrapper{
        display: none !important;
    }
</style>
