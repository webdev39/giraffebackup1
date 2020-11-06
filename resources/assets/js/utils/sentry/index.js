import Vue                  from 'vue'
import Raven                from 'raven-js'
import RavenVue             from 'raven-js/plugins/vue'

import config               from '@config'

if (config.api.sentry.dns) {
    Raven.config(config.api.sentry.dns, {release: config.release}).addPlugin(RavenVue, Vue).install();
}

export default Raven;