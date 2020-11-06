import Vue                      from 'vue'
import Vuex                     from 'vuex'
import camelCase                from 'lodash/camelCase'

/*----------   Global   ----------*/
import actions                  from './globals/actions'
import getters                  from './globals/getters'
import state                    from './globals/state'
import mutations                from './globals/mutations'

/*----------   Other   ----------*/
import plugins                  from '@store/plugins'
import config                   from '@config'

Vue.use(Vuex);

const requireModules = require.context("./modules", false, /\.js$/);
const modules = {};

requireModules.keys().forEach(fileName => {
    const moduleName = camelCase(fileName.replace(/(\.\/|\.js)/g, ""));

    modules[moduleName] = requireModules(fileName).default;
});

export default new Vuex.Store({
    actions,
    getters,
    mutations,
    state,
    modules,
    plugins,
    strict: config.debug,
})