<template>
    <theme-manage-container class="system-settings-bill-col">

        <form
            id="bill-template"
            class="manage-form"
            @submit.prevent="updateBillPdfTemplate"
        >

            <h1 class="manage-title">Bill PDF Template</h1>

            <template v-if="isEdit">
                <div class="manage-wrapper-item row">
                    <vue-pell-editor v-model="form.content"/>
                </div>
            </template>

            <template v-else>
                <div class="manage-wrapper-item row">
                    <comment-content :html="form.content" :style="style" />
                </div>
            </template>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <template v-if="isEdit">
                        <button type="submit" class="btn btn-success btn-md" :disabled="isLoading">
                            {{ $t("update") }}
                        </button>

                        <button type="button" class="btn btn-warning btn-md" @click="isEdit = false">
                            {{ $t("cancel") }}
                        </button>
                    </template>


                    <template v-if="! isEdit">
                        <theme-button-success
                            type="button"
                            class="btn btn-md"
                            @click.prevent.native="isEdit = true"
                        >{{ $t("edit") }}</theme-button-success>
                    </template>

                    <theme-button-success
                        type="button"
                        class="btn btn-info btn-md"
                        @click.prevent.native="$modal.show('info-setting-modal')"
                    >{{ $t("info") }}</theme-button-success>
                </div>
            </div>
        </form>

    </theme-manage-container>
</template>

<script>
    import { mapGetters }       from 'vuex';

    import CommentContent       from "@assets/js/views/elements/comments/CommentContent";
    import QuillToolbar         from "@assets/js/views/components/editor/QuillToolbar";
    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        name: "bill-template",
        components: {
            QuillToolbar,
            CommentContent,
            ThemeManageContainer,
            ThemeButtonSuccess,
        },
        data() {
            return {
                isEdit: false,
                form: {
                    content: '',
                    footer: '',
                }
            }
        },
        computed: {
            ...mapGetters({
                getFonts: 'default/getFonts',
                getSettings: 'settings/getSettings',
            }),
            font() {
                return this.getFonts.find(item => item.id === this.getSettings.font_id);
            },
            style() {
                const style = {};

                if (this.font) {
                    style['font-family'] = `"${this.font.name}" !important`
                }

                return style;
            }
        },
        mounted() {
            this.$api.tenants.getTemplates()
                .then(data => {
                    Object.assign(this.form, data.templates['bill']);
                })
        },
        methods: {
            updateBillPdfTemplate() {
                this.$api.tenants.updateTemplates({
                    type: "bill",
                    key: "content",
                    content: this.form.content
                });

                this.isEdit = false;
            }
        }
    }
</script>

<style lang="scss">
    #bill-template {
        .ql-toolbar.ql-snow{
            border: none;
            padding: 10px 20px;
        }

        .vp-editor {
            .ql-align-right {
                text-align: right;
            }
        }

        .ql-editor {
            position: relative;
            width: 100%;
            min-height: 30px;
            border-radius: 5px;
            background: #fff7000d;
            border: 1px solid #ff0000;

            &:before {
                content: "pdf";
                position: absolute;
                top: 5px;
                left: 0;
                background-color: #ff0000;
                color: #000;
                font-size: 14px;
                font-style: italic;
                padding: 8px 20px;
                border-top-right-radius: 10px;
                border-bottom-right-radius: 10px;
            }
        }

        .editor-wrapper {
            background: #fff;
            border-radius: 5px;
        }

        hr {
            margin-top: 15px;
            margin-bottom: 15px;
            border-top: 1px solid #ccc;
        }

        .vp-editor {
            width: 100%;
            background: #ffffff;
            border-radius: 5px;
            border: 1px solid #ff0000;

            .pell-actionbar {
                display: none;
            }

            .pell-content {
                height: auto;
                padding: 20px;
            }
        }
    }
</style>
