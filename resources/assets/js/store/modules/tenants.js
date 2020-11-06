// Optimized
const state = {
    list: [],
};

const actions = {
    setTenants({ commit }, tenants) {
        commit('setTenants', tenants);
    },
};

const getters = {
    getTenants(state) {
        return  [...state.list].sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
};

const mutations = {
    setTenants(state, tenants) {
        state.list = tenants;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}