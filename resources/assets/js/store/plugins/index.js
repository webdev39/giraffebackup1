import createLogger             from 'vuex/dist/logger'
import createPersistedState     from 'vuex-persistedstate'

/*----------   Other   ----------*/
import config                   from '@config';

/**
 * List of mutations that need to be stored in localStorage
 *
 * @type {string[]}
 */
const mutations = [];

/**
 * List of state variables that will be stored in localStorage
 *
 * @type {string[]}
 */
const paths = [
    'token',
    'deviceToken',
    'lastRoute',
    'sidebarShow',
    'user.id',
    'user.firstName',
    'user.lastName',
    'user.nickname',
    'user.email',
    'user.primaryColor',
    'user.secondaryColor',
    'user.language',
    'user.view_types'
];

/**
 * List enabled plugins
 *
 * @type {Array}
 */
const plugins = [];

/**
 * When logging in from another user, disable synchronization from the locale storage
 */
if (!localStorage.getItem('memberToken')) {
    plugins.push(createPersistedState({
        paths: paths,
        filter: mutation => (mutations.indexOf(mutation.type) === -1), // Boolean
    }));
}

/**
 * Output of mutations to the console log
 */
if (config.debug && config.vuexLogger) {
    plugins.push(createLogger());
}

export default plugins
