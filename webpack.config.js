const path                = require('path');
const webpack             = require('webpack');
// const WebpackShellPlugin  = require('webpack-shell-plugin');
// const { CleanWebpackPlugin }  = require('clean-webpack-plugin');
// const WebpackGitRevisionPlugin  = require('git-revision-webpack-plugin');
// const BundleAnalyzerPlugin      = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
 
// const gitRevision = new WebpackGitRevisionPlugin();
module.exports = {
    plugins: [
        // new WebpackShellPlugin({
        //     onBuildStart: [],
        //     onBuildEnd: []
        // }),

        // new CleanWebpackPlugin({
        //     verbose: true,
        // }),

        new webpack.DefinePlugin({
            'process.env': {
                'VERSION':      new Date().getTime(),
                // 'BRANCH':       JSON.stringify(gitRevision.branch()),
                // 'COMMITHASH':   JSON.stringify(gitRevision.commithash()),
            }
        }),

        // new BundleAnalyzerPlugin()
    ],
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        alias: {
            '@assets':      path.resolve(__dirname, 'resources/assets'),
            '@images':      path.resolve(__dirname, 'resources/assets/images'),
            '@sass':        path.resolve(__dirname, 'resources/assets/sass'),
            '@':            path.resolve(__dirname, 'resources/assets/js'),
            '@api':         path.resolve(__dirname, 'resources/assets/js/api'),
            '@config':      path.resolve(__dirname, 'resources/assets/js/config'),
            '@helpers':     path.resolve(__dirname, 'resources/assets/js/helpers'),
            '@mixins':      path.resolve(__dirname, 'resources/assets/js/mixins'),
            '@lang':        path.resolve(__dirname, 'resources/assets/js/lang'),
            '@router':      path.resolve(__dirname, 'resources/assets/js/router'),
            '@store':       path.resolve(__dirname, 'resources/assets/js/store'),
            '@utils':       path.resolve(__dirname, 'resources/assets/js/utils'),
            '@views':       path.resolve(__dirname, 'resources/assets/js/views'),
            '@pages':       path.resolve(__dirname, 'resources/assets/js/pages'),
            '@vendor':      path.resolve(__dirname, 'node_modules')
        }
    },
};