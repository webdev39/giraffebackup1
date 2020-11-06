<template>
    <styleContainer v-if="getCurrentTheme" :color="getCurrentTheme.navbar.color" :bg-color="getCurrentTheme.navbar.bg" :fill="getCurrentTheme.navbar.color" >
        <slot></slot>
    </styleContainer>
    <!--TODO it was throwing "Cannot read property 'navbar' of undefined". fix getter null pointer exception properly -->
    <styleContainer v-else>
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


