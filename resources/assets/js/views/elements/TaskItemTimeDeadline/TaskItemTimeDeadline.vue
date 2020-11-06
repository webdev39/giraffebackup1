<template>
    <!--todo need optimization-->
    <v-popover class="task-item-time-deadline" stylegi="position: relative;" v-if="handleReachDeadline">
        <button type="button" class="btn tooltip-target" @click="toggleShowModalTimeDeadline" :title="$t('status_reach_deadline')">
            <span class="status-reach-deadline">
                <i class="icon-reach-deadline">
                    <svg class="icon">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-reach-deadline">
                        </use>
                    </svg>
                </i>
            </span>
        </button>
        <template slot="popover">
            <div class="task-time-deadline-drop-down">
                {{ $t("deadline_in") }} {{ timeDeadline }}
                <div v-if="deadlineDays" class="color-danger">
                    {{deadlineDays}} {{ $t("days_to_deadline") }}
                </div>
            </div>
        </template>
    </v-popover>
</template>

<script>
    import moment from 'moment'
    import clickOutside from 'v-click-outside'

    export default {
        name: 'TaskItemTimeDeadline',
        directives: {
            'clickOutside': clickOutside.directive
        },
        data() {
            return {
                showModal: false,
                deadlineDays: null,
            }
        },
        props: {
            task: {
                type: Object
            },
        },
        computed: {
            timeDeadline() {
                return this.$moment.utc(this.task.deadline, 'YYYY-MM-DD HH:mm:ss').local().format('YYYY-MM-DD')
            },
            handleReachDeadline() {
                if (this.task.deadline && moment(this.task.deadline).isValid()) {
                    let afterDeadline = moment().isAfter(this.task.deadline);

                    if (afterDeadline) {
                        let deadline = moment(this.task.deadline, "YYYY-MM-DD HH:mm:ss");

                        this.deadlineDays = moment.utc(deadline).diff(moment.utc(new Date()).format('YYYY-MM-DD'), 'days');

                        return true;
                    }
                }

                return false;
            },
        },
        methods: {
            clickOutside(event) {
                if (event && event.target.closest('.task-item-time-deadline')) {
                    return;
                }
                this.hideModalTimeDeadline();
            },
            hideModalTimeDeadline() {
                if (!this.showModal) {
                    return;
                }
                this.showModal = false;
                this.$emit('onModalHide', 'TaskItemTimeDeadline');
            },
            showModalTimeDeadline() {
                if (this.showModal) {
                    return;
                }
                this.showModal = true;
                this.$emit('onModalShow', 'TaskItemTimeDeadline');
            },
            toggleShowModalTimeDeadline() {
                if (this.showModal) {
                    return this.hideModalTimeDeadline();
                }

                return this.showModalTimeDeadline();
            },
        },
    }
</script>

<style lang="scss">
    .icon-reach-deadline{
        width: 16px;
        height: 16px;
        display: flex;
        .icon{
            fill: #909090;
        }
        &:hover {
            .icon {
                fill:#376aa7;
            }
        }
    }
</style>
