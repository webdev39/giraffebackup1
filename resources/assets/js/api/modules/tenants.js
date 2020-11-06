import http     from '@utils/http'
import store    from '@store'

let request = {
    getTenants: false,
};

export default {
    getTenants() {
        if (request.getTenants || store.getters['tenants/getTenants'].length > 0) {
            return;
        }

        request.getTenants = true;

        return new Promise((resolve, reject) => {
            http.get('/api/v1/tenant').then((response) => {
                store.dispatch('tenants/setTenants', response.data.tenants);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getTenants = false;
            })
        })
    },
    getTemplates() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/tenant/templates').then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateTemplates(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/tenant/templates', form).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('update_template_bill')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateSettings(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/tenant/settings', form).then((response) => {
                store.dispatch('settings/setSettings', response.data.settings);
                window.app.$notify({type:'success', text: window.app.$t('update_bill')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
};
