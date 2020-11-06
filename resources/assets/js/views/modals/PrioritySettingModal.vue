<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="90%" :maxWidth="700" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1001" >
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
                {{ $t('priority_setting') }}
            </template>

            <template slot="body">
                <div class="form-group">
                    <ul class="priorities-list">
                        <draggable v-model="priorities" :move="checkMove" :disabled="!isUpdate">
                            <li v-for="(priority, index) in priorities" :key='index' class="row">
                                <div class="col-xs-2 col-sm-3 btn-control text-right">
                                    <button class="btn" v-if="isUpdate" @click.stop="handleUpdateInvisiblePriority(priority)" :disabled="isLoading">
                                            <i  :class="[priority.is_invisible ? 'icon-eye-slash' : 'icon-eye']">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                :xlink:href="[priority.is_invisible ? '#icon-eye-slash' : '#icon-eye']">
                                                </use>
                                            </svg>
                                            </i>
                                    </button>
                                </div>

                                <div class="col-xs-5 col-sm-6 priority-item">
                                    <template v-if="priority.id !== form.id">
                                        <input type="text" class="form-control priority-item-name" v-model="priority.name" disabled>
                                        <label class="priority-item-priority" :style="{ backgroundColor: priority.color.hex || priority.color }"></label>
                                    </template>

                                    <template v-else>
                                        <input type="text" class="form-control priority-item-name" v-model="form.name">
                                        <label class="priority-item-priority" :style="{ backgroundColor: form.color.hex || form.color }"></label>

                                        <chrome v-model="form.color"/>
                                    </template>
                                </div>

                                <div class="col-xs-5 col-sm-3 btn-control" v-if="isUpdate">
                                    <template v-if="priority.id !== form.id">
                                        <button class="btn" @click.stop="setEditCustomPriority(priority)">
                                        <i class="icon-pencil">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xlink:href="#icon-pencil">
                                            </use>
                                        </svg>
                                        </i>
                                        </button>

                                        <button class="btn" @click.stop="handleRemovePriority(priority.id)" v-if="!priority.is_primary" :disabled="isLoading">
                                            <i class="icon-trash">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                xlink:href="#icon-trash">
                                                </use>
                                            </svg>
                                            </i>
                                        </button>
                                    </template>

                                    <template v-else>
                                        <button class="btn" @click.stop="handleUpdatePriority" v-if="priority.id === form.id" :disabled="isLoading">
                                            <i class="icon-check">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                xlink:href="#icon-check">
                                                </use>
                                            </svg>
                                            </i>
                                        </button>

                                        <button class="btn" @click.stop="clearEditCustomPriority">
                                            <i class="icon-close">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                xlink:href="#icon-close">
                                                </use>
                                            </svg>
                                            </i>
                                        </button>
                                    </template>
                                </div>
                            </li>
                        </draggable>
                    </ul>
                </div>
            </template>

            <template slot="footer">
                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="button" class="btn" v-if="isUpdate" @click.native="handleCreatePriority" :disabled="isLoading">
                    {{ $t('add_custom_priority') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import Draggable            from 'vuedraggable'
    import { mapGetters }       from "vuex";
    import { Chrome }           from "vue-color";

    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "priority-setting-modal",
        data() {
            return {
                boardId:    null,
                form: {
                    id:     null,
                    name:   null,
                    color:  null,
                },
            }
        },
        computed: {
            ...mapGetters({
                getPriorities:  'priorities/getPriorities',
                getUserId:      'user/getUserId',
                boards:         'groups/getBoards',
            }),
            isUpdate () {
                return this.isOwnerBoard || this.isTenant || this.handlePermissionByBoardId('update-board', this.boardId)
            },
            getBoard() {
                return this.boards.find(item => item.id === this.boardId);
            },
            isOwnerBoard() {
                if (this.getBoard) {
                    return this.getBoard.creator_id === this.getUserId
                }
            },
            priorities: {
                get() {
                    return this.getPriorities
                        .filter(item => item.board_id === this.boardId)
                        .sort((a,b) => {
                            if (!a.sort_order) {
                                return a.id - b.id;
                            }

                            return sorter(a.sort_order, b.sort_order);
                        });
                },
                set(value) {
                    this.handleSortPriority(value);
                }
            },
            formData() {
                return  {
                    board_id:       this.boardId,
                    priority_id:    this.form.id,
                    name:           this.form.name,
                    color:          this.form.color.hex || this.form.color
                };
            }
        },
        components: {
            ModalWrapper,
            Chrome,
            Draggable,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.boardId) {
                    this.boardId = event.params.boardId;
                }

                if (event.params.priority) {
                    this.form = event.params.priority;
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            setEditCustomPriority(priority) {
                this.form = {...priority};
            },
            clearEditCustomPriority() {
                this.form = this.$options.data().form;
            },
            closeModal() {
                this.$modal.hide("priority-setting-modal")
            },
            checkFormData() {
                if (!this.formData.name) {
                    this.$notify({type:'error', text: this.$t('enter_priority_name')});
                    return false;
                }

                if (!this.formData.color) {
                    this.$notify({type:'error', text: this.$t('pick_priority')});
                    return false;
                }

                return true;
            },
            handleCreatePriority() {
                if (!this.handlePermissionByBoardId('update-board', this.boardId)) {
                    return this.sendNotifyPermissionInfo('update-board');
                }

                Object.assign(this.form, {
                    name:    this.getNameByIndex(this.priorities.length),
                    color:   this.getRandomColor()
                });

                if (this.checkFormData()) {
                    this.$api.priorities.createCustomPriority(this.formData).then(res => {

                        this.form = {...res.priority};

                    }).catch((err) => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                }
            },
            handleUpdatePriority() {
                if (!this.handlePermissionByBoardId('update-board', this.boardId)) {
                    return this.sendNotifyPermissionInfo('update-board');
                }

                if (this.checkFormData()) {
                    this.$api.priorities.updateCustomPriority(this.formData).then(() => {
                        this.clearEditCustomPriority();
                    }).catch((err) => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                }
            },
            handleUpdateInvisiblePriority(priority) {
                const formData = {
                    priority_id: priority.id,
                    board_id: priority.board_id,
                    is_invisible: !priority.is_invisible
                };

                this.$api.priorities.updateCustomPriority(formData).then(() => {
                    this.clearEditCustomPriority();
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handleRemovePriority(priorityId) {
                if (!this.handlePermissionByBoardId('update-board', this.boardId)) {
                    return this.sendNotifyPermissionInfo('update-board');
                }

                this.$api.priorities.deleteCustomPriority(priorityId).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handleSortPriority(priorities) {
                const data = priorities.map(item => item.id);

                this.$store.dispatch('priorities/updateSortOrderPriorities', data);

                this.$api.priorities.updateSortOrderCustomPriority(data);
            },
            checkMove(event) {
                return this.form.id !== event.draggedContext.element.id;
            },
            getRandomColor() {
                let priority = '#';
                let letters = '0123456789ABCDEF';

                for (let i = 0; i < 6; i++) {
                    priority += letters[Math.floor(Math.random() * 16)];
                }

                return priority;
            },
            getNameByIndex(index) {
                return `New priority #${index}`;
            },
        }
    }
</script>

<style lang="scss">
    #priority-setting-modal {
        overflow: hidden;
        .priorities-list {
            list-style-type: none;
            margin: 0;
            padding: 0;

            .priority-item {
                padding: 0;
                margin-bottom: 5px;

                .priority-item-priority {
                    width: 46px;
                    height: 36px;
                    border-radius: 4px 0 0 4px;
                    border: 1px solid #cccccc;
                    position: absolute;
                    left: 0;
                    top: 0;
                }

                .priority-item-name {
                    border-radius: 0 4px 4px 0;
                    text-align: center;
                    width: calc(100% - 45px);
                    margin-left:45px;
                }
            }

            .btn-control {
                .btn {
                    padding:8px 12px;
                    background:#f1f1f1;
                    border:1px solid #bfbebe;
                    i {
                        display:block;
                        font-size:0;
                        .icon {
                            width:16px;
                            height:18px;
                        }
                    }

                    &:hover {
                        background:transparent;
                        i .icon {
                            fill:#5078f2;
                        }
                        .icon-trash .icon, .icon-close .icon {
                            fill:#e04545;
                        }
                    }
                }
            }
        }


        .vc-chrome {
            margin-top: 10px;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            box-sizing: initial;
            width:auto;
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
    }
</style>
