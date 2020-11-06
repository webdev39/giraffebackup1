const mix       = require('laravel-mix');
const config    = require('./webpack.config');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig(config)
// .options({
//     clearConsole: true
// });

if(process.env.BROWSER_SYNC_ENABLE) {
    mix.browserSync({
        proxy: process.env.APP_HOST,
        host: process.env.APP_HOST,
        open: 'external',
    });
}

mix
    .options({
        processCssUrls: false,
        imgLoaderOptions: {
            enabled: false,
        }
    })
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/print.scss', 'public/css')
    .sourceMaps()
    .version();


mix.extract([
    'axios',
    'lodash',
    'moment',
    'jquery',
    'raven-js',
    'pusher-js',
    'laravel-echo',
    'vue',
    'vue-router',
    'vuex',
    'vuex-persistedstate',
    'vuedraggable',
]);

if (mix.config.production) {
    mix.disableNotifications();
    mix.config.webpackConfig.output = {
        chunkFilename: 'js/[name].bundle-[chunkhash].js',
        publicPath: '/',
    };
} else {
    // mix.sourceMaps(true, 'source-map');
    mix.config.webpackConfig.output = {
        chunkFilename: 'js/[name].bundle.js',
        publicPath: '/',
    };
}