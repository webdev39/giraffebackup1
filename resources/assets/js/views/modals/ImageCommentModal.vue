<!--Optimized-->
<template>
    <modal :name="$options.name" :id="$options.name" height="auto" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1000" >
        <modal-wrapper :name="$options.name">
            <template slot="title">
                {{ $t('description_comment') }}
            </template>
            <template slot="body">
                <div class="row">
                    <div class="col-sm-12">
                        <textarea style="width: 100%" name="" id="" v-model="form.body" cols="30" rows="10"></textarea>
                    </div>
                </div>
            </template>
            <template slot="footer">
                <button type="button" title="Remove" v-if="edit" @click="removeComment" class="btn btn-remove">
                    <i aria-hidden="true" class="fa fa-trash-o"></i>
                </button>
                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>
                <theme-button-success type="button" class="btn btn-update" @click.prevent.native="updateComment" v-if="edit" :disabled="isLoading" >
                    {{ $t('save') }}
                </theme-button-success>
                <theme-button-success type="button" class="btn btn-update" @click.prevent.native="createComment" v-if="create" :disabled="isLoading" >
                    {{ $t('create') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import formMixin            from '@mixins/form'
    import Validation           from '@views/components/Validation'
    import ModalWrapper         from "@assets/js/views/layouts/ModalWrapper";
    import ThemeButtonSuccess   from "@assets/js/views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@assets/js/views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "image-comment-modal",
        data() {
            return {
                form: {
                    attachmentId:   null,
                    taskId:         null,
                    body:           '',
                    mentions:       [],
                    spatial: {
                        x:          null,
                        y:          null,
                        w:          0,
                        h:          0
                    },
                },
                create: false,
                edit:   false,

                createCallback: null,
                updateCallback: null,
                removeCallback: null,
            }
        },
        computed: {
            ...mapGetters({

            }),

        },
        components: {
            ModalWrapper,
            Validation,
            ThemeButtonClose,
            ThemeButtonSuccess
        },
        mixins: [
            formMixin
        ],
        methods: {
            beforeOpen(event) {
                if (event.params.comment) {
                    this.form = event.params.comment
                }

                if (event.params.create) {
                    this.create = event.params.create
                }

                if (event.params.edit) {
                    this.edit = event.params.edit
                }

                if (event.params.createCallback) {
                    this.createCallback = event.params.createCallback;
                }
                if (event.params.updateCallback) {
                    this.updateCallback = event.params.updateCallback;
                }
                if (event.params.removeCallback) {
                    this.removeCallback = event.params.removeCallback;
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('image-comment-modal');
            },
            createComment() {
                this.$api.comment.createAttachmentComment(this.form).then(res => {
                    if (this.isFunction(this.createCallback)) {
                        this.createCallback(res);
                    }
                    this.closeModal();
                });
            },
            updateComment() {
                this.form.commentId = this.form.id;
                this.$api.comment.updateAttachmentComment(this.form).then(res => {
                    if (this.isFunction(this.updateCallback)) {
                        this.updateCallback(res);
                    }
                    this.closeModal();
                });
            },
            removeComment() {
                this.form.commentId = this.form.id;
                this.$api.comment.removeAttachmentComment(this.form).then(res => {
                    if (this.isFunction(this.updateCallback)) {
                        this.removeCallback(res);
                    }
                    this.closeModal();
                });
            }
        }
    }
</script>