import http     from '@utils/http'
import store    from '@store'

export default {
    getBillings(year) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/billing/year/${year}`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateStatus(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/billing/status/update', data).then((response) => {
                store.dispatch('timers/changeBillStatusTimer', data);

                window.app.$notify({type:'success', text: window.app.$t('billing_update_status')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
};
