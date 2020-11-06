<template>
    <div class="manage-wrapper-item manage-wrapper-item_white row">
            
            <div class="control-label cursor-pointer" @click="showGroupSettingsModal(group)">
                <strong>{{group.name}}</strong>
            </div>
            <div class="manage-wrapper-buttons">
            <div class="control-label-center ">
                <button type="button" :title="$t('show_boards_list')" :disabled="isLoading" @click="showBoardListModal(group, false)" class="btn" >
                    <span>{{ countActiveBoard }} X</span>
                        <i class="icon-calendar">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-calendar">
                            </use>
                        </svg>
                        </i>
                </button>
            </div>

            <div class="control-label-center">
                <button type="button" :disabled="isLoading" @click="showBoardListModal(group, true)" class="btn" :title="$t('show_archived_boards')">
                    <span>{{ countArchivedBoard }} X</span>
                        <i class="icon-archive">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-archive">
                            </use>
                        </svg>
                        </i>
                </button>
            </div> 

            <div class="control-label-center">
                <button type="button" :disabled="isLoading" @click="showGroupSettingsModal(group)" class="btn" :title="$t('title_setting_group')">
                    <span>{{ group.members.length }} X</span>
                        <i class="icon-user">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-user">
                            </use>
                        </svg>
                        </i>
                </button>
            </div>

            <div class="control-label-center ">
                <button type="button" class="btn" :title="$t('title_setting_group')" :disabled="isLoading" @click="showGroupSettingsModal(group)">
                        <i class="icon-settings">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-settings">
                            </use>
                        </svg>
                        </i>
                </button>

                <button type="button" class="btn" :title="$t('archived_or_delete_group')" :disabled="isLoading" @click="handleArchivedGroup(group)" v-if="!isGroupArchive">
                    <i class="icon-trash">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-trash">
                        </use>
                    </svg>
                    </i>
                </button>

                <button type="button" class="btn" :title="$t('un_archived_group')" :disabled="isLoading" @click="handleUnarchivedGroup(group)" v-if="isGroupArchive">
                    <i class="fa fa-dropbox bordered" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "groups-list-item",
        props: {
            group: {
                type: Object,
                required: true,
            },
        },
        computed:{
            isGroupArchive () {
                return this.group.is_archive;
            },
            boards() {
                return this.group.boards || [];
            },
            countActiveBoard () {
                return this.boards.filter(board => !board.is_archive).length;
            },
            countArchivedBoard () {
                return this.boards.filter(board => board.is_archive).length;
            }
        },
        methods: {
            showBoardListModal(group, isArchived) {
                this.$modal.show('board-list-modal', {
                    groupId:        group.id,
                    isArchived:     isArchived,
                })
            },
            showGroupSettingsModal(group) {
                this.$modal.show('group-setting-modal', {groupId: group.id})
            },
            handleArchivedGroup(group) {
                if (this.checkPermission('delete-group', true)) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('delete_this_group'),
                        body: this.$t('are_you_sure_you_want_to_delete_this_group'),
                        confirmCallback: () => {
                            this.$api.group.removeGroup(group.id).then(() => {
                                this.$api.task.getTasksDeadline();
                            }).catch(err => {
                                this.$notify({type:'error', text: err.response.data.message});
                            });
                        },
                    });
                }
            },
            handleUnarchivedGroup(group) {
                this.$api.group.unarchivedGroup(group.id).then(() => {
                    this.$api.task.getTasksDeadline();
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
        }
    }
</script>
