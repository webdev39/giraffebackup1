// Optimized

export default {
    getToken (state) {
        return localStorage.getItem('memberToken') || state.token
    },
    getDeviceToken (state) {
        return state.deviceToken
    },
    getLoggedIn (state) {
        return state.loggedIn
    },
    getLastRoute(state) {
        return state.lastRoute;
    },
    getQuickTimerState(state) {
        return state.quickTimerState
    },
    isMemberLoggedIn(state, getters, context, rootGetters) {
        let stateStorage = JSON.parse(localStorage.getItem('vuex'));

        if (Object.keys(stateStorage).length > 0) {
            return getters.getLoggedIn && rootGetters['user/getUserId'] !== stateStorage.user.id;
        }

        return false;
    },
    getPagePreloader(state) {
        return state.pagePreloader;
    },
    getCurrentTour(state) {
        return state.tour;
    }
};
