<template>
    <div :style="{'right': right, 'left': left }">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
            position: {
                type: String,
                default: 'right'
            },
        },
        data() {
            return {
                right:  'inherit',
                left:   'inherit',
            }
        },
        mounted() {
            this.$nextTick(function () {
                this.isOffset();
            });
        },
        methods: {
            isOffset() {
                let windowWidth  = window.innerWidth;
                let { x, width } = this.$el.getBoundingClientRect();

                if (x + width > windowWidth && this.position === 'left') {
                    this.right = 0;
                }
            },
        }
    }
</script>