import http     from '@utils/http'

export default {
    find(query) {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/search', { query: query }).then((response) => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
}
