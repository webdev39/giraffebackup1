import http     from '@utils/http'
import store    from '@store'

export default {
    getPriorities() {
        if (store.getters['priorities/getPriorities'].length > 0) {
            return;
        }

        return new Promise((resolve, reject) => {
            http.get('/api/v1/priority').then((response) => {
                store.dispatch('priorities/setPriorities', response.data.priorities);

                resolve(response.data);
            }, error => {
                window.app.$notify({type:'error', text: window.app.$t('error_in_getting_available_priorities')});

                reject(error);
            })
        })
    },
    createCustomPriority(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/priority/create', data).then((response) => {
                store.dispatch('priorities/addPriority', response.data.priority);

                window.app.$notify({type:'success', text: window.app.$t('create_custom_priority')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateCustomPriority(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/priority/update', data).then((response) => {
                store.dispatch('priorities/updatePriority', response.data.priority);

                window.app.$notify({type:'success', text: window.app.$t('update_custom_priority')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateSortOrderCustomPriority(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/priority/update/sort', { order: data }).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('update_order_custom_priority')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    deleteCustomPriority(priorityId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/priority/${priorityId}`).then((response) => {
                store.dispatch('priorities/removePriority', priorityId);

                if (response.data.board) {
                    store.dispatch('groups/changeBoard', response.data.board);
                    store.dispatch('reports/changeBoard', response.data.board);
                }

                window.app.$notify({type:'success', text: window.app.$t('remove_custom_priority')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
