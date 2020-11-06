<template>
    <div class="board-type-select">
        <select
            v-model="getSelectSortType"
            :disabled="isLoading"
            class="form-control form-theme_reset"
        >
            <option
                v-for="(sortType, index) in sortTypes"
                :key="index"
                :value="sortType"
            >
                {{ $t(sortType.alias) }}
            </option>
        </select>

        <info-placeholder
            :content="$t('info_popup.sorting')"
            class="board-type-select__info-placeholder"
        ></info-placeholder>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    import infoPlaceholder  from '@views/components/infoPlaceholder/infoPlaceholder';

    export default {
        components: {
            infoPlaceholder
        },
        props: {
            sortTypes: {
            	type: Array
            }
        },
        computed: {
        	...mapGetters({
				getSelectSortTypeStore: 'groups/getSelectSortType'
            }),
            getSelectSortType: {
                get() {
                	if (this.sortTypes.find(item => item.name === this.getSelectSortTypeStore.name) === undefined) {
						this.$store.dispatch('groups/changeSelectSortType', this.sortTypes[0]);
					}
                	return this.getSelectSortTypeStore;
                },
                set(newValue) {
                    this.$store.dispatch('groups/changeSelectSortType', newValue);
                }
            },
        },

    }
</script>
