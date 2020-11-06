import moment               from 'moment';
import find                 from "@helpers/findInGroups";

let timeInterval;

const state = {
    timers:         [],
    logs:           [],
    timerIsPlaying: false,
    currentDate:    moment().format('YYYY-MM-DD')
};

const getters = {
    getCurrentDate (state) {
        return state.currentDate;
    },
    getTimers (state) {
        return [...state.timers].sort((a,b) => {
            return new Date(b.start_time) - new Date(a.start_time)
        });
    },
    getTimersGroupByTaskId (state) {
        return [...state.timers].filter(timer => timer.task_id !== null).reduce((prev, curr) => {
            prev[curr.task_id] = prev[curr.task_id] || [];
            prev[curr.task_id].push(curr);

            return prev;
        }, Object.create(null));
    },
    getCurrentStartTimer (state) {
        return state.timers.find((item) => {
            return item.status === 'started';
        });
    },
    getCurrentTimer (state, getters) {
        let timers = getters.getTimers.filter((item) => {
            return item.status === 'paused';
        });

        if (getters.getCurrentStartTimer) {
            timers.unshift(getters.getCurrentStartTimer)
        }

        if (timers.length) {
            return timers[0];
        }

        return null;
    },
    getCurrentRelations(state, getters, rootState) {
        if (getters.getCurrentTimer && getters.getCurrentTimer.task_id && rootState.groups.list.length) {
            let result = {
                task:  {},
                board: {},
                group: {},
            };

            find.search(rootState.groups.list, getters.getCurrentTimer, 'task', (tasks, task, index, board, group) => {
                result.task  = task;
                result.board = board;
                result.group = group;
            });

            return result;
        }

        return {};
    },

    getLogs (state) {
        return state.logs;
    },
    groupByDate (state) {
        return [...state.logs]
            .sort(function(a, b) {
                return moment(b.timer.end_time) - moment(a.timer.end_time);
            })
            .reduce(function (r, a) {
                let date = moment(a.timer.end_time).format('YYYY-MM-DD');
                r[date] = r[date] || [];
                r[date].push(a);
                return r;
            }, Object.create(null));
    },
    getLogsByCurrentDate (state, getters) {
        return getters.groupByDate[state.currentDate];
    },
    groupById (state) {
        return [...state.logs]
            .sort(function(a, b) {
                return moment(a.timer.end_time) - moment(b.timer.end_time);
            })
            .reduce(function (r, a) {
                if (!a.timer) {
                    return {}
                }
                let id = a.timer.task_id;
                if(!id){
                    id = 'withoutId'
                }
                r[id] = r[id] || [];
                r[id].push(a);
                return r;
            }, Object.create(null));
    },
};

const actions = {
    setCurrentDate({commit}, date) {
        commit('setCurrentDate', date);
    },

    /**
     * Timers
     */
    setTimers ({commit, dispatch}, timers) {
        commit('setTimers', timers);
        dispatch('startCurrentTimer');
    },
    addTimer ({commit, dispatch}, timer) {
        commit('addTimer', timer);
    },
    changeTimer ({commit, dispatch}, timer) {
        commit('changeTimer', timer);
    },
    removeTimer ({commit, dispatch}, timerId) {
        commit('removeTimer', timerId);
    },

    changeStatusCurrentTimer({commit, getters}, status) {
        if (getters.getCurrentTimer) {
            commit('changeTimer', {
                id:         getters.getCurrentTimer.id,
                status:     status,
                updated_at: moment().utc().format('YYYY-MM-DD HH-mm-ss')
            });
        }
    },
    startCurrentTimer({commit, state, dispatch}) {
        let index = state.timers.findIndex((item) => {
            return item.status === 'started';
        });

        dispatch('stopCurrentTimer');

        if (index >= 0) {
            timeInterval = setInterval(() => {
                commit('startCurrentTimer', index)
            }, 1000);
        }
    },
    stopCurrentTimer() {
        clearInterval(timeInterval);
    },









    /**
     * Logs
     */
    getLogs({commit}, logs) {
        commit('setLogs', logs);
    },
    addLog ({commit}, timer) {
        commit('addLog', timer);
    },
    changeLog ({commit}, log) {
        commit('changeLog', log);
    },
    removeLog ({commit}, logId) {
        commit('removeLog', logId);
    },
    changeBillStatusTimer ({commit}, payload) {
        commit('changeBillStatusTimer', payload);
    },
    changeLogCommentAndTime ({commit}, payload) {
        commit('changeLogCommentAndTime', payload);
    },
    changeTimerTimeAndComment ({commit}, timer) {
        commit('changeTimerTimeAndComment', timer);
    },
};

const mutations = {
    setCurrentDate (state, date) {
        state.currentDate = moment(date).format('YYYY-MM-DD')
    },



    /**
     * Timers
     */
    setTimers (state, timers) {
        state.timers = timers
    },
    addTimer (state, timer) {
        state.timers.push(timer)
    },
    changeTimer (state, timer) {
        state.timers.find((item) => {
            if (item.id === timer.id) {
                Object.assign(item, timer);
                return true
            }
        })
    },
    removeTimer (state, timerId) {
        state.timers = state.timers.filter(item => item.id !== timerId)
    },
    startCurrentTimer(state, index) {
        state.timers[index].time.s += 1;

        if (state.timers[index].time.s > 59) {
            state.timers[index].time.s = 0;
            state.timers[index].time.i += 1;

            if (state.timers[index].time.i > 59) {
                state.timers[index].time.i = 0;
                state.timers[index].time.h += 1;
            }
        }
    },





    /**
     * Logs
     */
    setLogs (state, logs) {
        state.logs = logs;
    },
    addLog (state, log) {
        state.logs.push(log);
    },
    changeLog (state, log) {
        state.logs.find((item, index) => {
            if (item.id === log.id) {
                Object.assign(state.logs[index], log);
                return true
            }
        })
    },
    removeLog (state, logId) {
        state.logs = state.logs.filter(item => item.id !== logId)
    },
    changeBillStatusTimer (state, payload) {
        state.logs.find(item => {
            if(item.timer && item.timer.timer_billing_id === payload.timerBillingId) {
                item.timer.billing_status_id = payload.billingStatusId;
                return true
            }
        });
    },
    changeLogCommentAndTime (state, log) {
        state.logs.find(item => {
            if (item.id === log.id) {
                item.timer.comment = log.comment;
                item.timer.time = log.time;
                item.header = log.header;
                return true
            }
        })
    },
    changeTimerTimeAndComment (state, timer) {
        state.timers.find(item => {
            if (item.id === timer.id) {
                item.time = timer.time;

                if (timer.comment) {
                    item.comment = timer.comment
                }

                return true
            }
        })
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
