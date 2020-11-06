<template>
    <ul class="comment-list">
        <comment-item
            v-for="(comment, index) in comments"
            :key='index'
            :comment="comment"
            :taskId="taskId"
            :showReply="showReply"
            :replyComment="replyComment"
            :action_id="action_id"
            :inTask="inTask"
            :permissions="permissions"
            :index="index"
            @remove="remove"
            @sticky="stickyComment"
        />
    </ul>
</template>

<script>
    import { mapGetters }       from 'vuex'
    import CommentItem          from '@views/elements/comments/CommentItem'

    export default {
        props: {
            value: {
                type: [Array],
                default: () => {
                    return [];
                }
            },
            taskId: {
                type: Number
            },
            showReply: {
                type: [Boolean, Number],
                default: false
            },
            replyComment: {
                type: Boolean,
                default: false
            },
            action_id: {
                type: Number,
                default: null
            },
            inTask: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapGetters({
                isTaskOwner:    'task/isOwner',
                getUserId:      'user/getUserId',
            }),
            comments: {
                get() {
                    return [...this.value]
                        .filter(item => {
                            /* TODO remove filter when backend wont be send all comment  */
                            const isCantRead = !this.handlePermissionByGroupId('read-task', item.group_id);
                            // const isCantRead = !this.handlePermissionByGroupId('read-other-time-logs', item.group_id);
                            const isLogOwner = item.user_id || item.user.id === this.getUserId;

                            if (item.source === 'timer_log') {
                                if (!this.checkPermission('time-tracking')) {
                                    return false;
                                }

                                if (isCantRead) {
                                    if (isLogOwner || this.isTaskOwner) return true;
                                    return false;
                                }

                                return true;
                            }

                            if (item.source === 'comment') {
                                if (isCantRead) {
                                    if (isLogOwner || this.isTaskOwner) return true;
                                    return false;
                                }
                            }

                            return true;
                        })
                        .sort((a, b) => {
                            if (a.reactions && b.reactions) {

                                if (a.reactions.stick.group_id && !b.reactions.stick.group_id) {
                                    return -1
                                }

                                if (!a.reactions.stick.group_id && b.reactions.stick.group_id) {
                                    return 1
                                }

                                if (a.reactions.stick.task_id && !b.reactions.stick.task_id) {
                                    return -1
                                }

                                if (!a.reactions.stick.task_id && b.reactions.stick.task_id) {
                                    return 1
                                }


                                // if (a.reactions.stick.task_id && b.reactions.stick.task_id) {
                                //     return a.reactions.stick.task_id - b.reactions.stick.task_id;
                                // } else if (!a.reactions.stick.task_id && b.reactions.stick.task_id) {
                                //     return 1
                                // } else if (a.reactions.stick.task_id && !b.reactions.stick.task_id) {
                                //     return -1
                                // }
                            }

                            const diff = new Date(b.created_at) - new Date(a.created_at);
                            return diff > 0 ? 1 : -1;
                        })

                },
                set(value) {
                    return this.$emit('input', value);
                }
            },
            permissions() {
                const { group_id } = this.value[0];
                const manageOTL = this.handlePermissionByGroupId('manage-other-time-logs', group_id);
                const canTrack = this.checkPermission('time-tracking');
                const update = this.handlePermissionByGroupId('update-task', group_id);

                return {
                    manageOTL,
                    canTrack,
                    update
                };
            },
        },
        components: {
            CommentItem
        },
        data() {
            return {}
        },
        methods: {
            stickyComment(comment) {
                return this.$api.comment.stickyComment(comment).catch((err) => {
                    this.$notify({type: 'error', text: this.$t('failed_to_change_comment')});
                })
            },
            // getReplies(comment) {
            //
            //     if (Object.keys(this.fetchReplies).length !== 0 && this.fetchReplies[comment.id] || this.replyComment) {
            //         return this.toggleListReply(comment);
            //     }
            //
            //     this.$api.comment.getCommentDetails(comment.id).then(res => {
            //         this.toggleListReply(comment);
            //         this.fetchReplies[comment.id] = true;
            //     }).catch(err => {
            //         this.$notify({type: 'error', text: this.$t('can_not_get_replies')});
            //     })
            // },
            remove(comment) {
                if (comment.timer) {
                    return this.$api.log.removeLog(comment).catch((err) => {
                        if (err.response.status === 422) {
                            if (err.response.data.errors) {
                                handleErrors(err.response.data.errors, this.errors, _self, true);
                            }
                        } else {
                            this.$notify({type: 'error', text: err.response.data.message});
                        }

                        this.$notify({type: 'error', text: this.$t('failed_to_remove_log')});
                    })
                }

                comment.action_id = this.action_id;

                return this.$api.comment
                    .removeComment(comment)
                    .catch(() => {
                        this.$notify({type: 'error', text: this.$t('failed_to_remove_comment')});
                    })
            }
        }
    }
</script>

<style lang="scss">
    .comment-reply{
        background-color: #edf6ff;
        padding: 20px;
        padding-right: 0;

        .comment-list > li{
            box-shadow: none;
        }
    }
    .comment-list {
        padding: 0 !important;

        & > li {
            width: 100%;
            background-color: #fff;
            margin: 10px 0;
            border-radius: 5px;
            -webkit-box-shadow: 0 2px 3px rgba(0,0,0,0.16), 0 2px 3px rgba(0,0,0,0.2);
            box-shadow: 0 2px 3px rgba(0,0,0,0.16), 0 2px 3px rgba(0,0,0,0.2);
            transition: .3s background-color ease;

            &:first-child {
                margin: 0 !important;
            }

            &:last-child {
                margin-bottom: 0;
            }

            &.comment-item-new {
                background-color: #e2ffea;
            }
        }

        .comment-item {
            position: relative;

            .comment-title {
                padding: 10px 15px;
                line-height: 28px;
                color: #B2B2B2;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;

                .user-avatar {
                    display: inline-block;
                    padding-right: 10px;

                    i {
                        position: relative;
                        width: 18px;
                        height: 18px;
                        top: -1px;
                        right: -20px;
                        padding: 4px;
                        background: #5078f2;
                        font-size: 11px;
                        text-align: center;
                        color: #FFFFFF;
                        border-radius: 100%;
                        @media (max-width: 991px) {
                            right: 0;
                        }
                    }
                }

                .comment-task-name {
                    color: #5078f2;
                    cursor: pointer;
                }

                .comment-controls {
                    float: right;
                    display: inline-block;

                    i {
                        margin: 0 5px;
                    }
                }

                .comment-task {
                    display: block;
                }

            .comment-content {
                border-top: 1px solid #e8e8e8;
                // padding-top: 10px;
                // margin-top: 10px;

                .ql-editor {
                    padding: 10px 15px;
                    overflow: hidden;
                    min-height: auto;

                        .toolbar-container {
                            padding-top: 0;
                        }

                        a {
                            color: #5078f2;
                        }

                        pre {
                            overflow: hidden;
                            white-space: pre-wrap;
                        }
                    }

                    .ql-snow.toolbar-container {
                        padding-top: 0;
                    }

                    .ql-snow .ql-editor {
                        padding: 15px;
                        min-height: 100px;
                    }
                }
            }
        }
    }

    .htmlText{
        display: inline-block;
        p {
            margin: 0;
        }
        p, *{
            max-width: 100%
        }
        img{
            width: 100px;
            margin-right: 10px;
            border-radius: 5px;
        }
    }
</style>
