<template>
    <div class="timer-list-item">
        <div class="row">
            <div class="itemTaskDetails">
                <div class="this-timer-task-dropdown dropdown">
                    <div id="task_menue_modal_button" @click="showChangeCurrentTimerTaskModal" class="task-headline demo_button">
                        <span class="task-headline">{{ getCurrentTaskName }}</span>
                    </div>
                </div>

                <div class="comment-dropdown" v-click-outside="hideTimerComment">
                    <div id="comment_menue_multiline_button" class="task-subline demo_button dropdown-toggle timer-item-comment over-ellipses" style="cursor:pointer;" @click="showTimerComment = !showTimerComment">
                        {{ !getCurrentTimerComment ? $t('enter_comment') : getCurrentTimerComment }}
                    </div>
                </div>
            </div>

            <div class="itemTimerControlHolder">
                <div class="itemTimerTimeDetails clearfix" >
                    <div class="timer-item-time" :class="{ timer_paused: getCurrentTimerStatus !== 'started'}">
                        {{ getCurrentTimerTime | timerToStringFormat(false) }}
                    </div>
                    <div class="timer-item-interval" v-html="getCurrentTimerInterval"></div>
                </div>

                <div class="task-item-time-controller">
                    <button type="button" class="btn btn-lg btn-timer-start" :title="$t('play')" @click="startTimer(currentTimer.id)" v-if="getCurrentTimerStatus === 'stopped'">
                        <i class="icon-start-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-start-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-start" :title="$t('play')" @click="continueTimer(currentTimer.id)" v-if="getCurrentTimerStatus === 'paused'">
                        <i class="icon-start-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-start-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-pause" :title="$t('pause')" @click="pauseTimer(currentTimer.id)" v-if="getCurrentTimerStatus === 'started'">
                        <i class="icon-pause-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-pause-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn-timer-stop" :title="$t('stop')" @click="stopTimer(currentTimer.id)" >
                        <i class="icon-stop-timer">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                     xlink:href="#icon-stop-timer">
                                </use>
                            </svg>
                        </i>
                    </button>

                    <button type="button" class="btn btn-lg btn_timer_remove" :title="$t('title_remove_timer')" :disabled="isLoading" @click="confirmRemoveTimer(currentTimer)" v-if="getCurrentTimerStatus !== 'started'">
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

        <div class="row">
            <timer-comment-dropdown :task_id="getCurrentTask.id" :timer-id="getCurrentTimerId" :timer-comment="getCurrentTimerComment" :show.sync="showTimerComment" :rows="1" />
        </div>
    </div>
</template>

<script>
    import {mapGetters}             from 'vuex'
    import clickOutside             from 'v-click-outside'

    import timerMixin               from "@mixins/timer";
    import find                     from "@helpers/findInGroups";
    import TimerCommentDropdown     from "@views/components/timer/TimerCommentDropdown";
    import { getLoggedTime }        from '@helpers/time'

    export default {
        name: "timer-list-item",
        props: {
            timer: {
                type: Object,
                default: () => {
                    return {};
                }
            }
        },
        data() {
            return {
                showTimerComment: false,
            }
        },
        computed: {
            ...mapGetters({
                groups: 'groups/getStateGroups',
                timers: 'timers/getTimers',
            }),
            currentTimer() {
                return this.timer;
            },
            getCurrentTimerId() {
                return this.currentTimer.id;
            },
            getCurrentTimerComment() {
                return this.currentTimer.comment;
            },
            getCurrentTimerStatus() {
                return this.currentTimer.status;
            },
            getCurrentTimerTime() {
                return this.currentTimer.time;
            },
            getCurrentTimerInterval() {
                let interval = '';

                if (this.currentTimer.start_time) {
                    interval = `${this.toLocalTime(this.currentTimer.start_time, 'YYYY-MM-DD')} <span class="timer-item-interval_hours">${this.toLocalTime(this.currentTimer.start_time, 'HH:mm')}</span><br />`;
                }

                if (this.currentTimer.end_time) {
                    interval += `${this.toLocalTime(this.currentTimer.end_time, 'YYYY-MM-DD')} <span class="timer-item-interval_hours">${this.toLocalTime(this.currentTimer.end_time, 'HH:mm')}</span><br />`;
                }

                return interval;
            },
            getCurrentRelations() {
                if (this.groups.length) {
                    let result = {
                        task:  {},
                        board: {},
                        group: {},
                    };

                    find.search(this.groups, this.currentTimer, 'task', (tasks, task, index, board, group) => {
                        result.task = task;
                        result.board = board;
                        result.group = group;
                    });

                    return result;
                }

                return {};
            },
            getCurrentTask () {
                return this.getCurrentRelations.task || null
            },
            getCurrentTaskName () {
                return this.getCurrentTask.name || this.$t('select_task');
            },
        },
        components: {
            TimerCommentDropdown
        },
        mixins: [
            timerMixin
        ],
        directives: {
            'clickOutside': clickOutside.directive
        },
        methods: {
            confirmRemoveTimer(timer) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('are_you_sure_you_want_to_delete_this_timer'),
                    body: `${this.getCurrentTask.name ? this.$t('task') + ': ' + this.getCurrentTask.name : ""} ${this.$t('task')}: ${getLoggedTime(timer.time)}`,
                    confirmCallback: () => {
                      this.removeTimer(timer.id);
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
    .timer-list-item {
        // width: 580px;
        border-bottom: solid 1px #d2caca;
        position: relative;

            .row {
                display:flex;
                margin:5px 0;

                .itemTaskDetails {
                    flex:1;
                     width: 135px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    margin-right: 10px;
                        .task-headline {
                                overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;
                                width: 100%;
                    }
                }
                .itemTimerTimeDetails {
                    display:flex;
                    flex-direction:column;
                    align-items: flex-end;
                    margin-right: 6px;
                }
            }

        @media (min-width: 576px) {

        .itemTimerControlHolder {
            display:flex;
            align-items:center;

            .itemTimerTimeDetails {
            margin-right:15px;
        }
        }

    }
        @media (min-width: 768px) {


        .timer-item-interval,
        .timer-item-time {
            float: none;
            text-align: right;
                }

         }


        .timer-item-time {
            padding-right: 0;
        }

        .itemTimerTimeDetails {
            padding-left: 0;
        }

        #timer-comment-dropdown {
            position: relative;
            left: 0;
            margin: 0 15px;
            width: calc(100% - 30px);
        }
    }
</style>
