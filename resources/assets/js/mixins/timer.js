// Optimized
export default {
    data () {
        return {
            showTimerComment:   false,
            showTimerLogs:      false,
            showTimers:         false,
        }
    },
    methods: {
        hideTimers() {
            this.showTimers = false;
        },
        hideTimerLogs($event) {

            let modalConfirm;

            modalConfirm = document.querySelector('.v--modal');

            if (modalConfirm) {
                return modalConfirm.contains($event.target);
            }

            this.showTimerLogs = false;
        },
        hideTimerComment(event) {
            if (event.target.id !== "timer-comment-dropdown") {
                this.showTimerComment = false;
            }
        },
        createStartTimer(form = {}) {
            this.$api.timer.createStartTimer(form).catch(err => {
                this.$notify({type:'' +
                        '', text: err.response.data.message});
            });
        },
        startTimer(timerId) {
            this.$api.timer.startTimer(timerId).catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        pauseTimer(timerId) {
            this.$api.timer.pauseTimer(timerId).catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        continueTimer(timerId) {
            this.$api.timer.continueTimer(timerId).catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        stopTimer(timerId) {
            this.$api.timer.stopTimer(timerId).catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        removeTimer(timerId) {
            this.$api.timer.removeTimer(timerId).catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        showChangeCurrentTimerTaskModal() {
            this.$modal.show('choose-modal', {type: "task", permission: "time-tracking", callback: (task) => {
                this.$api.timer.updateTimer({
                    ...this.currentTimer,
                    timerId: this.currentTimer.id,
                    taskId: task.id
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            }});
        },
    }
};
