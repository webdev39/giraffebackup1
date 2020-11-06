import http     from '@utils/http'
import store    from '@store'

let request = {
    getCustomers: false,
};

const clientUrl = '/api/v1/client'

export default {
    getCustomers(params = {}) {
        return new Promise((resolve, reject) => {
            if (request.getCustomers || store.getters['customers/getCustomers'].length > 0) {
                return resolve({});
            }

            request.getCustomers = true;

            http.get(clientUrl, params).then((response) => {
                store.dispatch('customers/setCustomers', response.data.clients);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getCustomers = false;
            })
        })
    },
    create(form) {
        return new Promise((resolve, reject) => {
            http.post(`${clientUrl}/create`, form).then((response) => {
                store.dispatch('customers/addCustomer', response.data.client);

                window.app.$notify({type:'success', text: window.app.$t('create_customer')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    update(form) {
        return new Promise((resolve, reject) => {
            http.put(`${clientUrl}/update`, form).then((response) => {
                store.dispatch('customers/updateCustomer', response.data.client);

                window.app.$notify({type:'success', text: window.app.$t('update_customer')});

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getCustomers = false;
            })
        })
    },
    delete(customerId) {
        return new Promise((resolve, reject) => {
            http.delete(`${clientUrl}/${customerId}`).then((response) => {
                store.dispatch('customers/removeCustomer', customerId);

                window.app.$notify({type:'success', text: window.app.$t('remove_customer')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },

    archive(id) {
        return new Promise((resolve, reject) => {
            http.post(`${clientUrl}/archive`, { id }).then(
                (response) => {
                    store.dispatch('customers/updateCustomer', response.data.client);

                    window.app.$notify({type:'success', text: window.app.$t('remove_customer')});

                    resolve(response.data);
                }, error => reject(error)
            );
        })
    },

    unArchive(id) {
        return new Promise((resolve, reject) => {
            http.post(`${clientUrl}/restore`, { id }).then(
                (response) => {
                    store.dispatch('customers/updateCustomer', response.data.client);

                    window.app.$notify({type:'success', text: window.app.$t('remove_customer')});

                    resolve(response.data);
                }, error => reject(error)
            );
        })
    }
};
