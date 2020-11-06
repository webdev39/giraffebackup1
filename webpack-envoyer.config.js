require('./node_modules/laravel-mix/src/index');
Mix.paths.setRootPath(path.resolve(__dirname));

// Mix.config.webpackConfig.output = {
//     chunkFilename: 'js/[name].bundle.js',
//     // crossOriginLoading: "anonymous",
//     publicPath: '/',
// };

module.exports = require('laravel-mix/setup/webpack.config.js');