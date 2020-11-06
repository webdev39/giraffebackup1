import NProgress            from 'nprogress'
import                      "@vendor/nprogress/nprogress.css"

let nprogress = NProgress.configure({
    showSpinner: false,
});

export default {
    start: () => {
        nprogress.start();
    },
    done: () => {
        nprogress.done()
    }
};