import VueI18n      from 'vue-i18n'
import Vue          from 'vue'
import store        from '@store'

import en from '@lang/en'
import de from '@lang/de'

Vue.use(VueI18n);

const messages = {
    en: en,
    de: de,
};


// Create VueI18n instance with options
export default new VueI18n({
    locale: 'en',
    messages,
})