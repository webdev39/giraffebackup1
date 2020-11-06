// Optimized
const state = {
    boards: [],
    groups: [],
    criteria: {},
    selected: {}
};

const getters = {
    getBoards(state) {
        return [...state.boards].sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
    getGroups(state) {
        return [...state.groups].sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
    getCriteria(state) {
        return state.criteria;
    },
    getSelected(state) {
        return state.selected;
    },
};

const actions = {
    setBoards({ commit }, items) {
        commit('setBoards', items);
    },
    addBoard({ commit }, item) {
        commit('addBoard', item);
    },
    removeBoard({ commit }, id) {
        commit('removeBoard', id);
    },
    changeBoard({ commit }, item) {
        commit('updateBoard', item);
    },
    setGroups({ commit }, items) {
        commit('setGroups', items);
    },
    addGroup({ commit }, item) {
        commit('addGroup', item);
    },
    changeGroup({ commit }, item) {
        commit('changeGroup', item);
    },
    removeGroup({ commit }, id) {
        commit('removeGroup', id);
    },
    deleteGroups({ commit }, id) {
        commit('deleteGroups', id);
    },

    setCriteria({ commit }, data) {
        commit('getCriteria', data);
    },
    setSelected({ commit }, data) {
        commit('setSelected', data);
    },
};

const mutations = {
    setBoards(state, items) {
        state.boards = items;
    },
    addBoard(state, item) {
        state.boards.push(item);
    },
    updateBoard(state, item) {
        state.boards.find(board => {
            if (board.id === item.id) {
                Object.assign(board, item);
                return true;
            }
        });
    },
    removeBoard(state, id) {
        state.boards = state.boards.filter(board => board.id !== id);
    },

    setGroups(state, items) {
        state.groups = items;
    },
    addGroup(state, item) {
        state.groups.push(item);
    },
    changeGroup(state, item) {
        state.groups.find(group => {
            if (group.id === item.id) {
                Object.assign(group, item);
                return true;
            }
        });
    },
    removeGroup(state, id) {
        state.groups = state.boards.filter(group => group.id !== id);
    },

    setCriteria(state, data) {
        state.criteria = data;
    },
    setSelected(state, data) {
        state.selected = data;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
