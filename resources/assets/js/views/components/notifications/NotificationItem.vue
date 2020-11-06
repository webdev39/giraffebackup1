<template>
    <div class="notification-item alert " :class="notify.class" @click="close">
        <div class="alert-icon">
            <i class="fa" :class="notify.icon" aria-hidden="true"></i>
        </div>

        <div class="alert-message" v-html="data.text"></div>
    </div>
</template>

<script>
    export default {
        name: "notification-item",
        props: {
            data: {
                type: Object
            }
        },
        data() {
            return {
                list: [
                    {type: 'info',      class: 'alert-info',        icon: 'fa-info-circle'},
                    {type: 'success',   class: 'alert-success',     icon: 'fa-check-circle'},
                    {type: 'warning',   class: 'alert-warning',     icon: 'fa-exclamation-circle'},
                    {type: 'error',     class: 'alert-danger',      icon: 'fa-exclamation-triangle'},
                ]
            }
        },
        computed: {
            notify() {
                return this.list.find(item => item.type === this.data.type)
            }
        },
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>

<style lang="scss">
    .notification-item {
        box-shadow: 1px 1px 1px rgba(0,0,0,0.5);
        margin-bottom: 12px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: none;
        border:none;
        cursor: pointer;
        // padding: 8px 10px;
        padding: 0 5px;
                    animation: disappear 5.2s ease-out;
                    @keyframes disappear {
                    0% {
                        opacity: 1;
                    }
                    40% {
                        opacity: 1;
                    }
                    100% {
                        opacity: 0;
                    }
                    }
        
        @media (max-width: 768px) {
            display: flex;
            text-align: left;
            width: 300px;

        }
        @media (min-width: 1121px) {
            // box-shadow: none;
            // border:none;
            // background: #fff;
            height: 50px;

        }

        .alert-icon,
        .alert-message {
            padding: 0;
            // display: inline-block;
            // vertical-align: middle;
            // float: none;
        }

        .alert-icon {
            font-size: 18px;
            color: #fff;
            margin:0px 5px;
            @media (max-width: 768px) {
                margin-right: 5px;
            }
            @media (min-width: 1121px) {

            }
        }

        .alert-message {
            font-size: 14px;
            // word-wrap: break-word;
            color: #fff;
            text-align: center;
            font-size: 13px;
            line-height: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            max-height: 50px;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            @media (max-width: 768px) {
                // align-items: center;
                // display: flex;
            }
            @media (min-width: 1121px) {
            // font-size: 13px;
            // line-height: 1;
            // overflow: hidden;
            // text-overflow: ellipsis;
            // display: -webkit-box;
            // max-height: 50px;
            // -webkit-line-clamp: 3;
            // -webkit-box-orient: vertical;
            }
        }
    }
    .alert-success {
        background: #2aaba0;
    }
    .alert-danger {
        background: #ef5b64;
    }
    .alert-warning {
        background: #f5be27;
    }
    .alert-info {
        background: #5d98de;
    }
</style>
