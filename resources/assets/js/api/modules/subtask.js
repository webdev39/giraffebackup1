import http     from '@utils/http'
import store    from '@store'

export default {
    createSubtask (data) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/task/sub-task/create`, data).then(response => {
                store.dispatch('groups/addSubtaskInTask', response.data.sub_tasks);
                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('create_subtask')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateSubtask (data) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/sub-task/update`, data).then(response => {
                store.dispatch('groups/changeSubtaskInTask', response.data.sub_tasks);
                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('update_subtask')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeSubtask (data) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/task/sub-task/${data.id}`).then(response => {
                store.dispatch('groups/removeSubtaskInTask', data);
                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('remove_subtask')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    changeStatusSubtask (data) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/sub-task/workflow/change`, data).then(response => {
                store.dispatch('groups/changeSubtaskInTask', response.data.sub_tasks);
                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('change_status_subtask')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    changeOrderSubtask (data) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/task/sub-task/order/change`, data).then(response => {
                store.dispatch('groups/changeTask', {task_id: data.task_id, sub_tasks: response.data.sub_tasks});
                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('change_order_subtask')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
