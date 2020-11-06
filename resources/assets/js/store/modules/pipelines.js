const state = {
    collection: [],
    filters: []
};

const getters = {
    getPipelines(state) {
        return state.collection
    },
    getPipelineFilters(state) {
        return state.filters
    },
};

const actions = {
    setPipelines ({commit}, pipelines) {
        commit('setPipelines', pipelines)
    },
    addPipeline ({commit}, pipeline) {
        commit('addPipeline', pipeline)
    },
    updatePipeline ({commit}, pipeline) {
        commit('updatePipeline', pipeline)
    },
    removePipeline ({commit}, pipeline_id) {
        commit('removePipeline', pipeline_id)
    },

    setPipelineFilters ({commit}, filters) {
        commit('setPipelineFilters', filters)
    },

    changeBoardInRule({commit}, payload) {
        commit('changeBoardInRule', payload)
    },

    changeRule({commit}, payload) {
        commit('changeRule', payload)
    }
};

const mutations = {
    setPipelines(state, pipelines) {
        state.collection = pipelines
    },
    addPipeline(state, pipeline) {
        state.collection.push(pipeline);
    },
    updatePipeline(state, pipeline) {
        state.collection.find(item => {
            if (item.id === pipeline.id) {
                Object.assign(item, pipeline);
                return true
            }
        });
    },
    removePipeline(state, pipeline_id) {
        state.collection = state.collection.filter(item => item.id !== pipeline_id);
    },

    setPipelineFilters(state, filters) {
        state.filters = filters
    },

    changeRule(state, payload) {
        state.collection.forEach(item => {
            if (item.id === payload.pipeline_id) {
                let rule = item.rules.find(r => r.id === payload.rule_id);
                if (rule) rule[payload.field] = payload.option.map(item => item.value);
            }
        })
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
