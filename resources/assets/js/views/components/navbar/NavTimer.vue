<template>
    <div v-if="checkPermission('time-tracking')" class="timer-all-container"
         :class="{ 'order-4': getCurrentTimerId, 'timer-all-container-open': getCurrentTimerId}">
        <notifications groupName="timer" class="notifyTimer"/>
        <div class="menu-navigation-icons timer-timer-icon" :title="$t('title_create_timer')"
             v-if="isTenant || checkPermission('time-tracking')">
            <drop @drop="handleTaskDrop" class='drop'>
                <button
                        id="timer-create-trigger"
                        type="button"
                        @click="handleCreateTimer"
                        data-v-step="time_0"
                        class="btn btn-menu-item btn-sm btn-top-item-timer"
                >
                    <i class="icon-clock">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-clock">
                            </use>
                        </svg>
                    </i>
                </button>
            </drop>
        </div>
        <span class="no-active-timer-msg" v-if="!isShowTimeContainer">{{ $t('no_active_timer') }} </span>

        <div id="timeContainer" v-if="isShowTimeContainer">
            <div id="taskDetails">
                <div class="timer-task-icon checkbox-wrapper">
                    <input type="checkbox" style="cursor: pointer" v-model="getCurrentTaskIsDone"
                           :disabled="!getCurrentTask.id">
                    <span class="checkbox checkbox_theme_black "></span>
                </div>

                <div class="timer-task-block">
                    <div class="current-timer-task-dropdown dropdown">
                        <div id="task_menue_modal_button" class="task-headline timer-task-dropdown dropdown-toggle">
                            <span class="task_menue_modal_button-text" data-v-step="time_1"
                            >{{ getCurrentTaskName }}</span>
                        </div>
                        <i @click="showChangeCurrentTimerTaskModal"
                           class="current-timer-task-dropdown-change-task icon-pencil demo_button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-pencil"></use>
                            </svg>
                        </i>
                    </div>

                    <div
                            v-click-outside="hideTimerComment"
                            data-v-step="time_2"
                            class="comment-dropdown"
                    >
                        <div id="comment_menue_multiline_button" class="task-subline demo_button timer-item-comment"
                             style="cursor:pointer;" @click="showTimerComment = !showTimerComment">
                            <span
                                    data-v-step="time_2"
                            >{{ !currentTimer.comment ? $t('enter_comment') : getCurrentTimerComment }}</span>
                        </div>

                        <timer-comment-dropdown :task_id="getCurrentTask.id" :timer-id="getCurrentTimerId"
                                                :timer-comment="getCurrentTimerComment" :show.sync="showTimerComment"/>
                    </div>
                </div>
            </div>

            <div id="taskAssignation" class="current-timer-task-dropdown dropdown">
                <!--                 <div class="timer-board-icon">
                                    <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                </div> -->

                <div class="timer-board-info">
                    <div id="board_menue_modal_button"
                         class="task-subline demo_button timer-task-dropdown dropdown-toggle"
                         @click="showChangeCurrentTimerTaskModal">
                        <i class="icon-boards">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-boards">
                                </use>
                            </svg>
                        </i>
                        {{ getCurrentBoardName }}
                    </div>

                    <div id="group_menue_modal_button"
                         class="task-subline demo_button timer-task-dropdown dropdown-toggle"
                         @click="showChangeCurrentTimerTaskModal">
                        <i class="icon-sitemap">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-sitemap">
                                </use>
                            </svg>
                        </i>
                        {{ getCurrentGroupName }}
                    </div>
                </div>
            </div>

            <div class="timer-control-wrapper">
                <div id="timerTimeDetails" :class="{ timer_paused: getCurrentTimerStatus === 'paused'}">
                    <span>{{ getCurrentTimerTime | timerToStringFormat(false) }}</span>
                    <span class="nav-overtime" :title="$t('overtime_budget')" v-if="overtimeHardBudget">
                        <i class="icon-danger">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-danger">
                                </use>
                            </svg>
                        </i>
                    </span>
                </div>

                <div
                        data-v-step="time_3"
                        class="task-time-controller"
                >
                    <button type="button" class="btn btn-success btn-lg btn-timer-start" :title="$t('play')"
                            :disabled="isLoading" @click="startTimer(currentTimer.id)"
                            v-if="getCurrentTimerStatus === 'stopped'">
                        <i class="icon-play">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-play">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-start" :title="$t('play')"
                            @click="continueTimer(currentTimer.id)" v-if="getCurrentTimerStatus === 'paused'">
                        <i class="icon-start-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-start-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-pause" :title="$t('pause')"
                            @click="pauseTimer(currentTimer.id)" v-if="getCurrentTimerStatus === 'started'">
                        <i class="icon-pause-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-pause-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-stop" :title="$t('stop')"
                            @click="stopTimer(currentTimer.id)">
                        <i class="icon-stop-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-stop-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn_timer_remove" :title="$t('title_remove_timer')"
                            :disabled="isLoading" @click="confirmRemoveTimer(currentTimer)"
                            v-if="getCurrentTimerStatus === 'paused'">
                        <i class="icon-trash">
                            <svg class="icon font-color-red" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-trash">
                                </use>
                            </svg>
                        </i>
                    </button>
                </div>
            </div>
        </div>

        <div id="timer-list-buttons" :class="{ 'dispay-none-timer-list-buttons' : currentTimer }">
            <div
                    data-v-step="time_4"
                    class="navbar-pad"
                    v-click-outside="hideTimers"
            >
                <button class="btn btn-lg btn-menu-item btn-top-item-timer" :title="$t('show_timers')"
                        @click="showTimers = !showTimers">
                    <i class="icon-task">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-task">
                            </use>
                        </svg>
                    </i>
                </button>
                <timer-list v-if="showTimers" @hide="hideTimers"/>
            </div>

            <div
                    data-v-step="time_5"
                    class="navbar-pad"
                    v-click-outside="hideTimerLogs"
            >
                <button class="btn btn-lg btn-menu-item btn-top-item-timer" :title="$t('show_logs')"
                        @click="showTimerLogs = !showTimerLogs">
                    <i class="icon-list">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-list">
                            </use>
                        </svg>
                    </i>
                </button>

                <time-log v-if="showTimerLogs" @hide="hideTimerLogs"/>
            </div>
        </div>
    </div>
</template>

<script>
	import {mapGetters} from 'vuex'
	import {Drop} from 'vue-drag-drop'
	import clickOutside from 'v-click-outside'
	import {getLoggedTime} from '@helpers/time'

	import timerMixin from "@mixins/timer";
	import TimeLog from '@views/elements/timelog/Timelog'
	import TimerList from "@views/components/timer/TimerList";
	import TimerCommentDropdown from "@views/components/timer/TimerCommentDropdown";
	import Notifications from "@views/components/notifications/Notifications";

	export default {
		data() {
			return {
				overtimeSoftBudget: false,
				overtimeHardBudget: false,
			}
		},
		computed: {
			...mapGetters({
				groups: 'groups/getStateGroups',
				timers: 'timers/getTimers',
				currentTimer: 'timers/getCurrentTimer',
				currentRelations: 'timers/getCurrentRelations',
				getCurrentTour: 'getCurrentTour',
			}),
			isShowTimeContainer() {
				return this.getCurrentTimerId && this.groups.length;
			},
			getCurrentTimerId() {
				return this.currentTimer ? this.currentTimer.id : null
			},
			getCurrentTimerComment() {
				return this.currentTimer ? this.currentTimer.comment : null
			},
			getCurrentTimerStatus() {
				return this.currentTimer ? this.currentTimer.status : null
			},
			getCurrentTimerTime() {
				return this.currentTimer ? this.currentTimer.time : null
			},
			getCurrentTask() {
				return this.currentRelations.task || {}
			},
			getCurrentBoard() {
				return this.currentRelations.board || {}
			},
			getCurrentGroup() {
				return this.currentRelations.group || {}
			},
			getCurrentGroupName() {
				return this.getCurrentGroup.name || this.$t('group_name');
			},
			getCurrentBoardName() {
				return this.getCurrentBoard.name || this.$t('board_name');
			},
			getCurrentTaskName() {
				return this.getCurrentTask.name || this.$t('select_task');
			},
			getCurrentTaskIsDone: {
				get: function () {
					return this.getCurrentTask.done_by > 0 && this.getCurrentTask.id !== null;
				},
				set: function (value) {
					this.changeTaskWorkflow(this.getCurrentTask.id);
				}
			},
			getCurrentTaskSoftBudget() {
				return this.getBudgetSeconds(this.getCurrentTask.soft_budget);
			},
			getCurrentTaskHardBudget() {
				return this.getBudgetSeconds(this.getCurrentTask.hard_budget);
			},
			getCurrentTaskTrackedTime() {
				return this.getTrackedTimeSecondsByTask(this.getCurrentTask);
			},
			getAudioOvertimeNotification() {
				return new Audio('/overtime_budget_notification.mp3');
			}
		},
		watch: {
			getCurrentTimerId: function () {
				this.overtimeSoftBudget = false;
				this.overtimeHardBudget = false;
			},
			getCurrentTaskTrackedTime: function () {
				this.checkOvertime();
			},
			getCurrentTaskSoftBudget: function () {
				this.checkOvertime();
			},
			getCurrentTaskHardBudget: function () {
				this.checkOvertime();
			},
		},
		components: {
			Drop,
			TimerCommentDropdown,
			TimerList,
			TimeLog,
			Notifications,
		},
		mixins: [
			timerMixin
		],
		directives: {
			'clickOutside': clickOutside.directive
		},
		mounted() {
			// Don't remove this, for tour on site
			this.$root.$on('tour-start-timer', _ => this.createStartTimer());
			this.$root.$on('tour-stop-timer', _ => {
				// this.stopTimer(this.currentTimer.id);
				this.removeTimer(this.currentTimer.id);
			});
		},
		methods: {
			confirmRemoveTimer(timer) {
				this.$modal.show("confirm-modal", {
					title: this.$t('are_you_sure_you_want_to_delete_this_timer'),
					body: `${this.getCurrentTask.name ? 'Task:' + this.getCurrentTask.name : ""} Time: ${getLoggedTime(timer.time)}`,
					confirmCallback: () => {
						this.removeTimer(timer.id);
					}
				});
			},
			checkOvertime() {
				if (!this.getCurrentTaskTrackedTime || this.getCurrentTimerStatus !== 'started') {
					return;
				}

				if (this.isOvertimeBudget(this.getCurrentTaskHardBudget, this.getCurrentTaskTrackedTime)) {
					if (!this.overtimeHardBudget) {
						let time = this.secondsToTime(this.getCurrentTaskHardBudget);

						this.$notify({
							type: 'error',
							text: `The Hard-Budget of ${time.hours} hours and ${time.minutes} minutes is reached!`
						});

						this.getAudioOvertimeNotification.play();
					}

					this.overtimeHardBudget = true;
				} else {
					this.overtimeHardBudget = false;
				}

				if (this.isOvertimeBudget(this.getCurrentTaskSoftBudget, this.getCurrentTaskTrackedTime)) {
					if (!this.overtimeSoftBudget && !this.overtimeHardBudget) {
						let time = this.secondsToTime(this.getCurrentTaskSoftBudget);

						this.$notify({
							type: 'error',
							text: `The Soft-Budget of ${time.hours} hours and ${time.minutes} minutes is reached!`
						});
					}

					this.overtimeSoftBudget = true;
				} else {
					this.overtimeSoftBudget = false;
				}
			},
			changeTaskWorkflow(taskId) {
				this.$api.task.changeWorkflowTask({
					task_id: taskId,
					is_done: !this.getCurrentTask.done_by
				}).catch(err => {
					this.$notify({type: 'error', text: err.response.data.message});
				});
			},
			handleTaskDrop([taskId]) {
				this.createStartTimer(taskId);
			},
			handleCreateTimer() {
				this.createStartTimer();
			},
		},
	}
</script>

<style lang="scss">
    .current-timer-task-dropdown{
        display: flex;
    }
    .task_menue_modal_button-text{
        padding-right: 8px;
    }
    .timer-task-block .current-timer-task-dropdown-change-task{
        height: 16px;
        line-height: 16px;
    }
</style>
