// Optimized (globalPermissions)

import config from "@config";

const state = {
    list:           [],
    globalRoles:    [],
    customRoles:    [],
    allPermissions: []
};

const getters = {
    getPermissions(state) {
        return state.list;
    },
    getAllPermissions(state) {
        return state.allPermissions;
    },
    getGlobalRoles(state) {
        return state.globalRoles.map(item => {
            return {
                ...item,
                display_name: window.app.$t(item.display_name.replace(/\s+|-|\+/g, '_').toLowerCase()),
            }
        });
    },
    getCustomRoles(state) {
        return state.customRoles;
    },
    getDefaultRole(state, getters) {
        return getters.getCustomRoles.find(role => role.display_name === config.defaultRoleName);
    },
};

const actions = {
    setPermissions({ commit }, permissions) {
        commit('setPermissions', permissions);
    },

    setAllPermissions({ commit }, AllPermissions) {
        commit('setAllPermissions', AllPermissions);
    },

    setGlobalRoles({ commit }, roles) {
        commit('setGlobalRoles', roles);
    },
    setCustomRoles({ commit }, roles) {
        commit('setCustomRoles', roles);
    },

    addCustomRole({ commit }, role) {
        commit('addCustomRole', role);
    },
    updateCustomRole({ commit }, role) {
        commit('updateCustomRole', role);
    },
    deleteCustomRole({ commit }, roleId) {
        commit('deleteCustomRole', roleId);
    },
};

const mutations = {
    setPermissions (state, permissions) {
        state.list = permissions;
    },
    setAllPermissions (state, allPermissions) {
        state.allPermissions = allPermissions;
    },
    setGlobalRoles (state, roles) {
        roles.map(item => {
            item.description = window.app.$t(item.description.replace(/\s+|-/g, '_').toLowerCase() + '_title');
        });
        state.globalRoles = roles;
    },
    setCustomRoles(state, roles) {
        state.customRoles = roles;
    },

    addCustomRole(state, role) {
        state.customRoles.push(role);
    },
    updateCustomRole(state, role) {
        state.customRoles.find(item => {
            if (item.id === role.id) {
                Object.assign(item, role);
                return true;
            }
        });
    },
    deleteCustomRole(state, roleId) {
        state.customRoles = state.customRoles.filter(item => item.id !== roleId);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}