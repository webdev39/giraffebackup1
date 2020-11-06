<template>
    <notifications :max="1" :speed="0" :group="groupName" :duration="5000">
        <template slot="body" slot-scope="props">
            <notification-item :data="props.item" @close="props.close"/>
        </template>
    </notifications>
</template>

<script>
    import NotificationItem from "@views/components/notifications/NotificationItem";

    export default {
        name: 'notification',
        props: {
            groupName: {
                type: String,
                default: '',
            }
        },
        components: {
            NotificationItem
        },
    }
</script>

<style lang="scss">
    .notifications {
        right: 100px !important;
         width: 260px !important;
         top: 4px !important;
        @media (max-width: 768px) {
            right: inherit !important;
            top: inherit !important;
            bottom: 0;
            left: 0;
            width: 100% !important;
            padding: 0 5px;
            display: flex;
            justify-content: center;
        }
        @media (max-width: 1121px) {
           
        }
            @media (min-width: 1121px) {
            top: 4px !important;
            // right: 100% !important;
            width: 170px !important;
            left: 280px;
            position: absolute;
            z-index: 9999;
            margin-right: 5px;
            .notification-item {
            }
        }
        @media (min-width: 1350px) {
            width: 260px !important;
        }
    }
    .notifyTimer{
        @media (min-width: 1121px) {
            // top: 10px !important;
            // // right: 100% !important;
            // left: 300px;
            // position: absolute;
            // z-index: 9999;
            // margin-right: 5px;
            .notification-item {
                // background: transparent;
                // box-shadow: none;
                // border:none;
                    .alert-icon {
                        // display: none;
                    }
            }
        }
    }
</style>
