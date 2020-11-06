<template>
    <div style="position: relative;">
        <div class="row">
            <div class="col-sm-12">
                <group-board-info v-if="showGroupBoardInfo">
                    <template slot="info">
                        <div class="info-setting__item">
                            <div class="info-setting__item-text">
                                {{ $t('period_uom') }}
                            </div>
                            <div class="info-setting__item-control">
                                <select :disabled="isLoading" class="form-control form-theme_reset" v-model="displayPeriodUom" >
                                    <option v-for="(period, index) in displayPeriodUomData" :value="period" :key="index">
                                        {{ period.alias }}
                                    </option>
                                </select>
                            </div>
                            <info-placeholder
                                :content="$t('info_popup.period')"
                                class="info-setting__item-info-placeholder"
                            ></info-placeholder>
                        </div>
                        <div class="info-setting__item" v-if="this.displayPeriodUom !== 'days'">
                            <div class="info-setting__item-text">
                                {{ $t('period_count') }}
                            </div>
                            <div class="info-setting__item-control">
                                <select :disabled="isLoading" class="form-control form-theme_reset" v-model="displayPeriodCount" >
                                    <option v-for="(period, index) in displayPeriodCountData" :value="period" :key="index">
                                        {{ period }}
                                    </option>
                                </select>
                            </div>
                            <info-placeholder
                                :content="$t('info_popup.period_count')"
                                class="info-setting__item-info-placeholder"
                            ></info-placeholder>
                        </div>
                        <div class="info-setting__item">
                            <div class="info-setting__item-text">
                                {{ $t('starting_day_of_the_week') }}
                            </div>
                            <div class="info-setting__item-control">
                                <select :disabled="isLoading" class="form-control form-theme_reset" v-model="getStartingDayOfWeek" >
                                    <option v-for="(day, index) in startingDayOfWeekData" :value="day.id" :key="index">
                                        {{ day.name | capitalize }}
                                    </option>
                                </select>
                            </div>
                            <info-placeholder
                                :content="$t('info_popup.starting_day')"
                                class="info-setting__item-info-placeholder"
                            ></info-placeholder>
                        </div>
                    </template>
                </group-board-info>
            </div>
        </div>
        <div class="tasks-calendar-wrapper row">
            <div class="col-lg-4 col-lg-push-8">
                <fixed-component>
                    <task-create
                            v-if="showTaskCreate"
                            :in-board="true"
                            :redirect="false">
                    </task-create>
                </fixed-component>
                <tasks-drag v-if="getTenantId"
                            :timers="timers"
                            :listlimit="listlimit"
                            :tasksWithoutTime="true"
                            :filterFor='"list"'
                            :currentTimerTask="currentTimerTask" />
            </div>
            <div class="col-lg-8 col-lg-pull-4">
                <calendar-view
                        :events="tasks"
                        :enableDragDrop="enableDragDrop"
                        :show-date="showDate"
                        :show-event-times="showEventTimes"
                        :time-format-options="timeFormatOptions"
                        :displayPeriodUom="displayPeriodUom.name"
                        :displayPeriodCount="displayPeriodCount"
                        :startingDayOfWeek="startingDayOfWeek"
                        @show-date-change="setShowDate"
                        @show-first-date="setFirstDate"
                        @show-last-date="setLastDate"
                        @drop-on-date="dropOnDate"
                        @click-event="showTaskDetails"
                        :locale="getUserProfile.language.iso_639_1"
                        class="theme-ocam holiday-us-traditional holiday-us-official">
                </calendar-view>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapGetters}         from 'vuex'
    import moment               from 'moment'
    import CalendarMathMixin    from "@mixins/CalendarMathMixin"
    import TimeMixin            from "@mixins/time"

    import CalendarView         from "./CalendarView"
    import TasksDrag            from '@views/partcials/TasksDrag/TasksDrag'
    import GroupBoardInfo       from '@views/partcials/GroupBoardInfo/GroupBoardInfo'
    import TaskCreate           from '@views/components/task/TaskCreate'
    import FixedComponent       from '@views/components/fixedComponent/FixedComponent'
    import infoPlaceholder      from '@views/components/infoPlaceholder/infoPlaceholder';

    require("vue-simple-calendar/dist/static/css/default.css");
    require("vue-simple-calendar/dist/static/css/holidays-us.css");

    export default {
        props: {
            timers:             { type: Array },
            listlimit:          { type: Number, default: 5},
            currentTimerTask:   { type: [Object,Boolean], default: null },
            showGroupBoardInfo: { type: Boolean, default: true },
            showTaskCreate:     { type: Boolean, default: true }
        },
        mixins: [
            CalendarMathMixin,
            TimeMixin
        ],
        data: function() {
            return {
                showDate: new Date(),
                firstDate: null,
                lastDate: null,

                isFetching: false,
                enableDragDrop: true,
                showEventTimes: true,
                timeFormatOptions: {hour: 'numeric', minute:'2-digit'},

                /*selects*/
                displayPeriodUom: { name: 'month', alias: this.$t('month') },
                displayPeriodCount: 1,
                startingDayOfWeek: 1,
                selectStartingDayOfWeek: 1,

                displayPeriodUomData: [
                    { name: 'month', alias: this.$t('month') },
                    { name: 'week', alias: this.$t('week') },
                    { name: 'year', alias: this.$t('year') },
                    { name: 'days', alias: this.$t('days') },
                ],
                displayPeriodCountData: [1,2,3],
                startingDayOfWeekValue: '',
                startingDayOfWeekData: [],
            }
        },
        components: {
            CalendarView,
            TasksDrag,
            GroupBoardInfo,
            TaskCreate,
            FixedComponent,
            infoPlaceholder
        },
        created() {
            this.createDayOfWeek();
        },
        computed: {
            ...mapGetters({
                getTenantId:        'user/getTenantId',
                getCurrentBoard:    'groups/getCurrentBoard',
                getUserProfile:     'user/getUserProfile',
                getPriorities:      'priorities/getPriorities',
                getSelectMembers:   'members/getSelectMembers'
            }),
            getStartingDayOfWeek: {
                get: function() {
                    return this.startingDayOfWeek - this.selectStartingDayOfWeek;
                },
                set: function(newValue) {
                    this.startingDayOfWeek = newValue + this.selectStartingDayOfWeek
                },
            },
            getDisplayFirstDate() {
                if (this.$refs.calendarView) {
                    return this.$refs.calendarView.displayFirstDate;
                }
            },
            tasks: {
                get () {
                    if (!this.firstDate && !this.lastDate) {
                        return;
                    }

                    let tasks = [];
                    let repeatTask = [];

                    if (this.getCurrentBoard && this.getCurrentBoard.tasks.length) {
                        tasks = this.getCurrentBoard.tasks;
                    } else {
                        tasks = this.$store.getters['groups/getTaskByIds'];
                    }

                    let calendarTasks = tasks.filter(task => {
                        if (this.getCurrentBoard && this.getCurrentBoard.hide_done_tasks) {
                            return !task.done_by && !task.draft;
                        }

                        return !task.draft
                            && !this.getPriorities.find(priority => priority.id === task.priority_id).is_invisible
                            && (!this.getSelectMembers.length || task.subscribers.task.some(taskSubscribers => this.getSelectMembers.some(selectMembers => selectMembers === taskSubscribers)))
                    }).map((item, index) => {
                        let endDate;

                        if (item.soft_budget !== '00:00' && item.soft_budget) {
                            endDate = this.getTime('soft_budget', item);
                        } else if (item.hard_budget !== '00:00' && item.hard_budget) {
                            endDate = this.getTime('hard_budget', item);
                        } else {
                            endDate = item.planned_deadline;
                        }

                        item.title              = item.name;
                        item.startDate          = this.toLocalTime(item.planned_deadline, "YYYY-MM-DD HH:mm:ss");
                        item.endDate            = this.toLocalTime(endDate, "YYYY-MM-DD HH:mm:ss");
                        let repeat_started_at   = this.toLocalTime(item.repeat_started_at, "YYYY-MM-DD");
                        let repeat_ended_at     = this.toLocalTime(item.repeat_ended_at, "YYYY-MM-DD");

                        /* add repeat task */
                        if (item.repeat_unit) {
                            let i = 1,
                                position = item.repeat_interval,
                                countRepeat,countRepeat2;

                            if (item.repeat_ended_at) {
                                let end = item.repeat_ended_at > this.lastDate ? this.lastDate : item.repeat_ended_at

                                countRepeat = this.diffDate(this.$moment(item.startDate, "YYYY-MM-DD"), end, item.repeat_unit + "s");
                            } else {

                                let start = null;

                                if (this.firstDate > item.startDate) {
                                    start = this.firstDate;
                                    countRepeat2 = this.diffDate(item.startDate, this.firstDate, "days");
                                    position = Math.ceil(countRepeat2 / item.repeat_interval) * item.repeat_interval;
                                } else {
                                    start = item.startDate;
                                }

                                countRepeat = this.diffDate(start, this.lastDate, "days");
                            }

                            while (i <= countRepeat) {
                                i++;

                                let task = Object.assign({}, item, {
                                    copy_task:   true,
                                    startDate:   this.$moment(item.startDate, "YYYY-MM-DD").add(position, item.repeat_unit + "s"),
                                    endDate:     this.$moment(item.endDate, "YYYY-MM-DD").add(position, item.repeat_unit + "s"),
                                });

                                if (this.diffDate(task.startDate, repeat_ended_at, item.repeat_unit + "s") < 0) {
                                    break;
                                }

                                position +=  item.repeat_interval;

                                if (repeat_started_at && this.diffDate(repeat_started_at, task.startDate, item.repeat_unit + "s") < 0) {
                                    continue;
                                }

                                repeatTask.push(task);
                            }
                        }

                        /* end add repeat task */
                        return item;
                    });

                    return [...calendarTasks, ...repeatTask];
                },
            }
        },
        methods: {
            createDayOfWeek() {
                this.startingDayOfWeekData = this.dayNames();
                this.startingDayOfWeekValue = this.startingDayOfWeekData[this.startingDayOfWeek];
            },
            onChange(value) {
                this.startingDayOfWeek = value.id;
            },
            userLocale() {
                return this.getDefaultBrowserLocale
            },
            dayNames() {
                return this.getFormattedWeekdayNames(this.userLocale, "long", this.selectStartingDayOfWeek).map((item, index) => {
                    return {
                        id: index,
                        name: this.$t(item.toLowerCase()),
                    }
                });
            },
            getTime(typeBudget, item) {

                if (!item.planned_deadline) {
                    return null
                }

                let budgetTime = item[typeBudget].split(':');

                return this.$moment(item.planned_deadline)
                    .add(Number(budgetTime[0]), 'hours')
                    .add(Number(budgetTime[1]), 'minutes')
                    .format('YYYY-MM-DD HH:mm:ss');
            },
            showTaskDetails(task) {
                 if (!this.handlePermissionByGroupId('read-task', task.originalEvent.group_id)) {
                     return this.sendNotifyPermissionInfo('read-task');
                 }

                this.$router.replace({query: {taskId: task.id}});
            },

            setShowDate(d) {
                this.showDate = d;
            },
            setFirstDate(d) {
                this.firstDate = this.toLocalTime(d, "YYYY-MM-DD")
            },
            setLastDate(d) {
                this.lastDate = this.toLocalTime(d, "YYYY-MM-DD")
            },
            getDeadlineUpdate: function (task, budget) {
console.log('budget ', budget);

                if(!this.isFetching) {
                    this.isFetching = true;

                    task = Object.assign({}, task.originalEvent, task);

                    if (budget) {
                        task.soft_budget = budget;
                        task.hard_budget = budget;
                    } else {
                        if (task.soft_budget === '00:00' ) {
                            task.soft_budget = '01:00';
                        }
                        if (task.hard_budget === '00:00' ) {
                            task.hard_budget = '01:00';
                        }
                    }

                    task.task_id            = task.id;
                    task.planned_deadline   = this.$moment(task.startDate, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm:ss");

                    this.$api.task.updateTask(task)
                        .catch((err) => {
                            if (err.response.status === 403) {
                                this.$notify({type:'error', text: err.response.data.message});
                            }
                        })
                        .finally(() => {
                            this.isFetching = false;
                        })
                }
            },

            dropOnDate(event, date, typeChange = null) {
                console.log('dropOnDate');
                if (typeChange === "days") {
                    let budget, nextDay, soft_budget;

                    if (this.minutesDiff(event.endDate, event.startDate) >= 1439) {
                        budget = "01:00"
                    }

                    if (budget) {
                        soft_budget = this.getTimeHours(budget)
                    }  else {
                        soft_budget = this.getTimeHours(event.originalEvent.soft_budget)
                    }

                    nextDay = date.getHours() + soft_budget;

                    if (nextDay === 24) {
                        budget = `${soft_budget - 1}:59`
                    }

                    event.startDate = this.toUTCTime(date, "YYYY-MM-DD HH:mm:ss");
                    event.endDate = this.toUTCTime(event.endDate, "YYYY-MM-DD HH:mm:ss");

                    return this.getDeadlineUpdate(event, budget);
                }

                if (typeChange === "allDay" && this.dayDiff(event.startDate, event.endDate) === 0)  {

                    event.startDate = this.toUTCTime(this.$moment(date, "YYYY-MM-DD HH:mm:ss").startOf('day').format('YYYY-MM-DD HH:mm:ss'));
                    event.endDate = this.toUTCTime(this.$moment(date, "YYYY-MM-DD HH:mm:ss").endOf('day').format('YYYY-MM-DD HH:mm:ss'));

                    return this.getDeadlineUpdate(event, '23:59');
                }

                let startDate;
                let endDate;

                event.startDate ? startDate = event.startDate : startDate = date;
                event.endDate ? endDate = event.endDate : endDate = date;

                const eLength = this.dayDiff(event.startDate, date);

                event.startDate = this.toUTCTime(this.addDays(startDate, eLength), "YYYY-MM-DD HH:mm:ss");
                event.endDate = this.toUTCTime(this.addDays(endDate, eLength), "YYYY-MM-DD HH:mm:ss");

                this.getDeadlineUpdate(event);
            }
        }
    }
</script>
<style lang="scss">
    .tasks-calendar__filter{
        background-color: #fff;
        margin-bottom: 10px;
        @media (max-width: 767px) {
            .field {

                margin-bottom: 10px;
            }
        }
    }

    .tasks-calendar-wrapper{
        .task-list-item, .task-create{
            .control-btns-wrapper{
                display: block !important;
                position: relative !important;
            }

        }
        .task-list-item{
            .task-title-wrapper{
                // border-bottom: 1px solid #d7d7d7;
            }
        }
        .task-create{
            .btn{
                padding: 6px 12px;
            }
            .control-btns{
                margin-top: 5px;
            }
        }
    }

    .tasks-calendar-item-drag{
        /*user-drag: element;*/
        /*-webkit-user-drag: element;*/
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
    .theme-ocam{
        .cv-header .periodLabel{
            display: block;
            text-align: center;
        }
        .cv-header,
        .cv-header-days{
            background-color: #fafafa;
        }
        .cv-week{
            min-height: 100px;
        }
        .cv-day{
            /*background-color: #fff;*/
            &.today{
                background-color: #f4f7ff;
            }
            &.outsideOfMonth{
                background-color: #fafafa;
            }
            &.draghover{
                box-shadow: inset 0 0 0.1em 0.15em #5078f2;
            }
        }
        .cv-event{
            border-color: #e1e1e1;
            background-color: #dae5ff;
            cursor: pointer;
        }
        .cv-header{
            position: relative;
        }
        .previousPeriod,
        .nextPeriod{
            background-color: #fff;
            color: #5078f2;
            position: absolute;
            top: 50%;
            margin-top: -13px;
            &:hover{
                background-color: #5078f2;
                color: #fff;
            }
        }
        .previousPeriod{
            left: 15px;
        }
        .nextPeriod{
            right: 15px;
        }
        .cv-calendar{
            padding: 15px;
            border-radius: 5px;
            background-color: #fff;
        }
    }

    .tasks-calendar-wrapper{
        .control-btns{
            display: flex !important;
        }
        .task-name{
            padding-right: inherit;
        }
        .task-list-item{
            border-radius: 5px;
        }
        .task-title-wrapper{
            width: 100%;
            padding-right: 0 !important;
        }
        .btn-settings{
            margin-left: 0 !important;
        }
        .task-create{
            height: auto;
        }
    }

</style>
