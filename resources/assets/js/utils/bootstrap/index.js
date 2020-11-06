import lodash               from 'lodash';
import jquery               from 'jquery';

try {
    window._ = lodash;
    window.$ = window.jQuery = jquery;

    require('bootstrap-sass');
} catch (e) {
    console.log(e);
}

String.prototype.splice = function(idx, rem, str) {
    return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
};