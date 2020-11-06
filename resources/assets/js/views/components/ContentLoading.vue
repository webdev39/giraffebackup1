<template>
    <div class="loading">
        <slot v-if="!loading"></slot>

        <div class="waiting-spinner" :style="loadingStyle" v-else>
            <i class="fa fa-circle-o-notch fa-spin"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: "content-loading",
        props: {
            loading: {
                type: Boolean,
                default: false,
            },
            minHeight: {
                type: [String, Number],
                default: 100,
            },
            minWidth: {
                type: [String, Number],
                default: 200,
            },
            autosize: {
                type: Boolean,
                default: false
            },
            absolute: {
                type: Boolean,
                default: false
            },
        },
        computed: {
            loadingStyle() {
                if(!this.autosize) {
                    return {
                        'min-height': this.minHeight + 'px',
                        'min-width': this.minWidth + 'px',
                    }
                }

                if(this.absolute) {
                    return {
                        'position': 'absolute',
                        'top':      '0',
                        'left':     '0',
                        'width':    '100%',
                        'height':   '100%',
                        'z-index':  '999'
                    }
                }
            }
        }
    }
</script>
