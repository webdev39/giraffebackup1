const state = {
    font_id:            null,
    currency:           {},

    date_format:        null,
    money_format:       [],
};

const getters = {
    getSettings(state) {
        return state;
    },
    getMoneyFormat(state) {
        return state.money_format;
    },
    getDateFormat(state) {
        return state.date_format;
    },
};

const actions = {
    setSettings({ commit }, settings) {
        commit('setSettings', settings);
    },
};

const mutations = {
    setSettings (state, settings) {
        Object.assign(state, settings);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
