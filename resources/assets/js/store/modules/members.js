// Optimized
const state = {
    members:        [],
    selectMembers:  [],
};

const getters = {
    getMembers(state) {
        let members = [...state.members];

        return members.sort((a, b) => {
            return sorter(a.user.nickname, b.user.nickname);
        });
    },
    /*todo optimization members keyBy ID*/
    getMembersByUserKey(state, getters) {
        return window.app.$lodash.keyBy(getters.getMembers, 'user.id');
    },

    getOnlyMembers(state, getters, context, rootGetters) {
        return getters.getMembers.filter(member => member.id !== rootGetters['user/getUserTenantId']);
    },
    getActiveMembers(state, getters, context, rootGetters) {
        return getters.getMembers.filter(member => member.user.status);
    },
    getOwner(state, getters, context, rootGetters) {
        return getters.getMembers.find(member => member.id === rootGetters['user/getUserTenantId']);
    },


    getSelectMembers(state) {
        return state.selectMembers;
    },
};

const actions = {
    setMembers({commit}, members) {
        commit('setMembers', members);
    },
    addMember({commit}, member) {
        commit('addMember', member);
    },
    changeMember({commit}, member) {
        commit('changeMember', member);
    },
    setGroupRole({commit}, data) {
        commit('setGroupRole', data);
    },
    removeGroupRoleByMember({commit}, data) {
        commit('removeGroupRoleByMember', data);
    },
    changeGroupRole({commit}, data) {
        commit('changeGroupRole', data);
    },

    addSelectMembers({commit}, payload) {
        commit('addSelectMembers', payload);
    },
    removeSelectMembers({commit}, payload) {
        commit('removeSelectMembers', payload);
    },
    clearSelectMembers({commit}) {
        commit('clearSelectMembers');
    },
};

const mutations = {
    setMembers(state, members) {
        state.members = members || [];
    },
    addMember(state, member) {
        state.members.push(member);
    },
    changeMember(state, member) {
        state.members.find(item => {
            if (item.id === member.id) {
                Object.assign(item, member);
                return true;
            }
        });
    },
    setGroupRole(state, data) {
        state.members.find(member => {
            if (member.id === data.memberId && !member.group_role.some(item => item.group_id === data.groupId)) {

                member.group_role.push({
                    group_id: data.groupId,
                    role_id:  data.roleId,
                });

                return true;
            }
        });
    },
    removeGroupRoleByMember(state, data) {
        state.members.find(member => {

            if (member.id === data.member_id) {
                member.group_role = member.group_role.filter(item => item.group_id !== data.group_id);

                return true;
            }
        });
    },
    changeGroupRole(state, data) {
        state.members.forEach(member => {
            if (member.id === data.memberId) {
                member.group_role.find(role => {
                    if (role.group_id === data.groupId) {
                        role.role_id = data.roleId
                    }
                });
            }
        });
    },

    addSelectMembers(state, payload) {
        state.selectMembers.push(payload);
    },
    removeSelectMembers(state, payload) {
        state.selectMembers = state.selectMembers.filter(item => item !== payload);
    },
    clearSelectMembers(state) {
        state.selectMembers = [];
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
