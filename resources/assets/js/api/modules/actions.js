import http     from '@utils/http'
import store    from '@store'

import arrayToTree  from 'array-to-tree'

export default {
    getActionsByTaskId(taskId, currentPage, filters, showNewElement = null) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/action/task/${taskId}?page=${currentPage}`, filters).then((response) => {

                response.data.data.map(item => {
                    if (item.source === 'comment') {
                        item.replies = arrayToTree(item.replies, {childrenProperty: 'replies'});
                    }

                    return item;
                });

                if (showNewElement) {
                    response.data.data[0].new_comment = true
                }

                if (Number(taskId) === Number(store.getters['task/getTaskId'])) {
                    store.dispatch('task/addActions', response.data.data);
                }

                resolve(response.data);
            }, (error) => {
                reject(error);
            })
        })
    },
    getActionsByBoardId(boardId, currentPage, filters) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/action/board/${boardId}?page=${currentPage}`, filters).then((response) => {
                store.dispatch('actions/addActions', response.data.data);
                resolve(response.data);
            }, (error) => {
                reject(error);
            })
        })
    },
    getActionsByGroupId(groupId, currentPage, filters) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/action/group/${groupId}?page=${currentPage}`, filters).then((response) => {
                store.dispatch('actions/addActions', response.data.data);
                resolve(response.data);
            }, (error) => {
                reject(error);
            })
        })
    },
};
