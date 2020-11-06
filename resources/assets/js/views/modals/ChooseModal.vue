<!--Optimized-->
<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :class="{'v--modal--task': type === 'task'}"
        :maxWidth="700"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        width="100%"
        height="auto"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
        <modal-wrapper :name="$options.name">
               <template slot="header">
                <theme-button-close
                    class="btn-close-header"
                    @click.native="closeModal"
                >
                    <i class="icon-close" >
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
              </template>
            <template slot="title">
                <span class="modal-headline weight-normal">{{ getTitle }}</span>
                <h5 v-if="description">{{ description }}</h5>
            </template>

            <template slot="body">
                <div class="modal-groups-last-boards" v-if="lastItems.length">
                    <p>{{ $t('last_task_title') }}</p>
                    <div class="modal-groups-last-item" v-for="lastItem in lastItems" :class="{active: lastItem.id === selectItem.id }" @click="type === 'task' ? chooseTask(lastItem) : toggleChooseItem(type, lastItem)">
                        <span v-if="type === 'board'">
                            <i class="icon-groups" aria-hidden="true">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                   <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-boards"></use>
                                </svg>
                            </i>
                        </span>

                        {{lastItem.name}}
                    </div>
                </div>

                <div class="modal-groups-list">
                    <template v-for="group in getGroups" v-if="showArchiveGroup ? showArchiveGroup : !group.is_archive">
                        <div class="modal-groups-list-item" :class="{active: group.id === select.group.id }">
                            <span>
                                <i class="icon-groups" aria-hidden="true">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                       <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-groups"></use>
                                    </svg>
                                </i>
                            </span>
                            <span class="modal-groups-name-val" @click="toggleChooseItem('group', group)">
                               {{group.name}}
                            </span>
                        </div>

                        <div class="modal-groups-boards-list" v-if="select.group.id === group.id">
                            <template v-for="board in group.boards" v-if="showArchiveBoard ? showArchiveBoard : !board.is_archive">
                                <div class="modal-groups-board-item" :class="{active: board.id === select.board.id}" >
                                    <span class="modal-groups-board-icon">
                                        <i class="icon-boards">
                                           <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                               <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-boards"></use>
                                            </svg>
                                        </i>
                                    </span>

                                    <span class="modal-groups-board-name-val"  @click="toggleChooseItem('board', board)">
                                        {{board.name}}
                                    </span>

                                    <div class="choose-task-row-button" v-if="select.board.id === board.id && type === 'board'">
                                        <div class="modal-groups-add-task" v-if="showCreateTask && !board.is_archive">
                                            <input class="modal-groups-add-task-input form-control" v-model="task.name" type="text">
                                            <button class="modal-groups-add-task-button btn btn-xs button__color_primary" @click="handlerCreateTask">
                                                {{ $t("create")}}
                                            </button>
                                        </div>

                                        <button class="btn btn-xs button__color_danger" @click="toggleDoneTask" v-if="type === 'task' && hasDoneTask(board.tasks)">
                                            {{ showDoneTask ? 'Hide Done' : 'Show Done' }}
                                        </button>

                                        <button class="btn btn-xs button__color_primary" @click="toggleCreateTask" v-if="type === 'task' && !board.is_archive" >
                                            {{ showCreateTask ? 'Close' : 'Add Task' }}
                                        </button>
                                    </div>

                                    <div class="modal-groups-tasks-list" v-if="select.board.id === board.id && type === 'task'">
                                        <template v-for="task in board.tasks">
                                            <div class="modal-groups-tasks-item" v-if="!task.done_by || showDoneTask" :class="{active: task.id === select.task.id }" @click="chooseTask(task)">
                                                {{task.name}}
                                            </div>
                                        </template>

                                        <div class="choose-task-row-button">
                                            <div class="modal-groups-add-task" v-if="showCreateTask && !board.is_archive">
                                                <input class="modal-groups-add-task-input form-control" v-model="task.name" type="text">
                                                <button class="modal-groups-add-task-button btn btn-xs button__color_primary" @click="handlerCreateTask">
                                                    {{ $t("create")}}
                                                </button>
                                            </div>

                                            <button class="btn btn-xs button__color_danger" @click="toggleDoneTask" v-if="hasDoneTask(board.tasks)">
                                                {{ showDoneTask ? 'Hide Done' : 'Show Done' }}
                                            </button>

                                            <button class="btn btn-xs button__color_primary" @click="toggleCreateTask" v-if="!board.is_archive" >
                                                {{ showCreateTask ? 'Close' : 'Add Task' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="modal-groups-row-button" v-if="hasArchive(group.boards)">
                                <button class="btn btn-xs button__color_danger"  type="button" @click="toggleArchiveBoard">
                                    {{ showArchiveBoard ? $t('hide_archive') : $t('show_archive') }}
                                </button>
                            </div>
                        </div>
                    </template>
                    <div class="modal-groups-row-button" v-if="hasArchive(getGroups)">
                        <button class="btn btn-xs button__color_danger"  type="button" @click="toggleArchiveGroup">
                            {{ showArchiveGroup ? $t('hide_archive') : $t('show_archive') }}
                        </button>
                    </div>
                </div>
            </template>

        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }         from 'vuex'

    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import task                 from '@helpers/task';
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "choose-modal",
		components: {
			ModalWrapper,
			ThemeButtonSuccess,
			ThemeButtonClose
		},
        data() {
            return {
                type:           null,
                permission:     null,
                callback:       null,
                select: {
                    group:  {},
                    board:  {},
                    task:   {},
                },
                showConfirm: true,
                task: {
                    name: null,
                },
                showArchiveBoard:   false,
                showDoneTask:       false,
                showCreateTask:     false,
                showArchiveGroup:   false,
                titleModal: {
                    board: this.$t("choose_board_title"),
                    task: this.$t("choose_task_title")
                },
            }
        },
        computed: {
            ...mapGetters({
                groups:         'groups/getStateGroups',
                lastTasks:      'groups/getLastTasks',
                lastBoards:     'groups/getLastBoards',
                getFilters:     'filters/getFilters',
                getOwner:       'members/getOwner',
                getPriorities:  'priorities/getPriorities',
            }),
            getTitle() {
                const type = this.type;
                return this.titleModal[type];
            },
            getFilterId() {
                return this.$route.params ? this.$route.params.id : null;
            },
            getFilter() {
                if (this.getFilters.length && this.getFilterId) {
                    return this.getFilters.find(item => item.id === Number(this.getFilterId));
                }
            },
            isFilterTimeRange () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.range || this.getFilter.range === 'today' || this.getFilter.range === 'yesterday' || this.getFilter.range === 'tomorrow' ) {
                    return true
                }
            },
            isFilterGroup () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.group_ids) {
                    return true
                }

                return this.getFilter.group_ids.some(item => item === this.select.board.group_id);
            },
            isFilterBoard () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.board_ids) {
                    return true
                }

                return this.getFilter.board_ids.some(item => item === this.select.board.id);
            },
            isFilterStatus () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.status) {
                    return true
                }
            },
            isFilterAssigned () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.assigner_ids) {
                    return true
                }

                return this.getFilter.assigner_ids.some(item => item === this.getOwner.id);
            },
            isFilterPriority () {
                if (!this.getFilter) {
                    return false
                }

                if (!this.getFilter.priority_ids) {
                    return true
                }

                let priorities = [], findPriorities;

                this.getFilter.priority_ids.forEach(priority => {
                    findPriorities = this.getPriorities.find(item => item.id === priority && item.board_id === this.select.board.id)

                    if (findPriorities) {
                        priorities.push(findPriorities);
                    }
                });

                if (priorities.length === 1) {
                    return priorities
                }

                return false;
            },
            title() {
                return this.type
            },
            description() {
                if (Object.keys(this.selectItem).length) {
                    return `Current ${this.type} - ${this.selectItem.name}`
                }

                return null;
            },
            selectItem() {
                if (this.type === "task" && Object.keys(this.select.task).length) {
                    return this.select.task
                }

                if (this.type === "board" && Object.keys(this.select.board).length) {
                    return this.select.board
                }

                return {};
            },
            lastItems() {
                if (this.type === "task" && this.lastTasks) {
                    return this.lastTasks
                }

                if (this.type === "board" && this.lastBoards) {
                    return this.lastBoards
                }

                return [];
            },
            getGroups() {
                let groups = this.groups.filter(item => this.showGroupWithTimeTracking(item.id)).map((group) => {
					let boards = group.boards.sort((a, b) => {
                        return sorter(a.name, b.name);
                    });

                    return Object.assign({}, group, { boards });
                });

                return groups.sort((a, b) => {
                    return sorter(a.name, b.name);
                });
            }
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return this.closeModal();
                }

                if (event.params.type) {
                    this.type = event.params.type;
                }

                if (event.params.permission) {
                    this.permission = event.params.permission;
                }

                if (event.params.callback) {
                    this.callback = event.params.callback;
                }

                if (this.type === "task") {
                    this.$api.task.getLastTasks();
                } else {
                    this.$api.board.getLastBoards().catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                }
            },
            beforeClose() {
                this.resetComponentData();
            },
            showGroupWithTimeTracking (groupId) {
                if (this.permission === 'time-tracking') {
                    return this.handlePermissionByGroupId('time-tracking', groupId)
                }

                return true;
            },
            closeModal() {
                this.$modal.hide('choose-modal')
            },
            hasArchive(items) {
                if (items) {
                    return !!items.find(item => item.is_archive);
                }

                return false;
            },
            hasDoneTask(items) {
                if (items) {
                    return !!items.find(item => item.done_by);
                }

                return false;
            },
            toggleArchiveBoard() {
                this.showArchiveBoard = !this.showArchiveBoard
            },
            toggleArchiveGroup() {
                this.showArchiveGroup = !this.showArchiveGroup
            },
            toggleDoneTask() {
                this.showDoneTask = !this.showDoneTask
            },
            toggleCreateTask() {
                this.showCreateTask = !this.showCreateTask
            },
            toggleChooseItem(key, value) {
				if (this.select[key].id === value.id) {
                    return this.select[key] = {};
                }

                this.select[key] = value;

                if (key === 'board' && this.$route.name === "filter") {
                    this.showFilterSetting();
                }

                if (this.type === "board" && key === 'board') {
                    this.handlerSave();
                }
            },
            chooseTask(value) {
                this.toggleChooseItem('task', value);
                this.handlerSave();
            },
            showFilterSetting() {
                if (!this.isFilterGroup || !this.isFilterBoard || !this.isFilterAssigned || !this.isFilterPriority || !this.isFilterStatus || !this.isFilterTimeRange) {
                    this.$notify({type:'error', text: 'This task has been created under conflicting conditions in the filter setting.' +
                        ' <a class="link_theme_default" onclick="window.app.$modal.show(\'filter-setting-modal\', {filterId: +window.app.$route.params.id})">Press here</a>' +
                        ' to change filter conditions' });
                }
            },
            handlerCreateTask() {

                let dataTask = {
                        name:       this.task.name.trim(),
                        board_id:   this.select.board.id,
                        is_draft:   0,
                };

                if (this.isArray(this.isFilterPriority) && this.isFilterPriority.length) {
                    dataTask.priority_id = this.isFilterPriority[0].id
                }

                if (this.isFilterTimeRange) {
                    switch (this.getFilter.range) {
                        case 'yesterday':
                            dataTask.planned_deadline = this.$moment.utc(new Date()).add(-1,'days').format("YYYY-MM-DD HH:mm:ss");
                            break;
                        case 'tomorrow':
                            dataTask.planned_deadline = this.$moment.utc(new Date()).add(1,'days').format("YYYY-MM-DD HH:mm:ss");
                            break;
                        case 'today':
                            dataTask.planned_deadline = this.$moment.utc().format("YYYY-MM-DD HH:mm:ss")
                    }
                }

                this.$api.task.createTask(dataTask).then(res => {
                    this.task.name      = null;
                    this.showCreateTask = false;

                    task.updateCountTask(res.task);

                    if (this.$route.name === 'filter') {
                        this.$api.task.getTasksByFilterId(this.$route.params.id).catch(err => {
                            this.$notify({type:'error', text: err.response.data.message});
                        })
                    }

                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handlerSave() {
                if (this.selectItem && this.selectItem.id) {
                    if (this.isFunction(this.callback)) {
                        this.callback(this.selectItem);
                    }

                    this.showConfirm = false;

                    this.closeModal();
                } else {
                    this.$notify({ type:'error', text: this.$t('choose_board') });
                }

            }
        }
    }
</script>

<style lang="scss">
    #choose-modal {
        overflow: hidden;
        .modal-groups-list {
            min-width: 300px;
        }

        .modal-groups-list-item,
        .modal-groups-last-item,
        .modal-groups-board-item,
        .modal-groups-tasks-item {
            margin-bottom: 2px;
            color: #585858;
            cursor: pointer;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            /* line-height: 16px; */
            /* max-height: 32px; */
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;

            &:last-child {
                margin-bottom: 0;
            }

            &:hover,
            &.active {
                color: #4f77f2;
            }
        }
        .modal-groups-tasks-item {
            position: relative;
            padding-left: 20px;
            cursor: pointer;
                &:before {
                    content: "";
                    position: absolute;
                    width: 12px;
                    height: 12px;
                    background: #000;
                    left: 0;
                    top: 5px;
                }
        }
        .modal-groups-last-item {
            position: relative;
            padding-left: 45px;
            cursor: pointer;
                &:before {
                    content: "";
                    position: absolute;
                    width: 12px;
                    height: 12px;
                    background: #000;
                    left: 25px;
                    top: 5px;
                }
        }

        .icon-groups,
        .icon-boards {
            display: inline-block;
            vertical-align: middle;
            fill: #333;

            .icon {
                width: 14px;
                height: 14px;
                display: block;
            }
        }

        .modal-groups-last-boards {
            max-height: 90px;
            background-color: lightgray;
            margin: -15px -15px 0 -15px;
            margin-bottom: 20px;
            overflow: auto;
            padding: 10px 15px;
        }

        .modal-groups-list-item {
            margin-bottom: 5px;
        }

        .modal-groups-boards-list {
            width: 100%;
            padding-left: 20px;
        }

        .modal-groups-tasks-list {
            padding-left: 20px;
            margin-bottom: 10px;
        }

        .modal-groups-name-val {
            cursor: pointer;
        }

        .modal-groups-add-task {
            display: flex;
            margin-bottom: 10px;
        }

        .modal-groups-add-task-button {
            margin-left: 10px;
        }

        .modal-groups-add-task-input {
            width: 100%;
            height: 28px;
        }

        .modal-groups-tasks-unarchived {
            .modal-groups-tasks-item {
                cursor: pointer;
                text-decoration: underline;

                &:hover {
                    color: #4f77f2;
                }
            }
        }

        .modal-groups-tasks-item.active {
            color: #4f77f2;
            text-decoration: none;
        }

        .modal-groups-row-button {
            margin-bottom: 5px;
        }

        .choose-task-row-button button {
            color: #fff;
        }
        .btn-close-header {
            background: transparent;
            border:none;
            box-shadow: none;
            fill:#fff;
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
                &:hover {
                    background: transparent; 
                    border:none;
                    box-shadow: none;
                    fill:#e2e6e9;
                }
            .icon-close {
                display: block;
                 .icon {
                     width: 14px;
                     height: 14px;
                 }
            }
        }
    }
</style>
