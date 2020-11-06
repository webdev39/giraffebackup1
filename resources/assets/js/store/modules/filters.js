// Optimized
const state = {
    list: [],
};

const getters = {
    getFilters(state) {
        return [...state.list].sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
};

const actions = {
    setFilters({ commit }, filters) {
        commit('setFilters', filters);
    },
    addFilter({ commit }, filter) {
        commit('addFilter', filter);
    },
    changeFilter({ commit }, filter) {
        commit('changeFilter', filter);
    },
    removeFilter({ commit }, filterId) {
        commit('removeFilter', filterId);
    },
};

const mutations = {
    setFilters(state, list) {
        state.list = list;
    },
    addFilter(state, filter) {
        state.list.push(filter);
    },
    changeFilter(state, filter) {
        state.list.find(item => {
            if (item.id === filter.id) {
                Object.assign(item, filter);
                return true
            }
        });
    },
    removeFilter(state, filterId) {
        state.list = state.list.filter(item => item.id !== filterId);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}