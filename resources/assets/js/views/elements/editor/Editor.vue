<template>
    <div class="editor">
        <template v-if="showEditDescription">
            <comment-editor :listenerChange="inTask"  v-model="getContent" :canLogged="false" :users="users" ref="editor" :placeholder="$t('create_new_comment')" @send="handleSend">
                <template slot="footer" slot-scope="slot">
                    <button @click="hideDescription" class="comment-box-options-item quill-editor-button-state btn btn-cancel pull-right">
                        {{ $t('cancel') }}
                    </button>
                    <button @click="handleSend" class="comment-box-options-item quill-editor-button-state btn btn-update pull-right">
                        {{ $t('create') }}
                    </button>
                </template>
            </comment-editor>
        </template>
        <template v-else>
            <comment-content :html="task.description" />
            <div class="editor-controls">
                <button class="button" :disabled="isLoading" @click="showEditDescription = !showEditDescription" >
                    <span>{{ $t('edit') }}</span>
                </button>
            </div>
        </template>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import find                 from '@helpers/findInGroups'
    import CommentEditor        from "@views/components/editor/CommentEditor";
    import CommentContent       from "@views/elements/comments/CommentContent";

    export default {
        name: "editor",
        props: {
            taskId:     Number,
            task: {
                type: Object,
                default: {}
            },
            inTask: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                select: {},
                showEditDescription: false,
            }
        },
        computed: {
            ...mapGetters({
                getGroups:          'groups/getStateGroups',
                getMembers:         'members/getMembers',
                getTask:            'task/getTask',
                getChangeComments:  'task/getChangeComments',
            }),
            getContent () {
                return {
                    body: this.task.description
                }
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

                if (group.members) {
                    return find.searchMembersInGroups(group, this.getMembers).map(member => member.user);
                }

                return [];
            },
            innerGroupId() {
                return Number(this.select.group_id || this.$route.params.group_id || this.getTask.group_id);
            },
            innerBoardId() {
                return Number(this.select.board_id || this.$route.params.board_id || this.getTask.board_id);
            },
            innerTaskId() {
                return Number(this.select.task_id || this.taskId || this.getTask.id);
            }
        },
        components: {
            CommentEditor,
            CommentContent
        },
        methods: {
            getRequestData(comment) {
                if (!this.innerTaskId) {
                    this.$notify({type:'error', text: "Choose task, please"});
                    return null;
                }

                if (!comment.content && !comment.time && !comment.attachments) {
                    return null;
                }

                return {
                    id:             this.comment.id || null,
                    commentId:      this.comment.id || null,
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
                const comment = this.$refs.editor.getComment();
                this.hideDescription();
                this.$emit('send', comment);
            },
            hideDescription() {
                this.showEditDescription = false;
            },
            resetData() {
                this.showEditDescription = false;
            },
        }
    }
</script>

<style lang="scss">
    .editor {
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
    .input-task-description{
        min-height: auto;
        border: none;
        padding-left: 0;
        padding-right: 0;
    }
    .editor-controls{
        display: flex;
        justify-content: flex-end;
        .button{
            background: none;
            font-size: 15px;
            border: none;
            color: #b2b2b2;
        }
    }
</style>
