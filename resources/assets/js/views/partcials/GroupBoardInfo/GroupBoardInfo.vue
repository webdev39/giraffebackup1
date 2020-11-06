<template>
    <div class="group-board-info-wrapper">
        <div class="group-board-info">
            <div class="group-board-info__item">
                <i class="icon-groups">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-groups"></use>
                    </svg>
                </i>

                <a href="#" class="boardGroupLinks" @click.prevent="showGroupSettings(_getCurrentGroupId)">
                    {{ _getCurrentGroupName }}
                </a>
            </div>

            <div class="group-board-info__item" v-if="_getCurrentBoardId">
                <i class="icon-boards">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-boards">
                        </use>
                    </svg>
                </i>
                <a href="#" class="boardGroupLinks" v-if="handlePermissionByBoardId('update-board', _getCurrentBoardId)"  @click.prevent="showBoardSettings(_getCurrentBoardId)">
                    {{ _getCurrentBoardName }}
                </a>
                <span v-else>
                    {{ _getCurrentBoardName }}
                </span>
            </div>

            <div class="group-board-info__item" v-if="getBoardInvisiblePriorities.length">
                <i class="fa fa-eye-slash icon-boards" aria-hidden="true"></i>
                <span class="boardGroupLinks">{{ $t('priorities_hidden') }}</span>:
                <span class="priority-tag" v-for="priority in getBoardInvisiblePriorities" :key="priority.id"
                      :style="{backgroundColor: priority.color}">
                    {{ priority.name }}
                </span>
            </div>

            <div class="group-board-info__item" v-if="_getCurrentBoard">
                <span v-if="isSoftBudget" :title="$t('soft_budget_more')">
                    <i class="fa fa-eur" aria-hidden="true"></i>
                </span>
                <span v-if="isHardBudget" :title="$t('hard_budget_more')">
                    <i class="fa fa-eur" aria-hidden="true"></i>
                    <i class="fa fa-eur" aria-hidden="true"></i>
                </span>
            </div>
            <div class="group-board-info__item">
                <subscribers-board class="subscribers-board" v-if="isSubscribersBoard" :group_id="_getCurrentGroupId" />
            </div>
            <div class="group-board-info__item group-board-info__item_info-setting" v-if="showInfoSetting">
                <info-settings>
                    <slot name="info"></slot>
                </info-settings>
            </div>
            <slot name="right-panel"></slot>
        </div>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import find                 from '@helpers/findInGroups'
    import timeMixin            from '@mixins/time'
    import groupsMixin          from '@mixins/groups'

    import subscribersBoard     from '@views/components/subscribers/subscribers-board'
    import infoSettings         from '@views/components/infoSettings/infoSettings'
 
    export default {
        components: {
            subscribersBoard,
            infoSettings
        },
        props: {
            isSubscribersBoard: {
                type: Boolean,
                default: true
            },
            showInfoSetting: {
                type: Boolean,
                default: true
            }

        },
        computed: {
            ...mapGetters({
                viewTypes:      'default/getViewTypes',
                priorities:     'priorities/getPriorities',
            }),

            isSoftBudget() {
                if (this._getCurrentBoard) {
                    return this.isOvertimeBudget(this.getBudgetMinutes(this._getCurrentBoard.soft_budget), this.getBoardTrackedTime);
                }
            },
            isHardBudget() {
                if (this._getCurrentBoard) {
                    return this.isOvertimeBudget(this.getBudgetMinutes(this._getCurrentBoard.hard_budget), this.getBoardTrackedTime);
                }
            },

            /*Board*/
            getCurrentBoardHideTask() {
                if (this._getCurrentBoard) {
                    return this._getCurrentBoard.hide_done_tasks
                }
            },
            getBoardTrackedTime() {
                return this._getCurrentBoard.trackedTime
            },
            getBoardInvisiblePriorities() {
                return this.priorities.filter((item) => {
                    return item.board_id === this._getCurrentBoardId && item.is_invisible;
                });
            },
        },
        mixins:[
            timeMixin,
            groupsMixin
        ],
        methods: {
            isOvertime (budget) {
                if (this._getCurrentBoard[budget] !== '0:00') {
                    return this.isOvertimeBudget(this.getBudgetMinutes(this._getCurrentBoard[budget]), this.getBoardTrackedTime);
                } else {

                    let sum = 0;
                    let that = this;

                    this._getCurrentBoard.tasks.map(function(item) {

                        if (item[budget] && item[budget] !== "0:00") {
                            sum += that.getBudgetMinutes(item[budget])
                        } else {
                            sum += Number((item['budget'] / 60).toFixed(2));
                        }
                    });

                    return this.isOvertimeBudget(sum, this.getBoardTrackedTime);
                }
            },
            showGroupSettings(){
                if (!this.handlePermissionByGroupId('read-group', this._getCurrentGroupId)) {
                    return this.sendNotifyPermissionInfo('read-group');
                }

                this.$modal.show('group-setting-modal', {groupId: this._getCurrentGroupId})
            },
            showBoardSettings() {
                this.$modal.show('board-setting-modal', {
                    boardId: this._getCurrentBoardId
                })
            },
        }
    }
</script>

<style lang="scss">
    .priority-tag {
        border-radius: 4px;
        padding: 0px 4px;
        font-size: 12px;
        margin: 2px 4px;
        color: #fff;
    }
    .group-board-info__piority-setting{
        margin-left: 10px;
    }
    .subscribers-board{
        margin-left: 5px;
    }

</style>
