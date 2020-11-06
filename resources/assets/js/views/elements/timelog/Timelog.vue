<template>
    <div class="timelog-wrapper" :class="{'timelog-timer-opened' : !timerState}">
        <theme-modal-header class="timelog-header modal-header">
            <slot class="timelog-title modal-title">{{ $t("title_time_log_book") }}</slot>
            <theme-button-close class="btn-close-header">
                <i class="icon-close" @click="$emit('hide')">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-close">
                        </use>
                    </svg>
                </i>
            </theme-button-close>
        </theme-modal-header>
        <horizontal-datepicker ref="horizontalDatepicker"></horizontal-datepicker>
        <div class="timelog-body">
            <template>
                <div v-if="getLogs">
                    <ul class="timelog-list" >
                        <li
                            class="timelog-list-item"
                            v-for="(log, index) in getLogs"
                            :key="log.id"
                        >
                            <div class="timelog-list-item-element">

                                <span>{{ log.user.name }}</span>
                                <div class="timelog-link-secondary" :title="$t('change_date')">
                                    <timelog-calendar
                                        :dateLog="log"
                                        :loading="dateLogLoading"
                                        @update="dateLogUpdate"
                                    />
                                </div>

                                <template v-if="updateLogTimeModal === index">
                                    <div
                                        class="modal-time-log"
                                        style="top: 35px;"
                                        v-click-outside="logTimerModalOutside.bind(null, $event, log)"
                                    >

                                        <div class="modal-time-log__list">
                                            <div class="modal-time-log__item">
                                                <div class="modal-time-log__content">
                                                    <span class="modal-time-log__addon">
                                                           <i class="icon-clock">
                                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                xlink:href="#icon-clock">
                                                                </use>
                                                            </svg>
                                                            </i>
                                                    </span>

                                                    <input type="number" step="1" min="0" max="9999999" class="modal-time-log__input" v-model="log_value.time.item_hours"/>
                                                    <span class="modal-time-log__time-type">H</span>
                                                </div>
                                            </div>
                                            <div class="modal-time-log__item">
                                                <div class="modal-time-log__content">
                                                    <span class="modal-time-log__addon">
                                                             <i class="icon-clock">
                                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                xlink:href="#icon-clock">
                                                                </use>
                                                            </svg>
                                                            </i>
                                                    </span>
                                                    <input type="number" step="1" min="0" max="60" class="modal-time-log__input" v-model="log_value.time.item_minutes"/>
                                                    <span class="modal-time-log__time-type">M</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <span
                                    class="timelog-link-secondary"
                                    @click="handleEditLogTime(log, index)"
                                >
                                       <i class="icon-clock" :title="$t('edit_log_time')">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                xlink:href="#icon-clock">
                                                </use>
                                            </svg>
                                        </i>
                                    <time v-if="hasTime(log.timer.time)">&nbsp;{{ log.timer.time | getLoggedTime }}</time>
                                </span>

                                <span style="margin:0; position:absolute; right:5px; top:5px;">

                                    <i class="icon-trash" 
                                        :title="$t('title_remove_log')"
                                        @click="handleDeleteLog(log, index)"
                                    >
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-trash"></use>
                                        </svg>
                                    </i>

                                <i class="icon-pencil"
                                    :title="$t('title_edit_log')"
                                    @click="handleEditLogBody(index, log.timer.comment)" >
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-pencil">
                                        </use>
                                    </svg>
                                </i>

                                </span>
                            </div>
                            <div class="timelog-list-item-element timelog-board-info">
                                <template v-if="log.task_id">
                                    <span>{{ $t("board") }}
                                        :
                                        <a
                                            href="javascript:void(0)"
                                            class="timelog-link"
                                            @click="selectBoardTask(log)"
                                        >
                                            {{ getLogBoardName(log) }}
                                        </a>
                                    </span>
                                    <span>
                                        {{ $t("task") }} :
                                        <a href="javascript:void(0)" class="timelog-link" @click="selectBoardTask(log)" >
                                            {{ getLogTaskName(log) }}
                                        </a>
                                    </span>
                                </template>
                                <template v-else>
                                    <a href="javascript:void(0)" class="timelog-link" @click="selectBoardTask(log)">
                                        {{ $t("select_task") }}
                                    </a>
                                </template>
                            </div>
                            <div class="timelog-list-item-element" style="padding-right:0;">
                                <p style="margin-bottom:0;">
                                    <template v-if="editLogBodyNumber === index">
                                        <TimeLogCommentEditor
                                                class="timelog-list-item-textarea"
                                                v-model="editLogBodyDraft"
                                        />
                                        <template v-if="editLogBodyIsProcessing">
                                                <span class="logs-spinner-wrapper">
                                                    <i class="icon-circle-loading">
                                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        xlink:href="#icon-circle-loading">
                                                        </use>
                                                    </svg>
                                                    </i>
                                                </span>
                                        </template>
                                        <template v-else>
                                            <span class="badge1" @click="updateLogComment(log, index)">{{ $t("ok") }}</span>
                                            <span class="badge1" @click="editLogBodyCancel">{{ $t("cancel") }}</span>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <span class="timelog-list-item-comment" v-html="stringToHTML(log.timer.comment)"></span>
                                    </template>
                                </p>
                            <div >
                                <button class="time-log-change-status" @click="changeStatus(log)">
                                    <i class="icon-euro"
                                       :class="{ not_billable: is_not_billable(log)}"
                                       :title="is_not_billable(log) ? $t('not_billable') : $t('billable')"
                                    >
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xlink:href="#icon-euro">
                                            </use>
                                        </svg>
                                    </i>
                                </button>
                            </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="text-center" v-if="!getLogs">
                    {{ this.$t('no_time_entries_for_this_day') }}
                </div>

            </template>
        </div>
            <div class="timelog-footer">
                <div class="timelog-add-manual-time">
                    <theme-button-success @click.native="createLogRequest" class="timelog-add-manual-time__button">
                        <i class="icon-plus timelog-add-manual-time__icon">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-plus">
                            </use>
                        </svg>
                        </i>
                        {{ $t("add_manual_time") }}
                    </theme-button-success>
                </div>
                <div class="timelog-all-time-text" v-if="getLogs && getAllDateLogs" >{{ $t("overall_time") }}: {{ getAllDateLogs }} </div>
            </div>
    </div>
</template>
<script>
    import clickOutside             from 'v-click-outside';
    import DatePicker               from 'vue-bootstrap-datetimepicker';
    import find                     from "@helpers/findInGroups";
    import {mapGetters}             from 'vuex';

    import commentMixin             from "@mixins/comment";
    import TimelogCalendar          from '@views/elements/timelog/TimelogCalendar';
    import HorizontalDatepicker     from '@views/elements/timelog/HorizontalDatepicker';
    import TimeLogCommentEditor     from '@views/partcials/TimeLogCommentEditor/TimeLogCommentEditor';

    import ThemeButtonSuccess       from '@views/layouts/theme/buttons/ThemeButtonSuccess'
    import ThemeModalHeader         from '@views/layouts/theme/ThemeModalHeader'
    import ThemeButtonClose         from "@views/layouts/theme/buttons/ThemeButtonClose";

    const getDateInFormat = (timer) => {
        let h,i,s = 0;

        let allSeconds = timer.h * 60 * 60 + timer.i * 60 + timer.s;

        h = Math.floor(allSeconds / 3600);
        i = Math.floor((allSeconds / 60) % 60);
        s = allSeconds % 60;

        return {
            h,
            i,
            s
        }
    };

    export default {
        props:{
            groupList: Array,
            timerState: Boolean
        },
        data(){
            return{
                canClose: false,
                logs: [],
                log_value: {
                    time: {
                        item_hours: 0,
                        item_minutes: 0
                    }
                },
                old_log_value: {
                    time: {
                        item_hours: 0,
                        item_minutes: 0
                    }
                },
                editLogBodyIsProcessing: false,
                editLogBodyDraft: '',
                editProcessing: false,
                isProcessingBudget: false,
                editLogBodyNumber: null,
                updateLogTimeModal: null,
                deleteLogIsProcessing: false,
                getLogsIsProcessing: false,
                dateLogLoading: false,
                calendarDay: '',
                billing_statuses: window.Laravel.billing_statuses,
                isProcessingStatuses: false,
                dayLog: '',
                currentLog: {},
                oldLog: {}
            }
        },
        created() {
            this.$api.log.getLogs().catch((err) => {
                this.$notify({type:'error', text: err.response.data.message});
            })
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        components:{
            HorizontalDatepicker,
            DatePicker,
            TimelogCalendar,
            ThemeButtonSuccess,
            ThemeModalHeader,
            ThemeButtonClose,
            TimeLogCommentEditor,
        },
        mixins: [
            commentMixin
        ],
        computed: {
            ...mapGetters({
                groups:     'groups/getStateGroups',
            }),
            getAllDateLogs () {

                let resultLogs = {};

                this.getLogs.map(item => {
                    if(Object.keys(resultLogs).length === 0) {
                        resultLogs = Object.assign({}, item.timer.time);
                    } else {
                        for(let value in resultLogs) {
                            resultLogs[value] += item.timer.time[value]
                        }
                    }
                });

                resultLogs = getDateInFormat(resultLogs);

                let result = '';

                if(resultLogs.h) {
                    result += resultLogs.h + ' h '
                }
                if(resultLogs.i) {
                    result += resultLogs.i + ' m '
                }

                return result
            },
            getLogs: {
                get () {
                    if (this.getLogsIsProcessing) {
                        return false;
                    }

                    return this.$store.getters['timers/getLogsByCurrentDate']
                }
            },
        },
        methods:{
            hasTime(time) {
                return time.h || time.i || time.s;
            },
            is_not_billable (log) {
                let status = this.billing_statuses.find(item => item.id === log.timer.billing_status_id);

                if(status) {
                    if (status.name === 'Not Billable') {
                        return true
                    }
                }
            },
            getLogTaskName({group_id, board_id, task_id}) {
                let taskName;

                find.search(this.groups, {group_id, board_id, task_id}, 'task', (tasks, task, index) => {
                    taskName = task.name
                });

                return taskName
            },
            getLogBoardName({group_id, board_id}) {
                let boardName;

                find.search(this.groups, {group_id, board_id}, 'board', (boards, board, index) => {
                    boardName = board.name
                });

                return boardName
            },
            changeStatus(log) {
                if (this.isLoading) {
                    return
                }

                let billingStatus = this.billing_statuses.find(item => item.name === "Not Billable");;

                if (billingStatus.id === log.timer.billing_status_id) {
                    billingStatus = this.billing_statuses.find(item => item.name === "Unbilled");
                }

                if (!log.timer.timer_billing_id) {
                    return this.$notify({type:'error', text: this.$t('need_create_timer')});
                }

                let data = {
                    timerBillingId:     log.timer.timer_billing_id,
                    billingStatusId:    billingStatus.id
                };

                this.$api.billing.updateStatus(data).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                })
            },
            getLogTimeFormat(timer) {
                return `${timer.h}:${timer.i}:${timer.s}`;
            },
            dateLogUpdate (dateLog, oldDateLog) {
                if(this.isLoading) {
                    return
                }

                let data = {
                    taskId:     dateLog.task_id,
                    logDate:    dateLog.end_time,
                    time:       this.getLogTimeFormat(dateLog.time),
                    id:         dateLog.id
                };

                if (oldDateLog.timer.comment) {
                    data.comment = oldDateLog.timer.comment
                }

                this.$api.log.updateLog(data, oldDateLog).then(() => {
                    this.$refs.horizontalDatepicker.setCurrentDate(dateLog.end_time);
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            createLogRequest () {
                let loggedDate = `${this.$store.getters['timers/getCurrentDate']} ${this.$moment().format('HH:mm:ss')}`;

                this.$api.log.createLog({
                    time: '0:0:0',
                    logDate: this.toUTCTime(loggedDate)
                }).then((res)=> {
                    this.$event.$emit('task-detail-create-comment', res.log);
                    this.selectBoardTask(res.log);
                }).catch((err)=> {
                    if (err.response.status === 422) {
                        if (err.response.errors) {
                            handleErrors(err.response.errors, this.errors, self, true);
                        }
                    } else {
                        self.$notify({type:'error', text: err.response.data.message});
                    }
                })
            },
            selectBoardTask(log, event = null) {
                this.$modal.show('choose-modal', {type: "task", permission: "time-tracking", callback: (task) => {
                    this.$api.log.changeLogTask(log.id, task.id).catch((err) => {
                        this.$notify({type:'error', text: err.response.data.message});
                    })
                }});
            },
            editLogBodyCancel() {
                this.editLogBodyNumber = null;
                this.editLogBodyDraft = '';
            },
            logTimerModalOutside($event, log) {
                if (this.canClose) {
                    this.canClose = false;
                    this.updateLogComment(log);
                    this.updateLogTimeModal = null;
                }
            },
            updateLogComment(log) {
                let time    = this.getLogTimeInFormat(log);
                let comment = log.timer.comment;

                if (this.isLoading) {
                    return false;
                }

                if (this.editLogBodyDraft && this.editLogBodyDraft.length > 255) {
                    return this.$notify({type:'error', text: this.$t('comment_must_be_no_more_then_255_char')});
                }

                if (!this.editLogBodyDraft && this.editLogBodyNumber) {
                    return this.$notify({type:'error', text: this.$t('not_valid_input')});
                }

                // if (this.editLogBodyDraft.length > 0) {
                    comment = this.editLogBodyDraft;
                // }

                this.$api.log.updateLog({
                    taskId:     log.task_id,
                    time:       time,
                    logDate:    log.timer.end_time,
                    comment:    comment,
                    id:         log.id
                }, log).then(res => {

                    let payload = {
                        id:         res.log.id,
                        comment:    res.log.timer.comment,
                        time:       res.log.timer.time,
                        header:     res.log.header,
                    };

                    let minutes = (res.log.timer.time.h * 60) + res.log.timer.time.i;
                    let oldMinutes = (this.old_log_value.time.item_hours * 60) + this.old_log_value.time.item_minutes;

                    let trackedTime = minutes - oldMinutes;

                    let trackedTask = {
                        logged_time: trackedTime,
                        group_id: res.log.group_id,
                        board_id: res.log.board_id,
                        task_id: res.log.task_id
                    };

                    //this.$store.dispatch('groups/incrementLoggedTimerInTask', trackedTask);
                    this.$store.dispatch('timers/changeLogCommentAndTime', payload);
                    this.$store.dispatch('timers/changeTimerTimeAndComment', {id: res.log.timer.id, time: res.log.timer.time});

                    this.$event.$emit('task-detail-update-comment', null, res.log);
                    this.editLogBodyCancel();
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                })
            },
            getLogTimeInFormat(log = {}) {
                if (this.log_value.time.item_hours > 0 || this.log_value.time.item_minutes > 0) {
                    return this.log_value.time.item_hours + ':' + this.log_value.time.item_minutes + ':00';
                }

                let time = this.getLogTime(log);

                if (time.h || time.i || time.s) {
                    return time.h + ':' + time.i + ':' + time.s;
                }

                return '00:00:00';
            },
            getLogTime(log) {
                if (log.timer !== undefined) {
                    return log.timer.time
                }

                return null;
            },
            handleEditLogTime(log, index) {

                if (this.updateLogTimeModal !== null) {
                    return;
                }

                let time = this.getLogTime(log);

                let hours   = time.h,
                    minutes = time.i;

                if (time.m) {
                    hours += time.m * 730;
                }

                if (time.d) {
                    hours += time.d * 24;
                }

                this.old_log_value.time.item_hours =    hours;
                this.old_log_value.time.item_minutes =  minutes;

                this.updateLogTimeModal =           index;

                this.log_value.time.item_hours =    hours;
                this.log_value.time.item_minutes =  minutes;
                this.currentLog = log;
                setTimeout(() => {
                    this.canClose = true;
                }, 100);
            },
            handleEditLogBody(index, body) {
                this.editLogBodyNumber = index;
                this.editLogBodyDraft = body;
            },
            handleDeleteLog(log, index) {

                if (this.isLoading) {
                    return false;
                }

                this.$modal.show('confirm-modal', {
                    title: this.$t('delete_this_log'),
                    body: this.$t('are_you_sure_you_want_to_delete_this_log'),
                    confirmCallback: () => {
                        this.$api.log.removeLog(log)
                            .then(res => {
                                this.clearDraftLogComment(index);
                            }).catch(err => {
                                this.$notify({type:'error', text: this.$t('failed_to_remove_log')});
                            })
                    },
                });
            },
            clearDraftLogComment(index) {
                if (this.editLogBodyNumber === index) {
                    this.editLogBodyCancel();
                }
            },
            // openBoard(boardId) {
            //     this.$modal.show('board-setting-modal', { boardId: boardId })
            // },
        },
        filters: {
            getLoggedTime (time) {
                if (time.h || time.i || time.s) {
                    return time.h + ' h : ' + time.i + ' m : ' + time.s + ' s';
                }
            },
        }

    }
</script>

<style scoped lang="scss">
    .timelog-header {
        font-weight: 700;
        font-size: 20px;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
    }
    .timelog-list-item-textarea{
        display: block;
        margin-bottom: 10px;
    }
    .timelog-list-item-element {
        display: flex;
        flex-direction:column;
        flex-wrap: wrap;
        align-items:flex-start;
        padding-right:10px;
        .modal-time-log {
            position: absolute;
        }
            span {
            margin-right:15px;
            margin-bottom:5px;
                .icon-clock {
                    .icon {
                        width:13px;
                        height:13px;
                        position:relative;
                        top:1px;
                        &:hover {
                        fill:#376aa7;
                        }
                    }
                }
            }

            .icon-trash {
                padding:3px;
                    .icon {
                    width:15px;
                    height:15px;
                    cursor:pointer;
                    &:hover {
                        fill:#f71919;
                    }
            }
        }
            .icon-pencil {
                padding:3px;
                    .icon {
                    width:15px;
                    height:15px;
                    cursor:pointer;
                    &:hover {
                        fill:#62a8ea;
                    }
            }
        }

            .icon-euro {
                .icon {
                     width:13px;
                    height:13px;
                    cursor:pointer;
                        &:hover {
                            fill:#62a8ea;
                        }
                }
             }

        .logs-spinner-wrapper {
            .icon-circle-loading {
                .icon {
                    width:20px;
                    height:20px;
                }
            }    
        }
    }
    .timelog-list-item-element:last-child {
        flex-direction:row;
        justify-content:space-between;
}   
     .timelog-list-item-element .badge1 {
         background-color:#5d98de;
         color:#fff;
             &:hover {
                background-color:#376aa7;
         }
     }

    .timelog-board-info {
        justify-content: flex-start !important;
}
    .timelog-all-time-text{
        font-size: 10px;
        color: #333;
        text-transform: uppercase;
    }
    .timelog-add-manual-time{
        color: #fff;
    }
    .timelog-add-manual-time__button{
        text-transform: uppercase;
        /*padding: 0;*/
        /*background: #376aa7;*/
        /*color: #fff;*/
        padding: 2px 4px;
        border-radius: 2px;
        border-width: 1px;
        border-style: solid;
        font-size: 11px;
        line-height: 20px;
            /*&:hover{*/
                /*color: #376aa7;*/
                /*background-color:#fff;*/
            /*}*/
    }
    .timelog-add-manual-time__icon{
        .icon {
            width:12px;
            height: 12px;
            fill:#fff;
            position:relative;
            top:1px;
        }
    }
    .time-log-change-status{
        background-color: transparent;
        border: none;
        font-size: 16px;
    }
    .not_billable {
        color: red;
        fill: red;
    }

    .timelog-link-secondary {
        display: inline-block;
    }

        @media (min-width: 420px) {
            .timelog-list-item-element {
                flex-direction:row;
                align-items:center;
                justify-content: space-between;
                padding-right:50px;
                }
                //     .timelog-list-item-element:last-child {
                //         flex-direction:row;
                //         justify-content:space-between;
                // }

                .timelog-all-time-text {
                font-size:12px;
            }
               .timelog-add-manual-time__button {
               font-size:12px;
           }
         }
</style>
