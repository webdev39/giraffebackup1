import http     from '@utils/http'

export default {
    createMedia(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/attachment/save', data, {'Content-Type': 'multipart/form-data'}).then((response) => {

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    }
}
