// Optimized
const state = {
    list: [],
};

const getters = {
    getCustomers(state) {
        return [...state.list].sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
    getActiveCustomers(state) {
        return [...state.list].filter(customer => {
            return customer.status === 'active'
        }).sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
    getArchiveCustomers(state) {
        return [...state.list].filter(customer => {
            return customer.status === 'archived'
        }).sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
};

const actions = {
    setCustomers({ commit }, customers) {
        commit('setCustomers', customers);
    },
    addCustomer({ commit }, customer) {
        commit('addCustomer', customer);
    },
    updateCustomer({ commit }, customer) {
        commit('updateCustomer', customer);
    },
    removeCustomer({ commit }, customerId) {
        commit('removeCustomer', customerId);
    },
};

const mutations = {
    setCustomers(state, customers) {
        state.list = customers;
    },
    addCustomer(state, customer) {
        state.list.push(customer);
    },
    updateCustomer(state, customer) {
        state.list.find(item => {
            if (item.id === customer.id) {
                Object.assign(item, customer);
                return true;
            }
        });
    },
    removeCustomer(state, customerId) {
        state.list = state.list.filter(item => item.id !== customerId);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
