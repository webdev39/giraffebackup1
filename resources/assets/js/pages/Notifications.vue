<template>
    <div class="notifications-page">
        <notifications :scrollContainer="scrollContainer" :in-page="true" />
    </div>
</template>

<script>
    import Notifications from '@views/components/navbar/Notifications'

    export default {
        data() {
            return {
                scrollContainer: '.content'
            }
        },
        components: {
            Notifications
        },
        mounted() {
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
		}
	}
</script>

<style lang="scss">
    .notifications-page{
        padding: 0;
        padding-bottom: 20px;
            @media (min-width: 576px) {
                padding:0 10px;
            }
        #notifications-box{
            display: block;
            position: static;
            width: 100%;
            min-width: 100%;
            margin: 0;
            // overflow: hidden;
            height: auto;
            border-radius: 5px;
            .notification-body {
                max-height: inherit;
                // background-color: #fff;
                border-radius: 0px;
                overflow: auto;
            }
            .notifications-header{
                margin-bottom: 10px;
                background-color: #fff;
                border-radius: 5px;
            }
            .notifications-header__title{
                font-size: 20px;
            }
        }
    }

</style>
