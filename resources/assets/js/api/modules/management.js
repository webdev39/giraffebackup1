import http     from '@utils/http';
import router   from '@router';
import store    from '@store';

let request = {
    getGroup: false
};

export default {
    getManagement(isArchived = null) {
        if (request.getGroup) {
            return;
        }

        request.getGroup = true;

        return new Promise((resolve, reject) => {
            let param = isArchived === null ? {} : isArchived ? 1 : 0;

            http.get('/api/v1/group/all?board_res=long', param).then((response) => {
                if (response.data.groups === Object(response.data.groups) && !Array.isArray(response.data.groups)) {
                    response.data.groups = Object.values(response.data.groups);
                }

                store.dispatch('management/setGroups', response.data.groups);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getGroup = false;
            })
        })
    },
};
