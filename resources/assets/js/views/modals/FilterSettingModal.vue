<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :maxWidth="700"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        height="auto"
        width="100%"
        transition="nice-modal-fade"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
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
                {{ form.id ? $t('edit_filter') : $t('add_filter') }}
            </template>

            <template slot="body">
                <div class="form-group row">
                    <label for="form-name-input" class="col-sm-3 col-form-label">{{ $t('title') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="name">
                        <input type="text" class="form-control" id="form-name-input" v-model="form.name" :placeholder="$t('enter_filters_name')">
                    </validation>
                </div>

                <div class="form-group row">
                    <label for="form-time-range-input" class="col-sm-3 col-form-label">{{ $t('time_range') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="range">
                        <select class="form-control" style="margin-bottom: 10px;" id="form-time-range-input" v-model="form.range">
                            <option v-for="(range, index) in optionsRanges" :value="range.value" :key="index">
                                {{ range.label }}
                            </option>
                        </select>

                        <div class="row" v-show="form.range === 'custom' ">
                            <div class="col-sm-6">
                                <label>{{ $t('start') }}</label>
                                <date-picker
                                    v-model="customRange.start"
                                    :config="Object.assign(dateTimeStart, { locale: getUserProfile.language.iso_639_1 })"
                                    @dp-change="handleChangeStartDate"
                                    ref="rangeStartDate"
                                />
                            </div>

                            <div class="col-sm-6">
                                <label>{{ $t('end') }}</label>
                                <date-picker
                                    v-model="customRange.end"
                                    :config="Object.assign(dateTimeEnd, { locale: getUserProfile.language.iso_639_1 })"
                                    @dp-change="handleChangeEndDate"
                                    ref="rangeEndDate"
                                />
                            </div>
                        </div>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('assigned') }}</label>
                    <validation class="col-sm-9" :validator="validator" label="assigner_ids">
                        <multiselect
                                v-model="selection.members"
                                :options="optionsMembers"
                                label="label"
                                track-by="value"
                                :placeholder="$t('select_member')"
                                :multiple="true"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                                :limit="2"
                                :close-on-select="false"
                        />
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('groups') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="group_ids">
                        <multiselect
                                v-model="selection.groups"
                                :options="optionsGroups"
                                label="label"
                                track-by="value"
                                :placeholder="$t('select_group')"
                                :multiple="true"
                                :limit="2"
                                :close-on-select="false"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                        />
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('boards') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="board_ids">
                        <multiselect
                                v-model="selection.boards"
                                :options="optionsBoards"
                                label="label"
                                track-by="value"
                                :placeholder="$t('select_board')"
                                :multiple="true"
                                :limit="2"
                                :close-on-select="false"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                        />
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('priority') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="priority_ids">
                        <multiselect
                                v-model="selection.priorities"
                                :options="optionsPriorities"
                                label="label"
                                track-by="value"
                                :placeholder="$t('select_priority')"
                                :multiple="true"
                                :limit="2"
                                :close-on-select="false"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                        >
                            <template slot="tag" slot-scope="props">
                                <span class="multiselect__tag" :style="{backgroundColor: props.option.color}">
                                    <span>{{props.option.label}}</span>
                                    <i aria-hidden="true" class="multiselect__tag-icon" @keydown.enter.prevent="props.remove(props.option)" @mousedown.prevent="props.remove(props.option)"></i>
                                </span>
                            </template>
                        </multiselect>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('status') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="status">
                        <multiselect
                                v-model="form.status"
                                :options="optionsStatuses"
                                label="label"
                                track-by="value"
                                placeholder="Select Status"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                        >
                            <template slot="tag" slot-scope="props">
                                <span class="multiselect__tag" :style="{backgroundColor: props.option.color}">
                                    <span>{{props.option.label}}</span>
                                    <i aria-hidden="true" class="multiselect__tag-icon" @keydown.enter.prevent="props.remove(props.option)" @mousedown.prevent="props.remove(props.option)"></i>
                                </span>
                            </template>
                        </multiselect>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">{{ $t('default_view') }}</label>

                    <validation class="col-sm-9" :validator="validator" label="view_type_id">
                        <multiselect
                                v-model="form.view_type"
                                :options="optionsViewTypes"
                                label="label"
                                track-by="value"
                                :placeholder="$t('select_default_view')"
                                :selectLabel="selectLabelPlaceholder"
                                :deselectLabel="deselectLabelPlaceholder"
                        />
                    </validation>
                </div>
            </template>

            <template slot="footer">
                <button type="button" class="btn btn-remove" @click="handleRemoveFilter" :disabled="isLoading" v-if="form.id">
                    <i class="icon-trash">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-trash">
                            </use>
                        </svg>
                    </i>
                </button>

                <theme-button-close type="button" class="btn" @click.native="closeModal">
                   {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="button" class="btn" @click.native="handleUpdateFilter" :disabled="isLoading" v-if="form.id">
                   {{ $t('save') }}
                </theme-button-success>

                <theme-button-success type="button" class="btn" @click.native="handleCreateFilter" :disabled="isLoading" v-if="!form.id">
                   {{ $t('create') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import {mapGetters}         from "vuex";
    import DatePicker           from 'vue-bootstrap-datetimepicker'

    import formMixin            from "@mixins/form"
    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import Validation           from "@views/components/Validation";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "filter-setting-modal",
        data() {
            return {
                form: {
                    id:             null,
                    name:           '',
                    range:          null,
                    assigner_ids:   [],
                    group_ids:      [],
                    board_ids:      [],
                    priority_ids:   [],
                    status:         false,
                    view_type:      null,
                },
                initForm: {},
                customRange: {
                    start:          null,
                    end:            null,
                },
                timeRanges: [
                    { id: 0, name: window.app.$t('custom'),           value: 'custom',        isCustom: true  },
                    { id: 1, name: window.app.$t('today'),            value: 'today',         isCustom: false },
                    { id: 2, name: window.app.$t('yesterday'),        value: 'yesterday',     isCustom: false },
                    { id: 3, name: window.app.$t('tomorrow'),         value: 'tomorrow',      isCustom: false },
                    { id: 4, name: window.app.$t('next_week'),        value: 'next_week',     isCustom: false },
                    { id: 5, name: window.app.$t('this_week'),        value: 'this_week',     isCustom: false },
                    { id: 6, name: window.app.$t('last_week'),        value: 'last_week',     isCustom: false },
                    { id: 7, name: window.app.$t('this_month'),       value: 'this_month',    isCustom: false },
                    { id: 8, name: window.app.$t('last_month'),       value: 'last_month',    isCustom: false },
                ],
                statuses: [
                    { id: 0, name: window.app.$t('open'),          value: 0,       color:'#ff9800' },
                    { id: 1, name: window.app.$t('done'),          value: 1,       color:'green' }
                ],
                dateTimeStart:{
                    format:             'YYYY-MM-DD',
                    useCurrent:         false,
                    showTodayButton:    true,
                    showClear:          true,
                    showClose:          true,
                },
                dateTimeEnd: {
                    format:             'YYYY-MM-DD',
                    useCurrent:         false,
                    showTodayButton:    true,
                    showClear:          true,
                    showClose:          true,
                },
                selection: {
                    groups:     [],
                    boards:     [],
                    members:    [],
                    priorities: [],
                },
                previous: {
                    groups:     [],
                    boards:     [],
                    members:    [],
                    priorities: [],
                },
                selectLabelPlaceholder: window.app.$t('press_enter_to_select'),
                deselectLabelPlaceholder: window.app.$t('press_enter_to_remove'),
            }
        },
        computed: {
            ...mapGetters({
                filters:    'filters/getFilters',
                members:    'members/getOnlyMembers',
                owner:      'members/getOwner',
                groups:     'groups/getGroups',
                boards:     'groups/getBoards',
                viewTypes:  'default/getViewTypes',
                priorities: 'priorities/getPriorities',
				getUserProfile: 'user/getUserProfile',
			}),
            optionsRanges() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                this.timeRanges.map(range => {
                    options.push({value: range.value, label: range.name, default: false});
                });

                return defaultOptions.concat(options);
            },
            optionsMembers() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                if (this.refreshSelectionByAllSelected('members', defaultOptions)) {
                    return defaultOptions;
                }

                if (this.owner) {
                    defaultOptions.push({value: this.owner.id,  label: "Me", default: false});
                }

                if (this.members) {
                    this.members.map(member => {
                        options.push({value: member.id, label: `${member.user.last_name} ${member.user.name}`, default: false});
                    });
                }

                return defaultOptions.concat(options.sort((a, b) => {
                    return sorter(a.label, b.label)
                }));
            },
            optionsGroups() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                if (this.refreshSelectionByAllSelected('groups', defaultOptions)) {
                    return defaultOptions;
                }

                if (this.groups) {
                    this.groups.map(group => {
                        options.push({value: group.id, label: group.name, default: false});
                    });
                }

                return defaultOptions.concat(options.sort((a, b) => {
                    return sorter(a.label, b.label)
                }));
            },
            optionsBoards() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                if (this.refreshSelectionByAllSelected('boards', defaultOptions)) {
                    return defaultOptions;
                }

                let boards = this.boards;

                if (this.selection.groups && this.selection.groups.some(item => item.value !== null)) {
                    boards = boards.filter(board => {
                        return this.selection.groups.some(item => item.value === board.group_id);
                    });
                }

                if (boards) {
                    boards.map(board => {
                        options.push({value: board.id, label: board.name, default: false});
                    });
                }

                return defaultOptions.concat(options.sort((a, b) => {
                    return sorter(a.label, b.label)
                }));
            },
            optionsPriorities() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                if (this.refreshSelectionByAllSelected('priorities', defaultOptions)) {
                    return defaultOptions;
                }

                let priorities = this.priorities;

                if (this.selection.boards && this.selection.boards.some(board => board.value !== null)) {
                    priorities = priorities.filter(priority => {
                        return this.selection.boards.some(board => board.value === priority.board_id)
                    });
                }

                if (this.selection.groups && this.selection.groups.some(item => item.value !== null)) {
                    priorities = priorities.filter(priority => {
                        return this.boards.some(board => {
                            return this.selection.groups.some(group => {
                                if (group.value === board.group_id) {
                                    return board.id === priority.board_id;
                                }
                            })
                        })
                    });
                }

                if (priorities && this.boards) {
                    priorities.map(priority => {
                        let board = this.boards.find(board => board.id === priority.board_id);
                        let label = `${priority.name}`;

                        if (board) {
                            label += ` (${board.name})`
                        }

                        options.push({value: priority.id, label: label, color: priority.color, default: false});
                    });
                }

                return defaultOptions.concat(options.sort((a, b) => {
                    return sorter(a.label, b.label)
                }));
            },
            optionsStatuses() {
                let options         = [];
                let defaultOptions  = [
                    {value: null, label: "All", default: true},
                ];

                this.statuses.filter(priority => {
                    options.push({value: priority.value, label: priority.name, color: priority.color, default: false});
                });

                return defaultOptions.concat(options);
            },
            optionsViewTypes() {
                let options = [];

                this.viewTypes.filter(item => {
                    if (item.name !== 'Communication') {
                        options.push({
                            value: item.id,
                            label: window.app.$t(item.name.toLowerCase()),
                            default: item.name === 'List'
                        });
                    }
                });

                return options;
            },
        },
        watch: {
            "selection.groups": {
                deep: true,
                handler() {
                    if (this.selection.boards && this.selection.boards.some(item => item.value !== null)) {
                        this.selection.boards = this.selection.boards.filter(item => {
                            return this.optionsBoards.some(board => board.value === item.value);
                        });
                    }

                    if (this.selection.priorities && this.selection.priorities.some(item => item.value !== null)) {
                        this.selection.priorities = this.selection.priorities.filter(item => {
                            return this.optionsPriorities.some(priority => priority.value === item.value);
                        });
                    }
                },
            },
            "selection.boards": {
                deep: true,
                handler() {
                    if (this.selection.priorities && this.selection.priorities.some(item => item.value !== null)) {
                        this.selection.priorities = this.selection.priorities.filter(item => {
                            return this.optionsPriorities.some(priority => priority.value === item.value);
                        });
                    }
                },
            },
        },
        components: {
            Validation,
            ModalWrapper,
            DatePicker,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        mixins: [
            formMixin
        ],
        methods: {
            beforeOpen(event) {
                this.setDefaultData();

                if (!event.params) {
                    return;
                }

                if (event.params.filterId) {
                    this.setFilter(event.params.filterId);
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
            setDefaultData() {
                this.form.status    = this.optionsStatuses.find(option => option.default);
                this.form.view_type = this.optionsViewTypes.find(option => option.default);
                this.initForm       = {...this.form};
            },
            setFilter(filterId) {
                const filter = this.copyObject(this.filters.find(filter => filter.id === filterId));

                if (filter) {
                    let item = {
                        id:     filter.id,
                        name:   filter.name,
                    };

                    if (filter.status !== null) {
                        item.status = this.optionsStatuses.find(status => status.value === filter.status);
                    }

                    if (filter.view_type_id) {
                        item.view_type = this.optionsViewTypes.find(option => option.value === filter.view_type_id);
                    }

                    if (this.isString(filter.range) && filter.range.split('/').length === 2) {
                        let range = filter.range.split('/');

                        this.customRange = {
                            start:  range[0] ? this.$moment.unix(range[0]).format(this.dateTimeStart.format) : null,
                            end:    range[1] ? this.$moment.unix(range[1]).format(this.dateTimeEnd.format)   : null,
                        };

                        item.range = 'custom';
                    } else {
                        item.range = filter.range;
                    }

                    if (this.isArray(filter.assigner_ids)) {
                        this.selection.members = this.optionsMembers.filter(member => filter.assigner_ids.includes(member.value));
                    } else {
                        this.selection.members = this.optionsMembers.filter(option => option.default);
                    }

                    if (this.isArray(filter.group_ids)) {
                        this.selection.groups = this.optionsGroups.filter(group => filter.group_ids.includes(group.value));
                    } else {
                        this.selection.groups = this.optionsGroups.filter(option => option.default);
                    }

                    if (this.isArray(filter.board_ids)) {
                        this.selection.boards = this.optionsBoards.filter(board => filter.board_ids.includes(board.value));
                    } else {
                        this.selection.boards = this.optionsBoards.filter(option => option.default);
                    }

                    if (this.isArray(filter.priority_ids)) {
                        this.selection.priorities = this.optionsPriorities.filter(priority => filter.priority_ids.includes(priority.value));
                    } else {
                        this.selection.priorities = this.optionsPriorities.filter(option => option.default);
                    }

                    Object.assign(this.form, item);
                }

                this.initForm = {...this.form};
            },
            closeModal() {
                this.$modal.hide(this.$options.name);
            },
            handleChangeStartDate(event) {
                $(this.$refs.rangeEndDate.$el).data("DateTimePicker").minDate(event.date)
            },
            handleChangeEndDate(event) {
                $(this.$refs.rangeStartDate.$el).data("DateTimePicker").maxDate(event.date)
            },
            refreshSelectionByAllSelected(key, defaultOptions) {
                if (this.selection[key].find(item => item.value === null)) {
                    this.previous[key]  = this.selection[key].filter(item => item.value !== null);
                    this.selection[key] = defaultOptions;

                    return true;
                } else {
                    if (this.previous[key].length) {
                        this.selection[key] = this.previous[key];
                        this.previous[key]  = [];
                    }

                    return false;
                }
            },
            getFormData() {
                let formData = {
                    filter_id:      this.form.id,
                    name:           this.form.name,
                    view_type_id:   this.form.view_type.value,
                    assigner_ids:   this.getSelectedIds('members'),
                    board_ids:      this.getSelectedIds('boards'),
                    group_ids:      this.getSelectedIds('groups'),
                    priority_ids:   this.getSelectedIds('priorities'),
                    status:         this.form.status.value,
                    range:          this.form.range,
                };

                if (this.form.range === 'custom') {
                    formData.range = this.getCustomRange();

                    if (formData.range === null) {
                        this.$notify({type:'error', text: window.app.$t('correct_time_range')});
                        return null
                    }
                }

                if (this.isObject(formData.status) && formData.status !== null) {
                    formData.status = formData.status[0];
                }

                return formData;
            },
            getSelectedIds(key) {
                if (this.selection[key].find(item => item.value === null)) {
                    return null;
                }

                let items = this.selection[key].map(item => item.value);

                return items.length > 0 ? items : null ;
            },
            getCustomRange(){
                if (this.customRange.start && this.customRange.end) {
                    let start = this.$moment.utc(this.customRange.start,    this.dateTimeStart.format).format('X');
                    let end   = this.$moment.utc(this.customRange.end,      this.dateTimeEnd.format).format('X');

                    return start + '/' + end;
                }

                return null;
            },
            handleCreateFilter() {
                let form = this.getFormData();

                if (form) {
                    this.$api.filter.createFilter(form).then(() => {
                        this.initForm = {...this.form};
                        this.closeModal();
                    }).catch((err) => {
                        this.defaultError(err.response);
                    })
                }
            },
            handleUpdateFilter() {
                let form = this.getFormData();

                if (form) {
                    this.$api.filter.updateFilter(form).then(() => {
                        this.initForm = {...this.form};
                        this.$api.task.getTasksByFilterId(this.$route.params.id);
                        this.closeModal();
                    }).catch((err) => {
                        this.defaultError(err.response);
                    })
                }
            },
            handleRemoveFilter() {
                this.$api.filter.deleteFilter(this.form.id).then(() => {
                    this.initForm = {...this.form};
                    this.closeModal();
                    this.$router.push({name: 'home'});
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
        }
    }
</script>

<style lang="scss">
    #filter-setting-modal {
        overflow: hidden;
        .col-form-label {
            padding: 4px 0 2px 15px;
            font-size: 16px;
            text-align: right;
        }
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


</style>
