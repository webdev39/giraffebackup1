<template>
    <div v-click-outside="onClickOutside" class="multi-select">
        <div class="multi-select__input-dropdown" @click="showMultiSelect = !showMultiSelect">
            <div class="input-dropdown__selected-items">{{showComputedBadge}}</div>
            <div class="input-dropdown__angle-down-wrapper"><i class="fa fa-angle-down"></i></div>
        </div>
        <div class="timerange-multi-select__dropdown" v-show="showMultiSelect">
            <div class="timerange-multi-select-checkbox-wrapper">
                <label class="multi-select-checkbox-label" v-for="option in options" :key="option.id">
                    <input type="checkbox"
                           class="multi-select-checkbox"
                           :value="option.id"
                           v-model="selectedMultiSelect"
                           @change="handleCheck(option.id)">
                    <span class="label-text"><span class="multi-select-checkbox-label-text">{{ option.name }}</span></span>
                </label>
            </div>
            <div class="timerange-multi-select-datepicker-area" v-show="showCustomDatepicker">
                <div class="custom-timerange-title">
                    <span>{{ getRangeDate }}</span>
                    <span class="close-datepicker-wrapper"><i class="fa fa-times icon-close-datepicker" @click="closeDatepicker"></i></span>
                </div>
                <vue-datepicker-local v-model="range" type="inline" :local="local"></vue-datepicker-local>
            </div>
        </div>
    </div>
</template>
<script>
    import moment               from 'moment'
    import clickOutside         from 'v-click-outside'
    import VueDatepickerLocal   from 'vue-datepicker-local'

    import MultiSelectItem      from './MultiSelectItem'

    export default {
        props: {
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
            defaultLabel:{
                type: [String],
                default: () => ''
            },
            withoutDynamicLabel:{
                type: [Boolean],
                default: () => false
            },
            selected:{
                type: Array
            }
        },
        data() {
            return {
                showMultiSelect: false,
                range: [new Date(), new Date()],
                localFormat: 'Do MMM YYYY',
                local: {
                    dow: 0, // Sunday is the first day of the week
                    hourTip: 'Select Hour', // tip of select hour
                    minuteTip: 'Select Minute', // tip of select minute
                    secondTip: 'Select Second', // tip of select second
                    yearSuffix: '', // suffix of head year
                    monthsHead: 'January_February_March_April_May_June_July_August_September_October_November_December'.split('_'), // months of head
                    months: 'Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec'.split('_'), // months of panel
                    weeks: 'Su_Mo_Tu_We_Th_Fr_Sa'.split('_'), // weeks,
                    cancelTip: 'cancel',
                    submitTip: 'confirm'
                },
                showCustomDatepicker: false,
                customDatepicker: 8
            }
        },
        directives: {
            'clickOutside' : clickOutside.directive
        },
        components: {
            MultiSelectItem,
            VueDatepickerLocal
        },
        watch:{
            getRangeDate: function(){
                this.$event.$emit('change-custom-timerange', this.range, true);
            },
            selectedMultiSelect: function (currentValue) {
                let selected = currentValue.find(((item)=>item === this.customDatepicker));
                if (selected){
                    this.showCustomDatepicker = true;
                }else{
                    this.showCustomDatepicker = false;
                }
            }
        },
        computed: {
            getSelected() {
                return this.options.filter((item) => {
                    let foundSelected = this.selected.find((selectedId) => {
                        if (selectedId === item.id) {
                            return true;
                        }
                    });
                    if (foundSelected >= 0) {
                        return true;
                    }
                })
            },
            selectedMultiSelect: {
                get: function () {
                    return this.selected;
                },
                set: function () {
                    return false;
                }
            },
            showSelected() {
                let selected = this.getSelected;
                if (selected.length >= 1) {
                    if(selected[0].name === 'Custom') {
                        return this.getRangeDate;
                    } else {
                        return selected[0].name;
                    }

                }
                return this.defaultLabel;
            },
            showSelectedBadge() {
                let selected = this.getSelected;
                if (selected.length > 1) {
                    return selected.length - 1
                }
                return '';
            },
            showComputedBadge(){
                if (this.withoutDynamicLabel){
                    return this.defaultLabel;
                }
                return this.showSelected + ' ' + this.showSelectedBadge;
            },
            getRangeDate(){
                let start = moment(this.range[0]),
                    end = moment(this.range[1]);

                if (start.isValid() && end.isValid()){
                    return start.format(this.localFormat) +  " - " +  end.format(this.localFormat);
                }
                return ''
            }

        },
        methods: {
            onClickOutside() {
                this.showMultiSelect = false;
            },
            handleCheck(id, selectedArrayName = null, singleSelect = null) {
                if (this.customDatepicker === id){
                    this.$event.$emit('change-custom-timerange', this.range);
                    this.$emit('trigger-multi-select', id, this.selectedArrayName, true, this.customDatepicker);
                    return false;
                }
                this.$emit('trigger-multi-select', id, this.selectedArrayName, this.singleSelect, this.customDatepicker);
            },
            closeDatepicker() {
                this.showCustomDatepicker = false;
                this.$emit('trigger-multi-select',this.customDatepicker, this.selectedArrayName, true, this.customDatepicker);
            },
            translateKey(key) {
                if (key) {
                    return this.$t(key.toLowerCase().toString().replace(/\s+/g,"_"));
                }
            }
        }
    }
</script>
