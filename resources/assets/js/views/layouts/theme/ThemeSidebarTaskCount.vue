<template>
    <styleContainer :color="getCurrentTheme.sidebar.color" :bg-color="getCurrentTheme.sidebar.task_count_bg">
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


