<template>
    <tr class="board-item">
        <td class="board__column-setting">
            <div class="board-settings-icon" v-if="isGroup" >
                <i @click="openBoardSettings(board.id, group)"
                   class="oc icon-settings"
                   aria-hidden="true"
                >
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-settings">
                        </use>
                    </svg>
                </i>
            </div>
        </td>
        <td class="board__column-board-name">
            <span :class="{'board-link': isGroup}" @click="isGroup ? openTaskList(board) : null">
                {{ board.name }}
            </span>
        </td>
        <td class="board__column-budget-type">
            <i class="fa fa-repeat" v-if="board.budget_type_id === 3"></i>
        </td>
        <td class="board__column-bar" v-if="checkPermission('time-tracking')">
            <div class="management-bar">
                <div class="management-bar-text management-bar-text-time" :title="getBudgetTime + ' h'">
                    {{ getBudgetTime }} h
                </div>
                <management-bar :percent="getDifferenceBudget" :color="getColor" />
                <div class="management-bar-text management-bar-text-time text-right" :title="getTrackedTime + ' h'">
                    {{ getTrackedTime }} h
                </div>
            </div>
        </td>
        <td class="board__column-bar board__column-bar_open-task">
            <div class="management-bar">
                <div class="management-bar-text">
                    {{ getOpenTasks }}
                </div>
                <management-bar :percent="getDifferenceTasks" />
                <div class="management-bar-text text-right">
                    {{ getDoneTasks }}
                </div>
            </div>
        </td>
        <td class="board__column-bar-day">
            <management-bar-day :tasks="board.calendar_opened_tasks" :days="days" />
        </td>
        <td class="board__column-bar-day">
            <management-bar-day :tasks="board.calendar_closed_tasks" :days="days" />
        </td>
        <td class="board__column-deadline">
            {{ getDeadlineDays }}
        </td>
    </tr>
</template>

<script>
    import timeMixin            from '@mixins/time'
    import {mapGetters}         from "vuex"

    import ManagementBar        from '@views/partcials/Management/ManagementBar'
    import ManagementBarDay     from '@views/partcials/Management/ManagementBarDay'

    const getDifference = (count1, count2) => {

        if (!count1 && !count2) {
            return 0;
        }

        return ((+count1 / (+count2 + +count1)) * 100).toFixed(3)
    };

    const getDifferenceBudget = (count1,count2) => {
        if (!count1 && !count2) {
            return 0;
        }

        return ((+count2 / +count1) * 100).toFixed(3)
    };

    const getHours = (item, val) => Number((item[val] / 60).toFixed(2));

    const getTaskCount = (arr, openTask = true) => {
        return arr.reduce((accumulator, currentValue, index, array) => {
            if (openTask) {
                return accumulator + !+currentValue.done_by;
            }

            return accumulator + +currentValue.done_by;
        }, 0);
    };

    export default {
        props: {
            board:  {type: Object},
            days:   {type: Array},
            group:  {type: Object}
        },
        components: {
            'management-bar':       ManagementBar,
            'management-bar-day':   ManagementBarDay
        },
        computed: {
            ...mapGetters({
                budgetTypes: 'default/getBudgetTypes',
            }),
            getTrackedTime: function() {
                return getHours(this.board, 'trackedTime');
            },
            getBudgetTime: function() {

                if (this.board.hard_budget && this.board.hard_budget !== "0:00") {
                    return this.getTimeHours(this.board.hard_budget)
                }

                return getHours(this.board, 'budget');
            },
            getDifferenceBudget: function() {
                return  getDifferenceBudget(this.getBudgetTime, this.getTrackedTime);
            },
            getColor: function() {

                if (this.getBudgetTime && this.getBudgetTime < this.getTrackedTime) {
                    return '#e24747';
                }

                if (this.board.soft_budget && this.board.soft_budget !== "00:00") {
                    if (this.getTimeHours(this.board.soft_budget) < this.getTrackedTime) {
                        return '#ff7c3e';
                    }
                }

                return 'deepskyblue'
            },
            getDifferenceTasks: function(){
                return getDifference(this.getOpenTasks, this.getDoneTasks);
            },
            getOpenTasks: function(){
                return getTaskCount(this.board.tasks.filter(task => !task.draft));
            },
            getDoneTasks() {
                return getTaskCount(this.board.tasks.filter(task => !task.draft), false);
            },
            getDeadlineDays() {

                let utc = this.$moment.utc(this.board.deadline);

                if (!this.board.deadline || !utc.isValid()) {
                    return this.$t('not_selected')
                }

                return utc.diff(this.$moment.utc(new Date()).format('YYYY-MM-DD'), 'days') + ` ${this.$t('not_selected').toLowerCase()}`;

            },
            /*getMounthly () {
                if (this.board.budget_type_id === 2) {

                }
            },*/
            isGroup() {
                return this.handleManagementByGroupId(this.group.id);
            },
        },
        mixins:[
            timeMixin
        ],
        methods: {
            openBoardSettings(boardId, group, val = null) {
                this.$modal.show('board-setting-modal', {boardId: boardId})
            },
            openTaskList(board) {
                this.$router.push({name: 'board', params: {group_id: board.group_id, board_id: board.id}});
            },
        }
    }
</script>
