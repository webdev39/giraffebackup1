<!--Optimized-->
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
            style="z-index:1000"
            @before-open="beforeOpen"
            @before-close="beforeClose"
    >
        <modal-wrapper
                :name="$options.name"
                color="white"
        >
            <template slot="header">
                <theme-button-close
                    class="btn-close-header"
                    @click.native="closeModal"
                >
                    <i class="icon-close">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
            </template>

            <template slot="title">
                {{ title }}
            </template>

            <template slot="body">
                <div class="row form-group">
                    <label for="board-name-input" class="col-sm-6 col-form-label">{{ $t("board_name") }}</label>

                    <validation
                        data-v-step="group_5"
                        class="col-sm-6"
                        :validator="validator"
                        label="name"
                    >
                        <QuillTag class="form-control"
                                  ref="quill-name"
                                  :is-input="true"
                                  :content="form.name">
                        </QuillTag>
                    </validation>
                </div>

                <div class="row form-group">
                    <label for="group-name-input" class="col-sm-6 col-form-label">{{ $t("group_name") }}</label>

                    <validation
                        :validator="validator"
                        class="col-sm-6"
                        label="group_id"
                    >
                        <select
                                v-model="form.group_id"
                                :disabled="!isUpdate"
                                class="form-control"
                                style="margin-bottom: 10px;"
                                id="group-name-input"
                        >
                            <option
                                    v-for="(group, index) in getGroups"
                                    :key="index"
                                    :value="group.id"
                            >
                                {{ group.name }}
                            </option>
                        </select>
                    </validation>
                </div>

                <div class="row form-group">
                    <label for="board-description-input" class="col-sm-6 col-form-label">{{ $t("board_description") }}</label>

                    <validation
                            :validator="validator"
                            class="col-sm-6"
                            label="description"
                    >
                        <QuillTag ref="quill-description" :content="form.description"></QuillTag>
                    </validation>
                </div>

                <div class="row form-group">
                    <label class="col-sm-6 col-form-label">{{ $t('deadline') }}</label>

                    <validation
                        data-v-step="group_6"
                        class="col-sm-6"
                        :validator="validator"
                        label="deadline"
                    >
                        <date-picker
                            v-model="form.deadline"
                            :config="Object.assign(datetimeOptions, { locale: getUserProfile.language.iso_639_1 })"
                            :disabled="!isUpdate"
                        />
                    </validation>
                </div>

                 <div class="form-group row">
                    <label for="board-hard-budget-input" class="col-sm-6 col-form-label">{{ $t('hard_budget') }}</label>

                    <validation
                        data-v-step="group_7"
                        class="col-sm-6"
                        :validator="validator"
                        label="hard_budget"
                    >
                        <input
                                type="number"
                                class="form-control"
                                id="board-hard-budget-input"
                                min="0"
                                max="99999"
                                :disabled="!isUpdate"
                                v-model="form.hard_budget"
                        >
                    </validation>
                </div>

                <div class="form-group row">
                    <label for="board-soft-budget-input" class="col-sm-6 col-form-label">{{ $t('soft_budget') }}</label>

                    <validation
                            :validator="validator"
                            class="col-sm-6"
                            label="soft_budget"
                    >
                        <input
                                v-model="form.soft_budget"
                                :disabled="!isUpdate"
                                type="number"
                                class="form-control"
                                id="board-soft-budget-input"
                                min="0"
                                max="99999"
                        >
                    </validation>
                </div>

                <div class="form-group row">
                    <label for="board-budget-type-input" class="col-sm-6 col-form-label">{{ $t('budget_type') }}</label>

                    <validation
                            :validator="validator"
                            class="col-sm-6"
                            label="budget_type_id"
                    >
                        <select
                                v-model="form.budget_type_id"
                                :disabled="!isUpdate"
                                class="form-control"
                                id="board-budget-type-input"
                        >
                            <option
                                    v-for="budgetType in budgetTypes"
                                    :key="budgetType.id"
                                    :value="budgetType.id"
                            >
                                {{ budgetType.name }}
                            </option>
                        </select>
                    </validation>
                </div>

                <div class="form-group row">
                    <label for="board-view-type-input" class="col-sm-6 col-form-label">{{ $t("view_type") }}</label>

                    <validation
                            :validator="validator"
                            class="col-sm-6"
                            label="view_type_id"
                    >
                        <select
                                v-model="form.view_type_id"
                                :disabled="!isUpdate"
                                id="board-view-type-input"
                                class="form-control"
                        >
                            <option
                                    v-for="viewType in viewTypes"
                                    :key="viewType.id"
                                    :value="viewType.id"
                            >
                                {{ $t(viewType.name.toLowerCase()) }}
                            </option>
                        </select>
                    </validation>
                </div>

                <div class="form-group row">
                    <label for="hide_done_tasks" class="col-sm-6 col-form-label">{{ $t("hide_done_tasks") }}</label>

                    <validation
                            :validator="validator"
                            class="col-sm-6"
                            label="hide_done_tasks"
                    >
                        <input
                                v-model="form.hide_done_tasks"
                                :disabled="!isUpdate"
                                type="checkbox"
                                id="hide_done_tasks"
                        >
                    </validation>
                </div>

                <div class="form-group row" v-if="form.board_id">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <theme-button-success type="button" class="btn" @click.native="showPrioritySettingModal">
                            <span>{{ $t("priority_settings") }}</span>
                        </theme-button-success>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <theme-button-dangerous
                        v-if="!form.is_archive && form.board_id && isUpdate"
                        :disabled="isLoading"
                        type="button"
                        class="btn pull-left btn-archived"
                        @click.native="handleArchiveBoard"
                >
                    <span>{{ $t("archivate") }}</span>
                </theme-button-dangerous>

                <theme-button-dangerous
                        v-if="form.is_archive && form.board_id && isUpdate"
                        :disabled="isLoading"
                        type="button"
                        class="btn pull-left btn-archived"
                        @click.native="handleUnarchiveBoard"
                >
                    <span>{{ $t("un_archive") }}</span>
                    <i class="fa fa-archive" aria-hidden="true"></i>
                </theme-button-dangerous>

                <button
                        v-if="form.board_id"
                        :disabled="isLoading"
                        :title="$t('title_remove_board')"
                        type="button"
                        class="btn btn-remove"
                        @click="handleRemoveBoard"
                >
                    <i class="icon-trash">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-trash">
                            </use>
                        </svg>
                    </i>
                </button>

                <theme-button-close
                        type="button" class="btn"
                        @click.native="closeModal"
                >
                    {{ $t("close") }}
                </theme-button-close>

                <theme-button-success
                    v-if="form.board_id && isUpdate"
                    :disabled="isLoading"
                    type="button"
                    class="btn"
                    @click.native="handleUpdateBoard"
                >
                    {{ $t("save") }}
                </theme-button-success>

                <theme-button-success
                    v-if="!form.board_id"
                    :disabled="isLoading"
                    type="button" class="btn"
                    @click.native="handleCreateBoard"
                    data-v-step="group_8"
                >
                    {{ $t("create_board") }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from "vuex";
    import DatePicker           from "vue-bootstrap-datetimepicker";

    import formMixin            from "@mixins/form";
    import Validation           from "@views/components/Validation";
    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import QuillTag             from "@views/partcials/QuillTag/QuillTag";

    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";
    import ThemeButtonDangerous from "@views/layouts/theme/buttons/ThemeButtonDangerous";

    const CURRENT_TIMESTAMP = "CURRENT_TIMESTAMP";

    export default {
        name: "board-setting-modal",
        data() {
            return {
                form: {
                    board_id:       null,
                    group_id:       null,
                    name:           '',
                    description:    '',
                    deadline:       null,
                    soft_budget:    null,
                    hard_budget:    null,
                    budget_id:      null,
                    budget_type_id: 2,
                    view_type_id:   null,
                    priority_id:    null,
                    is_archive:     false,
                    hide_done_tasks:false,
                },
                initForm: {},
                datetimeOptions: {
                    format:             'YYYY-MM-DD',
                    minDate:            new Date(),
                    useCurrent:         true,
                    showTodayButton:    true,
                    showClear:          true,
                    showClose:          true
                },
            };
        },
        computed: {
            ...mapGetters({
				getGroups:      'groups/getGroups',
                budgetTypes:    'default/getBudgetTypes',
                viewTypes:      'default/getViewTypes',
                userId:         'user/getUserId',
				getCurrentTour: 'getCurrentTour',
				getUserProfile: 'user/getUserProfile',
			}),
            // getGroups () {
			// 	return this.groups.filter(item => this.handlePermissionByGroupId('create-board', item.id))
            // },
            isUpdate () {
                const { board_id } = this.form;
                /**
                 * Check if board is exist
                 */
                if (board_id) {
                    const isCreater = this.userId === this.form.creator_id;
                    return this.isTenant || isCreater ||  this.handlePermissionByBoardId('update-board', board_id)
                }
                /**
                 * Otherwise just check permission create-board
                 */
                return this.checkPermission('create-board');
            },

            title () {
                return `${this.$t('board_settings')} ${this.form.name}`;
            },
        },
        components: {
            ModalWrapper,
            Validation,
            DatePicker,
            ThemeButtonSuccess,
            ThemeButtonClose,
            ThemeButtonDangerous,
            QuillTag
        },
        mixins: [
            formMixin,
        ],
        methods: {
            beforeOpen(event) {
                this.getTags();

                if (!event.params) {
                    this.initForm = {...this.form};
                    return;
                }

                if (event.params.name) {
                    this.form.name = event.params.name;
                }

                if (event.params.boardId) {
                    this.form.board_id = event.params.boardId;

                    this.$api.board.getBoardById(this.form.board_id, 'board_res=long&board_relations=none').then((res) => {
                        const { board } = res;
                        this.form = Object.assign(board, {
                            board_id:    board.id,
                            creator_id:  board.creator_id,
                            group_id:    board.group_id,
                            soft_budget: this.convertBudgetToInt(board.soft_budget),
                            hard_budget: this.convertBudgetToInt(board.hard_budget),
                        });
                        this.initForm = {...this.form}
                    }).catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    })
                }
            },
            getTags() {
                this.$api.tag.getTags()
            },
            setQuillDescription() {
                this.form.description = this.$refs['quill-description'].getEditorContent();
            },
            setQuillName() {
                this.form.name = this.$refs['quill-name'].getEditorContent();
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
            convertBudgetToInt(budget) {
                return budget ? parseInt(budget.split(":")[0]) : 0;
            },
            convertBudgetToString(budget) {
                return budget ? budget + ":00" : "00:00";
            },
            closeModal() {
                this.$modal.hide('board-setting-modal');
            },
            showPrioritySettingModal() {
                this.$modal.show('priority-setting-modal', {boardId: this.form.board_id});
            },
            handleArchiveBoard() {
                this.$modal.show("confirm-modal", {
                    title: this.$t('archived_this_Board'),
                    body: this.$t('are_you_sure_you_want_to_archived_this_board'),
                    confirmCallback: () => {
                        this.$api.board.archiveBoard(this.form.board_id, 'board_res=long&board_relations=none').then((res) => {
                            this.$api.task.getTasksDeadline();
                            this.closeModal();
                        }).catch((err) => {
                            this.$notify({type:'error', text: err.response.data.message});
                        })
                    },
                });
            },
            handleUnarchiveBoard() {
                this.$api.board.unarchiveBoard(this.form.board_id, 'board_res=long&board_relations=none').then((res) => {
                    this.$api.task.getTasksDeadline();

                    this.closeModal();
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                })
            },
            handleCreateBoard() {
                if (this.form.soft_budget < 0  ||  this.form.hard_budget < 0 ){
                    return this.$notify({type:'error', text: this.$t('budget_positive_number')});
                }

                this.setQuillDescription();
                this.setQuillName();

                let data = Object.assign({}, this.form, {
                    deadline:       this.form.deadline === CURRENT_TIMESTAMP ? null : this.form.deadline,
                    soft_budget:    this.convertBudgetToString(this.form.soft_budget),
                    hard_budget:    this.convertBudgetToString(this.form.hard_budget),
                });
                // board_res=long&board_relations=none
                this.$api.board.createBoard(data, 'board_res=long').then(() => {
                    this.initForm = {...this.form};
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleUpdateBoard() {
                if (this.form.soft_budget < 0  ||  this.form.hard_budget < 0 ){
                    return this.$notify({type:'error', text: this.$t('budget_positive_number')});
                }

                this.setQuillDescription();
                this.setQuillName();

                let data = Object.assign({}, this.form, {
                    deadline:       this.form.deadline === CURRENT_TIMESTAMP ? null : this.form.deadline,
                    soft_budget:    this.convertBudgetToString(this.form.soft_budget),
                    hard_budget:    this.convertBudgetToString(this.form.hard_budget),
                });

                /*todo remove*/
                this.$store.dispatch('groups/changeBoard', data);
                /**/

                this.$api.board.updateBoard(data, 'board_res=long&board_relations=none').then((res) => {
                    if (this.$route.name === 'board') {
                        this.$store.dispatch('groups/setDefaultSelectSortType');
                    }

                    this.initForm = {...this.form};
                    this.closeModal();

                    if (res.members.length) {
                        return this.$modal.show("choose-members-modal", {members: res.members, data: data });
                    }

                    if (res.members.length === 0) {
                        return this.$modal.show("confirm-modal", {
                            title: this.$t('board_move_to_another_group'),
                            body: this.$t('are_you_sure_you_want_to_move_this_board'),
                            confirmCallback: () => {
                                data.members = [];
                                this.$api.board.updateBoard(data, 'board_res=long', true).catch((err) => {
                                    this.defaultError(err.response);
                                })
                            }
                        });
                    }

                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleRemoveBoard() {
                this.$modal.show("confirm-modal", {
                    title: this.$t('delete_this_board'),
                    body: this.$t('are_you_sure_you_want_to_delete_this_board'),
                    confirmCallback: () => {
                        this.$api.board.removeBoard({ board_id: this.form.board_id, group_id: this.form.group_id }, 'board_res=long&board_relations=none').then(() => {
                            this.$api.task.getTasksDeadline();

                            this.closeModal();
                        }).catch((err) => {
                            this.defaultError(err.response);
                        })
                    },
                });
            },
        }
    }
</script>
<style lang="scss">
#board-setting-modal {
    overflow: hidden;
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
