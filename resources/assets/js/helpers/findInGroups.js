// Optimized

function checkMatchId(findObj, findKey, currentKey, currentId) {
    let findId = findObj[findKey + '_id'];

    if (findId) {
        return findId === currentId;
    } else if (findKey === currentKey) {
        return findObj['id'] === currentId;
    } else if (findObj['id']) {
        return findKey !== currentKey;
    } else {
        return false;
    }
}


export default {
    /**
     * Find object in collection
     *
     * @param groups
     * @param findObj
     * @param key
     * @param callback
     */
    search(groups, findObj, key, callback) {
        if (key === 'groups') {
            return callback(groups, null, null, null, null);
        }

        return groups.find((group, groupIndex) => {

            if (checkMatchId(findObj, 'group', key, group.id)) {
                if (key === 'group') {
                    callback(groups, group, groupIndex, null, null);
                    return true;
                }

                if (key === 'boards') {

                    callback(group.boards, null, null, null, group);
                    return true;
                }

                group.boards.find((board, boardIndex) => {
                    if (checkMatchId(findObj, 'board', key, board.id)) {
                        if (key === 'board') {
                            callback(group.boards, board, boardIndex, null, group);
                            return true;
                        }

                        if (key === 'tasks') {
                            callback(board.tasks, null, null, board, group);
                            return true;
                        }

                        board.tasks.find((task, taskIndex) => {
                            if (checkMatchId(findObj, 'task', key, task.id)) {
                                if (key === 'task') {
                                    callback(board.tasks, task, taskIndex, board, group);
                                    return true;
                                }
                            }
                        });
                    }
                });
            }
        });
    },

    /**
     * Search tasks by ids
     *
     * @param groups
     * @param taskIds
     * @returns {Array}
     */
    searchTasksByIds(groups, taskIds, callback) {
        let tasks = [];

        groups.find((group) => {
            group.boards.find((board) => {
                board.tasks.find((task) => {
                    if (taskIds.includes(task.id.toString())) {
                        if (callback) {
                            return callback(task, board, group);
                        }

                        tasks.push(task);
                    }
                });
            });
        });

        return tasks;
    },

    searchUnArchivedTasksByIds(groups, taskIds) {
        let tasks = [];

        this.searchTasksByIds(groups, taskIds, function (task, board, group) {
            if (!task.is_archive && !board.is_archive && !group.is_archive) {
                tasks.push(task);
            }
        });

        return tasks;
    },

    /**
     * Search task by id
     *
     * @param groups
     * @param taskId
     * @returns {*}
     */
    searchTaskById(groups, taskId) {
        let result = null;

        groups.find((group) => {
            return group.boards.find((board) => {
                return board.tasks.find((task) => {
                    if (task.id === taskId) {
                        result = task;
                        return true;
                    }
                });
            });
        });

        return result;
    },

    /**
     * Search board by id
     *
     * @param groups
     * @param boardId
     * @returns {*}
     */
    searchBoardById(groups, boardId) {
        let result = null;
        
        groups.find((group) => {
            return group.boards.find((board) => {
                if (board.id === boardId) {
                    result = board;
                    return true;
                }
            });
        });

        return result;
    },

    /**
     * Search boards by ids
     *
     * @param groups
     * @param boardIds
     * @returns {Array}
     */
    searchBoardsByIds(groups, boardIds) {
        let boards = [];

        groups.find((group) => {
            return group.boards.find((board) => {
                if (boardIds.includes(board.id.toString())) {
                    boards.push(board);
                }
            });
        });

        return boards;
    },

    /**
     * Search group by id
     *
     * @param groups
     * @param groupId
     * @returns {Array}
     */
    searchGroupById(groups, groupId) {
        return groups.find((group) => group.id === groupId);
    },

    /**
     * Search group by board id
     *
     * @param groups
     * @param boardId
     * @returns {*}
     */
    searchGroupByBoardId(groups, boardId) {
        return groups.find((group) => {
            return group.boards.find((board) => board.id === boardId);
        });
    },

    /**
     * Search group by task id
     *
     * @param groups
     * @param taskId
     * @returns {*}
     */
    searchGroupByTaskId(groups, taskId) {
        return groups.find((group) => {
            return group.boards.find((board) => {
                return board.tasks.find(task => task.id === taskId);
            });
        });
    },

    /**
     * Search task by user
     *
     * @param task
     * @param userId
     * @returns {*}
     */
    searchTaskByUser(task, userId) {
        return task.subscribers.task.some( taskSubscribersId => userId === taskSubscribersId)
    },

    /**
     * Filter members in group
     *
     * @param group
     * @param members
     * @returns {*}
     */
    searchMembersInGroups(group, members) {
        return members.filter(member => group.members.includes(member.id)).sort((a, b) => {
            return sorter(a.user.nickname, b.user.nickname)
        });
    },
}
