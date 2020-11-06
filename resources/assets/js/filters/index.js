import Vue from 'vue';
import moment from 'moment-timezone';

const filters = {

    init() {
        Vue.filter('capitalize', this.methods.capitalize);

        Vue.prototype.$filters = { ...this.methods };
    },

    methods: {

        capitalize(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

    }

};

export default filters;
