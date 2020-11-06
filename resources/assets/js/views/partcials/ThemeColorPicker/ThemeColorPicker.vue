<template>
    <div class="theme-setting-modal__item">
        <div class="theme-setting-modal__item-text">
            {{ text }}
        </div>
        <div class="theme-setting-modal__item-color" :style="{'background-color': getColor}"></div>
        <div class="theme-setting-modal__item-colorpicker-wrapper">
            <button class="theme-setting-modal__item-button" v-if="!showPicker" @click="handleShowPicker">
                <i  class="fa fa-pencil"></i>
            </button>
            <button class="theme-setting-modal__item-button" v-if="showPicker" @click="handleHidePicker">
                <i class="fa fa fa-check"></i>
            </button>
            <button class="theme-setting-modal__item-button" v-if="showPicker" @click="handleSetDefault">
                <i class="fa fa-times"></i>
            </button>
            <chrome class="theme-setting-modal__item-colorpicker" v-if="showPicker" v-model="color"/>
        </div>
    </div>
</template>

<script>
    import { Chrome } from "vue-color";

    export default {
        components: {
            Chrome,
        },
        props:{
            value:  [String, Object],
            text:   String,
        },
        computed: {
            getColor: {
                get() {
                    if (this.color.hex) {
                        return this.color.hex
                    }

                    return this.color
                }
            },
        },
        data() {
            return {
                color: this.value,
                base_color: this.value,
                showPicker: false
            }
        },
        methods: {
            handleShowPicker() {
                this.showPicker = true;
            },
            handleHidePicker() {
                this.showPicker = false;
                this.$emit('input', this.color.hex);
            },
            handleSetDefault() {
                this.$emit('input', this.base_color);
                this.showPicker = false;
            }
        }
    }
</script>

<style lang="scss">
    .theme-setting-modal__item-colorpicker-wrapper{
        position: relative;
    }
    .theme-setting-modal__item-colorpicker{
        position: absolute;
        z-index: 2;
    }
    .theme-setting-modal__item{
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .theme-setting-modal__item-color{
        width: 30px;
        height: 30px;
        margin-left: 20px;
    }
    .theme-setting-modal__item-button{
        width: 30px;
        height: 30px;
        border: none;
    }
    .theme-setting-modal__item-text{
        width: 150px;
    }
</style>

