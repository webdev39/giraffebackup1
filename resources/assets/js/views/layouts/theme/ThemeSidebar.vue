<template>
    <styleSidebar v-if="getCurrentTheme" :color="getCurrentTheme.sidebar.color" :bg-color="getCurrentTheme.sidebar.bg">
        <slot></slot>
    </styleSidebar>
    <!--TODO it was throwing "Cannot read property 'sidebar' of undefined". fix getter null pointer exception properly -->
    <styleSidebar v-else>
        <slot></slot>
    </styleSidebar>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import styleSidebar         from '@views/layouts/theme/styles/styleSidebar'

    export default {
        computed: {
            ...mapGetters({
                getCurrentTheme:    'user/getCurrentTheme',
            }),
        },
        components: {
            styleSidebar
        },
    }
</script>


