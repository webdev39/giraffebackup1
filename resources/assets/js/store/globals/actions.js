export default {
    setTokenAndLoggedIn({ commit, state }, token) {
        commit('setToken', token);
        commit('setLoggedIn', Boolean(token));
    },
    setToken({ commit, state }, token) {
        commit('setToken', token);
    },
    setDeviceToken({ commit }, token) {
        commit('setDeviceToken', token);
    },
    setLoggedIn({ commit }, loggedIn) {
        commit('setLoggedIn', loggedIn);
    },
    setLastRoute({ commit }, route) {
        if (route === '/') {
            return commit('setLastRoute', 'deadline/day');
        }
        let isNotRedirectRoute = ['login', 'register', 'reset-password', 'restore-password', 'confirm', 'invite'];
        if (! isNotRedirectRoute.includes(route.replace(/\//g, ''))) {
            commit('setLastRoute', route);
        }
    },
    setQuickTimerState({ commit }, open) {
        commit('setQuickTimerState', open);
    },
    setPagePreloader({ commit }, payload) {
        commit('setPagePreloader', payload);
    },
    setTourStep({ commit }, payload) {
        commit('setTourStep', payload);
    },
    resetStore({ dispatch }) {
        dispatch('groups/resetGroupsState');
        dispatch('task/resetTaskState');
    }
};