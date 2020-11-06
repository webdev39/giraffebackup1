<template>
    <div class="communication-inner">
        <fixed-component>
            <group-board-info :isSubscribersBoard="true" :show-info-setting="false">
                <template slot="right-panel">
                    <div class="group-board-info__item group-board-info__item_filter">
                        <comment-filter :fetching="isLoading" @update="updateCommentsAndLogs" />
                    </div>
                </template>
            </group-board-info>
        </fixed-component>
        <div class="comment-list-wrapper_communication">
            <div class="comment-list__create">
                <comment-edit
                    :task-id="null"
                    :isSelectTask="true"
                    :canLogged="false"
                />
            </div>
            <div class="comment-list_communication">
                <div class="comment-list-content_communication">
                    <comment-list ref="comment-list" :in-page="true" :showReply="true" v-model="getActions" />
                    <content-loading v-if="!fetchData"
                            :absolute="false"
                            :loading="!fetchData"
                    >
                    </content-loading>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters }   from 'vuex'

    import pusher           from '@utils/pusher/index'

    import CommentList      from '@views/elements/comments/CommentList'
    import TaskCreate       from '@views/components/task/TaskCreate'
    import CommentFilter    from '@views/elements/comments/CommentFilter'
    import GroupBoardInfo   from '@views/partcials/GroupBoardInfo/GroupBoardInfo'
    import CommentEdit      from '@views/elements/comments/CommentEdit'
    import FixedComponent   from '@views/components/fixedComponent/FixedComponent'
    import ContentLoading   from '@views/components/ContentLoading'

    export default {
        name: "communication",
        data() {
            return {
                filters: {
                    names:      [],
                    columns:    [],
                },
                range:      [],
                created_by: [],
                pagination: {
                    current_page:   0,
                    per_page:       0,
                    count:          0,
                },
                container: {
                    content: null
                },
                fetchData: false,
                assignedLength: 0
            }
        },
        computed: {
            ...mapGetters({
                isDraftTask:    'task/isDraftTask',
                getActions:     'actions/getActions',
                getTask:        'task/getTask',
                getGroups:      'groups/getStateGroups',
                getMembers:     'members/getMembers',
                getSelectMembers: 'members/getSelectMembers',
            }),
        },
        components: {
            CommentList,
            CommentFilter,
            TaskCreate,
            GroupBoardInfo,
            CommentEdit,
            FixedComponent,
            ContentLoading
        },
        mounted() {
            this.nextCommentsAndLogs();
            pusher.listenCommunication(this.$route.params.group_id);
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        beforeRouteLeave(to, from, next) {
            pusher.leavingCommunication(this.$route.params.group_id);
            next();
        },
        destroyed() {
			let content = document.querySelector('.content');
			content.removeEventListener('scroll', () => this.activeScrollPagination(content));
        },
        watch: {
            '$route'(to, from) {
                pusher.leavingCommunication(from.params.group_id);
                if (+to.params.group_id !== +from.params.group_id || (to.params.board_id && +to.params.board_id != +from.params.board_id)) {
                    this.updateCommentsAndLogs(true);
                    pusher.listenCommunication(to.params.group_id);
                }
            },
            getSelectMembers(newValue) {
                if (newValue.length !== this.assignedLength) {
                    this.assignedLength = newValue.length;
                    this.updateCommentsAndLogs({created_by: newValue});
                }
            }
        },
        methods: {
			activeScrollPagination(content) {
				if (Math.ceil(content.scrollTop + content.clientHeight) >= content.children[0].scrollHeight && this.$route.name === 'communication') {
					if (this.pagination.count && this.pagination.count === this.pagination.per_page && !this.isLoading) {
						this.getCommentsAndLogs();
					}
				}
            },
            nextCommentsAndLogs() {
                let content = document.querySelector('.content');
                content.addEventListener('scroll', () => this.activeScrollPagination(content));
            },
            updateCommentsAndLogs (data) {
                if (data.filters) {
                    this.filters = data.filters;
                }
                if (data.range) {
                    this.range = data.range;
                }
                if (data.created_by) {
                    this.created_by = data.created_by;
                }

                this.pagination.current_page    = 0;
                this.pagination.count           = 0;
                this.pagination.per_page        = 0;
                this.getCommentsAndLogs('newFilter');
            },
            getCommentsAndLogs(clear) {

                this.fetchData = false;

                if (clear) {
                    this.$store.dispatch('task/clearActions');
                    this.$store.dispatch('actions/clearActions');
                }

                let action, actionId;

                if (this.$route.name === 'communication') {
                    action = 'getActionsByGroupId';
                    actionId = +this.$route.params.group_id
                } else if (this.$route.name === 'board') {
                    action = 'getActionsByBoardId';
                    actionId = +this.$route.params.board_id
                } else {
                    return;
                }

                this.$api.actions[action](actionId, this.pagination.current_page + 1, {
                    filters:    this.filters.names,
                    columns:    this.filters.columns,
                    range:      this.range,
                    created_by: this.created_by,
                    other:      this.other,
                    subscribed: this.subscribed
                }).then(res => {
                    this.pagination = res.pagination;
                    this.fetchData = true;
                });
            },
        }
    }

</script>

<style lang="scss" scoped>
    .group-board-info__item_filter{
        margin-left: auto;
        .comments-filter{
            position: static;
        }
    }
</style>
