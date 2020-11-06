import http     from '@utils/http'
import store    from '@store'

export default {
    updateUser(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/user/update', form).then((response) => {
                store.dispatch('user/setProfile', response.data.profile);
                store.dispatch('members/changeMember', { id: response.data.profile.user_tenant_id, user: response.data.profile });

                window.app.$notify({type:'success', text: window.app.$t('update_user')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    changePassword(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/profile/change-password', form).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('change_password_user')});
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateProfile(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/profile/update', form).then((response) => {
                store.dispatch('user/setProfile', response.data.profile);
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

    subscribeToWebPush(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/subscriptions', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

    unsubscribeFromWebPush(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/subscriptions/delete', form).then((response) => {
                window.app.$notify({type:'success', text: 'Subscription Deleted'});
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

    sendWelcomeNotification() {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/subscriptions/send-welcome').then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },

    addTheme(theme) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/user/color-scheme', theme).then((response) => {
                store.dispatch('user/addTheme', response.data);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateTheme(theme) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/user/color-scheme/${theme.id}`, theme).then((response) => {
                store.dispatch('user/changeThemes', response.data);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeTheme(id) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/user/color-scheme/${id}`).then((response) => {
                store.dispatch('user/removeTheme' , id);
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
