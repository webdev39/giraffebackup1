<template>
    <div class="drop-down" v-click-outside="handleHideDrop">
        <button class="drop-down__button button__icon">
            <i class="drop-down-icon" @click="handleToggleShowDrop">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         :xlink:href="'#' + icon">
                    </use>
                </svg>
            </i>
        </button>
        <div class="drop-down-content" v-if="show"> 
            <slot></slot>
        </div>
    </div>
</template>

<script>
    import clickOutside from 'v-click-outside'

    export default {
        directives: {
            'clickOutside': clickOutside.directive
        },
        props: {
            icon: {
                type: String,
                default: 'icon-settings'
            },
        },
        data() {
          return {
              show: false
          }
        },
        methods: {
            handleToggleShowDrop() {
                this.show = !this.show;
            },
            handleHideDrop() {
                this.show = false;
            }
        }
    }
</script>

<style lang="scss">
    .drop-down__button {
        display: block;
        right: 10px;
        top: 12px;
        width: 20px;
        height: 20px;
        padding: 0;
    }
    .drop-down-icon{
        display: flex;
        width: 100%;
        height: 100%;
        fill: #a2a2a2; 
    }
    .drop-down-content{
        position: absolute;
        min-width: 250px;
        top: 33px;
        right: 0;
        background-color: #fff;
        padding: 20px 12px 20px 10px;
        -webkit-box-shadow: 0px 2px 4px 0 rgba(0, 0, 0, 0.4);
        box-shadow: 0px 2px 4px 0 rgba(0, 0, 0, 0.4);
        border-radius: 5px;
        z-index: 2;
    }

</style>