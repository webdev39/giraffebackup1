import http     from '@utils/http'
import store    from '@store'

let request = {
    getRoles: false,
    getGlobalRoles: false,
};

export default {
    getGlobalRoles() {
        return new Promise((resolve, reject) => {
            if (request.getGlobalRoles || store.getters['permissions/getGlobalRoles'].length > 0) {
                return resolve({});
            }

            request.getGlobalRoles = true;

            http.get('/api/v1/tenant/roles').then((response) => {
                store.dispatch('permissions/setGlobalRoles', response.data.roles.filter((role) => role.name !== 'admin'));

                resolve(response.data);
            }, error => {
                window.app.$notify({type:'error', text: window.app.$t('error_in_getting_available_roles')});

                reject(error);
            }).finally(() => {
                request.getGlobalRoles = false;
            })
        })
    },
    getRoles() {
        if (request.getRoles || store.getters['permissions/getCustomRoles'].length > 0) {
            return;
        }

        request.getRoles = true;

        return new Promise((resolve, reject) => {
            http.get('/api/v1/roles').then((response) => {
                store.dispatch('permissions/setCustomRoles',  response.data.roles);

                resolve(response.data);
            }, error => {
                window.app.$notify({type:'error', text: window.app.$t('error_in_getting_roles')});

                reject(error);
            }).finally(() => {
                request.getRoles = false;
            })
        });
    },
};
