<template>
    <li
        :class="{'comment-item-new': comment.new_comment}"
        @mouseover="setSeen"
    >
        <div class="comment-item" :class="comment.source" >

            <div class="comment-title">
                <div class="comment-title__content">
                    <div class="user-avatar">
                        <theme-subscribers class="user-avatar-image" :style="{'background-image': 'url(' + comment.user.avatar + ')'}">
                            <span v-if="!comment.user.avatar ">{{comment | userInitials }}</span>
                        </theme-subscribers>
                    </div>
                    <div class="comments-list">
                        <template v-if="comment.source !== 'activity_log'">
                            <span v-if="debug">not activity_log</span>
                            <b>
                                {{ getMember ? getMember : comment  | userFullName }}
                            </b>

                            {{ comment | commentAction }}

                            <template v-if="comment.timer">
                                <b>{{ getLoggedTime(comment.timer.time) }}</b> for
                                <b>{{ comment.timer | loggedCreateAt('YYYY.MM.DD HH:mm:ss') }}</b> to
                            </template>
                            <b
                                v-if="comment.task_title"
                                class="comment-task-name"
                                @click="showTask(comment.task_id)"
                            >
                                {{ comment.task_title }}
                            </b>

                            {{ comment | commentCreateAt }}
                        </template>

<!--                        <template v-else-if="isChangeDescription">-->
<!--                            <span v-if="debug">isChangeDescription</span>-->
<!--                            <b>-->
<!--                                {{ getMember ? getMember : comment | userFullName }}-->
<!--                            </b>-->
<!--                            changed description of task-->
<!--                            <b-->
<!--                                v-if="comment.task_title"-->
<!--                                class="comment-task-name"-->
<!--                                @click="showTask(comment.task_id)"-->
<!--                            >-->
<!--                                {{ comment.task_title }}-->
<!--                            </b>-->

<!--                            {{ comment | commentCreateAt }}-->
<!--                        </template>-->

                        <template v-else>

                            <template
                                v-if="!isJsonComment(comment.body)"
                            >
                                <div class="htmlText" v-html="comment.body"></div>
                                <b
                                    v-if="comment.task_title"
                                    class="comment-task-name"
                                    @click="showTask(comment.task_id)"
                                >
                                    {{ comment.task_title }}
                                </b>

                                {{ comment | commentCreateAt }}
                            </template>

                            <template
                                v-if="isJsonComment(comment.body)"
                            >
                                <div class="task-detail-activity">
                                    <p class="task-detail-activity-sender">{{ parseJsonComment(comment.body).sender }}</p>
                                    <p class="task-detail-activity-action">{{ parseJsonComment(comment.body).action }}</p>
                                    <p class="task-detail-activity-task comment-task-name">
                                        <router-link :to="{ query: { taskId: comment.task_id } }">{{ parseJsonComment(comment.body).task }}</router-link>
                                    </p>
                                    <span>an {{ comment | commentCreateAt }} to:</span>
                                    <div
                                        v-html="parseJsonComment(comment.body).new"
                                        class="task-detail-activity-desc"
                                    ></div>
                                    <button
                                        @click="showOldDesc = !showOldDesc"
                                        class="task-detail-activity-button"
                                    >{{ !showOldDesc ? 'Show previous description' : 'Hide' }}</button>
                                    <div
                                        v-if="showOldDesc"
                                        v-html="parseJsonComment(comment.body).old"
                                        class="task-detail-activity-desc"
                                    ></div>
                                </div>
                            </template>

                        </template>

                        <small v-if="debug">{{ comment.source }}</small>
                    </div>
                </div>

                <div class="comment-controls">
                    <div
                        v-if="comment.canUpdate && (comment.source === 'comment' || replyComment)"
                        class="comment-reply-controls"
                    >
                        <button
                            class="button__size_xxs button__theme_outline-info"
                            @click="setReplyComment(comment.id)"
                        >
                            {{ $t('reply') }}
                        </button>
                        <button
                            v-if="comment.count_replies"
                            :disabled="isLoading"
                            class="button-show-replies button__size_xs button__theme_outline-succes"
                            @click="toggleListReply(comment)"
                        >
                            {{ listReplyCommentId !== comment.id ? "Hide replies" : "Show replies" }}
                            <span class="comment-count-replies">
                                {{ comment.count_replies || (comment.replies && comment.replies.length) }}
                            </span>
                        </button>
                    </div>
                    <i
                        v-if="comment.source === 'comment' && !replyComment"
                        :class="{'comment_sticky': isSticky(comment)}"
                        class="fa fa-map-pin custom-fa-log"
                        @click="$emit('sticky', comment)"
                    ></i>
                    <comment-likes
                        v-if="comment.source === 'comment' || replyComment"
                        :inTask="inTask"
                        :action_id="action_id"
                        :comment="comment"
                    />
                    <i
                        v-if="canComment(comment, 'update')"
                        class="fa fa-pencil custom-fa-log"
                        @click="showUpdateComment(index, comment)"
                    ></i>
                    <i
                        v-if="canComment(comment, 'delete')"
                        class="fa fa-trash-o custom-fa-log-del"
                        @click="showDeleteComment()"
                    ></i>
                </div>
            </div>

            <div
                v-if="(getCommentContent || updateIndex === index) && (comment.source !== 'activity_log' || comment.body.length > 100)"
                class="comment-content"
            >
                <template v-if="updateIndex === index">
                    <comment-edit
                        :comment="comment"
                        :index="index"
                        :inTask="inTask"
                        :action_id="action_id"
                        :task-id="comment.task_id"
                        :ref="'comment-' + index"
                        @cancel="updateIndex = null"
                    />
                </template>

                <template v-else>
                    <comment-content
                        v-if="comment.field !== 'description'"
                        :html="getCommentContent"
                        :files="comment.attachments"
                        :task_id="comment.task_id"
                    />
                    <comment-files
                        v-model="comment.attachments"
                        :user="comment.user"
                        :task_id="comment.task_id"
                    />
                </template>
            </div>

            <comment-edit
                v-if="replyCommentId === comment.id"
                v-model="comment"
                :inTask="inTask"
                :action_id="action_id"
                :setting="settingReply"
                :parentId="comment.id"
                :task-id="comment.task_id"
                :auto-save="false"
                @saveComment="setReplyComment"
            />
            <div
                v-if="comment.replies && comment.replies.length && listReplyCommentId !== comment.id && showReply"
                class="comment-reply"
            >
                <comment-list
                    :key="index"
                    v-model="comment.replies"
                    :inTask="inTask"
                    :action_id="action_id || comment.id"
                    :showReply="true"
                    :replyComment="true"
                />
            </div>
        </div>
    </li>
</template>

<script>
    import moment               from 'moment';
    import { mapGetters }       from 'vuex';
    import { getLoggedTime }    from '@helpers/time';

    import CommentEdit          from '@views/elements/comments/CommentEdit';
    import CommentFiles         from '@views/elements/comments/CommentFiles';
    import CommentContent       from "@views/elements/comments/CommentContent";
    import CommentLikes         from '@views/elements/comments/CommentLikes';
    import ThemeSubscribers     from '@views/layouts/theme/ThemeSubscribers';
	import task                 from '@helpers/task';
    const CommentList = () => import('@views/elements/comments/CommentList'); // don't remove. It is need use dynamic

    export default {
        components: {
            CommentContent,
            CommentFiles,
            CommentEdit,
            CommentList,
            CommentLikes,
            ThemeSubscribers
        },
		props: {
			comment: {
				type: Object
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
			},
			permissions: {
				type: Object
			},
			index: {
				type: Number,
				default: null
			},
		},
		data() {
            return {
                updateIndex: null,
                form: {},
                datetimeFormat: 'YYYY-MM-DD HH:mm',
                updateCommentId:    null,
                replyCommentId:     null,
                listReplyCommentId: null,
                settingReply: {
                    log: false,
                    date: false
                },
                commentId: '',
                fetchReplies : {},
                showOldDesc: false
            }
        },
        computed: {
            ...mapGetters({
                getStateGroups:         'groups/getStateGroups',
                getMembers:             'members/getMembers',
                isTaskOwner:            'task/isOwner',
                getUserId:              'user/getUserId',
                getMembersByUserKey:    'members/getMembersByUserKey',
            }),
            isChangeDescription(){
                if (this.comment.body) {
                    return this.comment.body.includes('description')
                }
            },
            getCommentContent() {
                if (this.comment.timer) {
                    return this.comment.timer.comment;
                }

                return this.comment.body;
            },
            getMember() {
                return this.getMembersByUserKey[this.comment.user_id];
            },
            commentAvatar() {
                if (this.comment.user && this.comment.user.avatar) {
                    return this.comment.user.avatar;
                }
                if (this.getMember) {
                    return  this.getMember.user.avatar;
                } else {
                    return '';
                }
            },
        },
        filters: {
            commentAction(comment) {
                return comment.timer ? 'logged' : 'commented';
            },
            loggedCreateAt(timer, format = null) {
                const date = moment.utc(timer.end_time).local();

                if (format) {
                    return date.format(format);
                }

                return date.fromNow();
            },
            commentCreateAt(comment, format = null) {
                const date = moment.utc(comment.created_at).local();
                if (format) {
                    return date.format(format);
                }
                return date.fromNow();
            },
            taskName(task) {
                return task ? `${task.name}` : null;
            },
            userInitials(user) {
                return user.user ? `${user.user.name[0]}${user.user.last_name[0]}` : 'S';
            },
            userFullName(user) {
                if (! user) {
                    return  'undefined';
                }
                return user.user  ? `${user.user.name} ${user.user.last_name}` : 'System message';
            },
        },
        // mounted() {
        //     this.$nextTick(function () {
        //         this.addEventListener();
        //     });
        // },
        methods: {
            // addEventListener() {
            //     document.querySelector('.comment-list-wrapper_communication').addEventListener('click', (e) => {
            //         console.log('e', e);
            //     })
            // },
        	isJsonComment(comment) {
        	    return task.checkJson(comment);
            },
            parseJsonComment(comment) {
        	    return JSON.parse(comment);
            },
            isSticky(comment) {
                if (comment.reactions.stick.task_id) {
                    return true;
                }
                if (comment.reactions.stick.group_id) {
                    return true;
                }
            },
            canComment(comment, type) {
                const { source, canUpdate, canDelete, user, user_id, group_id } = comment;
                const isLogOwner = this.getUserId === user_id || user.id;
                const { canTrack, manageOTL, update } = this.permissions;

                const isTimeLog = source === 'timer_log';
                const isComment = source === 'comment';
                const isActivity = source === 'activity_log';

                if (this.getUserId !== user.id) {
                	return false;
                }

                if (isActivity) {
                    return false;
                }

                if (isLogOwner) {
                    if (isComment || this.replyComment) {
                        return true;
                    }
                    if (isTimeLog) {
                        return canTrack;
                    }
                }

                if (this.isTaskOwner) {
                    if (type === 'update' && canUpdate) {
                        return manageOTL;
                    }

                    if (type === 'delete' && canDelete) {
                        return manageOTL;
                    }
                } else {
                    const timeLogPerm = manageOTL && canTrack && update
                    const commentPerm = manageOTL && update

                    if (type === 'update' && canUpdate) {
                        if (isComment || this.replyComment) {
                            return commentPerm;
                        }
                        if (isTimeLog) {
                            return timeLogPerm;
                        }
                    }

                    if (type === 'delete' && canDelete) {
                        if (isComment || this.replyComment) {
                            return commentPerm;
                        }
                        if (isTimeLog) {
                            return timeLogPerm;
                        }
                    }
                }

                return false;
            },
            showTask(taskId) {
                this.$router.replace({query: {taskId}});
            },
            getLoggedTime(time){
                return getLoggedTime(time);
            },
            showUpdateComment(index, comment) {
                if (this.updateIndex === index) {
                    return this.updateIndex = null;
                }

                return this.updateIndex = index;
            },
            showDeleteComment() {
                const isTimerLog = this.comment.source === 'timer_log';
                const modalTitle = isTimerLog ? 'delete_this_log' : 'delete_this_comment';
                const modalBodyText = isTimerLog ? 'are_you_sure_you_want_to_delete_this_log'
                    : 'are_you_sure_you_want_to_delete_this_comment';

                this.$modal.show('confirm-modal', {
                    title: this.$t(modalTitle),
                    body: this.$t(modalBodyText),
                    confirmCallback: () => {
                        this.$emit('remove', this.comment);
                    },
                });
            },
            toggleCommentBlock(state, index = null) {
                if (this[state] === index) {
                    return this[state] = null;
                }

                return this[state] = index;
            },
            setReplyComment(index = null) {
                return this.toggleCommentBlock('replyCommentId', index)
            },
            toggleListReply(comment) {
                return this.toggleCommentBlock('listReplyCommentId', comment.id)
            },
            setSeen() {
            	if (this.comment.new_comment) {
					return this.$store.dispatch('task/changeAction', {
						...this.comment,
						new_comment: false
					});
                }
            }
        }
    }
</script>

<style lang="scss">
    .comment-title__content {
        display: flex;
        .htmlText{
            line-height: 1.5em;
        }
    }
    .comments-list {
        .htmlText{
            * {
                display: inline;
            }
            img {
                vertical-align: top;
            }
        }
    }
</style>
