import auth     from '@helpers/auth'
import http     from '@utils/http'
import store    from '@store'
import router  from "@router/index";

export default {
    authenticate() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/authenticate?user_tenant_res=long&deadline_task_res=short').then((response) => {
                auth.login(response.data);

                resolve(response.data);
            }, error => {
                auth.logout(true);

                reject(error);
            })
        })
    },
    login(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/login?user_tenant_res=long', data).then((response) => {
                auth.login(response.data);

                if (store.getters.getToken) {
                    router.push({ path: store.getters.getLastRoute });
                }

                /**
                 * DEBUGGING FCM
                 */
                if (window.FlutterHost) {
                    window.FlutterHost.postMessage('fcmToken')
                }
                /**
                 * DEBUGGING FCM
                 */

                if (store.getters['getDeviceToken']) {
                    console.log("store.getters['getDeviceToken'] ", store.getters['getDeviceToken']);
                    this.setDeviceToken(store.getters['getDeviceToken']);
                }
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    logout() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/logout?user_tenant_res=long').then((response) => {
                auth.logout();
                window.location.reload();
            }, error => {
                reject(error);
            })
        })
    },
    invite(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/invite?user_tenant_res=long', form).then((response) => {
                store.dispatch('members/addMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('added_member_registration')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getJoin(form) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/join`, Object.assign(form, {invite_user: true})).then((response) => {
                if (auth.login(response.data)) {
                    window.app.$notify({type:'success', text: window.app.$t('successfully_registered')});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    join(form) {
        store.dispatch('loading/setAppLoading', true);

        return new Promise((resolve, reject) => {
            http.post(`/api/v1/join?user_tenant_res=long`, form).then((response) => {
                if (auth.login(response.data)) {
                    window.app.$notify({type:'success', text: 'You have successfully registered'});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                store.dispatch('loading/setAppLoading', false);
            })
        })
    },
    resetPassword(form) {
        store.dispatch('loading/setAppLoading', true);

        return new Promise((resolve, reject) => {
            http.post(`/api/v1/reset-password`, form).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('password_reset_link')});

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                store.dispatch('loading/setAppLoading', false);
            })
        })
    },
    getRestoreToken(token) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/restore-password`, {resetToken: token, user_res: 'short'}).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    restorePassword(form) {
        store.dispatch('loading/setAppLoading', true);

        return new Promise((resolve, reject) => {
            http.post(`/api/v1/restore-password?user_tenant_res=short`, form).then((response) => {
                auth.login(response.data);

                window.app.$notify({type:'success', text: window.app.$t('restore_password')});

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                store.dispatch('loading/setAppLoading', false);
            })
        })
    },
    getConfirmToken(hash) {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/confirm', {confirm_hash: hash}).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('confirm_token')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    confirm(data) {
        store.dispatch('loading/setAppLoading', true);

        return new Promise((resolve, reject) => {
            http.post('/api/v1/confirm?user_tenant_res=long', data).then((response) => {
                auth.login(response.data);

                window.app.$notify({type:'success', text: window.app.$t('register_completed')});

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                store.dispatch('loading/setAppLoading', false);
            })
        })
    },
    register(data) {
        store.dispatch('loading/setAppLoading', true);

        return new Promise((resolve, reject) => {
            http.post('/api/v1/register', data).then((response) => {
                window.app.$notify({type:'success', text: window.app.$t('check_your_email_continue_registration')});

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                store.dispatch('loading/setAppLoading', false);
            })
        })
    },
    loginUsingMemberId(memberId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/tenant/members/login/${memberId}`).then((response) => {
                localStorage.setItem('memberToken', response.data.token);

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    setDeviceToken(dataToken) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/device-token`, {token: dataToken}).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
};
