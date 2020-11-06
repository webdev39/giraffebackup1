<template>
    <div class="multi-select-checkbox-wrapper">
        <label class="multi-select-checkbox-label" v-for="option in options" :key="option.id">
            <input type="checkbox"
                   class="multi-select-checkbox"
                   :value="option.id"
                   v-model="selectedMultiSelect"
                   @change="makeCheck(option.id)"
                   :disabled="typeof option.disabled !== 'undefined'">
            <span class="label-text"><span class="multi-select-checkbox-label-text">{{option.name}}</span></span>
        </label>
    </div>
</template>
<script>
    export default {
        props:{
            options: {
                type: [Object, Array],
                default: () => []
            },
            singleSelect: {
                type: [Number, Boolean],
                default: () => false
            },
            selectedArrayName: {
                type: [String],
                default: () => ''
            },
            selected:{
                type: Array
            }
        },
        computed: {
            selectedMultiSelect: {
                get: function () {
                    return this.selected;
                },
                set: function () {
                    return false;
                }
            }
        },
        methods:{
            makeCheck(id){
                this.$emit('trigger-multi-select', id, this.selectedArrayName, this.singleSelect);
            }
        }
    }
</script>