// Optimized

export default {
    setToken(state, token) {
        state.token = token;
    },
    setDeviceToken(state, token) {
        state.deviceToken = token;
    },
    setLoggedIn(state, loggedIn) {
        state.loggedIn = loggedIn;
    },
    setLastRoute(state, route) {
        state.lastRoute = route;
    },
    setQuickTimerState(state, open) {
        state.quickTimerState = (open === 'true');
    },
    setPagePreloader(state, payload) {
        state.pagePreloader = payload;
    },
    setTourStep(state, payload) {
        state.tour.step = payload;
    },
};