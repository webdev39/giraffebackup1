<template>
    <div style="display: flex;">
        <button
            type="button"
            class="btn btn-start timer-button-list control-btns-hide"
            :title="$t('title_create_timer')"
            @click="createStartTimer"
            v-if="!isTrackingTask && handlePermissionByGroupId('time-tracking', task.group_id)"
        >
            <i class="icon-time">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-time">
                    </use>
                </svg>
            </i>
        </button>
        <button
            type="button"
            class="btn btn-start timer-button-list control-btns-hide"
            :title="$t('stop')"
            @click="stopTimer"
            v-if="isTrackingTask"
        >
            <i class="icon-stop">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-stop">
                    </use>
                </svg>
            </i>
        </button>
        <button
            type="button"
            class="btn btn-start timer-button-list control-btns-hide"
            :title="$t('pause')"
            @click="pauseTimer"
            v-if="isTrackingTask"
        >
            <i class="icon-pause">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-pause">
                    </use>
                </svg>
            </i>
        </button>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    import permissionsMixin from '@mixins/permissions'

    export default {
        name: 'TaskItemTimer',
        mixins: [
            permissionsMixin,
        ],
        props: {
            task: {
                type: Object
            },
        },
        computed: {
            ...mapGetters({
                getCurrentStartTimer: 'timers/getCurrentStartTimer',
            }),
            isTrackingTask() {
                if (this.getCurrentStartTimer) {
                    return this.getCurrentStartTimer.task_id === this.task.id;
                }

                return null;
            },
        },
        methods: {
            createStartTimer() {
                this.$api.timer.createStartTimer({taskId: this.task.id}).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            stopTimer() {
                this.$api.timer.stopTimer(this.getCurrentStartTimer.id).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            pauseTimer() {
                this.$api.timer.pauseTimer(this.getCurrentStartTimer.id).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
        }
    }
</script>