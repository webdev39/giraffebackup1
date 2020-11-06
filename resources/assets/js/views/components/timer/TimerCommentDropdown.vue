<template>
    <textarea-autosize :id="$options.name" v-model="comment" ref="textarea" :rows="rows" cols="40"
                       :min-height="50"
                       :max-height="400"
                       :disabled="isLoading"
                       @keydown.native="handleSaveComment"
                       v-if="show"/>
</template>

<script>
    export default {
        name: "timer-comment-dropdown",
        props:{
            timerId: {
                required: true
            },
            timerComment: {
                required: true
            },
            rows: {
                type: Number,
                default: 3,
            },
            show: {
                default: false
            },
            task_id: {
                type: Number
            },
        },
        data() {
            return {
                form: {
                    comment: null,
                }
            }
        },
        computed: {
            comment: {
                get: function () {
                    return this.timerComment;
                },
                set: function (value) {
                    this.form.comment = value;
                }
            }
        },
        methods: {
            handleSaveComment(event) {
                if (event.keyCode === 13) {
                    this.$api.timer.updateTimer({
                        timerId:    this.timerId,
                        comment:    this.form.comment,
                        taskId:    this.task_id
                    }).then(() => {
                        this.$emit('update:show', false)
                    }).catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                }
            }
        }
    }
</script>

<style lang="scss">
    #timer-comment-dropdown {
        position: absolute;
        resize: both;
        left: -43px;
        border-radius: 5px;
        z-index: 1001;
        font-size: 14px;
        padding: 3px 8px;
    }
    @media (max-width: 1200px) {
        #timer-comment-dropdown {
            left: 15px;
        }
    }
    @media (max-width: 480px) {
        #timer-comment-dropdown {
            width: 90%;
        }
    }
</style>
