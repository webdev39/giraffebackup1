const state = {
    tags: [],
};

const getters = {
    getTags(store) {
        return store.tags
    }
};

const actions = {
    setTags({commit}, tags) {
        commit('setTags', tags);
    }
};

const mutations = {
    setTags(state, tags) {
        tags.map(item => item.value = item.name);
        state.tags = tags;
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}