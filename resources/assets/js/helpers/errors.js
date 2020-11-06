// TODO : replace with validation from formMixin and Validation.vue
export function handleErrors(server_errors, form_errors, self, renderNotify) {
    self = self || null;
    renderNotify = renderNotify || null;
    Object.keys(server_errors).forEach((val) => {
        if (typeof form_errors[val] !== 'undefined') {
            form_errors[val] = server_errors[val];
            if (renderNotify && self && form_errors[val].length) {
                form_errors[val].forEach(function (message) {
                    self.$notify({type:'error', text: message});
                });
            }
        }
    });
}
