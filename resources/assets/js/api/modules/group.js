import http     from '@utils/http';
import router   from '@router';
import store    from '@store';

let request = {
    getGroup: false
};

export default {
    getGroups(isArchived = null) {
        if (request.getGroup) {
            return;
        }

        request.getGroup = true;

        return new Promise((resolve, reject) => {
            let param = isArchived === null ? {} : isArchived ? 1 : 0;

            http.get('/api/v1/group?board_res=long', param).then((response) => {
                if (response.data.groups === Object(response.data.groups) && !Array.isArray(response.data.groups)) {
                    response.data.groups = Object.values(response.data.groups);
                }

                store.dispatch('groups/setGroups', response.data.groups);

                if (response.data.permissions === Object(response.data.permissions) && !Array.isArray(response.data.permissions)) {
                    response.data.permissions = Object.values(response.data.permissions);
                }

                store.dispatch('groups/setGroupsPermissions', response.data.permissions);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getGroup = false;
            })
        })
    },
    getAllgroups() {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/group/all?board_res=long').then((response) => {
                store.dispatch('management/setGroups', Object.values(response.data.groups));
                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getGroup = false;
            })
        })
    },
    getGroupById(groupId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/group/${groupId}`)
                .then((response) => {
                    resolve(response.data);
                }, error => {
                    reject(error);
                });
        })
    },

    cloneGroup(groupId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/group/${groupId}/clone`).then((response) => {
                store.dispatch('groups/addGroup', response.data.group);

                /*TODO remove after optimization in backend*/
                store.dispatch('management/addGroup', JSON.parse(JSON.stringify(response.data.group)));
                /**/

                store.dispatch('reports/addGroup', response.data.group);
                window.app.$notify({type:'success', text: window.app.$t('clone_group_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    createGroup(form) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/group/create`, form).then((response) => {
                store.dispatch('groups/addGroup', response.data.group);
                // store.dispatch('management/addGroup', response.data.group);
                store.dispatch('reports/addGroup', response.data.group);

                window.app.$notify({type:'success', text: window.app.$t('create_group_success')});
                window.app.$api.tag.getTags();

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateGroup(form) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/group/update?group_relations=none`, form).then((response) => {
                store.dispatch('groups/changeGroup', response.data.group);
                store.dispatch('reports/changeGroup', response.data.group);

                window.app.$notify({type:'success', text: window.app.$t('update_group_success')});
                window.app.$api.tag.getTags();

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeGroup(groupId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/group/${groupId}`).then((response) => {
                if (response.data.is_removed) {
                    store.dispatch('groups/removeGroup', {group_id: groupId});
                    store.dispatch('management/removeGroup', {group_id: groupId});
                    store.dispatch('reports/removeGroup', groupId);

                    window.app.$notify({type:'success', text: window.app.$t('delete_group_success')});
                } else {
                    store.dispatch('groups/changeGroup', response.data.group);
                    store.dispatch('reports/changeGroup', response.data.group);

                    window.app.$notify({type:'success', text: window.app.$t('archive_group_success')});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    archivedGroup(groupId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/group/${groupId}/archived`).then((response) => {
                store.dispatch('groups/changeGroup', response.data.group);
                store.dispatch('management/changeGroup', response.data.group);
                store.dispatch('reports/changeGroup', response.data.group);

                window.app.$notify({type:'success', text: window.app.$t('archive_group_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    unarchivedGroup(groupId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/group/${groupId}/unarchived`).then((response) => {
                store.dispatch('groups/changeGroup', response.data.group);
                store.dispatch('reports/changeGroup', response.data.group);

                window.app.$notify({type:'success', text: window.app.$t('un_archive_group_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};
