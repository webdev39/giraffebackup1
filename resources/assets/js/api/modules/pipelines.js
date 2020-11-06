import http     from '@utils/http'
import store    from '@store'

let request = {
    getPipelines: false,
    getPipelineFilters: false,
};

export default {
    getPipelines() {
        return new Promise((resolve, reject) => {
            if (request.getPipelines || store.getters['pipelines/getPipelines'].length > 0) {
                return resolve({});
            }

            request.getPipelines = true;

            http.get('/api/v1/pipeline').then((response) => {
                store.dispatch('pipelines/setPipelines', response.data.pipelines);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getPipelines = false;
            })
        })
    },
    getPipelineFilters() {
        return new Promise((resolve, reject) => {
            if (request.getPipelineFilters || store.getters['pipelines/getPipelineFilters'].length > 0) {
                return resolve({});
            }

            request.getPipelineFilters = true;

            http.get('/api/v1/pipeline/filters').then((response) => {
                store.dispatch('pipelines/setPipelineFilters', response.data.filters);

                resolve(response.data);
            }, error => {
                reject(error);
            }).finally(() => {
                request.getPipelineFilters = false;
            })
        })
    },
    createPipeline(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/pipeline/create', data).then((response) => {
                store.dispatch('pipelines/addPipeline', response.data.pipeline);

                window.app.$notify({type:'success', text: window.app.$t('create_pipeline_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    updatePipeline(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/pipeline/update', data).then((response) => {
                store.dispatch('pipelines/updatePipeline', response.data.pipeline);

                window.app.$notify({type:'success', text: window.app.$t('update_pipeline_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    removePipeline(pipelineId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/pipeline/${pipelineId}`).then((response) => {
                store.dispatch('pipelines/removePipeline', pipelineId);

                window.app.$notify({type:'success', text: window.app.$t('delete_pipeline_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    /**
     * Rules
     */
    createPipelineRule(pipelineId, data) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/pipeline/${pipelineId}/rule/create`, data).then((response) => {
                store.dispatch('pipelines/updatePipeline', response.data.pipeline);

                window.app.$notify({type:'success', text: window.app.$t('create_pipeline_rule_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    updatePipelineRule(pipelineId, data) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/pipeline/${pipelineId}/rule/update`, data).then((response) => {
                store.dispatch('pipelines/updatePipeline', response.data.pipeline);

                window.app.$notify({type:'success', text: window.app.$t('update_pipeline_rule_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
    removePipelineRule(pipelineId, pipelineRuleId) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/pipeline/${pipelineId}/rule/${pipelineRuleId}`).then((response) => {
                store.dispatch('pipelines/updatePipeline', response.data.pipeline);

                window.app.$notify({type:'success', text: window.app.$t('delete_pipeline_rule_success')});

                resolve(response.data);
            }, error => {
                reject(error);
            });
        })
    },
};
