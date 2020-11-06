<template>
    <span v-if="getCount" class="task-unread-notification">{{getCount}}</span>
</template>

<script>
    export default {
        props: {
            task: {
                type: Object,
                required: true
            }
        },
        computed: {
            getCount() {
                return this.task.unreadNotificationsCount;
            }
        }
    }
</script>

<style lang="scss" >
    .task-unread-notification{
        display: inline-block;
        background-color: #fa3e3e;
        border-radius: 18px;
        color: #fff;
        font-size: 10px;
        line-height: 14px;
        height: 18px;
        min-width: 18px;
        padding: 3px;
        text-align: center;
    }
</style>
