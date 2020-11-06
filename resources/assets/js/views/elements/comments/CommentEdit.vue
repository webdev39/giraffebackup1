<template>
    <div class="comment-edit">
        <comment-editor
            v-model="comment"
            ref="editor"
            :canLogged="getCanLogged"
            :listenerChange="inTask"
            :replyTask="getReplyTask"
            :task-id="getTaskId"
            :users="users"
            :placeholder="$t('create_new_comment')"
            @send="handleSend"
        >
            <template slot="footer" slot-scope="slot">
                <div class="editor-log comment-box-options-item" v-if="slot.loggedInfo">
                    {{ slot.loggedInfo }}
                </div>

                <span class="comment-box-options-item" v-if="slot.loggedInfo && Object.keys(select).length">
                    in
                </span>

                <div class="select_info comment-box-options-item" v-if="Object.keys(select).length">
                    {{ select.name }}
                </div>

                <button type="button" class="comment-box-options-item quill-editor-button-state btn btn-cancel pull-right" @click="resetData" v-if="comment.id">
                    {{ $t('cancel') }}
                </button>
                <div class="comment-box-options-item comment-box-options-item__btns">
                    <theme-button-success type="button"
                                          class="quill-editor-button-state btn btn-outline-danger pull-right"
                                          :important="true"
                                          @click.native="showChooseTask"
                                          v-if="isSelectTask">
                        {{ $t('select_task') }}
                    </theme-button-success>

                    <theme-button-success type="button"
                                          class="comment-box-options-item quill-editor-button-state btn btn-update pull-right"
                                          :important="true"
                                          @click.native="handleSend"
                    >
                        {{ comment.id ? $t('save') : $t('create') }}
                    </theme-button-success>
                </div>
            </template>
        </comment-editor>
    </div>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import find                 from '@helpers/findInGroups'
    import CommentEditor        from "@views/components/editor/CommentEditor";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        name: "comment-edit",
        props: {
            taskId:     Number,
            isSelectTask: {
                type: Boolean,
                default: false
            },
            index:      Number,
            comment: {
                type: Object,
                default:() => {
                    return {};
                }
            },
            parentId: {
                type: Number,
                default: null
            },
            action_id: {
                type: Number,
                default: null
            },
            inTask: {
                type: Boolean,
                default: false
            },
            canLogged: {
                type: Boolean,
                default: true,
            },
        },
        data() {
            return {
                select: {},
            }
        },
        computed: {
            ...mapGetters({
                getGroups:          'groups/getStateGroups',
                getMembers:         'members/getMembers',
                getTask:            'task/getTask',
				getBoardId:          'task/getBoardId',
				getGroupId:          'task/getGroupId',
                getChangeComments:  'task/getChangeComments',
                getCurrentTheme:    'user/getCurrentTheme',
            }),
            getTaskId() {
                return this.taskId || this.select.id || null
            },
            getUserId () {
                return this.$store.getters['members/getOwner'].id
            },
            getCanLogged() {
				if (this.$route.name === 'communication') {
					return false;
                }

				if (!this.canLogged) {
                    return false;
                }

                if (this.handlePermissionByGroupId('time-tracking', +this.$route.params.group_id) && this.checkPermission('time-tracking')) {
                    return true;
                }

                if (this.handlePermissionByGroupId('manage-other-time-logs', +this.$route.params.group_id) && this.comment.user && this.getUserId !== this.comment.user.id) {
                    return true;
                }

                return false;
            },
            getCommentEditorId () {
                return this.getChangeComments.find(item => item === this.$refs.editor._uid)
            },
            getReplyTask () {

                if (this.comment.children && this.comment.children.length) {
                  return true
                }

                if (this.comment.parent_id) {
                  return true;
                }

                if (this.comment.count_replies) {
                  return true;
                }

                return false
            },
            users() {
                let group = find.searchGroupById(this.getGroups, this.innerGroupId);
                let result = [];

                if (group && group.members) {
                    find.searchMembersInGroups(group, this.getMembers).map(member => {
                      if (member.user.status) {
                        result.push(member.user);
                      }
                    });
                }

                return result;
            },
            innerGroupId() {
                return Number(this.select.group_id || this.getTask.group_id || this.$route.params.group_id);
            },
            innerBoardId() {
                return Number(this.select.board_id || this.getTask.board_id || this.$route.params.board_id);
            },
            innerTaskId() {
                return Number(this.select.task_id || this.taskId || this.getTask.id);
            }
        },
        components: {
            CommentEditor,
            ThemeButtonSuccess
        },
        methods: {
            removeChangeComment() {
                if (this.getCommentEditorId) {
                    this.$store.dispatch('task/removeChangeComments', this.$refs.editor._uid);
                }
            },
            showChooseTask() {
                this.$modal.show('choose-modal', {type: "task", callback: (task) => {
                    this.select = Object.assign(task, {task_id: task.id});
                }});
            },
            getRequestData(comment) {
                // if (!this.innerTaskId) {
                //     this.$notify({type:'error', text: this.$t('choose_task')});
                //     return null;
                // }

                if (!comment.content && !comment.time && !comment.attachments) {
                    return null;
                }

                return {
                    id:             this.comment.id || null,
                    commentId:      this.comment.id || null,
                    groupId:        this.innerGroupId,
                    taskId:         this.innerTaskId,
                    time:           comment.loggedTime,
                    logDate:        comment.loggedTime ? comment.loggedDate : null,
                    attachments:    comment.attachments,
                    comment:        comment.content,
                    body:           comment.content,
                    mentions:       comment.mentions,
                    parentId:       this.parentId,
                    action_id:      this.action_id,
                    inTask:         this.inTask
                };
            },
            handleSend() {
                this.removeChangeComment();

                const comment = this.$refs.editor.getComment();

                const request = this.getRequestData(comment);


                if (!request || this.isLoading) {
                    return;
                }

                request.boardId = this.$route.params.board_id

                /**
                 * Timer log
                 */
                if (request.time) {
					if (request.id) {
                        if (!this.comment.timer) {
                            return this.transformCommentToLog(request);
                        }

                        return this.updateLog(request);
                    }

                    return this.createLog(request);
                }

                /**
                 * Comment
                 */
                if (request.id) {
                    if (this.comment.timer) {
                        return this.transformLogToComment(request);
                    }

                    return this.updateComment(request);
                }

                return this.createComment(request);
            },
            createComment(request) {
                if (!request.comment && request.attachments.length === 0) {
                    return this.$notify({type:'error', text: this.$t('comment_required')});
                }
                this.$api.comment.createComment(request).then(() => {
                    this.resetData();
                    this.$emit('saveComment');
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            updateComment(request) {
                if (this.comment.source === "activity_log") {
                    return
                }

                this.$api.comment.updateComment(request).then(data => {
                    this.update(data.comment);

                    let diff = data.comment.attachments.length - this.comment.attachments.length;

                    if (diff !== 0) {
                        this.$store.dispatch('groups/changeAttachmentTasksCount', {
                            task_id: data.comment.task_id,
                            count: diff
                        });
                    }
                })
            },
            deleteComment(request) {
                this.removeChangeComment();

                this.$api.comment.removeComment(request).then(() => {
                    this.update(null);
                })
            },
            createLog(request) {
                if (this.getReplyTask) {
                    return this.$notify({type:'error', text: this.$t('can_not_create_log')});
                }

                this.$api.log.createLog(request).then(() => {
                    this.resetData();
                })
            },
            updateLog(request) {
                this.$api.log.updateLog(request, this.comment).then(data => {
                    this.update(data.comment);

                    let diff = data.log.attachments.length - this.comment.attachments.length;

                    if (diff !== 0) {
                        this.$store.dispatch('groups/changeAttachmentTasksCount', {
                            task_id: data.log.task_id,
                            count: diff
                        });
                    }
                })
            },
            deleteLog(request) {
                this.$api.log.removeLog(request).then(() => {
                    this.update(null);
                })
            },
            transformCommentToLog(request) {
                this.createLog(request);
                this.deleteComment(request);
            },
            transformLogToComment(request) {
                this.createComment(request);
                this.deleteLog(request)
                    .catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
            },
            delete(request) {
                if (this.comment.timer) {
                    return this.deleteLog(request);
                }

                this.deleteComment(request);
            },
            update(comment) {
                this.$emit('update', this.index, comment);

                this.resetData();
            },
            resetData() {
                this.$emit('cancel');

                this.removeChangeComment();

                this.resetComponentData();

                if (this.$refs.editor) {
                    this.$refs.editor.setDefaultData();
                }
            },
            // getRequestData() {
            //     let data = {
            //         taskId:         this.taskId || this.select_task.id || this.comment.task_id,
            //         isDraftTask:    this.isDraftTask,
            //         time:           this.getLogTimeInFormat,
            //         attachmentIds:  this.attachmentIds,
            //         comment:        this.commentContent,
            //         body:           this.commentContent,
            //         mentions:       this.commentMentions,
            //         commentId:      this.comment.id || null,
            //         parentId:       this.parentId,
            //         id:             this.comment.id || null
            //     };
            //
            //     if (this.form.loggedTime) {
            //         data.logDate = this.toUTCTime(this.form.loggedTime);
            //     }
            //
            //     if (this.$refs.editor) {
            //         this.$refs.editor.setDefaultData();
            //     }
            // },
        }
    }
</script>

<style lang="scss">
    .comment-edit {
        position: relative;
        width: 100%;
        background-color: #fff;
        border-radius: 5px;
        padding: 5px;
        .select_info {
            font-weight: 600;
            text-decoration: underline;
        }
    }
    .comment-box-options-item__btns{
        display: flex;
        @media (max-width: 768px) {
            margin-top: 10px;
        }
    }
</style>
