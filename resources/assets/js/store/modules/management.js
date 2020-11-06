import archived     from '@helpers/archived'
import find         from '@helpers/findInGroups'

// TODO after optimization in backend remove all action, which "changed group in management"

const state = {
    list: []
};

const getters = {
    getStateGroups(state) {
        return state.list.map((group) => {
            let boards = [...group.boards].sort((a, b) => {
                return sorter(a.name, b.name);
            });

            return Object.assign({}, group, {boards: boards});
        }).sort((a, b) => {
            return sorter(a.name, b.name);
        });
    },
    getActivedGroups(state, getters) {
        return getters.getStateGroups.filter(group => !group.is_archive);
    },
    getArchivedGroups(state, getters) {
        return getters.getStateGroups.filter(group => group.is_archive)
    },
};

const actions = {
    /**
     * Setters
     */
    setGroups({ commit }, group) {
        commit('setGroups', group);
    },


    /**
     * Groups
     */
    addGroup ({commit}, group) {
        commit('addGroup', group);
    },
    changeGroup ({commit}, group) {
        commit('changeGroup', group);
    },
    removeGroup ({commit}, groupId) {
        commit('removeGroup', groupId);
    },
    addGroupMembers({commit}, board) {
        commit('addGroupMembers', board);
    },
    /**
     * Boards
     */
    addBoard ({commit}, board) {
        commit('addBoard', board);
    },
    changeBoard ({commit}, board) {
        commit('changeBoard', board);
    },
    removeBoard ({commit}, boardId) {
        commit('removeBoard', boardId);
    },


    /**
     * Tasks
     */
    addTask ({commit}, task ) {
        commit('addTask', task);
    },
    changeTask ({commit}, task) {
        commit('changeTask', task);
    },
    removeTask ({commit}, task ) {
        commit('removeTask', task);
    },
    decrementLoggedTimerInTask ({commit}, payload) {
        commit('decrementLoggedTimerInTask', payload);
    },
    incrementLoggedTimerInTask ({commit}, payload) {
        commit('incrementLoggedTimerInTask', payload);
    },
    incrementBudgedBoard({commit}, item) {
        commit('incrementBudgedBoard', item);
    },
    decrementBudgedBoard({commit}, item) {
        commit('decrementBudgedBoard', item);
    },
    addTaskDeadline({commit}, data) {
        commit('addTaskDeadline', data);
    },
    removeTaskDeadline({commit}, data) {
        commit('removeTaskDeadline', data);
    },
};

const mutations = {
    /**
     * Setters
     */
    setGroups (state, groups) {
        state.list = groups;
    },

    /**
     * Groups
     */
    addGroup (state, item) {
        find.search(state.list, item, 'groups', (groups, group, index) => {
            groups.push(item);
        });
    },
    changeGroup (state, item) {
        find.search(state.list, item, 'group', (groups, group, index) => {
            if (group.is_archive !== item.is_archive) {
                item = archived.archivedGroup(item, item.is_archive);
            }

            Object.assign(group, item);
        });
    },
    removeGroup (state, item) {
        find.search(state.list, item, 'group', (groups, group, index) => {
            groups.splice(index, 1);
        });
    },

    /**
     * Boards
     */
    addBoard (state, item) {
        find.search(state.list, item, 'group', (groups, group, index) => {
            group.boards.push(item);
        });
    },
    changeBoard (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            if (board.is_archive !== item.is_archive) {
                item = archived.archivedBoard(item, item.is_archive);
            }

            Object.assign(board, item);
        })
    },
    removeBoard(state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            boards.splice(index, 1);
        })
    },

    incrementBudgedBoard (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            board.budget += item.budget;
        })
    },
    decrementBudgedBoard (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            board.budget -= item.budget;
        })
    },

    /**
     * Tasks
     */
    addTask (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            let task = board.tasks.find(task => task.id === item.id);

            if (task) {
                return Object.assign(task, item);
            }

            board.tasks.push(item);
        });
    },
    setTasks (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            board.tasks = item;
        });
    },

    changeTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            Object.assign(task, item);
        });
    },
    removeTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            tasks.splice(index, 1);
        })
    },

    decrementLoggedTimerInTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index, board) => {
            board.trackedTime = board.trackedTime - Math.abs(item.logged_time);

            let trakedTime = {...task.tracked_time};

            if (task.tracked_time && task.tracked_time[item.user.id]) {
                trakedTime[item.user.id].time = task.tracked_time[item.user.id].time - Math.abs(item.logged_time);
                trakedTime[item.user.id].activity_at = item.timer.updated_at;
            }

            Object.assign(task, {
                logged_time: task.logged_time - Math.abs(item.logged_time),
                tracked_time:   trakedTime
            });
        });
    },
    incrementLoggedTimerInTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index, board) => {
            board.trackedTime = board.trackedTime + Math.abs(item.logged_time);

            let trakedTime = {...task.tracked_time};

            if (task.tracked_time && task.tracked_time[item.user.id] ) {
                trakedTime[item.user.id].time = task.tracked_time[item.user.id].time + Math.abs(item.logged_time);
            } else {
                trakedTime[item.user.id] = {};
                trakedTime[item.user.id].time = Math.abs(item.logged_time);
            }

            trakedTime[item.user.id].activity_at = item.timer.updated_at;

            Object.assign(task, {
                logged_time: task.logged_time + Math.abs(item.logged_time),
                tracked_time:   trakedTime
            });
        });
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
