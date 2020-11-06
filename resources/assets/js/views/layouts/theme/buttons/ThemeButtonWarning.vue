<template>
    <styleButton :color="getCurrentTheme.buttons.warning.color"
                 :color_hover="getCurrentTheme.buttons.warning.color_hover"
                 :bg-color="getCurrentTheme.buttons.warning.bg"
                 :bg-color_hover="getCurrentTheme.buttons.warning.bg_hover"
                 :border="getCurrentTheme.buttons.warning.border"
                 :fill="getCurrentTheme.buttons.warning.color"
                 :fill_hover="getCurrentTheme.buttons.warning.color_hover"
                 :border_hover="getCurrentTheme.buttons.warning.border_hover"
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


