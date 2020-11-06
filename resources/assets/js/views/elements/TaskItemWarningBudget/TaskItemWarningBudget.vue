<template>
    <v-popover class="task-item-warning-budget" style="position: relative;" >
        <button
            v-if="isOvertimeSoftBudget || isOvertimeHardBudget"
            type="button"
            class="btn budget-warning tooltip-target"
        >
            <i class="icon-dollar">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-dollar">
                    </use>
                </svg>
            </i>
        </button>
        <template slot="popover">
            <div
                class="task-time-status-drop-down"
            >
                <ul class="task-time-status-drop-down__list">
                    <li class="task-time-status-drop-down__item">
                        <div class="task-time-status-drop-down__content">
                            {{ $t("used_time") }}: {{ getCurrentTaskTrackedTime | secondsToStringFormat }}
                        </div>
                    </li>
                    <li class="task-time-status-drop-down__item">
                        <div class="task-time-status-drop-down__content">
                            {{ $t("soft_budget") }}: {{ task.soft_budget }}
                        </div>
                    </li>
                    <li class="task-time-status-drop-down__item">
                        <div class="task-time-status-drop-down__content">
                            {{ $t("hard_budget") }}: {{ task.hard_budget }}
                        </div>
                    </li>
                </ul>
            </div>
        </template>
    </v-popover>
</template>

<script>
    import helpersTime from '@mixins/time'
    import clickOutside from 'v-click-outside'

    export default {
        name: 'TaskItemWarningBudget',
        mixins: [
            helpersTime
        ],
        props: {
            task: {
                type: Object
            },
        },
        computed: {
            getCurrentTaskSoftBudget () {
                return this.getBudgetSeconds(this.task.soft_budget);
            },
            getCurrentTaskHardBudget () {
                return this.getBudgetSeconds(this.task.hard_budget);
            },
            isOvertimeSoftBudget() {
                return this.isOvertimeBudget(this.getCurrentTaskSoftBudget, this.getCurrentTaskTrackedTime)
            },
            isOvertimeHardBudget() {
                return this.isOvertimeBudget(this.getCurrentTaskHardBudget, this.getCurrentTaskTrackedTime)
            },
            getCurrentTaskTrackedTime() {
                return this.getTrackedTimeSecondsByTask(this.task);
            },
        },
    }
</script>

<style lang="scss">
    .budget-warning {
        display: flex;
        position: relative;
        top: 1px;
        &:hover {
            .icon-dollar {
                .icon {
                    fill: #376aa7;
                }
            }
        }
        .icon-dollar {
            display: flex;
            width: 18px;
            height: 18px;
            .icon {
                fill:#909090;
            }
        }
    }
</style>
