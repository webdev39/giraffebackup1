import http from '@utils/http';
import store from '@store';

export default {

	create(request) {
		return new Promise((resolve, reject) => {
			http.post('/api/v1/reviews/create', request)
				.then(response => {
					resolve(response.data);
				}, error => {
					reject(error);
				})
		});
	}
}