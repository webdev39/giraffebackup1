import http     from '@utils/http'
import store    from '@store'

export default {
    createRole(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/roles/create', form).then((response) => {
                store.dispatch('permissions/addCustomRole', response.data.role);

                window.app.$notify({type:'success', text: window.app.$t('create_role')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateRole(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/roles/update', form).then((response) => {
                store.dispatch('permissions/updateCustomRole', response.data.role);

                window.app.$notify({type:'success', text: window.app.$t('update_role')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeRole(roleId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/roles/${roleId}`).then((response) => {
                store.dispatch('permissions/deleteCustomRole', roleId);

                window.app.$notify({type:'success', text: window.app.$t('remove_role')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
