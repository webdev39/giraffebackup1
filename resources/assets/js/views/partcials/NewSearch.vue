<template>
    <multiselect
        ref="newSearch"
        :placeholder="$t('type_to_search')"
        :options="results"
        :searchable="true"
        :loading="isLoading"
        :internal-search="false"
        :options-limit="300"
        :limit="3"
        :max-height="600"
        :show-no-results="false"
        :hide-selected="true"
        :group-select="false"
        group-values="value"
        group-label="label"
        id="ajax-search"
        label="name"
        track-by="code"
        open-direction="bottom"
        @search-change="asyncFind"
        @select="handleSelect"
    >

        <template slot="option" slot-scope="{ option }">
            <span v-if="option.$groupLabel">{{ option.$groupLabel }}</span>

            <span
                v-if="option.type === 'comment'"
                v-html="option.name"
            ></span>

            <span v-else>{{ option.name }}</span>
        </template>

        <template slot="clear" slot-scope="props">
            <div class="multiselect__clear" v-if="true" @mousedown.prevent.stop="clearAll(props.search)"></div>
        </template>

        <span class="line-wrap" slot="noOptions">{{ $t('oops_no_elements_found') }}</span>
    </multiselect>
</template>

<script>
    import Multiselect from 'vue-multiselect'

    export default {
        name: "new-search",
        components: {
            Multiselect
        },
        data() {
            return {
                results: [],
            }
        },
        beforeDestroy(){
            this.$event.$off('set-search-text');
        },
        created() {
            this.$event.$on('set-search-text', this.setSearchText);
        },
        methods: {
            setSearchText(text) {
              this.asyncFind(text);
              this.$refs['newSearch'].search = text;
              this.$refs['newSearch'].isOpen = true;
            },
            asyncFind: _.debounce(function (query) {
                this.isLoading = true;

                this.$http.post('/api/v1/search', { query })
                    .then(response => {
                        const { boards, groups, tasks, comments } = response.data.results;
                        const result = [];

                        if (tasks.length) result.push({ label: 'Tasks', value: tasks.map(item => ({ id: item.id, type: 'tasks', name: item.name, link: item.link })) });
                        if (boards.length) result.push({ label: 'Boards', value: boards.map(item => ({ id: item.id, type: 'boards', name: item.name, group_id: item.group_id })) });
                        if (groups.length) result.push({ label: 'Groups', value: groups.map(item => ({ id: item.id, type: 'groups', name: item.name })) });
                        if (Object.keys(comments).length) result.push({ label: 'Comments', value: Object.values(comments).map(item => ({ id: item.id, type: 'comment', name: item.body, group_id: item.groupId, task_id: item.task_id })) });

                        this.results = result;

                        this.isLoading = false
                    })
            }, 500),
            handleSelect(event) {
            	switch (event.type) {
					case 'tasks':
						this.$router.push({ path: event.link });
                        break;
					case 'boards':
						this.$router.push({ name: 'board', params: { board_id: event.id, group_id: event.group_id } });
						break;
					case 'groups':
						this.$router.push({ name: 'communication', params: { group_id: event.id } });
						break;
                    case 'comment':
                    	if (event.task_id) {
						    this.$router.push({ path: `/group/${event.group_id}/communication?taskId=${event.task_id}` });
                        } else  {
							this.$router.push({ name: 'communication', params: { group_id: event.group_id } });
                        }
						break;
                    default:
                    	return
				}
            },
        }
    }
</script>

<style scoped lang="scss">
    .multiselect {
        min-width: 265px;
        min-height: 36px;
        -webkit-box-shadow: 0px 1px 5px 0 rgba(0, 0, 0, 0.5);
        box-shadow: 0px 1px 5px 0 rgba(0, 0, 0, 0.5);
        position:absolute;
        top: 42px;
        left: -80px;
    }


        @media (min-width: 576px) {
        .multiselect {
            min-width: 350px;
            left:10px;
        }
    }


     @media (min-width: 1120px) {
        .multiselect {
        position:relative;
        top:0;
        left:0;
        -webkit-box-shadow: 0px 1px 0px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 0px 1px 0px 0 rgba(0, 0, 0, 0.3);
    }
 }

    .line-wrap {
        display: inline-block;
        width: 100%;
        white-space: pre-wrap;
    }

</style>
