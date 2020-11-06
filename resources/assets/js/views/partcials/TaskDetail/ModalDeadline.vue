<!--TODO need optimization short-->
<template>
    <div
        class="modal-deadline"
        :class="{'last-task-modal' : lastTask, 'modal-deadline_short' : short, 'details-modal': !short}"
    >

        <button type="button" class="btn btn-lg btn_details-modal_close">
            <i class="icon-close" @click="$emit('hide')">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-close"></use></svg>
            </i>
        </button>

        <div v-if="!short">
            <div class="row details-modal__item">
                <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                    {{ $t("todo") }} <small> {{ $t("your_date") }}</small>
                </label>

                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="icon-clock specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-clock"></use></svg>
                            </i>
                        </span>

                        <date-picker
                            v-model="timestamp.todo"
                            :config="Object.assign(datetimeOptions.date, { locale: getUserProfile.language.iso_639_1 })"
                        />
                    </div>
                </div>
            </div>
            <div class="row details-modal__item">
                <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                    {{ $t("time") }} <small>{{ $t("your_time") }}</small>
                </label>

                <div class="col-sm-8">
                    <div class="input-group datetimepicker_sm">
                        <span class="input-group-addon">
                            <i class="icon-clock specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-clock"></use></svg>
                            </i>
                        </span>

                        <date-picker
                            v-model="timestamp.time"
                            :config="Object.assign(datetimeOptions.time, { locale: getUserProfile.language.iso_639_1 })"
                        />
                    </div>
                </div>
            </div>
            <div class="row details-modal__item">
                <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                    {{ $t("deadline") }} <small> {{ $t("for_all_users") }} </small>
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="icon-clock specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-clock"></use></svg>
                            </i>
                        </span>

                        <date-picker
                            v-model="timestamp.deadline"
                            :config="Object.assign(datetimeOptions.date, { locale: getUserProfile.language.iso_639_1 })"
                        />
                    </div>
                </div>
            </div>

            <hr style="margin-top:10px;margin-bottom:10px;">

            <div class="row details-modal__item">
                <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                    {{ $t("repeat_unit") }} <small> {{ $t("for_all_users") }}</small>
                </label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="icon-refresh specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-refresh">
                                    </use>
                                </svg>
                            </i>
                        </span>
                        <select
                            v-model="repeat.repeat_unit"
                            class="form-control"
                        >
                            <option
                                v-for="item in repeatUnitList"
                                :key="item.label"
                                :value="item.value"
                            >
                                {{ item.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <template v-if="repeat.repeat_unit">
                <div class="row details-modal__item">
                    <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                        {{ $t("repeat_interval") }} <small> {{ $t("for_all_users") }}</small>
                    </label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="number" class="form-control" v-model="repeat.repeat_interval"/>
                            <span class="input-group-addon">
                            <i class="fa fa-hourglass-end" aria-hidden="true"></i>
                        </span>
                        </div>
                    </div>
                </div>

                <div class="row details-modal__item">
                    <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                        {{ $t("repeat_started_at") }} <small> {{ $t("for_all_users") }}</small>
                    </label>

                    <div class="col-sm-8">
                        <div class="input-group">
                            <date-picker
                                v-model="repeat.repeat_started_at"
                                :config="Object.assign(datetimeOptions.repeat, { locale: getUserProfile.language.iso_639_1 })"
                            />
                            <span class="input-group-addon">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row details-modal__item">
                    <label class="col-sm-4 col-form-label text-align-left details-modal__label">
                        {{ $t("repeat_ended_at") }} <small> {{ $t("for_all_users") }}</small>
                    </label>

                    <div class="col-sm-8">
                        <div class="input-group">
                            <date-picker
                                v-model="repeat.repeat_ended_at"
                                ref="repeatEndedAt"
                                :config="Object.assign(datetimeOptions.repeat, { locale: getUserProfile.language.iso_639_1 })"
                            />
                            <span class="input-group-addon">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div v-else class="datapicker-with-time-add">
            <div class="time-add" v-if="type === 'planned_deadline'">
                <div
                    v-for="item in customData"
                    :key="item.label"
                    class="time-add__row"
                >
                    <div class="time-add__list">
                        <div
                            v-for="index in item.loop"
                            v-if="index"
                            :key="index"
                            class="time-add__item"
                        >
                            <button
                                class="time-add__button"
                                @click="addTime(index, item.sign)"
                                v-html="`+${index}${item.label}`"
                            >
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <template v-if="type === 'planned_deadline'">
                <date-picker
                    v-model="timestamp.todo"
                    :config="Object.assign(datetimeOptions.datetime, { locale: getUserProfile.language.iso_639_1 } )"
                    @dp-change="handleUpdate"
                />
            </template>

            <template v-if="type === 'deadline'">
                <date-picker
                    v-model="timestamp.deadline"
                    :config="Object.assign(datetimeOptions.datetimeInline, { locale: getUserProfile.language.iso_639_1 })"
                />
            </template>

        </div>

        <div v-if="!short" class="deadline-button">

            <theme-button-warning
                type="button"
                class="btn"
                @click.native="$emit('hide')"
            >{{ $t('close') }}</theme-button-warning>

            <theme-button-success
                type="button"
                class="btn btn-update"
                @click.prevent.native="handleUpdate"
            >{{ $t('save') }}</theme-button-success>
        </div>

    </div>
</template>

<script>
    import { mapGetters }   from 'vuex'
    import moment           from 'moment'
    import datePicker       from 'vue-bootstrap-datetimepicker'
    import clickOutside     from 'v-click-outside'

	import ThemeButtonSuccess   from "@assets/js/views/layouts/theme/buttons/ThemeButtonSuccess";
	import ThemeButtonWarning   from "@assets/js/views/layouts/theme/buttons/ThemeButtonWarning";

    import task             from '@helpers/task'

    import ContentLoading   from '@views/components/ContentLoading'
    import {secondsToHHMM} from "@helpers/time";

    export default {
        name: "task-modal-deadline",
		components: {
			datePicker,
			ContentLoading,
			ThemeButtonSuccess,
			ThemeButtonWarning
		},
        props: {
            task: {
                type: Object,
            },
            type: {
              type: String,
              default: 'planned_deadline'
            },
            lastTask: {
                type: Boolean,
                default: false
            },
            short:{
                type: Boolean,
                default: false
            }
        },
		directives: {
			'clickOutside': clickOutside.directive
		},
        data() {
            const defaultDate = this.$moment().startOf('day');

            return {
                customData: [
                    {
                        loop: 6,
                        sign: 'h',
                        label: 'h'
                    },
                    {
                        loop: 6,
                        sign: 'd',
                        label: 'D'
                    },
                    {
                        loop: 4,
                        sign: 'w',
                        label: 'W'
                    },
                ],
                timestamp: {
                    deadline:           null,
                    time:               null,
                    todo:               null,
                },
                earlyTimestamp: {
                    time:               null,
                    todo:               null,
                },
                repeat: {
                    repeat_unit:        null,
                    repeat_interval:    null,
                    repeat_started_at:  null,
                    repeat_ended_at:    null,
                },
                datetimeOptions: {
                    datetime: {
                        format:             'DD.MM.YYYY HH:mm',
                        toolbarPlacement:   'bottom',
                        showTodayButton:    false,
                        showClose:          false,
                        showClear:          true,
                        inline:             true,
                        defaultDate,
                    },
                    date: {
                        format:             'DD.MM.YYYY',
                        toolbarPlacement:   'bottom',
                        showTodayButton:    true,
                        showClose:          true,
                        showClear:          true,
                        defaultDate,
                    },
                    datetimeInline: {
                        format:             'DD.MM.YYYY',
                        toolbarPlacement:   'bottom',
                        showTodayButton:    false,
                        showClose:          false,
                        showClear:          true,
                        inline:             true,
                        defaultDate,
                    },
                    time: {
                        format:             'HH:mm',
                        toolbarPlacement:   'bottom',
                        showTodayButton:    true,
                        showClose:          true,
                        showClear:          true,
                        defaultDate,
                    },
                    repeat: {
                        format:             'DD.MM.YYYY',
                        toolbarPlacement:   'bottom',
                        showTodayButton:    true,
                        showClose:          true,
                        showClear:          true,
                        minDate:            defaultDate,
                        defaultDate,
                    },
                },
                styleLoading: {
                    'absolute': true,
                    'autosize': true
                },
                repeatUnitList: [
                    { value: null,    label: window.app.$t('calendar_list.none') },
                    { value: 'day',   label: window.app.$t('calendar_list.day') },
                    { value: 'week',  label: window.app.$t('calendar_list.week') },
                    { value: 'month', label: window.app.$t('calendar_list.month') },
                    { value: 'year',  label: window.app.$t('calendar_list.year') },
                ],
                isFetching: false,
                format: {
                    ymdhms: 'YYYY-MM-DD HH:mm:ss',
                    dmyhms: 'DD.MM.YYYY HH:mm:ss',
                }
            }
        },
        computed: {
            ...mapGetters({
                getOwner:   'members/getOwner',
                getUserId:  'user/getUserId',
				getUserProfile: 'user/getUserProfile',
            })
        },
        mounted() {
            this.setDataComponent();
        },
        methods: {
            setDataComponent() {
                let options = {
                    isUtc: true,
                    toLocal: !this.short
                };
                const {
                    deadline,
                    planned_deadline,
                    repeat_started_at,
                    repeat_ended_at,
                    repeat_unit,
                    repeat_interval
                } = this.task;
                const { ymdhms, dmyhms } = this.format;

                this.timestamp.deadline         = this.getDateTime(deadline,         ymdhms, dmyhms, options);
                this.timestamp.todo             = this.getDateTime(planned_deadline, ymdhms, "DD.MM.YYYY", options);
                this.timestamp.time             = this.getDateTime(planned_deadline, ymdhms, "HH:mm", options);

                this.repeat.repeat_started_at   = this.getDateTime(repeat_started_at, ymdhms, dmyhms, options);
                this.repeat.repeat_ended_at     = this.getDateTime(repeat_ended_at,   ymdhms, dmyhms, options);

                this.repeat.repeat_unit         = repeat_unit;
                this.repeat.repeat_interval     = repeat_interval || 1;

                this.earlyTimestamp.todo        = this.timestamp.todo;
                this.earlyTimestamp.time        = this.timestamp.time;

                if (this.timestamp.time === '00:00') {
                    this.timestamp.time         = null;
                    this.earlyTimestamp.time    = null;
                }
            },
            addTime(time, period) {
                switch(period) {
                    case 'h':
                        time = time * 60;
                        break;
                    case 'd':
                        time = time * 1440;
                        break;
                    case 'w':
                        time = time * 10080;
                        break;
                }

                this.timestamp.todo = this.$moment().add(time, 'minutes').format(this.format.dmyhms);
                this.$notify({type:'info', text: this.timestamp.todo});
            },
            handleUpdate() {
                const { creator_id, group_id, id } = this.task;
                if (creator_id !== this.getUserId && !this.handlePermissionByGroupId('update-task', group_id)) {
                    return this.sendNotifyPermissionInfo('update-task');
                }

                if (!this.isDiffData()) {
                    return;
                }

                const { ymdhms, dmyhms } = this.format;
                const options = { toUtc: true };

                let form = {
                    task_id:            id,
                    deadline:           null,
                    planned_deadline:   null,

                    repeat_unit:        null,
                    repeat_interval:    null,
                    repeat_started_at:  null,
                    repeat_ended_at:    null,
                };

                const { deadline, todo, time } = this.timestamp;

                if (deadline) {
                    form.deadline = this.getDateTime(deadline, "DD.MM.YYYY", ymdhms, options);
                }


                if (todo || time && !this.short) {
                    let nowDate = this.$moment.utc().format("YYYY-MM-DD");
                    let nowTime = "00:00:00";

                    if (todo) {
                        nowDate = this.getDateTime(todo, dmyhms, "YYYY-MM-DD");

                        if (this.short) {
                            nowTime = this.getDateTime(todo, dmyhms, "HH:mm:ss");
                        }
                    }

                    if (time && !this.short) {
                        nowTime = this.getDateTime(time, "HH:mm:ss", "HH:mm:ss");
                    }

                    form.planned_deadline = this.getDateTime(`${nowDate} ${nowTime}`, ymdhms, ymdhms, options);

                    if (this.$route.name === "deadline") {
                        if (this.$route.params.period === "day") {
                            this.checkCurrentPeriod('day', form.planned_deadline);

                        } else if (this.$route.params.period === "week") {
                            this.checkCurrentPeriod('isoWeek', form.planned_deadline)
                        }
                    }
                }

                //calculate soft budget
                let softBudget = (moment(form.deadline) - moment(form.planned_deadline)) / 1000;
                form.soft_budget = secondsToHHMM(softBudget);

                const { repeat_unit, repeat_interval, repeat_started_at, repeat_ended_at } = this.repeat

                if (repeat_unit) {
                    form.repeat_unit        = repeat_unit;
                    form.repeat_interval    = repeat_interval;
                    form.repeat_started_at  = this.getDateTime(repeat_started_at, dmyhms, ymdhms);
                    form.repeat_ended_at    = this.getDateTime(repeat_ended_at, dmyhms, ymdhms);
                }

                if (this.isFetching) {
                    return
                }

                this.isFetching = true;
                const payload = Object.assign({...this.task}, form);



                this.$api.task
                    .updateTask(payload)
                    .then((res) => {
                        task.updateCountTask(res.task);
                        this.emitEvent('update', payload);
                    }).catch((err) => {
                        if(err.response) {
                            err = err.response.data.message;
                            this.$notify({type:'error', text: err});
                        }
                        console.log("err ", err);
                    }).finally(res => {
                        this.isFetching = false
                    });
            },
            emitEvent(eventName, data) {
                this.$root.$emit(`modaldeadline-${eventName}`, { data });
            },
            checkCurrentPeriod(period, date) {
                if (this.timestamp.time !== this.earlyTimestamp.time || this.timestamp.todo !== this.earlyTimestamp.todo) {
                    let isPeriod = moment().isSame(moment.utc(date, this.format.ymdhms).local().format(this.format.ymdhms), period);
                    if (!isPeriod) {
                        this.$notify({
                            type: 'success',
                            text: 'To do date of the task is ' + this.$moment.utc(date, this.format.ymdhms).local().format("YYYY-MM-DD")
                        });
                    }
                }
            },
            getDateTime(value, from, to, options = {}) {
                if (typeof value !== 'string') {
                    return null;
                }

                options = Object.assign({}, {
                    isUtc:   false,
                    toUtc:   false,
                    toLocal: false,
                }, options);

                let date = options.isUtc ? this.$moment.utc(value, from).local() : this.$moment(value, from);

                if (!date.isValid()) {
                    return null;
                }

                if (options.toUtc) {
                    date = date.utc();
                }

                if (options.toLocal) {
                    date = date.local();
                }

                return date.format(to);
            },
            isDiffData() {
                let taskRepeatInterval = this.task.repeat_interval || 1;
                let taskRepeatStart = this.repeat.repeat_started_at ? `${this.$moment(this.repeat.repeat_started_at, 'DD.MM.YYYY').format('YYYY-MM-DD')} 00:00:00` : null;
                let taskRepeatEnd = this.repeat.repeat_ended_at ? `${this.$moment(this.repeat.repeat_ended_at, 'DD.MM.YYYY').format('YYYY-MM-DD')} 00:00:00` : null;

                if (this.timestamp.deadline !== this.toLocalTime(this.task.deadline, "DD.MM.YYYY") ||
                    this.earlyTimestamp.time !== this.timestamp.time ||
                    this.earlyTimestamp.todo !== this.timestamp.todo ||
                    taskRepeatStart !== this.task.repeat_started_at ||
                    taskRepeatEnd !== this.task.repeat_ended_at ||
                    this.repeat.repeat_interval !== taskRepeatInterval ||
                    this.repeat.repeat_unit !== this.task.repeat_unit
                ) {
                   return true;
               }

               return false;
            },
        }
    }
</script>
