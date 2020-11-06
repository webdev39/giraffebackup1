import http     from '@utils/http'
import store    from '@store'
import FileDownload from 'js-file-download';

let request = {
    getBoards: false,
    getGroups: false,
};

export default {
    getBoards () {
        return new Promise((resolve, reject) => {
            // if (request.getBoards || store.getters['reports/getBoards'].length > 0) {
            //     return resolve({});
            // }

            request.getBoards = true;

            http.get(`/api/v1/reports/boards`).then((response) => {
                if (response.data.boards === Object(response.data.boards) && !Array.isArray(response.data.boards)) {
                    response.data.boards = Object.values(response.data.boards);
                }

                store.dispatch('reports/setBoards', response.data.boards);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getBoards = false;
            });
        })
    },
    getGroups () {
        return new Promise((resolve, reject) => {
            if (request.getGroups || store.getters['reports/getGroups'].length > 0) {
                return resolve({});
            }

            request.getGroups = true;

            http.get(`/api/v1/reports/groups`).then((response) => {
                if (response.data.groups === Object(response.data.groups) && !Array.isArray(response.data.groups)) {
                    response.data.groups = Object.values(response.data.groups);
                }

                store.dispatch('reports/setGroups', response.data.groups);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getGroups = false;
            });
        })
    },
    getFilter(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/reports/filter', form).then(response => {
                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    getFeedReportsExport(form) {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/reports/export', form).then(response => {
                FileDownload(response.data, 'OC_Reports.csv');
                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
};


