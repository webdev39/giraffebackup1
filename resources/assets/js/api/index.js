import camelCase from 'lodash/camelCase'

const requireModules = require.context("./modules", false, /\.js$/);
const includeModules = {};

requireModules.keys().forEach(fileName => {
    const moduleName = camelCase(fileName.replace(/(\.\/|\.js)/g, ""));

    includeModules[moduleName] = requireModules(fileName).default;
});

export default includeModules;