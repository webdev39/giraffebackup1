import http     from '@utils/http'
import store    from '@store'
import find     from '@helpers/findInGroups'

export default {
    getLastBoards () {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/board/last`).then((response) => {
                store.dispatch('groups/setLastBoardsIds', response.data.boards.map(item => item.id));

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getBoardById(boardId, param = '') {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/board/${boardId}?${param}`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    createBoard(data, param) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/board/create?${param}`, data).then((response) => {
                store.dispatch('groups/addBoard', response.data.board);
                store.dispatch('reports/addBoard', response.data.board);
                store.dispatch('management/addBoard', response.data.board);

                store.dispatch('priorities/addPriorities', response.data.priorities);

                window.app.$notify({type:'success', text: window.app.$t('create_board_success')});
                window.app.$api.tag.getTags();

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateBoard(data, param, changeGroup = false) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/board/update?${param}`, data).then((response) => {

                window.app.$api.tag.getTags();

                if (response.data.board) {

                    if (!changeGroup) {
                        store.dispatch('groups/changeBoard', response.data.board);
                        store.dispatch('management/changeBoard', response.data.board);
                        store.dispatch('reports/changeBoard', response.data.board);
                    } else {
                        if (data.members.length) {
                            store.dispatch('groups/addGroupMembers', data);
                        }
                        store.dispatch('groups/removeBoard', find.searchBoardById(store.getters['groups/getStateGroups'], response.data.board.id));
                        store.dispatch('management/removeBoard', find.searchBoardById(store.getters['groups/getStateGroups'], response.data.board.id));
                        store.dispatch('groups/addBoard', response.data.board);
                        store.dispatch('management/addBoard', response.data.board);
                    }

                    window.app.$notify({type:'success', text: window.app.$t('update_board')});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeBoard(board) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/board/${board.board_id}`).then((response) => {
                if (response.data.is_archive) {
                    store.dispatch('groups/changeBoard', response.data.board);
                    store.dispatch('management/changeBoard', response.data.board);
                    store.dispatch('reports/changeBoard', response.data.board);

                    window.app.$notify({type:'success', text: window.app.$t('change_board')});
                } else {
                    store.dispatch('groups/removeBoard', board);
                    store.dispatch('management/removeBoard', board);
                    store.dispatch('reports/removeBoard', board.board_id);

                    window.app.$notify({type:'success', text: window.app.$t('remove_board')});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    archiveBoard(boardId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/board/${boardId}/archived`).then((response) => {
                store.dispatch('groups/changeBoard', response.data.board);
                store.dispatch('management/changeBoard', response.data.board);
                store.dispatch('reports/changeBoard', response.data.board);

                window.app.$notify({type:'success', text: window.app.$t('archive_board')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    unarchiveBoard(boardId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/board/${boardId}/unarchived`).then((response) => {
                response.data.board['is_archive'] = response.data.board.is_archive;

                store.dispatch('groups/changeBoard', response.data.board);
                store.dispatch('management/changeBoard', response.data.board);
                store.dispatch('reports/changeBoard', response.data.board);

                window.app.$notify({type:'success', text: window.app.$t('un_archive_board')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
};
