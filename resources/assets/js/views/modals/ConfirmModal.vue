<!--Optimized-->
<template>
    <modal :name="$options.name" :id="$options.name" height="auto"  width="80%" :maxWidth="550" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" style="z-index:2000">
        <modal-wrapper :name="$options.name">
              <template slot="header">
                <theme-button-close
                    class="btn-close-header"
                    @click.native="closeModal"
                >
                    <i class="icon-close" >
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
              </template>
            <template slot="title">
                {{ modal.title }}
            </template>

            <template slot="body">
                <p>{{ modal.body }}</p>
            </template>

            <template slot="footer">
                <theme-button-close type="button" class="btn" @click.native="close">{{ $t("cancel")}}</theme-button-close>
                <theme-button-success type="button" class="btn" @click.native="ok">{{ $t("ok")}}</theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import ModalWrapper         from "@assets/js/views/layouts/ModalWrapper";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "confirm-modal",
        data() {
            return {
                modal: {
                    title:  'Modal Header',
                    body:   'Some text in the modal.'
                },
                confirmCallback: null,
                cancelCallback: null,
            }
        },
        components: {
            ModalWrapper,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return this.closeModal();
                }

                if (event.params.title) {
                    this.modal.title = event.params.title;
                }

                if (event.params.body) {
                    this.modal.body = event.params.body;
                }

                if (event.params.confirmCallback) {
                    this.confirmCallback = event.params.confirmCallback;
                }

                if (event.params.cancelCallback) {
                    this.cancelCallback = event.params.cancelCallback;
                }
            },
            ok() {
                if (this.isFunction(this.confirmCallback)) {
                    this.confirmCallback(true);
                }

                this.closeModal();
            },
            close() {
                if (this.isFunction(this.cancelCallback)) {
                    this.cancelCallback(true);
                }

                this.closeModal();
            },
            closeModal() {
                this.$modal.hide("confirm-modal");
            },
        },
    }
</script>
<style lang="scss">
    #confirm-modal {
        overflow: hidden;
                .btn-close-header {
            background: transparent;
            border:none;
            box-shadow: none;
            fill:#fff;
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
                &:hover {
                    background: transparent; 
                    border:none;
                    box-shadow: none;
                    fill:#e2e6e9;
                }
            .icon-close {
                display: block;
                 .icon {
                     width: 14px;
                     height: 14px;
                 }
            }
        }
    }
</style>