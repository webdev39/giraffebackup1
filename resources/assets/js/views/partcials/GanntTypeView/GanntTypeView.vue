<template>
    <div class="board-type-select">
        <select
            v-model="getSelectGanttTypeView"
            :disabled="isLoading"
            class="form-control form-theme_reset"
        >
            <option
                v-for="(ganttTypeView, index) in ganttTypesViewTranslate"
                :key="index"
                :value="ganttTypeView"
            >
                {{ ganttTypeView.alias }}
            </option>
        </select>
        <info-placeholder
            :content="$t('period_content')"
            class="board-type-select__info-placeholder"
        />
    </div>
</template>

<script>
	import {mapGetters} from 'vuex';
	import infoPlaceholder from '@views/components/infoPlaceholder/infoPlaceholder';

	export default {
		components: {
			infoPlaceholder,
		},
		computed: {
			...mapGetters({
				selectGanttTypeView: 'groups/getSelectGanttTypeView',
				ganttTypesView: 'groups/getGanttTypesView',
			}),
			ganttTypesViewTranslate() {
			    if (this.ganttTypesView.length) {
                    return this.ganttTypesView.map(item => {
                    	return {
							alias: this.$t(item.name),
							name: item.name
                        }
                    })
                }
            },
			getSelectGanttTypeView: {
				get() {
					return this.selectGanttTypeView;
				},
				set(newValue) {
					this.$store.dispatch('groups/changeSelectGanttTypeView', newValue);
				}
			},
		},
	}
</script>