<template>
    <styleButton :color="getCurrentTheme.buttons.success.color"
                 :color_hover="getCurrentTheme.buttons.success.color_hover"
                 :bg-color="getCurrentTheme.buttons.success.bg"
                 :bg-color_hover="getCurrentTheme.buttons.success.bg_hover"
                 :border="getCurrentTheme.buttons.success.border"
                 :fill="getCurrentTheme.buttons.success.color"
                 :fill_hover="getCurrentTheme.buttons.success.color_hover"
                 :border_hover="getCurrentTheme.buttons.success.border_hover"
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


