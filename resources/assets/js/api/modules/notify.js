import http from '@utils/http';
import store from '@store';

export default {
	getNotifications() {
		let page = store.getters['notify/getCurrentPage'] + 1;

		return new Promise((resolve, reject) => {
			http.get(`/api/v1/notification?page=${page}`).then(response => {
				store.dispatch('notify/addMessage', response.data.data);
				store.dispatch('notify/setLastPage', response.data.pagination.total_pages);

				store.dispatch('notify/incrementCurrentPage');

				resolve(response.data);
			}, error => {
				reject(error);
			})
		});
	},
	markAllNotificationsRead() {
		return new Promise((resolve, reject) => {
			http.get(`/api/v1/notification/all_mark_read`).then(response => {
				store.dispatch('notify/markAllRead');
				store.dispatch('groups/readAllNotification');

				resolve(response.data);
			}, error => {
				reject(error);
			})
		});
	},
	markNotificationRead(notifyId) {
		return new Promise((resolve, reject) => {
			http.get(`/api/v1/notification/${notifyId}/read`).then(response => {
				store.dispatch('notify/markRead', {notifyId});
				resolve(response.data);
			}, error => {
				reject(error);
			})
		});
	},
	markNotificationUnRead(notifyId) {
		return new Promise((resolve, reject) => {
			http.get(`/api/v1/notification/${notifyId}/unread`).then(response => {
				store.dispatch('notify/markUnRead', notifyId);

				resolve(response.data);
			}, error => {
				reject(error);
			})
		});
	},
	getUnreadNotifications() {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/notification/unread`).then(response => {
                store.dispatch('notify/setUnreadMessages', response.data.notifications);
                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
	}
};
