import config               from "@config";
import find                 from "@helpers/findInGroups";
import findInComment        from "@helpers/findInComment";

const getDefaultState = () => {
    return {
        taskId:             null,
        task:               {
            name:           null,
            draft:          null,
            board:          {
                id:         null,
                name:       null,
            },
            group:          {
                id:         null,
                name:       null,
            },
            sub_tasks:      [],
            is_archive:     false,
            priority_id:    config.defaultPrimaryId,
            updated_at:     null,
        },
        tasksList:      [],
        actions:        [],
        changeComments: []
    }
}

const state = getDefaultState();

const getters = {
    isOwner(store, getters, rootState, rootGetters) {
        const userId = rootGetters['user/getUserId'];
        const { creator_id } = getters.getTask;

        return creator_id === userId;
    },
    getChangeComments(store) {
        return store.changeComments
    },
    getTaskId(store) {
        return store.taskId
    },
    getBoardId(store) {
        return store.task.board.id
    },
    getGroupId(store) {
        return store.task.group.id
    },
    getTask(store, getters, context) {
        let result = {...store.task};
        if (store.taskId) {
            find.search(context.groups.list, { id: store.taskId }, 'task', (tasks, task, index, board, group) => {
                Object.assign(result, task, {
                    board: { name: board.name },
                    group: { name: group.name }
                })
            });
        }
        return result
    },
    getTasksList(store) {
        return store.tasksList;
    },
    getSubTasks(store, getters, context) {
        let subtasks = [].concat(getters.getTask.sub_tasks);

        find.search(context.groups.list, {id: store.taskId}, 'task', (tasks, task, index, board, group) => {
            subtasks.concat(task.sub_tasks);
        });

        return subtasks.sort((a,b) => {
            if (!a.order || !a.order) {
                return Date(a.created_at) - new Date(b.created_at)
            }

            return a.order - b.order
        });
    },
    getPriority(store, getters, rootState, rootGetters) {
        return rootGetters['priorities/getPriorities'].find(item => item.id === getters.getTask.priority_id)
    },
    isDraftTask(store, getters) {
        return getters.getTask.draft > 0;
    },
    isDone(store, getters) {
        return getters.getTask.done_by > 0;
    },
    getActions(store) {
        return store.actions;
    },
    getScales(store) {
        return {
            levels: [
                {
                    name:"hours",
                    scale_height: 45,
                    min_column_width:100,
                    scales:[
                        {unit: "day", step: 1, format: "%d %M"},
                        {unit: "hour", format: "%H:%i", step: 1}
                    ]
                },
                {
                    name:"days",
                    scale_height: 45,
                    min_column_width:80,
                    scales:[
                        {unit: "day", step: 1, format: "%d %M"},
                        {unit: "hour", format: "%H:%i", step: 7}
                    ]
                },
                {
                    name:"weeks",
                    scale_height: 50,
                    min_column_width:50,
                    scales:[
                        {unit: "week", step: 1, format: function (date) {
                                var dateToStr = gantt.date.date_to_str("%d %M");
                                var endDate = gantt.date.add(date, -6, "day");
                                var weekNum = gantt.date.date_to_str("%W")(date);
                                return "#" + weekNum + ", " + dateToStr(date) + " - " + dateToStr(endDate);
                            }},
                        {unit: "day", step: 1, format: "%j %D"}
                    ]
                },
                {
                    name:"months",
                    scale_height: 50,
                    min_column_width:120,
                    scales:[
                        {unit: "month", format: "%F, %Y"},
                        {unit: "week", format: "Week #%W"}
                    ]
                },
                {
                    name:"quarters",
                    height: 50,
                    min_column_width:90,
                    scales:[
                        {unit: "month", step: 1, format: "%M"},
                        {
                            unit: "quarter", step: 1, format: function (date) {
                                var dateToStr = gantt.date.date_to_str("%M");
                                var endDate = gantt.date.add(gantt.date.add(date, 3, "month"), -1, "day");
                                return dateToStr(date) + " - " + dateToStr(endDate);
                            }
                        }
                    ]},
                {
                    name:"years",
                    scale_height: 50,
                    min_column_width: 30,
                    scales:[
                        {unit: "year", step: 1, format: "%Y"}
                    ]}
            ]
        }
    }
};

const actions = {

    resetTaskState({ commit }) {
        commit('resetTaskState');
    },

    setTask({ commit }, task) {
        commit('setTask', task);
    },
    setTaskId({ commit }, taskId) {
        commit('setTaskId', taskId);
    },
    setBoardId({ commit }, id) {
        commit('setBoardId', id);
    },
    setGroupId({ commit }, id) {
        commit('setGroupId', id);
    },
    setTasksList({ commit }, payload) {
        commit('setTasksList', payload)
    },
    addTaskToTaskList({ commit }, payload) {
        commit('addTaskToTaskList', payload)
    },
    clearTaskId({ commit }) {
        commit('clearTaskId');
    },
    setTaskSortWeight({commit}, payload) {
        commit('setTaskSortWeight', payload);
    },
    updateTaskUpdateAt({ commit }) {
        commit('updateTaskUpdateAt', new Date());
    },
    addChangeComments({ commit }, value) {
        commit('addChangeComments', value);
    },
    removeChangeComments({ commit }, value) {
        commit('removeChangeComments', value);
    },
    clearChangeComments({ commit }, value) {
        commit('clearChangeComments', value);
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

    /**
     * Actions
     */
    addActions({ commit }, actions) {
        commit('setActions', actions);
    },
    removeAction({ commit }, action) {
        commit('removeAction', action);
    },
    changeAction({ commit }, action) {
        commit('changeAction', action);
    },
    clearActions({ commit }) {
        commit('clearActions');
    },
    incrementLikes({ commit }, payload) {
        commit('incrementLikes', payload)
    },
    decrementLikes({ commit }, payload) {
        commit('decrementLikes', payload)
    },

    /**
     * Task List
     */

    changeTaskList({ commit }, payload) {
        return commit('changeTaskList', payload);
    },

    removeTask({ commit}, payload){
        return commit('removeTaskFromList', payload)
    }
};

const mutations = {

    resetTaskState (state) {
        Object.assign(state, getDefaultState())
    },

    addChangeComments (state, payload) {
        state.changeComments.push(payload);
    },
    removeChangeComments (state, payload) {
        state.changeComments = state.changeComments.filter(item => item !== payload)
    },
    clearChangeComments (state) {
        state.changeComments = [];
    },

    setTask (state, task) {
        state.task = Object.assign(state.task, task);
    },
    removeTaskFromList (state, data){
        state.tasksList = state.tasksList.filter(item => item.id !== data.task_id);
    },
    setBoardId(state, id) {
        state.task.board.id = id;
    },
    setGroupId(state, id) {
        state.task.group.id = id;
    },
    setTasksList(state, list) {
        state.tasksList = list;
    },
    addTaskToTaskList(state, task) {
        state.tasksList.push(task);
    },
    setTaskSortWeight (state, payload) {
        state.task = Object.assign(state.task, payload.task);
        state.task.sort_weight = parseInt(payload.sort_weight);
    },
    updateTaskUpdateAt(state, date) {
        state.task.updated_at = date;
    },
    setTaskId (state, taskId) {
        state.taskId = taskId;
    },
    clearTaskId (state) {
        state.taskId = null;
    },
    setActions (state, actions) {
        if (Array.isArray(actions)) {
            actions.forEach(item => {
                let action = state.actions.find(i => i.id === item.id);

                if (action === undefined) {
                    state.actions.push(item)
                }
            });
        } else {
            state.actions.unshift(actions);
        }
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
            }
        })
    },

    addReply (state, reply) {
        /* todo remove after optimization in backend (reply comment without replies field) */
        if (!reply.replies) {
            reply.replies = [];
        }
        /**/

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

                // if (item.id === reply.parent_id) {
                //     Object.assign(item, reply);
                //     return true;
                // }

                findInComment.findComment(item, reply.id, (item) => Object.assign(item, reply) );
                return true;
            }
        });
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
    changeTaskList (state, payload) {
        state.tasksList.map(item => {
            if (item.id === payload.id) {
                Object.assign(item, payload);
            }
        })
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
