<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="1000%" :maxWidth="700" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" >
        <modal-wrapper :name="$options.name" color="white">
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
                {{ pipeline.id ?  $t('edit_pipeline') : $t('new_pipeline') }}
            </template>

            <template slot="body">
                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('name') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="name">
                        <input type="text" class="form-control" id="pipeline-name-input" v-model="form.name">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('description') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="description">
                        <input type="text" class="form-control" id="pipeline-description-input" v-model="form.description">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('host') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="host">
                        <input type="text" class="form-control" id="pipeline-host-input" v-model="form.host">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('port') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="port">
                        <input type="number" class="form-control" id="pipeline-port-input" v-model="form.port" min="1" max="999">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('encryption') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="encryption">
                        <select class="form-control" id="pipeline-encryption-input" v-model="form.encryption">
                            <option v-for="encryption in encryptions" :value="encryption.value" :key="encryption.id">
                                {{ encryption.display_name }}
                            </option>
                        </select>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('email') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="email">
                        <input type="email" class="form-control" id="pipeline-email-input" v-model="form.email">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('password') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="password">
                        <input type="password" class="form-control" id="pipeline-password-input" v-model="form.password">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('active') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="is_active">
                        <input type="checkbox" class="form-check-input" id="pipeline-is_active-input" v-model="form.is_active">
                    </validation>
                </div>
            </template>

            <template slot="footer">
                <button type="button" class="btn btn-remove" @click="handleDelete" :disabled="isLoading" v-if="pipeline.id">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>

                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleUpdate" v-if="pipeline.id">
                    {{ $t('save') }}
                </theme-button-success>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleCreate" v-if="!pipeline.id">
                    {{ $t('create') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import MixinForm            from '@mixins/form'

    import ContentLoading       from "@views/components/ContentLoading";
    import Validation           from "@views/components/Validation";
    import ModalWrapper         from "@views/layouts/ModalWrapper";

    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "pipeline-modal",
        data() {
            return {
                form: {
                    pipeline_id:    null,
                    host:           null,
                    port:           null,
                    name:           null,
                    email:          null,
                    password:       null,
                    description:    null,
                    encryption:     'none',
                    is_active:      false,
                },
                initForm: {
                    pipeline_id:    null,
                    host:           null,
                    port:           null,
                    name:           null,
                    email:          null,
                    password:       null,
                    description:    null,
                    encryption:     'none',
                    is_active:      false,
                },
                encryptions: [
                    {'id': 0, 'value': 'none', 'display_name': window.app.$t('none')},
                    {'id': 1, 'value': 'ssl',  'display_name': 'SSL'},
                    {'id': 2, 'value': 'tls',  'display_name': 'TLS'},
                ]
            }
        },
        computed:{
            ...mapGetters({
                pipelines: 'pipelines/getPipelines',
            }),
            pipeline: {
                get: function() {
                    let pipeline = this.pipelines.find(item => item.id === this.form.pipeline_id);
                    this.initForm = Object.assign(this.initForm, pipeline);

                    return this.form = {...this.initForm};
                },
                set: function(value) {
                    console.log(value);
                }
            }
        },
        components: {
            ModalWrapper,
            Validation,
            ContentLoading,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        mixins: [
            MixinForm
        ],
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.pipeline_id) {
                    this.form.pipeline_id       = event.params.pipeline_id;
                    this.initForm.pipeline_id   = event.params.pipeline_id;
                }
            },
            beforeClose(event) {
                if (JSON.stringify(this.form) !== JSON.stringify(this.initForm)) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('confirm_modal'),
                        body: this.$t('are_you_sure_you_want_to_close_modal'),
                        confirmCallback: () => {
                            this.initForm = {...this.form};
                            this.closeModal();
                        }
                    });

                    return event.stop();
                }

                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('pipeline-modal')
            },
            handleCreate() {
                this.$api.pipelines.createPipeline(this.form).then(() => {
                    this.initForm = {...this.form};

                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            },
            handleUpdate() {
                this.$api.pipelines.updatePipeline(this.form).then(() => {
                    this.initForm = {...this.form};

                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            },
            handleDelete() {
                this.$api.pipelines.removePipeline(this.form.pipeline_id).then(() => {
                    this.initForm = {...this.form};

                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            }
        }
    }
</script>

<style lang="scss">
    #pipeline-modal {
        overflow: hidden;
        .col-form-label {
            padding-top: 5px;
        }

        input[type=password] {
            height: 36px;
        }
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
