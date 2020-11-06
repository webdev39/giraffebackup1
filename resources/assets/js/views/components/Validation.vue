<template>
    <div class="validation-container" :class="{'is-invalid': isInvalid}">
        <slot></slot>
        <div class="invalid-feedback" v-html="error || message"></div>
    </div>
</template>

<script>
    export default {
        name: "validation",
        props: {
            label: {
                type: String,
                default: null
            },
            or: {
                type: Boolean,
                default: null
            },
            and: {
                type: Boolean,
                default: null
            },
            message: {
                type: String,
                default: null
            },
            validator: {
                type: [Object, String],
                default: function () {
                    return {
                        errors: {}
                    }
                }
            },
        },
        computed: {
            error() {
                return this.validator.errors[this.label];
            },
            isInvalid() {
                switch (true) {
                    case this.or !== null:
                        return this.error || this.or;
                    case this.and !== null:
                        return this.error && this.or;
                    case this.or !== null && this.and !== null:
                        return this.error && this.and || this.or;
                    default:
                        return this.error;
                }
            }
        },
    }
</script>

<style lang="scss">
    .validation-container {
        &.is-invalid {
            div.form-control,
            input.form-control,
            select.form-control,
            textarea.form-control,
            .multiselect__tags {
                border: solid 1px #a94442 !important;
            }

            div.invalid-feedback {
                display: block;
                color: #a94442;
                font-weight: 700;
            }
        }

        &.is-valid {
            input.form-control {
                border-color: #28a745;
            }
        }
        .multiselect__single {
            padding-top: 8px;
        }
        .multiselect__tags {
            padding-top: 0;
        }
        .multiselect--above .multiselect__content-wrapper {
            bottom: auto;
        }
        .multiselect-timezone  .multiselect__content-wrapper {
            bottom: 0;
        }
    }
</style>