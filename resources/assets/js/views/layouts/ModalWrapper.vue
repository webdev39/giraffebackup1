<template>
    <div class="modal-content">
        <theme-modal-header class="modal-header">
            <slot name="header"></slot>

            <span class="modal-title">
                <slot name="title"></slot>
            </span>
        </theme-modal-header>

        <div class="modal-body">
            <slot name="body"></slot>
        </div>

        <div class="modal-footer" :class="'modal-footer-' + color">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
    import ThemeModalHeader from '@views/layouts/theme/ThemeModalHeader'

    export default {
        name: "modal-wrapper",
        props: {
            name: {
                type: String,
                default: null,
            },
            color: {
                type: String,
                default: null,
            },
        },
        components: {
            ThemeModalHeader
        },
    }
</script>

<style lang="scss">
    .modal-content {
        background-color: transparent;
        border: none;
        max-height: 100vh;
        max-height: calc(var(--vh, 1vh) * 100);
        display: flex;
        flex-direction: column;
        .modal-header {
            position: relative;
        }
        .modal-body {
            height: 100%;
            overflow-y: auto;
        }

        .modal-title {
            font-weight: bold;
            font-size: 20px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-footer-white {
            //
        }

        .modal-footer {
            background-color: #fff;
            border-top: 1px solid #e5e5e5;

            .btn {
                margin-left: 5px;
                margin-bottom: 0;
                padding: 8px 12px;
                //color: #fff;
            }

            .btn-archived {
                float: left;
                /*padding: 5px 15px;*/
                /*background-color: #e04545;*/
                /*font-size: 18px;*/
                /*font-weight: bold;*/

                margin-right: 10px;
                // margin-left: 10px;
            }

            .btn-remove {
                float: left;
                background: transparent;
                padding: 0;
                padding-top: 4px;
                line-height: 1;
                .icon-trash {
                    .icon {
                        width:25px;
                        height:27px;
                        fill:#e04545;
                            &:hover {
                                fill:#d5d5d5;
                            }
                    }
                }

            }

            /*.btn-close {*/
                /*background-color: #425967;*/
            /*}*/

            /*.btn-update {*/
                /*background-color: #5078f2;*/
                /*border-color: #2a88bd;*/
            /*}*/

            /*.btn-create {*/
                /*background-color: #2ab27b;*/
                /*border-color: #259d6d;*/
            /*}*/
        }
    }
    .v--modal--task .modal-footer-null{
        display: none;
    }

    @media (max-width: 767px) {
    .btn-remove {
        margin-right:0;
    }
    .modal-footer {
        // margin-bottom: 60px;
    }
    .modal-content {
        height: 100%;
        max-height: calc(var(--vh, 1vh) * 100 - 50px);
    }
}
</style>
