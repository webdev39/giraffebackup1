import findInComment from "@helpers/findInComment";

const state = {
    actions: [],
};

const getters = {
    getActions(state) {
        return state.actions;
    },
};

const actions = {
    addActions({ commit }, actions) {
        commit('setActions', actions);
    },
    addReply({ commit }, reply) {
        commit('addReply', reply);
    },
    removeReply({ commit }, reply) {
        commit('removeReply', reply);
    },
    changeReply({ commit }, reply) {
        commit('changeReply', reply);
    },
    clearActions({ commit }) {
        commit('clearActions');
    },
    removeAction({ commit }, action) {
        commit('removeAction', action)
    },
    changeAction({ commit }, action) {
        commit('changeAction', action)
    },
    incrementLikes({ commit }, payload) {
        commit('incrementLikes', payload)
    },
    decrementLikes({ commit }, payload) {
        commit('decrementLikes', payload)
    }
};

const mutations = {
    setActions (state, actions) {
        if (Array.isArray(actions)) {
            return state.actions = [...state.actions, ...actions];
        } else {
            let getAction = state.actions.find(item => item.id === actions.id);

            if (getAction) {
                return state.actions.some((item, index) => {
                    if (item.id === actions.id) {
                        Object.assign(state.actions[index], actions)
                    }
                })
            }
            return state.actions.unshift(actions);
        }
    },

    addReply (state, reply) {

        state.actions.find(item => {
            if (item.id === reply.action_id) {
                item.count_replies += 1;

                if (item.id === reply.parent_id) {
                    item.replies.push(reply);
                    return true;
                }

                findInComment.findComment(item, reply.parent_id, (item) => item.replies.push(reply));
                return true;
            }
        });
    },
    removeReply (state, reply) {
        state.actions.find(item => {
            if (item.id === reply.action_id) {

                if (item.id === reply.parent_id) {

                    item.replies = item.replies.filter(itemReply => {
                        if (itemReply.id === reply.id) {
                            item.count_replies -= findInComment.countComments(itemReply);
                            return false
                        }

                        return true;
                    });
                    return true;
                }

                findInComment.findComment(item, reply.parent_id, (comment) => {
                    comment.replies = comment.replies.filter(itemReplies => {
                        if (itemReplies.id === reply.id) {
                            item.count_replies -= findInComment.countComments(itemReplies);
                            return false
                        }

                        return true;
                    })
                });

                return true;
            }
        });
    },
    changeReply (state, reply) {
        state.actions.find(item => {
            if (item.id === reply.action_id) {
                findInComment.findComment(item, reply.id, (item) => Object.assign(item, reply) );
                return true;
            }
        });
    },
    clearActions (state) {
        state.actions = [];
    },
    removeAction (state, action) {
        state.actions = state.actions.filter(item => item.id !== action.id)
    },
    changeAction (state, action) {
        state.actions.find((item, index) => {
            if (item.id === action.id) {
                Object.assign(state.actions[index], action);
                return true
            }
        })
    },
    incrementLikes (state, payload) {
        if (payload.action_id) {
            state.actions.find((item, index) => {
                if (item.id === payload.action_id) {
                    item.replies.find(itemReply => {
                        if (itemReply.id === payload.id) {
                            itemReply.reactions.like += 1;
                            return true;
                        }
                    });

                    return true;
                }
            })
        } else {
            state.actions.find((item, index) => {
                if (item.id === payload.id) {
                    item.reactions.like += 1;
                }
            })
        }
    },
    decrementLikes (state, payload) {
        if (payload.action_id) {
            state.actions.find((item, index) => {
                if (item.id === payload.action_id) {
                    item.replies.find(itemReply => {
                        if (itemReply.id === payload.id) {
                            itemReply.reactions.like -= 1;
                            return true;
                        }
                    });

                    return true;
                }
            })
        } else {
            state.actions.find((item, index) => {
                if (item.id === payload.id) {
                    item.reactions.like -= 1;
                }
            })
        }
    },

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
