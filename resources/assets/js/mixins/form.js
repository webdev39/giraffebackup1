// Optimized

export default {
    data () {
        return {
            validator: {
                errors:     {},
                message:    null
            },
        }
    },
    methods: {
        setValidator(data) {
            this.validator.errors = {};

            if (typeof data === 'object') {
                if (typeof data.errors === 'object') {
                    for (let i in data.errors) {
                        data.errors[i] = data.errors[i][0];
                    }

                    this.validator.errors = data.errors;
                }

                this.validator.message = (typeof data.error === 'string') ? data.error : data.message;
            }
        },
        defaultResponse() {
            this.isLoading = false;

            Object.assign(this.$data.validator, this.$options.data().validator);
        },
        defaultError(e) {
            this.isLoading = false;

            if (e === undefined || e.status === 401) {
                return;
            }

            if (typeof e.data.errors === 'object' && this.validator !== undefined) {
                this.setValidator(e.data);
            } else {
                this.$notify({type:'error', text: e.data.message});
            }
        }
    }
};
