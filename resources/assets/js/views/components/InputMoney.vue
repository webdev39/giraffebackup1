<template>
    <input type="text" v-model="innerValue" :disabled="disabled" :placeholder="placeholder" />
</template>

<script>
    export default {
        name: "input-money",
        props: {
            value: {
                type: Number,
                default: 0
            },
            disabled: {
                type: Boolean,
                default: false
            },
            placeholder: {
                type: String,
                default: ''
            },
            decimalCount: {
                type: Number,
                default: 2
            },
            decimal: {
                type: String,
            },
            thousands: {
                type: String,
            },
        },
        computed: {
            innerValue: {
                get() {
                    return this.$options.filters.formatMoney(this.value, this.decimalCount, this.decimal, this.thousands);
                },
                set(value) {
                    this.$emit('input', this.$options.filters.formatMoney(value, this.decimalCount, this.decimal, this.thousands, true));
                }
            }
        }
    }
</script>

<style lang="scss">

</style>
