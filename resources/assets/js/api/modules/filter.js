import http     from '@utils/http'
import store    from '@store'

export default {
    getFilters() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/filter').then((response) => {
                store.dispatch('filters/setFilters', response.data.filters);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    createFilter(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/filter/create?filter_task_res=short', form).then((response) => {
                store.dispatch('filters/addFilter', response.data.filter);

                window.app.$notify({type:'success', text: window.app.$t('create_filter')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateFilter(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/filter/update?filter_task_res=short', form).then((response) => {
                store.dispatch('filters/changeFilter', response.data.filter);

                if (window.app.$route.name === 'filter') {
                    let tasksIds = {};

                    response.data.tasks.map(item => {
                        tasksIds[item.task_id] = item.sort_order
                    });

                    store.dispatch('groups/setTasksIds', tasksIds);
                }

                window.app.$notify({type:'success', text: window.app.$t('update_filter')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    deleteFilter(filterId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/filter/${filterId}`).then((response) => {
                store.dispatch('filters/removeFilter', filterId);

                window.app.$notify({type:'success', text: window.app.$t('remove_filter')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
