<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="90%" :maxWidth="700" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" >
        <modal-wrapper :name="$options.name" color="white">
            <template slot="title">
                {{ rule.id ?  $t('edit_pipeline_rule') : $t('new_pipeline_rule') }}
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
                    <label class="col-sm-3 col-form-label">{{ $t('boards') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="name">
                        <multiselect
                            v-model="form.boards"
                            :options="optionsBoards"
                            label="label"
                            track-by="value"
                            :placeholder="$t('select_board')"
                            :multiple="true"
                            :limit="2"
                            :close-on-select="false"
                            @input="changeRule($event, 'boards')"
                        />
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('filters') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="pipeline_filter_id">
                        <select class="form-control" id="pipeline-encryption-input" v-model="form.pipeline_filter_id">
                            <option v-for="filter in filters" :value="filter.id" :key="filter.id">
                                {{ filter.display_name }}
                            </option>
                        </select>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('keywords') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="keywords">
                        <multiselect
                            v-model="form.keywords"
                            :options="[]"
                            label="value"
                            track-by="value"
                            :multiple="true"
                            :taggable="true"
                            @tag="addTag"
                            @input="changeRule($event, 'keywords')"
                            :placeholder="$t('add_tag')"
                            :selectLabel="$t('press_enter_to_select')"
                        >
                            <template slot="noOptions">{{ $t('list_is_empty') }}</template>
                        </multiselect>
                    </validation>
                </div>
            </template>

            <template slot="footer">
                <button type="button" class="btn btn-remove" @click="handleDelete" :disabled="isLoading" v-if="rule.id">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>

                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleUpdate" v-if="rule.id">
                    {{ $t('save') }}
                </theme-button-success>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleCreate" v-if="!rule.id">
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
        name: "pipeline-rule-modal",
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
        data() {
            return {
            	init: false,
                form: {
                    pipeline_id:        null,
                    pipeline_filter_id: null,
                    rule_id:            null,
                    name:               null,
                    description:        null,
                    keywords:           [],
                    boards:             [],
                },
            }
        },
        computed:{
            ...mapGetters({
                boards:     'groups/getBoards',
                pipelines:  'pipelines/getPipelines',
                filters:    'pipelines/getPipelineFilters',
            }),
            rule: {
                get() {
					let pipeline = this.pipelines.find(item => item.id === this.form.pipeline_id);

					if (pipeline && pipeline.rules) {
						let rule = pipeline.rules.find(item => item.id === this.form.rule_id);

						if (rule) {
							Object.assign(this.form, rule);

							this.form.boards = this.optionsBoards.filter(item => this.form.boards.includes(item.value));
							this.form.keywords = this.form.keywords.map(item => {
								return {value: item};
							});
							this.init = true;
						}
					}
                    return this.form;
                },
                set(value) {
                    console.log(value);
                }
            },
            optionsBoards() {
                return this.boards.map(board => {
                    return {value: board.id, label: board.name};
                }).sort((a, b) => {
                    return sorter(a.label, b.label)
                });
            },
        },
        methods: {
            beforeOpen(event) {
                this.$api.pipelines.getPipelineFilters();

                if (!event.params) {
                    return;
                }

                if (event.params.pipeline_id) {
                    this.form.pipeline_id = event.params.pipeline_id;
                }

                if (event.params.rule_id) {
                    this.form.rule_id = event.params.rule_id;
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('pipeline-rule-modal')
            },
			changeRule(option, field) {
				this.$store.dispatch('pipelines/changeRule', {
					pipeline_id: this.form.pipeline_id,
					rule_id: this.form.rule_id,
					option: option,
					field: field
				});
			},
            addTag(tag) {
                this.form.keywords.push({value: tag})
            },
            getFormData() {
                return Object.assign({}, this.form, {
                    boards: this.form.boards.map(item => item.value),
                    keywords: this.form.keywords.map(item => item.value)
                });
            },
            handleCreate() {
                this.$api.pipelines.createPipelineRule(this.form.pipeline_id, this.getFormData()).then(() => {
                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            },
            handleUpdate() {
                this.$api.pipelines.updatePipelineRule(this.form.pipeline_id, this.getFormData()).then(() => {
                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            },
            handleDelete() {
                this.$api.pipelines.removePipelineRule(this.form.pipeline_id, this.form.rule_id).then(() => {
                    this.closeModal();
                }).catch(err => {
                    this.defaultError(err.response)
                })
            }
        }
    }
</script>

<style lang="scss">
    #pipeline-rule-modal {
        .col-form-label {
            padding-top: 5px;
        }
    }
</style>
