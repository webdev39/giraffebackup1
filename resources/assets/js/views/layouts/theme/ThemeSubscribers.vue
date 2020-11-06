<template>
    <styleContainer :bg-color="getCurrentTheme.subscribers.bg" :color="getCurrentTheme.subscribers.color">
        <slot></slot>
    </styleContainer>
</template>

<script>
    import {mapGetters} from 'vuex'

    import styleContainer    from '@views/layouts/theme/styles/styleContainer'

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


