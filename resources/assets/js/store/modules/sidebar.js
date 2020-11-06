const state = {
    sidebarShow:    null,
    typesSidebar: [
        { id: 0, name: 'close'},
        { id: 1, name: 'small'},
        { id: 2, name: 'open'},
    ]
};

const getters = {
    getSidebarShow(state) {
        return state.sidebarShow;
    },
    getTypesSidebar(state) {
        return state.typesSidebar;
    },
};

const actions = {
    setTypeShowSidebar({commit}, payload) {
        commit('setTypeShowSidebar', payload);
    },
};

const mutations = {
    setTypeShowSidebar(state, payload){
        state.sidebarShow = payload;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
