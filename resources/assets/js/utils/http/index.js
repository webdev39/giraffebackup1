import Axios                from 'axios'
import Vue                  from 'vue'

import config               from '@config'
import store                from '@store'
import auth                 from '@helpers/auth'

const http = Axios.create({
    baseURL: config.http.url,
    headers: config.http.defaultRequest.headers,
    
});

/**
 * Sets the X-CSRFToken header for ajax non-GET request to make CSRF protection easy.
 */
if (Laravel.csrf_token) {
    http.defaults.headers.common['X-CSRF-TOKEN'] = Laravel.csrf_token;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Add a request and response interceptor
 */
const beforeRequestSuccess = (config) =>
{
    config.headers.Authorization = `Bearer ${store.getters.getToken}`;

    store.dispatch('loading/setLoading', true);

    return config;
};

const beforeRequestError = (error) =>
{
    store.dispatch('loading/setLoading', false);

    return Promise.reject(error);
};

const onSuccess = (response) =>
{
    store.dispatch('loading/setLoading', false);

    return response;
};

const onError = (error) =>
{
    if (error.response) {
        switch (error.response.status) {
            case 401:
                auth.logout(true);
                break;
            default:
                break;
        }
    }

    store.dispatch('loading/setLoading', false);

    return Promise.reject(error);
};

http.interceptors.request.use(beforeRequestSuccess, beforeRequestError);
http.interceptors.response.use(onSuccess, onError);

/**
 * Change get request
 *
 * @param url
 * @param param
 * @param config
 * @returns {*}
 */
http.get = function(url, param, config) {
    return http({method: 'GET', url: url, params: param, config: config})
};

/**
 * Change delete request
 *
 * @param url
 * @param param
 * @param config
 * @returns {*}
 */
http.delete = function(url, param, config) {
    return http({method: 'DELETE', url: url, params: param, config: config})
};

export default http;
