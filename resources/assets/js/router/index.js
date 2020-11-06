import Vue              from 'vue'
import VueRouter        from 'vue-router'

import routes           from './routes'
import middleware       from './middleware'

Vue.use(VueRouter);

/**
 * Router Configuration
 *
 * @type {VueRouter}
 */
const router = new VueRouter({
    mode: 'history',
    hashbang: false,
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
});

/**
 * App Routes
 */
routes(router);

/**
 * App Middleware
 */
middleware(router);


export default router;