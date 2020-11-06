<template>
    <styleContainer :color="getCurrentTheme.modal.header_color" :bg-color="getCurrentTheme.modal.header_bg">
        <slot></slot>
    </styleContainer>
</template>

<script>
    import {mapGetters} from 'vuex'

    import styleContainer from '@views/layouts/theme/styles/styleContainer'

    export default {
        computed: {
            ...mapGetters({
                getCurrentTheme:    'user/getCurrentTheme',
            }),
        },
        components: {
            styleContainer
        },
    }
</script>


