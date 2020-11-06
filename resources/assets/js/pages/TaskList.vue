<template>
    <div class="task-list" :class="getViewTypeName">

        <transition name="fade">

            <div
                v-if="!loadingPage"
                class="task-list-holder"
            >
                <template v-if="getViewTypeName === 'List'">
                    <fixed-component is-scroll >
                        <group-board-info />
                        <task-create/>
                    </fixed-component>
                    <tasks-drag :filterFor='"list"'/>
                </template>

                <template v-if="getViewTypeName === 'Kanban'">
                    <fixed-component is-scroll :fixed="true">
                        <group-board-info/>
                        <task-create :in-board="true"/>
                        <div class="pb-1"></div>
                    </fixed-component>
                    <tasks-kanban/>
                </template>

                <communication v-if="getViewTypeName === 'Communication'" :showReply="true"/>

                <tasks-calendar v-if="getViewTypeName === 'Calendar'"/>

                <template v-if="getViewTypeName === 'Gantt'">
                    <fixed-component is-scroll>
                        <group-board-info/>
                        <task-create/>
                        <div class="pb-1"></div>
                    </fixed-component>
                    <gantt/>
                </template>
            </div>

        </transition>

    </div>
</template>

<script>
    import { mapGetters }   from 'vuex'
    import GroupsMixin      from '@mixins/groups'

    import Gantt            from '@views/layouts/gantt/Gantt';
    import TaskCreate       from '@views/components/task/TaskCreate';
    import TasksDrag        from '@views/partcials/TasksDrag/TasksDrag';
	import TasksKanban      from '@views/partcials/TasksKanban/TasksKanban';
    import TasksCalendar    from '@views/partcials/TasksCalendar/TasksCalendar';
	import GroupBoardInfo   from '@views/partcials/GroupBoardInfo/GroupBoardInfo';
    import Communication    from '@views/layouts/task/communication/communication';
    import FixedComponent   from '@views/components/fixedComponent/FixedComponent';
    import { _setDocumentTitle, _setDefaultDocumentTitle } from '@helpers/controlDocumentTitle';

    export default {
        name: "task-list",
		components: {
			TasksKanban,
			TasksCalendar,
			GroupBoardInfo,
			TaskCreate,
			TasksDrag,
			Communication,
			FixedComponent,
			Gantt,
		},
		mixins: [
			GroupsMixin
		],
        data() {
        	return {
				loadingPage: true
            }
        },
        computed: {
            ...mapGetters({
                getViewTypes:       "default/getViewTypes",
                getCurrentBoard:    "groups/getCurrentBoard",
				getStateGroups:     "groups/getStateGroups"
            }),
            getViewTypeName() {
                if (this.getCurrentBoard) {

                    let viewTypeId, viewTypeName;

                    if (this._getUserViewTypeId) {
                        viewTypeId = +this._getUserViewTypeId;
                    } else {
                        viewTypeId = this.getCurrentBoard.view_type_id;
                    }

                    this.getViewTypes.find(item => {
                        if (item.id === viewTypeId) {
                            return viewTypeName = item.name;
                        }
                    });

                    return viewTypeName;
                }
            },
        },
        watch: {
			'$route'(to, from) {
				if (to.path != from.path) {
				    this.setCurrentBoard();
                }
			},
            'getCurrentBoard': function() {
                this.setTaskListTitle();
            },
        },
        mounted() {
            this.$nextTick(function () {
                this.$store.dispatch('setPagePreloader', false);

                this.setCurrentBoard();

                if (this.getCurrentBoard === null) {
                    this.$notify({type:'error', text: this.$t('board_not_found')});
                    return this.$router.push({ name: "not-found"})
                }
			});
        },
        beforeRouteUpdate (to, from, next) {
            if (!to.query.taskId) {
                this.setTaskListTitle();
            }
            next();
        },
        beforeRouteLeave(to, from, next) {
            this.$store.dispatch('groups/clearCurrentBoardId');
            _setDefaultDocumentTitle();
            next();
        },
        methods: {
            setTaskListTitle() {
                if (this.getCurrentBoard) {
                    _setDocumentTitle(this.getCurrentBoard.name);
                }
            },
            setCurrentBoard() {
                this.$store.dispatch('groups/setCurrentBoardId', Number(this.$route.params.board_id));
				this.getTasksList();
            },
			/**
             * Method for get tasks list for current board
			 */
			getTasksList() {
				this.loadingPage = true;

                this.$api.task.getTaskByBoardId(this.$route.params.board_id, this._getCurrentBoard.hide_done_tasks)
                    .then(response => {
						this.loadingPage = false;
                    });
            }
        }
    }
</script>

<style lang="scss">
    .task-list {
        height: auto;

        .page-preloader-holder {
            background-color: transparent;
        }
    }
    .pb-1 {
        padding-bottom: 15px
    }
</style>
