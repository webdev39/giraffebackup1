<template>

    <div class="management-group-item">
        <management-collapse @toggleShow="toggleShow">
            <template slot="header">
                <table class="board-header">
                    <tr>
                        <td style="width: 500px;">
                            <div class="board-header__column-name">
                                <div class="board-header__name">{{ group.name }}</div>
                                <div
                                    v-if="groupControls && isPermissionReadAllGroups"
                                    @click.stop="showGroupSettings(group)"
                                    class="board-header__setting"
                                >
                                    <i class="fa fa-user-circle-o"></i>{{ $t('group_setting') }} / {{ $t('assign_user') }}
                                </div>
                            </div>
                        </td>

                        <td class="board-header__column-board-count">
                            <div class="board-header__column-board-count-text">
                                <button
                                    v-if="groupControls"
                                    @click.stop="toggleBoardArchive"
                                    class="board-header__button-archived"
                                >
                                    <i class="fa fa-archive" aria-hidden="true"></i> {{ $t('show_archived_boards') }}
                                </button>
                                <span
                                    v-else
                                    class="board-header__board-count"
                                >
                                    {{ isActiveBoard }} {{ $t('boards') }} [{{ isArchiveBoard }}]
                                </span>
                            </div>
                        </td>

                        <td
                            v-if="groupControls && isPermissionCreateGroup"
                            style="width: 116px;"
                        >
                            <button
                                :disabled="isLoading"
                                @click.stop="handleCloneGroup"
                                class="board-header__button-clone"
                            >
                                {{ $t('clone_group') }}
                            </button>
                        </td>

                        <td
                            v-if="!groupControls && checkPermission('time-tracking')"
                            class="board__column-bar"
                        >
                            <div class="management-bar">
                                <div class="management-bar-text management-bar-text-time text-right" :title="getGroupBudgetTime + ' h'">
                                    {{ getGroupBudgetTime }} h
                                </div>

                                <management-bar
                                    :percent="getDifferenceBudget"
                                    :color="getColor"
                                />
                                <div class="management-bar-text management-bar-text-time" :title="getGroupTrackedTime + ' h'">
                                    {{ getGroupTrackedTime }} h
                                </div>
                            </div>
                        </td>

                        <td class="board__column-bar board__column-bar_open-task" v-if="!groupControls">
                            <div class="management-bar">
                                <div class="management-bar-text text-right">
                                    {{ getGroupOpenTasksCount }}
                                </div>
                                <management-bar :percent="getGroupDifferenceTasks" />
                                <div class="management-bar-text">
                                    {{ getGroupDoneTasksCount }}
                                </div>
                            </div>
                        </td>

                        <td class="board__column-bar-day" v-if="!groupControls">
                            <management-bar-day :tasks="getGroupCalendarOpenTasks" :days="days" />
                        </td>

                        <td class="board__column-bar-day" v-if="!groupControls">
                            <management-bar-day :tasks="getGroupCalendarDoneTasks" :days="days" />
                        </td>

                    </tr>

                </table>

            </template>

            <template slot="content">

                <input-create-board
                    v-if="isGroup"
                    :groupId="group.id"
                />

                <management-boards
                    v-if="isActiveBoard"
                    :boards="getActiveBoard"
                    :group="group"
                    :days="days"
                ></management-boards>

                <div
                    v-if="boardArchiveShow"
                    class="board-archive"
                >

                    <div v-if="isArchiveBoard">

                        <div class="board-archive__header">
                            {{ $t('archivate') }}
                        </div>

                        <management-boards
                            :boards="getArchiveBoard"
                            :group="group"
                            :days="days"
                        ></management-boards>

                    </div>

                    <div v-else class="board-nothing">
                        {{ $t('archive_hasnt_boards') }}
                    </div>

                </div>

            </template>

        </management-collapse>

    </div>
</template>

<script>
    import ManagementCollapse   from '@views/partcials/Management/ManagementCollapse';
    import ManagementBoards     from '@views/partcials/Management/ManagementBoards';
    import InputCreateBoard     from '@views/elements/input_create_board/InputCreateBoard';
    import ManagementBar        from '@views/partcials/Management/ManagementBar';
    import ManagementBarDay     from '@views/partcials/Management/ManagementBarDay';

    export default {
        props: {
            group: {
            	type: Object,
				default: () => {}
            },
            days: {
            	type: Array,
                default: () => []
            },
        },
		components: {
			ManagementCollapse,
			InputCreateBoard,
			ManagementBoards,
			ManagementBar,
			ManagementBarDay
		},
		data() {
			return {
				boardArchiveShow:   false,
				groupControls:      false,
			}
		},
        computed: {
            getArchiveBoard() {
                return this.group.boards.filter(board => {
                    return !!+board.is_archive
                });
            },
            getActiveBoard() {
                return this.group.boards.filter(board => {
                    return !+board.is_archive
                });
            },
            isArchiveBoard() {
                return this.getArchiveBoard.length
            },
            isActiveBoard() {
                return this.getActiveBoard.length
            },
            getGroupTrackedTime() {
                return this.getHours(this.getActiveBoard, 'trackedTime');
            },
            getGroupBudgetTime() {
                let sum = 0;
                let that = this;

                this.getActiveBoard.map(function(item) {

                    if (item['hard_budget'] && item['hard_budget'] !== "0:00") {
                        sum += that.getTimeHours(item['hard_budget'])
                    } else {
                        sum += Number((item['budget'] / 60).toFixed(2));
                    }
                });

                return sum
            },
            getDifferenceBudget() {
                return this.getDifferenceBudgetTask(this.getGroupBudgetTime, this.getGroupTrackedTime);
            },
            getGroupOpenTasksCount() {
                return this.getAllTaskCount(this.getActiveBoard);
            },
            getGroupDoneTasksCount() {
                return this.getAllTaskCount(this.getActiveBoard, false);
            },
            getGroupDifferenceTasks() {
                return this.getDifference(this.getGroupOpenTasksCount, this.getGroupDoneTasksCount);
            },
            getGroupCalendarOpenTasks() {
                return this.getCalendarTask(this.getActiveBoard,'calendar_opened_tasks');
            },
            getGroupCalendarDoneTasks() {
                return this.getCalendarTask(this.getActiveBoard,'calendar_closed_tasks');
            },
            getColor: function() {

                if (this.getGroupBudgetTime && this.getGroupBudgetTime < this.getGroupTrackedTime) {
                    return '#e24747';
                }

                return 'deepskyblue'
            },
            isGroup() {
                return this.handleManagementByGroupId(this.group.id);
            },
            isPermissionReadAllGroups() {
                return this.checkPermission('read-all-groups');
            },
            isPermissionCreateGroup() {
                return this.checkPermission('create-group');
            }
        },
        methods: {
            toggleBoardArchive() {
                this.boardArchiveShow = !this.boardArchiveShow;
            },
			toggleShow(toggle) {
                this.groupControls = toggle;
            },
            showGroupSettings(group) {
                this.$modal.show('group-setting-modal', { groupId: group.id })
            },
            handleCloneGroup() {
                this.$api.group.cloneGroup(this.group.id)
                    .catch((err) => {
                        this.$notify({type:'error', text: err.response.data.message});
                    })
            },
			getDifference(count1, count2) {
				if (!count1 && !count2) {
					return 0;
				}
				return ((+count1 / (+count2 + +count1)) * 100).toFixed(3)
			},
			getDifferenceBudgetTask(count1, count2) {
				if (count1 === '0.00' && count2 === '0.00') {
					return 0;
				}
				return ((+count2 / +count1) * 100).toFixed(3)
			},
			getHours(arr, prop) {
				let sum = this.getAllCount(arr,prop);
				return +(sum / 60).toFixed(2);
			},
			getAllCount(arr, prop){
				let sum = 0;
				arr.map(function(item) {
					sum += item[prop]
				});
				return sum;
			},
			getAllTaskCount(arr, openTask = true) {
				let sum = 0;

				arr.map(function(board) {
					sum += board.tasks.filter(task => !task.draft).reduce((accumulator, currentValue, index, array) => {
						if (openTask) {
							return accumulator + !+currentValue.done_by;
						}
						return accumulator + +currentValue.done_by;
					}, 0);
				});

				return sum;
			},
			getCalendarTask(arr, prop) {

				let calendarTasks = [];
				let boardTasks = [];
				let task = {};

				arr.map(item => {
					boardTasks = item[prop];

					if(!Array.isArray(boardTasks)) {
						boardTasks = Object.entries(boardTasks).map( ([date, tasks]) => ({date,tasks}) );
					}

					if(!calendarTasks.length){
						calendarTasks = boardTasks;
					} else {
						boardTasks.map(boardTask => {
							task = calendarTasks.find(calendarTask => {
								if(calendarTask.date === boardTask.date) {
									calendarTask.tasks += boardTask.tasks;
									return true;
								}
							});

							if(!task) {
								calendarTasks.push(boardTask)
							}
						})
					}
				});

				return calendarTasks;
			}
        }
    }
</script>

<style lang="scss">
    .board-header__button-clone{
        background: #fff;
        border: none;
        color: #4e77f2;
        padding: 5px 10px;
        border-radius: 5px;
    }
</style>
