import { mapGetters } from 'vuex'
import moment         from 'moment'
import store          from '@store'
import config         from '@config';

export default {
    computed:{
        ...mapGetters({
            isLoggedIn: 'getLoggedIn',
            isTenant:   'user/isTenant',
        }),
        isLoading: {
            get() {
                return this.$store.getters['loading/isLoading']
            },
            set(value) {
                // return this.$state
            }
        },
    },
    data(){
        return {
            debug: config.debug,
        }
    },
    filters: {
        /* todo get vuex (profile) format for currency unit */
        replaceDot(value) {
            if (value) {
                return value.toString().replace(/\./, ",");
            }
        },
        sliceText(value, max = 180) {
            if (value) {
                let sliced = value.slice(0, max);

                if (sliced.length < value.length) {
                    sliced += '...';
                }

                return sliced;
            }
        },
        cropExtension(value) {
            if (value) {
                return value.replace(/\.[^.]+$/, "");
            }
        },
        sizeForHumans(value){
            let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

            if (!value || value === 0) {
                return '0 ' + sizes[0];
            }

            let i = parseInt(Math.floor(Math.log(value) / Math.log(1024)));

            return Math.round(value / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        toLocalTime(time, format = 'YYYY-MM-DD HH:mm:ss', def = null) {
            if (moment(time).isValid()) {
                return moment.utc(time).local().format(format)
            }

            return def;
        },
        timerToStringFormat(timer) {
            if (!timer) return '';

            let days    = timer.d > 0 ? timer.d + ' d ' : '',
                hours   = timer.h > 0 ? timer.h + ' h ' : '',
                minutes = timer.i > 0 ? timer.i + ' m ' : '';

            return (days + hours + minutes).trim() || '0 m';
        },
        minutesToHours(time) {
            if (!time) return '0h';
            time = parseInt(time) / 60;
            return time = `${Math.floor(time*100) / 100}h`;
        },
        minutesToStringFormat(time) {
            if (!time) return '0m';

            time = parseInt(time);
            time = {
                d: Math.floor(time / 1440),
                h: Math.floor(time / 60),
                i: Math.floor(time & 60),
            };

            let days    = time.d > 0 ? time.d + ' d ' : '',
                hours   = time.h > 0 ? time.h + ' h ' : '',
                minutes = time.i > 0 ? time.i + ' m ' : '';

            return (days + hours + minutes).trim();
        },
        toRoleDescription(role) {
            if (!role.description) {
                return `${role.display_name}`;
            }

            return `${role.display_name} (${role.description[0].toLowerCase() + role.description.substr(1)})`;
        },
        count(value) {
            if (Array.isArray(value)) {
                return value.length;
            }

            return 0;
        },
        formatMoney(amount, decimalCount = 2, decimal, thousands, reverse = false) {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

                if (thousands === undefined) {
                    thousands = store.getters['settings/getMoneyFormat'][1];
                }

                if (decimal === undefined) {
                    decimal = store.getters['settings/getMoneyFormat'][0];
                }

                if (reverse) {
                    amount = `${this.substr(0, amount.length - 3)}.${this.substr(amount.length - 2)}`;

                    return parseFloat(amount.replace("/\\${thousands}/gi", ''));
                }

                const negativeSign = amount < 0 ? "-" : "";

                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;

                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
                console.log(e)
            }
        }
    },

    methods: {
        toLocalTime(time, format = 'YYYY-MM-DD HH:mm:ss', def = null,) {
            if (moment(time).isValid()) {
                const result = moment.utc(time).local();

                if (format) {
                    return result.format(format);
                }

                return result
            }

            return def;
        },
        toUTCTime(time, format = 'YYYY-MM-DD HH:mm:ss') {
            if (moment(time).isValid()) {
                return moment(time).utc().format(format)
            }

            return null;
        },
        sizeForHumans(value) {
            let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

            if (!value || value === 0) {
                return '0 ' + sizes[0];
            }

            let i = parseInt(Math.floor(Math.log(value) / Math.log(1024)));

            return Math.round(value / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        secondsToTime(seconds) {
            seconds = parseInt(seconds, 10);

            let result = {
                hours: 0,
                minutes: 0,
                seconds: 0,
            };

            result.hours   = Math.floor(seconds / 3600);
            result.minutes = Math.floor((seconds - (result.hours * 3600)) / 60);
            result.seconds = seconds - (result.hours * 3600) - (result.minutes * 60);

            return result;
        },
        copyCollection(array) {
            let data = [];

            array.forEach((item) => {
                data.push({...item});
            });

            return data;
        },
        resetComponentData () {
            Object.assign(this.$data, this.$options.data());
        },
        isUndefined (value) {
            return value === undefined;
        },
        isNull (value) {
            return value === null;
        },
        isBoolean (value) {
            return typeof value === 'boolean';
        },
        isString (value) {
            return typeof value === 'string';
        },
        isObject (value) {
            return value === Object(value);
        },
        isArray (value) {
            return Array.isArray(value);
        },
        isNumber (value) {
            return value !== null && typeof value.size === 'number';
        },
        isFunction(value) {
            return value && {}.toString.call(value) === '[object Function]';
        },
        copyData(data) {
            if (this.isUndefined(data) || this.isNull(data)) {
                return null;
            } else if (this.isArray(data)) {
                return Object.keys(data).map((i) => {
                    return {...data[i]}
                })
            } else if (this.isObject(data)) {
                return {...data}
            }

            throw new Error('Argument must be a object ot array');
        },
        copyObject(src) {
            let target = {};
            for (let prop in src) {
                if (src.hasOwnProperty(prop)) {
                    target[prop] = src[prop];
                }
            }
            return target;
        },
    }
};
