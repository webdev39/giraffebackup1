// Optimized
const state = {
    countries:          [],
    currencies:         [],
    languages:          [],
    fonts:              [],

    customerStatuses:   [],
    billingStatuses:    [],
    userStatuses:       [],

    billLayoutTypes:    [],
    budgetTypes:        [],
    notifyTypes:        [],
    viewTypes:          [],

    timeZones:          [],

    audio_sounds:       []
};

const getters = {
    getCountries(state) {
        return state.countries;
    },
    getCurrencies(state) {
        return state.currencies;
    },
    getFonts(state) {
        return state.fonts;
    },

    getLanguages(state) {
        return state.languages;
    },
    getLocalLanguages(state) {
        return state.languages.filter(item => item.is_local);
    },

    getCustomerStatuses(state) {
        return state.customerStatuses;
    },
    getBillingStatuses(state) {
        return state.billingStatuses;
    },
    getUserStatuses(state) {
        return state.userStatuses;
    },

    getBillLayoutTypes(state) {
        return state.billLayoutTypes;
    },
    getBudgetTypes(state) {
        return state.budgetTypes;
    },
    getNotifyTypes(state) {
        return state.notifyTypes;
    },
    getViewTypes(state) {
        return state.viewTypes;
    },

    getTimeZones(state) {
        return state.timeZones;
    },

    getAudioSounds(state) {
        return state.audio_sounds;
    },
};

const actions = {
    setDefaultData({ commit }, data) {
        commit('setCurrencies',         data.currencies);
        commit('setCountries',          data.countries);
        commit('setLanguages',          data.languages);
        commit('setFonts',              data.fonts);

        commit('setBillLayoutTypes',    data.bill_layout_types);
        commit('setBudgetTypes',        data.budget_types);
        commit('setNotifyTypes',        data.notify_types);
        commit('setViewTypes',          data.view_types);

        commit('setCustomerStatuses',   data.customer_statuses);
        commit('setBillingStatuses',    data.billing_statuses);
        commit('setUserStatuses',       data.user_statuses);

        commit('setTimeZones',          data.time_zones);

        commit('setAudioSounds',        data.audio_sounds);
    },
};

const mutations = {
    setCurrencies(state, currencies) {
        state.currencies = currencies;
    },
    setCountries(state, countries) {
        state.countries = countries;
    },
    setLanguages(state, languages) {
        state.languages = languages;
    },
    setFonts(state, fonts) {
        state.fonts = fonts;
    },

    setCustomerStatuses(state, statuses) {
        state.customerStatuses = statuses;
    },
    setBillingStatuses(state, statuses) {
        state.billingStatuses = statuses;
    },
    setUserStatuses(state, statuses) {
        state.userStatuses = statuses;
    },

    setBillLayoutTypes(state, types) {
        state.billLayoutTypes = types;
    },
    setBudgetTypes(state, types) {
        state.budgetTypes = types;
    },
    setNotifyTypes(state, types) {
        state.notifyTypes = types;
    },
    setViewTypes(state, types) {
        state.viewTypes = types;
    },
    setTimeZones(state, types) {
        state.timeZones = types;
    },
    setAudioSounds(state, types) {
        state.audio_sounds = types;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
