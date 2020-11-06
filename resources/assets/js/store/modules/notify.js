const state = {
    messages: [],
    last_page: 1,
    current_page: 0,

    unread_messages: [],
};

const getters = {

    getMessages(state) {
        return [...state.messages].sort((a,b) => {
            return new Date(b.created_at) - new Date(a.created_at);
        });
    },

    getUnreadMessages(state) {
        return [...state.unread_messages].sort((a,b) => {
            return new Date(b.created_at) - new Date(a.created_at);
        });
    },

    getLastPage() {
        return state.last_page;
    },

    getCurrentPage(state) {
        return state.current_page;
    },

    getCountUnreadNotifications(state) {
        return state.unread_messages.length
    },
};

const actions = {
    addMessage({ commit }, message) {
        commit('setMessage', message);
    },

    setLastPage({ commit }, page) {
        commit('setLastPage', page);
    },

    setUnreadMessages({ commit }, payload) {
        commit('setUnreadMessages', payload);
    },

    addUnreadMessages({ commit }, payload) {
        commit('addUnreadMessages', payload);
    },

    removeUnreadMessages({ commit }, payload) {
        commit('removeUnreadMessages', payload);
    },

    incrementCurrentPage({ commit }) {
        commit('incrementCurrentPage');
    },

    markAllRead({ commit }) {
        commit('markAllRead');
    },

    markRead({ commit, state }, { notifyId, taskId }) {
        state.messages.map(message => message.id === notifyId || message.task_id === taskId ? commit('setMarkRead', message) : false);
        state.unread_messages.map(message => message.id === notifyId || message.task_id === taskId ? commit('removeUnreadMessages', message) : false);
    },

    markUnRead({ commit, state }, notifyId) {
        state.messages.find(message => {
            if(message.id === notifyId) {
                commit('setMarkUnRead', message);
                commit('addUnreadMessages', message);
            }
        });
    },
};

const mutations = {

    setMessage(state, messages) {
        if (Array.isArray(messages)) {
            let newMessages = messages.filter(item => {
                let el = state.messages.find(i => i.id === item.id);
                if (! el) {
                    return item;
                }
            });
            state.messages = [...state.messages, ...newMessages];
        } else {
            state.messages.unshift(messages);
        }
    },

    setLastPage(state, page) {
        state.last_page = page;
    },

    incrementCurrentPage(state) {
        state.current_page += 1;
    },

    setUnreadMessages(state, payload) {
        state.unread_messages = payload;
    },

    addUnreadMessages(state, payload) {
        state.unread_messages.push(payload);
    },

    removeUnreadMessages(state, payload) {
        state.unread_messages = state.unread_messages.filter(item => item.id !== payload.id);
    },

    setMarkRead(state, payload) {
        payload.read_at = true;
    },

    setMarkUnRead(state, payload) {
        payload.read_at = null;
    },

    markAllRead(state) {
        state.unread_messages = [];

        state.messages.map(message => {
            message.read_at = true;

            return message;
        });
    },

    clear(state) {
        state.messages = [];
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
