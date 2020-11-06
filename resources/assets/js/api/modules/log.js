import http     from '@utils/http'
import store    from '@store'

let request = {
    getLogs: false,
};

export default {
    getActivityLogs(page) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/activity_logs?page=${page}`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getLogs() {
        return new Promise((resolve, reject) => {
            if (request.getLogs) {
                return resolve({});
            }

            request.getLogs = true;

            http.get('/api/v1/log/timer?log_details=true').then((response) => {
                store.dispatch('timers/getLogs', response.data.logs);
                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getLogs = false;
            })
        })
    },
    createLog(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/log/timer?log_details=true&timer_details=true', data).then((response) => {
                store.dispatch('timers/addLog', response.data.log);

                response.data.log.logged_time = response.data.log.timer.time.h * 60 + response.data.log.timer.time.i;

                store.dispatch('groups/incrementLoggedTimerInTask', response.data.log);
                store.dispatch('actions/addActions', response.data.log);

                if (response.data.log.task_id) {
                    store.dispatch('groups/changeAttachmentTasksCount', {
                        task_id: response.data.log.task_id,
                        count: response.data.log.attachments.length
                    });

                    store.dispatch('task/addActions', response.data.log);
                }

                window.app.$notify({type:'success', text: window.app.$t('create_log')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateLog(form, log) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/log/timer/${form.id}?log_details=true`, form).then((response) => {

                let loggedHours = response.data.log.timer.time.h * 60 + response.data.log.timer.time.i;
                let currentHours = log.timer.time.h * 60 + log.timer.time.i;

                response.data.log.logged_time = loggedHours - currentHours;

                if (response.data.log.logged_time > 0) {
                    store.dispatch('groups/incrementLoggedTimerInTask', response.data.log);
                } else {
                    store.dispatch('groups/decrementLoggedTimerInTask', response.data.log);
                }

                store.dispatch('actions/changeAction', response.data.log);
                store.dispatch('task/changeAction', response.data.log);
                store.dispatch('timers/changeTimerTimeAndComment', response.data.log.timer);
                store.dispatch('timers/changeLog', response.data.log);

                window.app.$notify({type:'success', text: window.app.$t('update_log')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

    changeLogTask(logId, taskId) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/log/timer/${logId}`, {taskId : taskId}).then((response) => {
                store.dispatch('timers/changeLog', response.data.log);

                window.app.$notify({type:'success', text: window.app.$t('update_log')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeLog(log) {
        return new Promise((resolve, reject) => {
            http.delete('/api/v1/log/timer/' + log.id).then((response) => {
                store.dispatch('actions/removeAction', log);
                store.dispatch('task/removeAction', log);

                if (log.task_id) {
                    store.dispatch('groups/changeAttachmentTasksCount', { task_id: log.task_id, 'count': `-${log.attachments.length}` });
                }

                store.dispatch('timers/removeTimer', log.timer.id);
                store.dispatch('timers/removeLog', log.id);

                log.logged_time = log.timer.time.h * 60 + log.timer.time.i;

                store.dispatch('groups/decrementLoggedTimerInTask', log);

                window.app.$notify({type:'success', text: window.app.$t('remove_log')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

};
