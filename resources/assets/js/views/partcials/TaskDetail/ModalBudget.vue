<template>
    <div class="details-modal budget-modal">
                            <button type="button" class="btn btn-lg btn_details-modal_close">
                        <i class="icon-close" @click="$emit('hide')">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                        </i>
                    </button>
                <div class="row budget-modal__row">
            <label class="col-sm-5 col-form-label">
                {{ $t("hard_budget") }}<small>{{ $t("maximal_time_in_hours") }}</small>
            </label>

            <div class="col-sm-7">
                <div class="input-group-block">
                    <div class="input-group-holder">
                        <span class="input-group-addon">
                           <i class="icon-clock specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-clock">
                                    </use>
                                </svg>
                            </i>
                        </span>

                        <div class="split-budget-holder-input">
                            <input type="number" class="form-control split-budget-input" v-model="hard_budget.hours" min="0" max="9999" @change="updateHardBudget">
                            <span class="span-hard-budget-h">H</span>
                        </div>

                        <div class="split-budget-holder-input">
                            <input type="number" class="form-control split-budget-input" v-model="hard_budget.minutes" min="0" max="99999" @change="updateHardBudget">
                            <span class="span-hard-budget-m">M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row budget-modal__row">
            <label class="col-sm-5 col-form-label">
                {{ $t("soft_budget") }} <small>{{ $t("info_after_x_hours") }}</small>
            </label>

            <div class="col-sm-7">
                <div class="input-group-block">
                    <div class="input-group-holder">
                        <span class="input-group-addon">
                            <i class="icon-clock specialsize">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-clock">
                                    </use>
                                </svg>
                            </i>
                        </span>

                        <div class="split-budget-holder-input">
                            <input type="number" class="form-control split-budget-input" v-model="soft_budget.hours" min="0" max="9999" @change="updateSoftBudget">
                            <span class="span-soft-budget-h">H</span>
                        </div>

                        <div class="split-budget-holder-input">
                            <input type="number" class="form-control split-budget-input" v-model="soft_budget.minutes" min="0" max="99999" @change="updateSoftBudget">
                            <span class="span-soft-budget-m">M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-5 col-form-label">
                {{ $t("budget_type") }}<small> {{ $t("one_time_or_monthly") }}</small>
            </label>

            <div class="col-sm-7">
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="icon-clock specialsize">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-clock">
                        </use>
                    </svg>
                    </i>
                    </span>
                    <select class="form-control" v-model="typeBudgetId">
                        <option v-for="type in getBudgetTypes" :value="type.id">
                            {{ $t(`budget_types.${type.id}`) }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters}     from 'vuex'

    import permissionsMixin from '@mixins/permissions'
    import find             from '@helpers/findInGroups'
    import moment from "moment";

    export default {
        name: "modal-budget",
        props: {
            taskId: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                typeBudgetId: 1,
                soft_budget: {
                    hours:   0,
                    minutes: 0,
                },
                hard_budget: {
                    hours:   0,
                    minutes: 0,
                },
                old_hard_budget: '',
                canUpdateSoftBudget: false,
                canUpdateHardBudget: false,
            }
        },
        computed: {
            ...mapGetters({
                getGroups:      'groups/getStateGroups',
                getBudgetTypes: 'default/getBudgetTypes',
                getUserId:      'user/getUserId',
            }),
            getTask() {
                return find.searchTaskById(this.getGroups, this.taskId);
            },
            total_soft_budget() {
                return Number(this.soft_budget.hours) * 60 + Number(this.soft_budget.minutes);
            },
            total_hard_budget() {
                return  Number(this.hard_budget.hours) * 60 + Number(this.hard_budget.minutes);
            },
        },
        watch: {
            soft_budget: {
                handler() {
                    this.soft_budget.hours    = Number(this.soft_budget.hours) + Math.floor(Number(this.soft_budget.minutes) / 60);
                    this.soft_budget.minutes  = Number(this.soft_budget.minutes) % 60;
                },
                deep: true
            },
            hard_budget: {
                handler() {
                    this.hard_budget.hours    = Number(this.hard_budget.hours) + Math.floor(Number(this.hard_budget.minutes) / 60);
                    this.hard_budget.minutes  = Number(this.hard_budget.minutes) % 60;
                },
                deep: true
            },
        },
        mixins: [
            permissionsMixin
        ],
        beforeDestroy () {
            this.getUpdateBudget();
        },
        mounted() {
            if (this.getTask.soft_budget) {
                this.soft_budget.hours      = Number(this.getTask.soft_budget.split(":")[0]);
                this.soft_budget.minutes    = Number(this.getTask.soft_budget.split(":")[1]);
            }

            if (this.getTask.hard_budget) {
                this.hard_budget.hours          = Number(this.getTask.hard_budget.split(":")[0]);
                this.hard_budget.minutes        = Number(this.getTask.hard_budget.split(":")[1]);
                this.old_hard_budget_minutes    = this.hard_budget.hours * 60 + this.hard_budget.minutes;
            }

            if (this.getTask.budget_type_id) {
                this.typeBudgetId = this.getTask.budget_type_id;
            }
        },
        methods: {
            updateSoftBudget() {
                if (this.total_soft_budget > this.total_hard_budget) {
                    if (this.canUpdateSoftBudget) {
                        return this.updateBudget('soft');
                    }

                    this.$modal.show("confirm-modal", {
                        title: this.$t('update_budget'),
                        body: this.$t('update_budget_content'),
                        confirmCallback: () => {
                            this.updateBudget('soft');
                        },
                        cancelCallback: () => {
                            this.clearUpdateBudget('soft')
                        }
                    });
                }
            },
            updateHardBudget() {
                if (this.total_soft_budget > this.total_hard_budget) {
                    if (this.canUpdateHardBudget === true) {
                        return this.updateBudget('hard');
                    }

                    this.$modal.show("confirm-modal", {
                        title: this.$t('update_budget'),
                        body: 'Soft budget is less then Hard budget. Confirm you want update Soft budget too',
                        confirmCallback: () => {
                            this.updateBudget('hard');
                        },
                        cancelCallback: () => {
                            this.clearUpdateBudget('hard')
                        }
                    });
                }
            },
            updateBudget(budget) {
                if (budget === 'soft') {
                    this.canUpdateSoftBudget = true;
                    this.hard_budget = {...this.soft_budget};
                } else if (budget === 'hard') {
                    this.canUpdateHardBudget = true;
                    this.soft_budget = {...this.hard_budget};
                }
            },
            clearUpdateBudget(budget) {
                if (budget === 'soft') {
                    this.canUpdateSoftBudget = false;
                    this.soft_budget = {...this.hard_budget};
                } else if (budget === 'hard') {
                    this.canUpdateHardBudget = false;
                    this.hard_budget = {...this.soft_budget};
                }
            },
            getUpdateBudget() {
                if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('update-task', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('update-task');
                }

                let soft_budget_hours   = (this.soft_budget.hours   > 9 ? this.soft_budget.hours :   '0' + this.soft_budget.hours);
                let soft_budget_minutes = (this.soft_budget.minutes > 9 ? this.soft_budget.minutes : '0' + this.soft_budget.minutes);

                let hard_budget_hours   = (this.hard_budget.hours   > 9 ? this.hard_budget.hours :   '0' + this.hard_budget.hours);
                let hard_budget_minutes = (this.hard_budget.minutes > 9 ? this.hard_budget.minutes : '0' + this.hard_budget.minutes);

                if (this.getTask.soft_budget === `${soft_budget_hours}:${soft_budget_minutes}` &&
                    this.getTask.hard_budget === `${hard_budget_hours}:${hard_budget_minutes}` &&
                    this.getTask.budget_type_id === this.typeBudgetId) {
                    return
                }
                let plannedDeadline = this.getTask.planned_deadline;
                let deadline = moment(plannedDeadline).add((soft_budget_hours*60)+soft_budget_minutes, 'minute');
                deadline = this.toUTCTime(deadline, 'YYYY-MM-DD HH:mm:ss');
                let data = Object.assign({}, this.getTask, {
                    deadline:       deadline,
                    soft_budget:    `${soft_budget_hours}:${soft_budget_minutes}`,
                    hard_budget:    `${hard_budget_hours}:${hard_budget_minutes}`,
                    task_id:        this.getTask.id,
                    budget_type_id: this.typeBudgetId
                });

                let diffHardBudget = this.hard_budget.hours * 60 + this.hard_budget.minutes - this.old_hard_budget_minutes;

                this.$api.task.updateTask(data).then(data => {

                    data.task.budget = Math.abs(diffHardBudget);

                    if (diffHardBudget > 0) {
                        this.$store.dispatch('groups/incrementBudgedBoard', data.task);
                    } else {
                        this.$store.dispatch('groups/decrementBudgedBoard', data.task);
                    }
                });
                this.resetComponentData();
            }
        }
    }
</script>
<style lang="scss">
    .input-group {
        .input-group-addon {
            .icon-clock {
                display: block;
                    .icon {
                        width: 16px;
                        height: 16px;
                        fill:#fff;
                    }
            }
        }
    }
</style>
