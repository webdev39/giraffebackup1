export default function middleware(router) {
    const requireModules = require.context("./modules", false, /\.js$/);

    requireModules.keys().forEach(fileName => {
        requireModules(fileName).default(router)
    });
}