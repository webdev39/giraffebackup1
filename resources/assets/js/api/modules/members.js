import http     from '@utils/http'
import store    from '@store'
import api      from '@api'

export default {
    getMembers() {
        if (store.getters['members/getMembers'].length > 0 ) {
            return;
        }

        return new Promise((resolve, reject) => {
            http.get('/api/v1/tenant/members?user_tenant_res=long').then((response) => {
                store.dispatch('members/setMembers', response.data.members);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateMember(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/tenant/members/update?user_tenant_res=long', data).then((response) => {
                store.dispatch('members/changeMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('update_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    attachMemberGlobalRole(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/tenant/role/attach?user_tenant_res=long', form).then((response) => {
                store.dispatch('members/changeMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('attach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    detachMemberGlobalRole(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/tenant/role/detach?user_tenant_res=long', form).then((response) => {
                store.dispatch('members/changeMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('detach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    attachMemberRoleGroup(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/group/role/attach?user_tenant_res=long', form).then((response) => {
                store.dispatch('members/changeMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('attach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    detachMemberRoleGroup(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/group/role/detach?user_tenant_res=long', form).then((response) => {
                store.dispatch('members/changeMember', response.data.member);

                window.app.$notify({type:'success', text: window.app.$t('detach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    attachMemberToGroup(form, group) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/group/member/attach?user_tenant_res=long', form).then((response) => {
                store.dispatch('groups/changeGroup', response.data.group);

                if (form.user_tenant_ids.includes(store.getters['user/getUserId'])) {
                    store.dispatch('groups/addGroup', group);
                    store.dispatch('reports/addGroup', group);
                }

                /* TODO remove after optimization in backend */
                store.dispatch('management/changeGroup', response.data.group);
                /**/

                window.app.$notify({type:'success', text: window.app.$t('attach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    detachMemberToGroup(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/group/member/detach?user_tenant_res=long', form).then((response) => {
                store.dispatch('groups/changeGroup', response.data.group);

                if (form.user_tenant_ids.includes(store.getters['user/getUserId'])) {
                    store.dispatch('groups/removeGroup', {group_id: form.group_id});
                    store.dispatch('reports/removeGroup', form.group_id);
                }

                store.dispatch('members/removeGroupRoleByMember', form);

                /* TODO remove after optimization in backend */
                store.dispatch('management/changeGroup', response.data.group);
                /**/

                window.app.$notify({type:'success', text: window.app.$t('detach_member')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
