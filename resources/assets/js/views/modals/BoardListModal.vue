<!--Optimized-->
<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        height="auto"
        :maxWidth="700"
        width="100%"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >

        <modal-wrapper :name="$options.name">
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
                {{ isArchived ? $t('alive_archived_boards') : $t('alive_boards') }}
            </template>

            <template slot="body">
                <div class="row">
                    <div class="col-xs-4 col-sm-5"></div>
                    <div class="col-xs-3 col-sm-3 text-align-center">
                        <b>
                            {{ $t('tasks') }}
                        </b>
                    </div>
                    <div class="col-xs-3 col-sm-3 text-align-center" v-if="checkPermission('time-tracking')">
                        <b>
                            {{ $t('time') }}
                        </b>
                    </div>
                    <div class="col-xs-1 col-sm-1"></div>
                </div>

                <div class="row">
                    <div class="col-xs-4 col-sm-5"></div>
                    <div class="col-xs-3 col-sm-3 text-align-center">
                        <b>
                            {{ $t('open_closed_tasks') }}
                        </b>
                    </div>
                    <div class="col-xs-3 col-sm-3 text-align-center" v-if="checkPermission('time-tracking')">
                        <b>
                            {{ $t('logged_billed_hours') }}
                        </b>
                    </div>
                    <div class="col-xs-1 col-sm-1"></div>
                </div>

                <div class="board-list-container">
                    <div class="row board-list-row" v-for="board in boards" :key="board.id">
                        <div class="col-xs-4 col-sm-5">
                            <span>{{ board.name }}</span>
                        </div>

                        <div class="col-xs-3 col-sm-3">
                            <!--<span>{{ board.tasks | count }} / {{ board.tasks | count }}</span>-->
                            <span>{{ getBoardTaskCount(board.tasks) }}</span>
                        </div>

                        <div class="col-xs-3 col-sm-3" v-if="checkPermission('time-tracking')">
                            <span>
                                <span class="nowrap">{{ board.trackedTime | minutesToStringFormat }}</span>
                                /
                                {{ board.billedTime | minutesToStringFormat }}</span>
                        </div>

                        <div class="col-xs-1 col-sm-1">
                            <button type="button" class="btn" :title="$t('title_setting_board')" @click="showBoardSettingsModal(board)">
                                <i class="fa fa-cog bordered" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import find                 from '@helpers/findInGroups'
    import ModalWrapper         from "@assets/js/views/layouts/ModalWrapper";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "board-list-modal",
        data() {
            return {
                groupId: null,
                isArchived: false,
            }
        },
        computed:{
            ...mapGetters({
                groups: 'groups/getStateGroups',
            }),
            group() {
                if (this.groupId) {
                    return find.searchGroupById(this.groups, this.groupId);
                }

                return null;
            },
            boards() {
                if (this.group) {
                    return this.group.boards
                        .filter(board => board.is_archive === this.isArchived)
                        .sort((a, b) => sorter(a.name, b.name));
                }

                return [];
            }
        },
        components: {
            ModalWrapper,
            ThemeButtonClose
        },
        methods: {
            beforeOpen(event) {
                this.groupId    = event.params.groupId;
                this.isArchived = event.params.isArchived;
            },
            beforeClose() {
                this.resetComponentData()
            },
            getBoardTaskCount (tasks) {

                let countDoneTasks = 0, countOpenTasks = 0;

                tasks.forEach(task => {
                    if (!task.draft) {
                        if (task.done_by) {
                            countDoneTasks += 1;
                        } else {
                            countOpenTasks += 1;
                        }
                    }
                });

                return  countOpenTasks + ' / ' + countDoneTasks;
            },
            showBoardSettingsModal(board) {
                this.$modal.show('board-setting-modal', {boardId: board.id})
            },
              closeModal() {
                this.$modal.hide("board-list-modal")
            },
        }
    }
</script>
<style lang="scss">
#board-list-modal {
    overflow: hidden
    ;
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
