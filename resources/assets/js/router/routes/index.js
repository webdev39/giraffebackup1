import Default from './modules/default'

export default function routes(router) {
    const requireModules = require.context("./modules", false, /\.js$/);

    requireModules.keys().forEach(fileName => {
        if (fileName === "./default.js") {
            return;
        }

        router.addRoutes(requireModules(fileName).default);
    });

    router.addRoutes(Default);
}