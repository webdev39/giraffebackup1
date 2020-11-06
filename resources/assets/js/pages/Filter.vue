<template>
    <div class="task-list filter-page" :class="getFilterView">
        <fixed-component is-scroll :fixed="true">
            <div class="row task-wrapper task-list-options">

                <div class="filter-name-container">
                    <h4 class="filter-name">
                        <i class="icon-clipboard">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-clipboard"></use>
                            </svg>
                        </i>
                        {{ getFilterName }}
                    </h4>
                </div>

                <subscribersByTask
                    v-if="getFilter"
                    :members="getFilter.assigner_ids"
                    class="filter-subscribers"
                />

                <div class="filter-list">
                    <div class="filter-item filter-item_desktop ">
                        <div class="filter-item_mobile">
                            <i class="icon-groups">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-sitemap">
                                    </use>
                                </svg>
                            </i>
                            {{ getFilterCount.groups }}
                        </div>
                    </div>
                    <div class="filter-item filter-item_desktop">
                        <div class="filter-item_mobile">
                            <i class="icon-boards">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-boards">
                                    </use>
                                </svg>
                            </i>
                            {{ getFilterCount.boards }}
                        </div>
                    </div>
<!--                     <div class="filter-item filter-item_desktop ">
                        <div class="filter-item_desktop">
                            <h4 class="filter-list__title">{{ $t('assigners')}}</h4>
                            <div v-for="assigner in getFilterAssigners">
                                <div class="text-align-center">
                                    <p>{{assigner.user.name}} {{assigner.user.last_name}}</p>
                                </div>
                            </div>
                            <div v-if="!getFilterAssigners">
                                <p>{{ $t('all')}}</p>
                            </div>
                        </div>
                        <div class="filter-item_mobile">
                            <i class="icon-members" :title="$t('Members')">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-user">
                                    </use>
                                </svg>
                            </i>
                            {{ getFilterCount.members }}
                        </div>
                    </div> -->
                    <div class="filter-item">

                        <div class="filter-item_mobile">
                            <i class="icon-priorities">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-priorities">
                                    </use>
                                </svg>
                            </i>
                            <div v-for="priority in getFilterPriorities">
                                <div>
                                    <p>{{priority.name}}</p>
                                </div>
                            </div>
                            <div v-if="!getFilterPriorities">
                                <p>All</p>
                            </div>
                        </div>
                    </div>

                    <div class="filter-item">
                         <div class="filter-item_mobile">
                            <i class="icon-status">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-status">
                                    </use>
                                </svg>
                            </i>
                            <div>
                                <p> {{ getFilterStatus ? getFilterStatus : $t('all') }} </p>
                            </div>
                        </div>
                    </div>


                    <div class="filter-item">
                        <div class="filter-item_mobile">
                            <i class="icon-calendar">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-calendar">
                                    </use>
                                </svg>
                            </i>
                            <div>
                                <p> {{ getFilterRange ? getFilterRange : $t('all') }} </p>
                            </div>
                        </div>
                    </div>
                    <div class="filter-item">
                        <div class="filter-item_mobile">
                            <i class="icon-view">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-view">
                                    </use>
                                </svg>
                            </i>
                            <div>
                                <p>{{getFilterView}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-setting">
                    <dropDown>
                        <div class="info-setting__list">
                            <div class="info-setting__item">
                                <div class="info-setting__item-text">
                                    Sorting:
                                </div>
                                <div class="info-setting__item-control">
                                    <board-task-sorting :sortTypes="getSortTypes" />
                                </div>
                            </div>
                        </div>
                    </dropDown>
                </div>

            </div>

            <task-create :redirect="false" />

        </fixed-component>

        <template v-if="getFilterView === 'List'">
            <tasks-drag :filterFor='"filter"' />
        </template>

        <template v-if="getFilterView === 'Kanban'">
            <kanban-list />
        </template>

        <template v-if="getFilterView === 'Calendar'">
            <tasks-calendar :showGroupBoardInfo="false" :showTaskCreate="false" />
        </template>

    </div>

</template>

<script>
    import { mapGetters }       from 'vuex'
    import FixedComponent       from '@views/components/fixedComponent/FixedComponent'
    import TaskCreate           from '@views/components/task/TaskCreate'
    import SubscribersByTask    from '@views/components/subscribers/subscribersByTask'
    import dropDown             from '@views/components/dropDown/dropDown'
    import boardTaskSorting     from '@views/partcials/BoardTaskSorting/BoardTaskSorting'
	import TasksDrag            from '@views/partcials/TasksDrag/TasksDrag'
	import KanbanList           from '@views/layouts/kanban-list/KanbanList'
    import TasksCalendar        from '@views/partcials/TasksCalendar/TasksCalendar'
    import { _setDocumentTitle, _setDefaultDocumentTitle } from '@helpers/controlDocumentTitle';

    export default {
		components: {
			TasksDrag,
			TaskCreate,
			TasksCalendar,
			KanbanList,
			FixedComponent,
			SubscribersByTask,
			dropDown,
			boardTaskSorting
		},
        data() {
            return {
                statuses: [{
                    id: 0,
                    name: 'Open',
                    value: 0,
                    color:'#ff9800'
                }, {
                	id: 1,
                    name: 'Done',
                    value: 1,
                    color:'green'
                }]
            }
        },
        computed:{
            ...mapGetters({
                getFilters:     'filters/getFilters',
                getGroups:      'groups/getStateGroups',
                getPriorities:  'priorities/getPriorities',
                getMembers:     'members/getMembers',
                viewTypes:      'default/getViewTypes',
                getBoards:      'groups/getBoards',
            }),
            getSortTypes() {
                return this.$store.getters['groups/getSortTypes'].filter(item => item.name !== 'todo' )
            },
            getFilterId() {
                return this.$route.params ? this.$route.params.id : null;
            },
            getFilter() {
                if (this.getFilters.length && this.getFilterId) {
                    return this.getFilters.find(item => item.id === Number(this.getFilterId));
                }
            },
            getFilterAssignersLength() {
                return this.getFilterAssigners && this.getFilterAssigners.length;
            },
            getFilterCount() {
                let count = { groups: 0, boards: 0, members: 0};

                let groups  = new Set();
                let boards  = new Set();
                let members = new Set();

                const addCount = (board, group) => {
                    groups.add(board.group_id);
                    boards.add(board.id);

                    if (!this.getFilterAssignersLength) {
                        for (let item of group.members) {
                            members.add(item)
                        }
                    }
                };

                if (this.getFilter && this.getFilter.priority_ids) {
                    this.getGroups.forEach(group => {
                        group.boards.forEach(board => {
                            if (this.getFilter.priority_ids.some(filter => this.getPriorities.find(priority => priority.id === filter).board_id === board.id )) {
                                addCount(board, group);
                            }
                        });
                    });

                    count.groups = groups.size;
                    count.boards = boards.size;
                    count.members = this.getFilterAssignersLength ? this.getFilterAssignersLength : members.size;

                    return count;
                }

                if (this.getFilter && this.getFilter.board_ids) {

                    this.getGroups.forEach(group => {
                        group.boards.forEach(board => {
                            if (this.getFilter.board_ids.some(filter => filter === board.id)) {
                                addCount(board, group);
                            }
                        });
                    });

                    count.groups = groups.size;
                    count.boards = boards.size;
                    count.members = this.getFilterAssignersLength ? this.getFilterAssignersLength : members.size;

                    return count;
                }

                if (this.getFilter && this.getFilter.group_ids) {
                    this.getGroups.forEach(group => {
                        group.boards.forEach(board => {
                            if (this.getFilter.group_ids.some(filter => filter === board.group_id)) {
                                addCount(board, group);
                            }
                        });
                    });

                    count.groups = groups.size;
                    count.boards = boards.size;
                    count.members = this.getFilterAssignersLength ? this.getFilterAssignersLength : members.size;

                    return count;
                }

                if (!this.getFilterAssignersLength) {
                    this.getGroups.forEach(group => group.members.forEach(member => members.add(member)));
                }

                count.groups = this.getGroups.length;
                count.boards = this.getBoards.length;
                count.members = this.getFilterAssignersLength ? this.getFilterAssignersLength : members.size;

                return count;
            },
            getFilterPriorities() {
                if (!this.getFilter || !this.getFilter.priority_ids) {
                    return false
                }

                return this.getPriorities.filter(priority => this.getFilter.priority_ids.some(item => item === priority.id))
            },
            getFilterAssigners() {
                if (!this.getFilter) {
                    return false
                }

                if (this.getFilter.assigner_ids) {
                    let members = [];

                    this.getFilter.assigner_ids.map(assigner => {
                        this.getMembers.some(member => {
                            if (member.id === assigner) {
                                members.push(member);
                            }
                        })
                    });

                    return members
                }
            },
            getFilterGroups() {
                if (!this.getFilter) {
                    return false
                }

                if (this.getFilter.group_ids) {
                    let groups = [];

                    this.getFilter.group_ids.map(groupId => {
                        this.getGroups.some(group => {
                            if (group.id === groupId) {
                                groups.push(group);
                            }
                        })
                    });

                    return groups;
                }
            },

            getFilterBoards() {
                if (!this.getFilter) {
                    return false
                }

                if (this.getFilter.board_ids) {
                    let board = [];

                    this.getFilter.board_ids.map(boardId => {
                        this.getGroups.map(group => {
                            group.boards.some(item => {
                                if (item.id === boardId) {
                                    board.push(item);
                                }
                            })
                        })
                    });

                    return board;
                }
            },
            getFilterStatus() {
                if (this.getFilter && this.getFilter.status !== null) {
                    return this.statuses[this.getFilter.status].name
                }
            },
            getFilterRange() {
                if (this.getFilter) {
                    return this.getFilter.range
                }
            },
            getFilterName() {
                if (this.getFilter) {
                    return this.getFilter.name
                }
            },
            getFilterView() {
                if (this.getFilter) {
                    return this.viewTypes.find(item => item.id === this.getFilter.view_type_id).name;
                }
            }
        },
        watch: {
            '$route'(to, from) {
                this.$store.dispatch('groups/setDefaultSelectSortType');
            },
            getFilterId(value) {
                if (value) {
                    this.updateData();
                }
            },
            'getFilterName': function() {
              _setDocumentTitle(this.getFilterName);
            },
        },
        mounted() {
            this.updateData();

            this.$nextTick(function () {
                this.$store.dispatch('groups/changeSelectSortType', this.getSortTypes[0]);
				this.$store.dispatch('setPagePreloader', false);
                _setDocumentTitle(this.getFilterName);
            });
        },
        beforeDestroy(){
            this.$store.dispatch('groups/clearTasksIds');
        },
        // beforeRouteUpdate (to, from, next) {
		//     console.log('this.getFilterName', this.getFilterName);
        //     _setDocumentTitle(this.getFilterName);
        //     next();
        // },
        beforeRouteLeave(to, from, next) {
            _setDefaultDocumentTitle();
            next();
        },
        methods: {
            updateData() {
                this.$api.task.getTasksByFilterId(this.$route.params.id)
                    .catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    })
            }
        },
    }
</script>
