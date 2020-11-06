<template>
    <styleButton :color="getCurrentTheme.buttons.close.color"
                 :color_hover="getCurrentTheme.buttons.close.color_hover"
                 :bg-color="getCurrentTheme.buttons.close.bg"
                 :bg-color_hover="getCurrentTheme.buttons.close.bg_hover"
                 :border="getCurrentTheme.buttons.close.border"
                 :fill="getCurrentTheme.buttons.close.color_hover"
                 :fill_hover="getCurrentTheme.buttons.close.color_hover"
                 :border_hover="getCurrentTheme.buttons.close.border_hover"
                 :important="getImportant"
    >
        <slot></slot>
    </styleButton>
</template>

<script>
    import {mapGetters} from 'vuex'

    import styleButton  from '@views/layouts/theme/styles/styleButton'

    export default {
        computed: {
            ...mapGetters({
                getCurrentTheme:    'user/getCurrentTheme',
            }),
            getImportant() {
                if (this.important) {
                    return '!important'
                }

                return ''
            }
        },
        props: {
            important: {
                type: Boolean,
                default: false
            },
        },
        components: {
            styleButton
        },
    }
</script>


