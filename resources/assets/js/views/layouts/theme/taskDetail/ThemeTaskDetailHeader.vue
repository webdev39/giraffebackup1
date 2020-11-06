<template>
    <styleContainer :color="getCurrentTheme.task_detail.header.color"
                    :fill="getCurrentTheme.task_detail.header.color"
                    :bg-color="getCurrentTheme.task_detail.header.bg">
        <slot></slot>
    </styleContainer>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import styleContainer       from '@views/layouts/theme/styles/styleContainer'

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


