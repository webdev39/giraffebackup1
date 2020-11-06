import http     from '@utils/http'
import store    from '@store'

export default {
    getTags () {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/tags`).then(response => {
                store.dispatch('tags/setTags', response.data.data);
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
};