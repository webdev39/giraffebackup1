<template>
    <styleButton :color="getCurrentTheme.buttons.primary.color"
                 :color_hover="getCurrentTheme.buttons.primary.color_hover"
                 :bg-color="getCurrentTheme.buttons.primary.bg"
                 :bg-color_hover="getCurrentTheme.buttons.primary.bg_hover"
                 :border="getCurrentTheme.buttons.primary.border"
                 :fill="getCurrentTheme.buttons.primary.color"
                 :fill_hover="getCurrentTheme.buttons.primary.color_hover"
                 :border_hover="getCurrentTheme.buttons.primary.border_hover"
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


