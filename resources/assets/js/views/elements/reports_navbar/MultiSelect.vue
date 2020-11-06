<template>
    <div v-click-outside="onClickOutside" class="multi-select">
        <div class="multi-select__input-dropdown" @click="showMultiSelect = !showMultiSelect">
            <div class="input-dropdown__selected-items">{{showComputedBadge}}</div>
            <div class="input-dropdown__angle-down-wrapper"><i class="fa fa-angle-down"></i></div>
        </div>
        <div class="multi-select__dropdown" v-show="showMultiSelect">
            <multi-select-item @trigger-multi-select="handleCheck" :options="options" :selected="selected"></multi-select-item>
            <slot></slot>
        </div>
    </div>
</template>

<script>
    import clickOutside         from 'v-click-outside'
    import MultiSelectItem      from './MultiSelectItem'

    export default {
        props: {
            options: {
                type: Array,
                default: () => {
                    return []
                }
            },
            singleSelect: {
                type: [Number, Boolean],
                default: false
            },
            selectedArrayName: {
                type: String,
                default: ''
            },
            defaultLabel:{
                type: String,
                default: ''
            },
            withoutDynamicLabel:{
                type: Boolean,
                default: false
            },
            selected: {
                type: Array,
                default: () => {
                    return []
                }
            },
        },
        data() {
            return {
                showMultiSelect: false
            }
        },
        directives: {
            'clickOutside' : clickOutside.directive
        },
        components: {
            'multi-select-item' : MultiSelectItem
        },
        computed: {
            getSelected() {
                if (this.isArray(this.options)) {
                    return this.options.filter((item) => {
                        let foundSelected = this.selected.find((selectedId) => {
                            if (selectedId === item.id) {
                                return true;
                            }
                        });

                        if (foundSelected >= 0) {
                            return true;
                        }
                    });
                }

                return [];
            },
            showSelected() {
                let selected = this.getSelected;
                if (selected.length >= 1) {
                    return selected[0].name;
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
            }

        },
        methods: {
            onClickOutside() {
                this.showMultiSelect = false;
            },
            handleCheck(id, selectedArrayName = null, singleSelect = null) {
                this.$emit('trigger-multi-select', id, this.selectedArrayName, this.singleSelect);
            },

        }
    }
</script>
