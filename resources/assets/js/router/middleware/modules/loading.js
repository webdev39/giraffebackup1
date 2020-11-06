import nprogress from '@utils/nprogress';

export default function Default (router) {
    router.beforeResolve((to, from, next) => {
        if (to.name) {
            nprogress.start()
        }

        next()
    });

    router.afterEach((to, from) => {
        nprogress.done()
    });
}
