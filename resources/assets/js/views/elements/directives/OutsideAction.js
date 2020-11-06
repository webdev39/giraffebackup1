// /*
// import Vue from 'vue'
//
// const isTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints > 0;
//
// const directive = {
//     instances: [],
//     events: isTouch ? ['touchstart', 'click'] : ['click']
// };
//
// directive.onEvent = function (event) {
//     directive.instances.forEach(({el, fn}) => {
//         let ids = fn('ids');
//             let condition = true;
//             ids.forEach((val) => {
//                 if (event.target.closest('#'+ val ) || event.target.closest('.'+ val )) {
//                     condition = false;
//                 }
//             });
//             if (!condition) {
//                 return false;
//             }
//         if (event.target !== el && !el.contains(event.target)) {
//             fn && fn();
//         }
//
//     })
// };
//
// directive.bind = function (el, binding) {
//     directive.instances.push({el, fn: binding.value})
//     if (directive.instances.length === 1) {
//         directive.events.forEach(e => document.addEventListener(e, directive.onEvent))
//     }
// };
//
// directive.update = function (el, binding) {
//     if (typeof binding.value !== 'function') {
//         throw new Error('Argument must be a function')
//     }
//     const instance = directive.instances.find(i => i.el === el)
//     instance.fn = binding.value
// };
//
// directive.unbind = function (el) {
//     const instanceIndex = directive.instances.findIndex(i => i.el === el)
//     if (instanceIndex >= 0) {
//         directive.instances.splice(instanceIndex, 1)
//     }
//     if (directive.instances.length === 0) {
//         directive.events.forEach(e => document.removeEventListener(e, directive.onEvent))
//     }
// };
//
// Vue.directive('click-outside', directive);
//
// export default directive;
// */
