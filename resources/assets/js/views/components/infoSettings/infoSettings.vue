<template>
    <dropDown class="info-setting">
        <div class="info-setting__list">
            <div class="info-setting__item">
                <div class="info-setting__item-text">
                    {{ $t('view')}}:
                </div>
                <div class="info-setting__item-control">
                    <board-view-type-select />
                </div>
            </div>
            <div class="info-setting__item" v-if="isSortTypes">
                <div class="info-setting__item-text">
                    {{ $t('sorting') }}:
                </div>
                <div class="info-setting__item-control">
                    <board-task-sorting :sortTypes="getSortTypes" />
                </div>
            </div>
            <div class="info-setting__item" v-if="isListTypeView || isCalendarTypeView">
                <div class="info-setting__item-text">{{ $t('splitted') }}:</div>
                <div class="info-setting__item-control">
                    <select :disabled="isLoading" class="form-control form-theme_reset" v-model="getSelectSplitted" >
                        <option v-for="(split, index) in getSplitted" :value="split" :key="index">
                            {{ split.alias }}
                        </option>
                    </select>
                </div>
                <info-placeholder
                    :content="$t('info_popup.splitted')"
                    class="info-setting__item-info-placeholder"
                ></info-placeholder>
            </div>
            <div class="info-setting__item" v-if="!isCommunicationTypeView">
                <div class="info-setting__item-text">{{ $t('hide_done') }}:</div>
                <div class="info-setting__item-control">
                    <select :disabled="isLoading" class="form-control form-theme_reset" v-model="getCurrentBoardHideTask" >
                        <option :value="true">
                            {{ $t('yes') }}
                        </option>
                        <option :value="false">
                            {{ $t('no') }}
                        </option>
                    </select>
                </div>
                <info-placeholder
                    :content="$t('info_popup.hide_task')"
                    class="info-setting__item-info-placeholder"
                ></info-placeholder>
            </div>
            <div class="info-setting__item" v-if="isListTypeView || isCalendarTypeView">
                <div class="info-setting__item-text">{{ $t('quick_navigation') }}:</div>
                <div class="info-setting__item-control">
                    <select :disabled="isLoading" class="form-control form-theme_reset" v-model="getSelectQuickNavigation" >
                        <option v-for="(quickNavigation, index) in getQuickNavigation" :value="quickNavigation" :key="index">
                            {{ quickNavigation.alias }}
                        </option>
                    </select>
                </div>
                <info-placeholder
                    :content="$t('info_popup.quick_navigation')"
                    class="info-setting__item-info-placeholder"
                ></info-placeholder>
            </div>
            <div class="info-setting__item" v-if="isKanbanTypeView">
                <div class="info-setting__item-text"></div>
                <div class="info-setting__item-control">
                    <theme-button-success
                            class="btn button__size_xs"
                            @click.native="showPrioritySettingModal"
                            :disabled="isLoading"
                    >
                        {{ $t('edit_columns') }}
                    </theme-button-success>
                </div>
            </div>
            <div class="info-setting__item" v-if="isGanttTypeView">
                <div class="info-setting__item-text">
                    {{ $t('period') }}:
                </div>
                <div class="info-setting__item-control">
                    <gannt-type-view />
                </div>
            </div>
            <div class="info-setting__item" v-if="isGanttTypeView">
                <div class="info-setting__item-text">
                    {{ $t('period') }}:
                </div>
                <div class="info-setting__item-control">
                    <gannt-range-type-view />
                </div>
            </div>
            <slot></slot>
        </div>
    </dropDown>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import groupsMixin          from '@mixins/groups'

    import find                 from '@helpers/findInGroups'
    import infoPlaceholder      from '@views/components/infoPlaceholder/infoPlaceholder'
    import boardViewTypeSelect  from '@views/partcials/BoardViewTypeSelect/BoardViewTypeSelect'
    import boardTaskSorting     from '@views/partcials/BoardTaskSorting/BoardTaskSorting'
    import ganntTypeView        from '@views/partcials/GanntTypeView/GanntTypeView'
    import ganntRangeTypeView   from '@views/partcials/GanntRangeTypeView/GanntRangeTypeView'

    import dropDown     from '@views/components/dropDown/dropDown'
    import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    const CURRENT_TIMESTAMP = "CURRENT_TIMESTAMP";

    export default {
        components: {
            ThemeButtonSuccess,
            infoPlaceholder,
            boardViewTypeSelect,
            boardTaskSorting,
            ganntTypeView,
            ganntRangeTypeView,
            dropDown
        },
        mixins: [
            groupsMixin
        ],
        computed: {
            ...mapGetters({
                viewTypes:          'default/getViewTypes',
                getGroups:          'groups/getStateGroups',
            }),
            isSortTypes () {
                if (this.getSelectViewType.name === 'Communication') {
                    return false
                }

                return true
            },
            getSortTypes() {
                if (this._getCurrentBoard) {

                    let sortTypes = this.$store.getters['groups/getSortTypes'];

                    if (this.isGanttTypeView) {
                        sortTypes = sortTypes.filter(item => item.name !== 'sort_weight')
                    }

                    if (this.isKanbanTypeView) {
                        sortTypes = sortTypes.filter(item => item.name !== 'priority')
                    }

                    if (this.isCommunicationTypeView) {
                        sortTypes = sortTypes.filter(item => item.name !== 'assignee');
                    }

                    return sortTypes;
                }
            },
            isKanbanTypeView() {
                return this.getSelectViewType.name === 'Kanban'
            },
            isListTypeView() {
                return this.getSelectViewType.name === 'List'
            },
            isCalendarTypeView() {
                return this.getSelectViewType.name === 'Calendar'
            },
            isCommunicationTypeView() {
                return this.getSelectViewType.name === 'Communication'
            },
            isGanttTypeView() {
                return this.getSelectViewType.name === 'Gantt'
            },
            getCurrentBoardHideTask: {
                get() {
                    if (this._getCurrentBoard) {
                        return this._getCurrentBoard.hide_done_tasks
                    }
                },
                set(status) {
                    this.handleToggleHideTask(status);
                }
            },
            getSelectViewType: {
                get() {
                    let viewTypeId;

                    if (this._getUserViewTypeId) {
                        viewTypeId = +this._getUserViewTypeId
                    } else {
                        viewTypeId = this._getCurrentBoard.view_type_id
                    }

                    return this.viewTypes.find(item => item.id === viewTypeId)
                },
            },
            getSplitted() {
                if (this._getCurrentBoard) {
                    return this.$store.getters['groups/getSplitted']
                }
            },
            getSelectSplitted: {
                get() {
                    return this.$store.getters['groups/getSelectSplitted'];
                },
                set(newValue) {
                    this.$store.dispatch('groups/changeSelectSplitted', newValue);
                }
            },
            getQuickNavigation() {
                if (this._getCurrentBoard) {
                    return this.$store.getters['groups/getQuickNavigation']
                }
            },
            getSelectQuickNavigation: {
                get() {
                    return this.getQuickNavigation.find(item => item.name === (this._getCurrentBoard.quick_nav | 0));
                },
                set(newValue) {
                    this.handleChangeQuickNavigation(newValue.name);
                }
            },
        },
        watch: {
            '$route'(to, from) {
                if (+to.params.board_id !== +from.params.board_id) {
                    this.$store.dispatch('groups/setDefaultSelectSortType');
                }
            },
        },
        methods: {
            handleToggleHideTask(status) {
                let data = {};

				if (this.$route.name === 'board') {
					this.$api.task.getTaskByBoardId(this.$route.params.board_id, status);
				}

                data = Object.assign({}, this._getCurrentBoard, {
                    deadline:           this._getCurrentBoard.deadline === CURRENT_TIMESTAMP ? null : this._getCurrentBoard.deadline,
                    hide_done_tasks :   status,
                    board_id:           this._getCurrentBoard.id
                });

                this.$api.board.updateBoard(data, 'board_res=long&board_relations=none').then(() => {}).catch((err) => {
                    this.defaultError(err.response);
                });
            },
            handleChangeQuickNavigation(status) {

                let data = {};

                data = Object.assign({}, this._getCurrentBoard, {
                    quick_nav:  status,
                    board_id:   this._getCurrentBoard.id
                });

                this.$api.board.updateBoard(data, 'board_res=long&board_relations=none').then(() => {}).catch((err) => {
                    this.defaultError(err.response);
                })
            } ,
            showPrioritySettingModal() {
                this.$modal.show('priority-setting-modal', {boardId: this._getCurrentBoardId});
            },
            handleToggleShowSetting() {
                this.show = !this.show;
            }
        }
    }
</script>
