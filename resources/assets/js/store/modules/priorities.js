import find from "@assets/js/helpers/findInGroups";

const state = {
    collection: [],
};

const getters = {
    getPriorities(state) {
        return [].concat(state.collection);
    },
};

const actions = {
    setPriorities ({commit}, priorities) {
        commit('setPriorities', priorities)
    },
    addPriorities ({commit}, priorities) {
        commit('addPriorities', priorities)
    },
    addPriority ({commit}, priority) {
        commit('addPriority', priority)
    },
    removePriority ({commit}, priority_id) {
        commit('removePriority', priority_id)
    },
    updatePriority ({commit}, priority) {
        commit('updatePriority', priority)
    },
    updateSortOrderPriorities ({commit}, priority) {
        commit('updateSortOrderPriorities', priority)
    }
};

const mutations = {
    setPriorities(state, priorities) {
        state.collection = priorities;
    },
    addPriorities(state, priorities) {
        state.collection = state.collection.concat(priorities);
    },
    addPriority(state, priority) {
        state.collection.push(priority);
    },
    updatePriority(state, priority) {
        state.collection.find(item => {
            if (item.id === priority.id) {
                Object.assign(item, priority);
                return true
            }
        });
    },
    removePriority(state, priority_id) {
        state.collection = state.collection.filter(item => item.id !== priority_id);
    },
    updateSortOrderPriorities(state, priorities) {
        state.collection = state.collection.map(priority => {
            const sortOrder = priorities.findIndex(item => item === priority.id);

            if (sortOrder >= 0) {
                priority.sort_order = sortOrder + 1;
            }

            return priority;
        });
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
