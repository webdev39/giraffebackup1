import Vue from 'vue'
import VModal from 'vue-js-modal'
import VueTextareaAutosize from 'vue-textarea-autosize'
import VueNotifications from 'vue-notification'
import VueMultiselect from 'vue-multiselect'
import VueTouchEvents from 'vue2-touch-events'
import PortalVue from 'portal-vue'
import VuePellEditor from 'vue-pell-editor'
import VueTour from 'vue-tour';
import VueDragscroll from 'vue-dragscroll';
import {VTooltip, VPopover} from 'v-tooltip';

require('vue-tour/dist/vue-tour.css');

// Comment because is not used
// import VueCookie                from 'vue-cookie'

import natsort from 'natsort'
import lodash from 'lodash'
import moment from 'moment-timezone'
import {extendMoment} from 'moment-range'
import arrayToTree from 'array-to-tree'

import api from '@api'
import config from '@config'
import router from '@router'
import store from '@store'
import i18n from "@lang"

import bootstrap from '@utils/bootstrap'
import quill from '@utils/quill'
import events from '@utils/events'
import pusher from '@utils/pusher'
// import sentry                   from '@utils/sentry'
import http from '@utils/http'

import globalMixin from '@mixins/global'
import permissionsMixin from '@mixins/permissions'
import timeMixin from '@mixins/time'

import App from '@/VueApp'

import filters from "@/filters";

var Rollbar = require('vue-rollbar');

Vue.use(Rollbar, {
	accessToken: '5be67f3ab70e42608fad353635c1b426',
	captureUncaught: true,
	captureUnhandledRejections: true,
	enabled: true,
	environment: 'frontend',
	payload: {
		client: {
			javascript: {
				code_version: '1.0',
				source_map_enabled: true,
				guess_uncaught_frames: true
			}
		}
	}
});

filters.init();

Vue.prototype.$api = api;
Vue.prototype.$http = http;
// Vue.prototype.$sentry  = sentry;
Vue.prototype.$pusher = pusher;
Vue.prototype.$config = config;
Vue.prototype.$moment = extendMoment(moment);
Vue.prototype.$lodash = lodash;
Vue.prototype.$event = events;
Vue.prototype.$arrayToTree = arrayToTree;

Vue.config.productionTip = config.debug;

Vue.mixin(globalMixin);
Vue.mixin(permissionsMixin);
Vue.mixin(timeMixin);

Vue.component('multiselect', VueMultiselect);

// Vue.use(VueCookie);
Vue.use(VueTouchEvents);
Vue.use(VueNotifications);
Vue.use(VueTextareaAutosize);
Vue.use(PortalVue);
Vue.use(VuePellEditor);
Vue.use(VueTour);
Vue.use(VueDragscroll);
Vue.directive('tooltip', VTooltip);
Vue.component('v-popover', VPopover);

Vue.use(VModal, {
	dialog: true,
	scrollable: true,
	adaptive: true,
});

/**
 * Add redirect page into store
 */
if (! store.getters.getToken) {
	if (document.location.pathname !== '/') {
		store.dispatch('setLastRoute', document.location.pathname + document.location.search);
	}
}

/**
 * For fixin modal height on mobile devices
 * ========================================
 */
function setVH() {
	let vh = window.innerHeight * 0.01;
	document.documentElement.style.setProperty('--vh', `${vh}px`);
}

window.addEventListener('resize', () => {
	setVH();
});

window.rInterval = function (callback, delay) {
	let dateNow = Date.now,
		requestAnimation = window.requestAnimationFrame,
		start = dateNow(),
		stop;

	let intervalFunc = function () {
		dateNow() - start < delay || (start += delay, callback());
		stop || requestAnimation(intervalFunc)
	};

	requestAnimation(intervalFunc);

	return {
		clear: function () {
			stop = 1
		}
	}
};

setVH();

/**
 * ========================================
 */
window.sorter = natsort({insensitive: true});
window.Vue = Vue;
window.app = new Vue({
	i18n,
	store,
	router,
	template: `<app></app>`,
	components: {App},
}).$mount('#app');
