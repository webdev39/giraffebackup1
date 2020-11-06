import store from '@store';
import { _setDocumentTitle } from '@helpers/controlDocumentTitle';
import { routeConfig } from '@config';
const redirectRoute = ['home', 'not-found', 'oauth'];

const setDocumentTitleDependenceRoute = (route) => {
    const { meta: { title } } = route;
    const isRouteFilter = title === routeConfig.filter;
    const isRouteBoard = title === routeConfig.board;

    if (route.params.period === routeConfig.deadlineDay) {
      return _setDocumentTitle('Your day');
    }
    if (route.params.period === routeConfig.deadlineWeek) {
      return _setDocumentTitle('Your week');
    }

    if (!isRouteFilter && !isRouteBoard) {
        return _setDocumentTitle(title);
    }
};

const socializeSetToken = (route) => {
    if (route.name === 'oauth') {
        let auth = route.query.token;
        if (auth) {
            store.dispatch('setToken', auth);
            store.dispatch('setLastRoute', 'deadline/day');
            let version = +window.localStorage.getItem('version');
            if (version !== process.env.VERSION) {
                window.localStorage.setItem('version', process.env.VERSION)
            }

            return true;
        }
    }
    return null;
};

export default function Default (router) {
    router.beforeEach((to, from, next) => {
        setDocumentTitleDependenceRoute(to);

        if (store.getters.getToken) {
            if (redirectRoute.includes(to.name) || to.meta.auth === false) {
                return next({path: store.getters.getLastRoute});
            }

            store.dispatch('setLastRoute', to.fullPath);
        } else {
            socializeSetToken(to);
        }

        if (to.meta.auth === true && !store.getters.getToken) {
            return next({ path: '/login' });
        }

        return next();
    })
}
