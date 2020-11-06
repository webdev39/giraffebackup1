import moment   from 'moment-timezone'
import store    from '@store'

export default {
    filters: {
        toLocalTime(time, format = 'YYYY-MM-DD HH:mm:ss') {
            if (moment(time).isValid()) {
                return moment.utc(time).local().format(format)
            }

            return null;
        },
        timerToHours(time) {
            if (!time) return '';

            return parseFloat((time.h * 60 * 60 + time.i * 60) / 3600).toFixed(2) + ' h';
        },
        timerToStringFormat(time, days = true) {
            if (!time) return '';

            if (days) {
                let days    = time.d > 0 ? time.d + ' d ' : '',
                    hours   = time.h > 0 ? time.h + ' h ' : '',
                    minutes = time.i > 0 ? time.i + ' m ' : '';

                return (days + hours + minutes).trim() || '0 m';
            }

            let hours   = time.h > 0 ? time.h + ' h ' : '',
                minutes = time.i > 0 ? time.i + ' m ' : '',
                second  = time.s > 0 ? time.s + ' s ' : '';

            return (hours + minutes + second).trim() || '0 s';
        },
        minutesToStringFormat(time) {
            if (!time) return '0m';

            time = parseInt(time);
            time = {
                d: Math.floor(time / 1440),
                h: Math.floor(time / 60),
                i: Math.floor(time % 60),
            };

            let days    = time.d > 0 ? time.d + ' d ' : '',
                hours   = time.h > 0 ? time.h + ' h ' : '',
                minutes = time.i > 0 ? time.i + ' m ' : '';

            return (days + hours + minutes).trim();
        },
        secondsToStringFormat(time) {
            if (!time) return '0m';

            time = Math.floor(parseInt(time) / 60);
            time = {
                h: Math.floor(time / 60),
                i: Math.floor(time % 60),
            };

            let hours   = time.h > 0 ? time.h < 10 ? '0' + time.h : time.h : '00',
                minutes = time.i > 0 ? time.i < 10 ? '0' + time.i : time.i : '00';

            return `${hours}:${minutes}`;
        },
        formatDateWithTimeZone(value) {
            return moment(value).tz(store.getters['user/getUserProfile'].timeZone).format('HH:mm DD.MM.YYYY');
        },
    },
    methods: {
        toLocalTime(time, format = 'YYYY-MM-DD HH:mm:ss') {
            if (moment(time, ).isValid()) {
                return moment.utc(time).local().format(format)
            }

            return null;
        },
        toUTCTime(time, format = 'YYYY-MM-DD HH:mm:ss') {
            if (moment(time).isValid()) {
                return moment(time).utc().format(format)
            }

            return null;
        },
        getTimeHours(time, hours = 0) {
            if (typeof time === 'string') {
                time = time.split(":");
            }

            hours += parseInt(time[0]) > 0 ? parseInt(time[0]) : 0;
            hours += parseInt(time[1]) > 0 ? parseInt(time[1]) / 60 : 0;
            hours += parseInt(time[2]) > 0 ? parseInt(time[2]) / 60 / 60 : 0;

            return (Math.round(hours * 100) / 100);
        },
        getBudgetMinutes (budget) {
            if (!budget || budget === "00:00") {
                return null;
            }

            let [hours, minutes] = budget.split(':');

            return parseInt(hours) * 60 + parseInt(minutes);
        },
        getBudgetSeconds(budget) {
            if (!budget || budget === "00:00") {
                return null;
            }

            let [hours, minutes] = budget.split(':');

            return (parseInt(hours) * 60 + parseInt(minutes)) * 60;
        },
        getSecondsByTime(time) {
            return time.h * 3600 + time.i * 60 + time.s;
        },
        getTrackedTimeSecondsByTask(task) {
            if (!task) {
                return null;
            }

            let trackedTime = task.logged_time * 60;
            let groupTimersByTask = store.getters['timers/getTimersGroupByTaskId'];

            if (groupTimersByTask[task.id]) {
                groupTimersByTask[task.id].filter(timer => {
                    trackedTime += this.getSecondsByTime(timer.time);
                });
            }

            return trackedTime;
        },
        isOvertimeBudget(budget, trackedTime) {
            if (budget && trackedTime) {
                return parseInt(budget) <= parseInt(trackedTime)
            }

            return false;
        },
        diffDate (startDate, endDate, period = "days", format = "YYYY-MM-DD HH:mm:ss" ) {
            return this.$moment(this.$moment(endDate, format)).diff(this.$moment(startDate, format), period)
        }
    }
};
