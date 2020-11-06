<template>
    <styleTableTr :color="getCurrentTheme.sidebar.color" :bg-color="getCurrentTheme.sidebar.bg">
        <slot></slot>
    </styleTableTr>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import styleTableTr         from '@views/layouts/theme/styles/styleTableTr'

    export default {
        computed: {
            ...mapGetters({
                getCurrentTheme:    'user/getCurrentTheme',
            }),
        },
        components: {
            styleTableTr
        },
    }
</script>


