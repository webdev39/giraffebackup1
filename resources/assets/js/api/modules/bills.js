import http     from '@utils/http'

export default {
    getFilter(data) {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/bill/filter', data).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getBills({ year, start_month, end_month, client }) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/bill/list`, {
                year: year,
                start_month: start_month,
                end_month: end_month,
                client: client
            }).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getBill(billId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/bill/edit/${billId}`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getAddBill() {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/bill/add`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    addToBill(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/bill/add', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    createBill(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/bill/create', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateStatuses(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/billing/statuses/update', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateStatus(form) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/billing/status/update', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    updateBill(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/bill/edit', form).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeBill(billId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/bill/delete/${billId}`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    downloadPdf(billId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/bill/${billId}/download`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getLogsBill(billId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/bill/${billId}/logs`).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
}
