import archived     from '@helpers/archived'
import find         from '@helpers/findInGroups'
import task         from '@helpers/task'

// Optimized
const getDefaultState = () => {
    return {
        list:           [],
        permissions:    [],
        currentBoardId: null,
        currentTaskId: null,
        selectTasksIds: {},
        tasksDeadline: {
            today: [],
            week: [],
        },
        last: {
            boardsIds: [],
            tasksIds: []
        },
        sortTypes: [
            { name: 'sort_weight',  alias: 'group_sorting'},
            { name: 'priority',     alias: 'priority'},
            { name: 'a-z',          alias: 'a_z'},
            { name: 'todo',         alias: 'todo'},
            { name: 'deadline',     alias: 'deadline'},
            { name: 'date',         alias: 'date'},
            { name: 'assignee',     alias: 'assignee'},
        ],
        defaultSortType: { name: 'sort_weight', alias: 'group_sorting' },
        selectSortType: { name: 'sort_weight',  alias: 'group_sorting' },
        ganttTypesView: [
            { name: 'hours',    alias: 'Hours'},
            { name: 'days',     alias: 'Days'},
        ],
        selectGanttTypeView: { name: 'hours', alias: 'Hours' },
        rangeGanttTypeView: { name: 'hours', alias: 'Hours' },
        splitted: [
            { name: true,   alias: 'yes'},
            { name: false,  alias: 'no'}
        ],
        selectSplitted: { name: true, alias: 'yes' },
        quickNavigation: [
            { name: 1, alias: 'yes'},
            { name: 0, alias: 'no'}
        ],
    };
};

const state = getDefaultState();

const getters = {
    getCurrentTask(state) {
        let currentTask = {};

        if (state.currentTaskId) {
            find.search(state.list, {id: state.currentTaskId}, 'task', (tasks, task, index, board, group) => {
                Object.assign(currentTask, task, {
                    board: { name: board.name, id: board.id },
                    group: { name: group.name, members: group.members }
                })
            });
        }

        if (Object.keys(currentTask).length === 0) {
            return {}
        }

        return currentTask;
    },
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
    getGroups(state, getters) {
        return getters.getActivedGroups.map((group) => {
            return Object.assign({}, group, {boards: group.boards.filter(board => !board.is_archive)});
        });
    },
    getActivedGroups(state, getters) {
        return getters.getStateGroups.filter(group => !group.is_archive);
    },
    getArchivedGroups(state, getters) {
        return getters.getStateGroups.filter(group => group.is_archive)
    },
    getGroupsPermissions(state) {
        return state.permissions;
    },

    getBoards(state) {
        let boards = [];

        state.list.filter(group => {
            group.boards.filter(board => {
                boards.push(board);
            });
        });

        return boards.sort((a, b) => {
            return sorter(a.name, b.name)
        });
    },

    getCurrentBoard(state) {
        if (state.currentBoardId) {
            return find.searchBoardById(state.list, state.currentBoardId)
        }
    },
    getCurrentDraftTask(state, getters, context, rootGetters) {
        if (getters.getCurrentBoard && rootGetters.getLoggedIn) {
            return getters.getCurrentBoard.tasks.find(task => task.draft)
        }
    },
    getTaskByIds(state, getters) {
        return find.searchUnArchivedTasksByIds(state.list, Object.keys(getters.getSelectTasksIds));
    },
    getSelectTasksIds(state) {
        return state.selectTasksIds;
    },

    getTasksDeadline(state) {
        return state.tasksDeadline;
    },

    getLastTasks(state) {
        return find.searchTasksByIds(state.list, Object.keys(state.last.tasksIds));
    },
    getLastBoards(state) {
        return find.searchBoardsByIds(state.list, Object.keys(state.last.boardsIds));
    },
    getSortTypes (state) {
        return state.sortTypes;
    },
    getSelectSortType (state) {
        return state.selectSortType;
    },
    getSelectGanttTypeView (state) {
        return state.selectGanttTypeView;
    },
    getRangeGanttTypeView (state) {
        return state.rangeGanttTypeView;
    },
    getGanttTypesView (state) {
        return state.ganttTypesView;
    },
    getSplitted (state) {
        return state.splitted;
    },
    getSelectSplitted (state) {
        return state.selectSplitted
    },
    getQuickNavigation (state) {
        return state.quickNavigation;
    },
};

const actions = {

    resetGroupsState({ commit }) {
        commit('resetGroupsState');
    },

    /**
     * Setters
     */
    setGroups({ commit }, group) {
        commit('setGroups', group);
    },
    setGroupsPermissions({ commit }, permissions) {
        commit('setGroupsPermissions', permissions);
    },

    setTasks ({commit}, tasks) {
        commit('setTasks', tasks);
    },
    setTasksIds ({commit}, tasksIds) {
        commit('setTasksIds', tasksIds);
    },
    setTasksListByBoardId({commit}, payload) {
        commit('setTasksListByBoardId', payload);
    },
    changeTasksIds ({commit}, tasksIds) {
        commit('changeTasksIds', tasksIds);
    },

    setLastTasksIds({commit}, tasksIds) {
        commit('setLastTasksIds', tasksIds);
    },
    setLastBoardsIds({commit}, boardsIds) {
        commit('setLastBoardsIds', boardsIds);
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

    setCurrentBoardId ({commit}, boardId) {
        commit('setCurrentBoardId', boardId);
    },
    clearCurrentBoardId ({commit}) {
        commit('clearCurrentBoardId');
    },
    incrementBudgedBoard({commit}, item) {
        commit('incrementBudgedBoard', item);
    },
    decrementBudgedBoard({commit}, item) {
        commit('decrementBudgedBoard', item);
    },

    /**
     * Tasks
     */
    setCurrentTaskId({commit}, id) {
        commit('setCurrentTaskId', id);
    },
    addTask ({commit}, task ) {
        commit('addTask', task);
    },
    changeTask ({commit}, task) {
        commit('changeTask', task);
    },
    changeSubscribersInTask ({commit}, task) {
        commit('changeSubscribersInTask', task);
    },
    attachSubscribers({commit}, task) {
        commit('attachSubscribers', task);
    },
    detachSubscribers({commit}, task) {
        commit('detachSubscribers', task);
    },
    subscribeSubscribers({commit}, task) {
        commit('subscribeSubscribers', task);
    },
    unsubscribeSubscribers({commit}, task) {
        commit('unsubscribeSubscribers', task);
    },
    readAllNotification({commit}) {
        commit('readAllNotification');
    },

    removeTask ({commit}, task ) {
        commit('removeTask', task);
    },
    changeAttachmentTasksCount ({commit}, task) {
        commit('changeAttachmentTasksCount', task);
    },
    changeCommentsTasksCount ({commit}, task) {
        commit('changeCommentsTasksCount', task);
    },
    decrementLoggedTimerInTask ({commit}, payload) {
        commit('decrementLoggedTimerInTask', payload);
    },
    incrementLoggedTimerInTask ({commit}, payload) {
        commit('incrementLoggedTimerInTask', payload);
    },
    changeTaskSortOrder({commit}, payload) {
        commit('changeTaskSortOrder', payload);
    },

    setTaskSortWeight({commit}, payload) {
        commit('setTaskSortWeight', payload);
    },

    setFilterTaskIds({commit}, taskIds) {
        commit('setFilterTaskIds', taskIds);
    },
    clearTasksIds ({commit}) {
        commit('clearTasksIds');
    },

    setTasksDeadline ({ commit }, tasks) {
        commit('setTodayTaskIds', tasks.today);
        commit('setWeekTaskIds', tasks.week);
    },
    setTodayTaskIds({commit}, taskIds) {
        commit('setTodayTaskIds', taskIds);
    },
    setWeekTaskIds({commit}, taskIds) {
        commit('setWeekTaskIds', taskIds);
    },
    addTaskDeadline({commit}, data) {
        commit('addTaskDeadline', data);
    },
    removeTaskDeadline({commit}, data) {
        commit('removeTaskDeadline', data);
    },

    /** Sub tasks **/
    addSubtaskInTask ({commit}, sub_task) {
        commit('addSubtaskInTask', sub_task);
    },
    changeSubtaskInTask ({commit}, sub_task) {
        commit('changeSubtaskInTask', sub_task);
    },
    removeSubtaskInTask ({commit}, task ) {
        commit('removeSubtaskInTask', task);
    },

    /*sort*/
    changeSelectSortType ({commit}, payload) {
        commit('changeSelectSortType', payload);
    },
    setDefaultSelectSortType ({commit}) {
        commit('setDefaultSelectSortType');
    },

    changeSelectSplitted({commit}, payload) {
        commit('changeSelectSplitted', payload);
    },

    changeSelectGanttTypeView ({commit}, payload) {
        commit('changeSelectGanttTypeView', payload);
    },

    changeRangeGanttTypeView ({commit}, payload) {
        commit('changeRangeGanttTypeView', payload);
    },
};

const mutations = {

    resetGroupsState (state) {
        Object.assign(state, getDefaultState())
    },

    /**
     * Setters
     */
    setGroups (state, groups) {
        state.list = groups;
    },
    setGroupsPermissions(state, permissions) {
        state.permissions = permissions;
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
    addGroupMembers (state, board) {
        find.search(state.list, board, 'group', (groups, group, index) => {
            group.members = [...group.members, ...board.members]
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
    setCurrentBoardId (state, boardId) {
        state.currentBoardId = boardId;
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
    clearCurrentBoardId (state) {
        state.currentBoardId = null
    },

    /**
     * Tasks
     */
    setCurrentTaskId(state, id) {
        state.currentTaskId = id
    },
    addTask (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            let task = board.tasks.find(task => task.id === item.id);
            if (task) {
                board.tasks = board.tasks.filter(task => task.id !== item.id);
            } else {
                if (! item.draft) {
                    board.tasks_count += 2
                }
            }
            board.tasks.push(item);
        });
    },
    setTasks (state, item) {
        find.search(state.list, item, 'board', (boards, board, index) => {
            board.tasks = item;
        });
    },
    setTasksListByBoardId(state, payload) {
        if ("board_id" in payload) {
            let board = find.searchBoardById(state.list, Number(payload.board_id));
            return board.tasks = payload.tasks;
        }
        payload.tasks.forEach(item => {
            let board = find.searchBoardById(state.list, Number(item.board_id));
            if (board) {
                let task = board.tasks.find(task => task.id === item.id);

                if (task) {
                    return Object.assign(task, item);
                }

                board.tasks.push(item);
            }
        });
    },
    setTasksIds (state, setTasksIds) {
        state.selectTasksIds = setTasksIds;
    },
    changeTasksIds (state, setTasksIds) {
        state.selectTasksIds = Object.assign({}, state.selectTasksIds, setTasksIds);
    },
    clearTasksIds (state) {
        state.selectTasksIds = {};
    },
    changeTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            Object.assign(task, item);
        });
    },
    removeTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            tasks.splice(index, 1);
        });
        find.search(state.list, item, 'board', (boards, board, index) => {
            board.tasks_count--;
        });
    },

    readAllNotification(state) {
        state.list.map(group => group.boards.map(board => board.tasks.map(task => task.unreadNotificationsCount = 0)))
    },

    changeSubscribersInTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            if (item.action === 'remove') {
                task.subscribers[item.subscriberType] = task.subscribers[item.subscriberType].filter(subscriberId => subscriberId !== item.subscriberId);
            } else {
                task.subscribers[item.subscriberType].push(item.subscriberId);
            }
        });
    },


    /**
     * Subscribers
     */

    attachSubscribers(state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            task.subscribers['task'].push(item.subscriberId);
        });
    },
    detachSubscribers(state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            task.subscribers['task'] = task.subscribers['task'].filter(subscriberId => subscriberId !== item.subscriberId);
        });
    },
    subscribeSubscribers(state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            task.subscribers['notify'].push(item.subscriberId);
        });
    },
    unsubscribeSubscribers(state, item) {
        find.search(state.list, item, 'task', (tasks, task, index) => {
            task.subscribers['notify'] = task.subscribers['notify'].filter(subscriberId => subscriberId !== item.subscriberId);
        });
    },


    /**
     * Sub tasks
     */
    addSubtaskInTask (state, item) {
        find.search(state.list, {id: item.task_id}, 'task', (tasks, task, index) => {
            task.count.open_sub_task +=  1;
            Object.assign(task, {sub_tasks: [...task.sub_tasks].concat([item])});
        });
    },
    changeSubtaskInTask (state, item) {
        find.search(state.list, {id: item.task_id}, 'task', (tasks, task, index) => {

            if (item.is_completed) {
                task.count.open_sub_task -=  1;
                task.count.done_sub_task +=  1;

            } else {
                task.count.open_sub_task +=  1;
                task.count.done_sub_task -=  1;
            }

            task.sub_tasks.find(sub_task => {
                if (sub_task.id === item.id) {
                    Object.assign(sub_task, item);
                    return true;
                }
            });
        });
    },
    removeSubtaskInTask (state, item) {
        find.search(state.list, item, 'task', (tasks, task) => {
            task.sub_tasks = task.sub_tasks.filter(sub_task => sub_task.id !== item.id);
        });
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

            if (task.tracked_time && task.tracked_time[item.user.id]) {
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

    changeTaskSortOrder(state, payload) {
        find.search(state.list, payload, 'tasks', (tasks, task, index) => {
            tasks.map(task => {
                payload.order.map((item, index) => {
                    if (task.id === Number(item)) {
                        if (task.sort_order !== undefined) {
                            task.sort_order[payload.sort_type] = index + 1;
                        }
                    }
                })
            })
        });
    },

    setTaskSortWeight (state, payload) {
        find.searchTaskById(state.list, payload.id).sort_weight = payload.sort_weight;
    },

    changeAttachmentTasksCount(state, item) {
        if(item.task_id) {
            let task = find.searchTaskById(state.list, item.task_id);
            if (task.hasOwnProperty('count')) {
                task.count.attachment += parseInt(item.count);
            }
        }
    },
    changeCommentsTasksCount(state, item) {
        if (item.task_id) {
            let task = find.searchTaskById(state.list, item.task_id);
            if(task.hasOwnProperty('count')) {
                task.count.comment += parseInt(item.count);
            }
        }
    },

    setFilterTaskIds(state, taskIds) {
        state.taskFilter = taskIds;
    },

    setTodayTaskIds(state, taskIds) {
        state.tasksDeadline.today = taskIds;
    },
    setWeekTaskIds(state, taskIds) {
        state.tasksDeadline.week = taskIds;
    },
    addTaskDeadline(state, data) {
        if (!state.tasksDeadline[data.period].some(item => item.task_id === data.task_id)) {
            state.tasksDeadline[data.period].push({
                task_id:    data.task_id,
                sort_order: null,
            });
        }
    },
    removeTaskDeadline(state, data) {
        state.tasksDeadline[data.period] = state.tasksDeadline[data.period].filter(item => item.task_id !== data.task_id);
    },
    setLastTasksIds(state, taskIds) {
        state.last.tasksIds = taskIds;
    },
    setLastBoardsIds({commit}, boardsIds) {
        state.last.boardsIds = boardsIds;
    },

    /*Sort*/
    changeSelectSortType(state, payload) {
        state.selectSortType = payload
    },
    setDefaultSelectSortType(state) {
        state.selectSortType = state.defaultSortType;
    },

    changeSelectSplitted(state, payload) {
        state.selectSplitted = payload;
    },

    changeSelectGanttTypeView(state, payload) {
        state.selectGanttTypeView = payload;
    },

    changeRangeGanttTypeView(state, payload) {
        state.rangeGanttTypeView = payload;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
