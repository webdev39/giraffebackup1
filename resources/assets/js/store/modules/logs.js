import moment               from 'moment';

const state = {
    logs:           [],
    currentDate:    moment().format('YYYY-MM-DD'),
};

const getters = {
    getLogs (state) {
        return state.logs;
    },
    groupByDate (state) {
        return [...state.logs]
            .sort(function(a, b) {
                return moment(a.updated_at) - moment(b.updated_at);
            })
            .reduce(function (r, a) {
                let date = moment(a.updated_at).format('YYYY-MM-DD');
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
};

const mutations = {
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
            if(item.timer) {
                if (item.timer.timer_billing_id === payload.timerBillingId) {
                    item.timer.billing_status_id = payload.billingStatusId;

                    return true
                }
            }
        });
    },
    changeLogCommentAndTime (state, log) {
        state.logs.find(item => {
            if (item.id === log.id) {
                item.timer.comment =    log.comment
                item.timer.time =       log.time
                item.header =           log.header
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
