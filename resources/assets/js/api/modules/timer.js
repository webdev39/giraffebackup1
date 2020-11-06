import http     from '@utils/http'
import store    from '@store'

export default {
    getTimers() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/timer').then((response) => {
                store.dispatch('timers/setTimers', response.data.timers);
                store.dispatch('timers/startCurrentTimer');

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/timer/${timerId}`).then((response) => {
                store.dispatch('timers/changeTimer', response.data.timer);
                store.dispatch('timers/startCurrentTimer');

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    createStartTimer(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/timer/create-start', form).then((response) => {
                store.dispatch('timers/changeStatusCurrentTimer', 'paused');
                store.dispatch('timers/addTimer', response.data.timer);
                store.dispatch('timers/startCurrentTimer');

                window.app.$notify({
                    group: 'timer',
                    type:'success',
                    text: window.app.$t('create_timer'),
                });

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    updateTimer(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/timer/update', data).then((response) => {
                store.dispatch('timers/changeTimer', response.data.timer);
                store.dispatch('timers/startCurrentTimer');

                window.app.$notify({
                    group: 'timer',
                    type:'success',
                    text: window.app.$t('update_timer'),

                });

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    startTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/timer/start`, {timerId: timerId}).then((response) => {
                store.dispatch('timers/changeStatusCurrentTimer', 'paused');
                store.dispatch('timers/changeTimer', response.data.timer);
                store.dispatch('timers/startCurrentTimer');

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    pauseTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/timer/pause`, {timerId: timerId}).then((response) => {
                store.dispatch('timers/changeTimer', response.data.timer);
                store.dispatch('timers/stopCurrentTimer');

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    continueTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/timer/continue`, {timerId: timerId}).then((response) => {
                store.dispatch('timers/changeStatusCurrentTimer', 'paused');
                store.dispatch('timers/changeTimer', response.data.timer);

                setTimeout(function(){
                    store.dispatch('timers/startCurrentTimer');
                }, 1000);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    stopTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/timer/stop`,  {timerId: timerId}).then((response) => {
                store.dispatch('timers/stopCurrentTimer');

                store.dispatch('timers/addLog',             response.data.log);
                store.dispatch('timers/removeTimer',        timerId);

                response.data.log.logged_time = response.data.log.timer.time.h * 60 + response.data.log.timer.time.i;

                store.dispatch('groups/incrementLoggedTimerInTask', response.data.log);
                store.dispatch('actions/addActions', response.data.log);

                if (response.data.log.task_id) {
                    store.dispatch('task/addActions', response.data.log);
                }

                window.app.$notify({
                    group: 'timer',
                    type:'success',
                    text: window.app.$t('stop_timer'),

                });

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    removeTimer(timerId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/timer/destroy`, {timerId:timerId}).then((response) => {
                store.dispatch('timers/removeTimer', timerId);
                store.dispatch('timers/startCurrentTimer');

                window.app.$notify({
                    group: 'timer',
                    type:'success',
                    text: window.app.$t('remove_timer'),
                });

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
};
