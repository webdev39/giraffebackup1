<template>
    <div class="task-description">
        <comment-editor v-if="showEditDescription || !getTask.description" :listenerChange="true" v-model="getContent" :canLogged="false" :users="users" ref="editor" :placeholder="$t('create_description')" @send="handleSend">
            <template slot="footer" slot-scope="slot">
                <button
                    v-if="getTask.description"
                    @click="hideDescription"
                    class="comment-box-options-item quill-editor-button-state btn btn-cancel pull-right"
                >
                    {{ $t("cancel") }}
                </button>
                <button @click="handleSend" class="comment-box-options-item quill-editor-button-state btn btn-update pull-right">
                    {{ getTask.description ? $t("update") : $t("create") }}
                </button>
            </template>
        </comment-editor>
        <template v-else>
            <comment-content :html="getTask.description" />
            <div class="task-description-controls">
                <button class="button" :disabled="isLoading" @click="showEditDescription = !showEditDescription" >
                    <span>{{ $t("edit") }}</span>
                </button>
            </div>
        </template>
    </div>
</template>

<script>
    import { mapGetters }       from 'vuex';
    import find                 from '@helpers/findInGroups';
    import permissionsMixin     from '@mixins/permissions';

    import CommentEditor        from "@views/components/editor/CommentEditor";
    import CommentContent       from "@views/elements/comments/CommentContent";

    export default {
        name: "task-description",
		components: {
			CommentEditor,
			CommentContent
		},
		mixins: [
			permissionsMixin,
		],
        props: {
			getTask: {
				type: Object,
                default: () => {}
            }
        },
        data() {
            return {
                select:              {},
                showEditDescription: false,
            }
        },
        computed: {
            ...mapGetters({
                getGroups:          'groups/getStateGroups',
                getMembers:         'members/getMembers',
                getChangeComments:  'task/getChangeComments',
                getUserId:          'user/getUserId',
            }),
            getCommentEditorId () {
                return this.getChangeComments.find(item => item === this.$refs.editor._uid)
            },
            getContent () {
                return {
                    body: this.getTask.description
                }
            },
            users() {
                let group = find.searchGroupById(this.getGroups, this.getTask.group_id);

                if (group && group.members) {
                    return find.searchMembersInGroups(group, this.getMembers).map(member => member.user);
                }

                return [];
            },
        },
        methods: {
            removeChangeComment() {
                if (this.getCommentEditorId) {
                    this.$store.dispatch('task/removeChangeComments', this.$refs.editor._uid);
                }
            },
            handleSend() {
                const comment = this.$refs.editor.getComment();

                if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                if (comment.content === this.getTask.description) {
                    return this.hideDescription()
                }

                if (!comment.content) {
                    return this.$notify({type:'error', text: this.$t('description_is_empty')});
                }

                let data = Object.assign({}, this.getTask, {description: comment.content.trim(), task_id: this.getTask.id});
                this.hideDescription();

                this.$api.task.updateTask(data).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },

            hideDescription() {
                this.removeChangeComment();
                this.showEditDescription = false;
            },
        }
    }
</script>

<style lang="scss">
    .task-description {
        position: relative;
        width: 100%;
        background-color: #fff;
        border-radius: 5px;
        padding: 5px;

        .select_info {
            font-weight: 600;
            text-decoration: underline;
        }
        > .ql-editor{
            min-height: auto;
            border: none;
            padding: 0;
            overflow: visible;
        }
        .ql-container{
            margin: 0;
        }
        .ql-toolbar.ql-snow{
            padding: 0;
        }
        .editor-wrapper .ql-toolbar.ql-snow .comment-box-options{
            margin-left: 0;
            margin-right: 0;
        }
        .editor-wrapper .toolbar-container{
            margin-left: -20px;
            margin-right: -20px;
        }
    }

    .task-description-controls{
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
