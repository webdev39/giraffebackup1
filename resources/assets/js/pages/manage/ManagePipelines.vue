<!--Optimized-->
<template>
    <div id="manage-page" class="manage-pipelines">

        <!-- Header -->
        <div class="manage-customers-navbar-header">
            <div class="manage-customers-navbar-title flex-1">
                {{ $t('manage_pipelines') }}
            </div>
            <div class="button-holder">
                <theme-button-success class="button__icon new-client-btn" @click.native="showPipelineModal" v-if="checkPermission('create-pipeline')">
                    <i class="icon-plus">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-plus">
                            </use>
                        </svg>
                    </i>
                </theme-button-success>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <theme-manage-container class="table">
                <div class="table-head">
                    <div class="col">{{ $t("id") }}</div>
                    <div class="col">{{ $t("email") }}</div>
                    <div class="col">{{ $t("description") }}</div>
                    <div class="col action">{{ $t("actions") }}</div>
                </div>
                <template v-for="(pipeline, index) in pipelines">
                    <div class="table-row" :key="pipeline.id">
                        <div class="col">{{ index + 1 }}</div>
                        <div class="col"><a :href="'mailto:' + pipeline.email">{{ pipeline.email }}</a></div>
                        <div class="col">{{ pipeline.description }}</div>
                        <div class="col action">
                            <a @click="showPipelineModal(pipeline)">
                                <i class="icon-settings">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-settings">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                            <a @click="toggleSelectPipeline(pipeline)" v-if="pipeline.rules && pipeline.rules.length > 0">
                                <i class="icon-list">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-list">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                            <a @click="showPipelineRuleModal(pipeline)" v-if="checkPermission('create-pipeline')">
                                <i class="icon-plus">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-plus">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                            <a @click="removePipeline(pipeline.id)" v-if="checkPermission('create-pipeline')">
                                <i class="icon-trash">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-trash">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </div>
                    <div class="table-row" v-if="pipeline.id === selectPipeline.id" v-for="rule in pipeline.rules" :key="rule.id">
                        <div class="col"></div>
                        <div class="col">{{ rule.name }}</div>
                        <div class="col">{{ rule.description }}</div>
                        <div class="col action">
                            <a @click="showPipelineRuleModal(pipeline, rule)">
                                <i class="fa fa-pencil icon" aria-hidden="true"></i>
                            </a>
                            <a @click="removePipelineRule(pipeline.id, rule.id)">
                                <i class="icon-trash">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-trash">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </div>
                </template>
                
            </theme-manage-container>
        </div>
        
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import config               from '@config'

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        data() {
            return {
                selectPipeline: {}
            }
        },
        components: {
            ThemeManageContainer,
            ThemeButtonSuccess
        },
        computed:{
            ...mapGetters({
                pipelines: 'pipelines/getPipelines',
            }),
        },
        mounted() {
            if (window.innerWidth < config.size.tablet) {
                return this.$router.push({name: 'home'});
            }

            this.$api.pipelines.getPipelines();
            this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            showPipelineModal(pipeline = {}) {
                this.$modal.show('pipeline-modal', {pipeline_id: pipeline.id})
            },
            showPipelineRuleModal(pipeline = {}, rule = {}) {
                this.$modal.show('pipeline-rule-modal', {pipeline_id: pipeline.id, rule_id: rule.id})
            },
            removePipeline(id) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('delete_pipeline'),
                    body: this.$t('are_you_sure_you_want_to_delete_this_pipeline'),
                    confirmCallback: () => {
                        this.$api.pipelines.removePipeline(id);
                    },
                });
            },
            removePipelineRule(pipelineId, pipelineRuleId) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('delete_pipeline_rule'),
                    body: this.$t('are_you_sure_you_want_to_delete_this_pipeline_rule'),
                    confirmCallback: () => {
                        this.$api.pipelines.removePipelineRule(pipelineId, pipelineRuleId);
                    },
                });
            },
            toggleSelectPipeline(pipeline) {
                if (this.selectPipeline.id === pipeline.id) {
                    return this.selectPipeline = {};
                }

                return this.selectPipeline = pipeline;
            }
        },
    }
</script>
