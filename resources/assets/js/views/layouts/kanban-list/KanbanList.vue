<template>
    <div class="kanban-list">
        <div class="kanban-list-tabs">

            <div
                v-for="(board, index) in getBoardsByFilter"
                :key="board.id"
                class="kanban-list-tabs__item"
                @click="handleShowKanban(board)"
            >

                <drop
                    @drop="handleTaskDrop(board, ...arguments)"
                    @dragover="handleDragOver(board)"
                    @dragleave="handleDragLeave()"
                    :title="board.name"
                    :class="['kanban-list-tabs__header link_theme_default', {active: board.id === currentBoard.id, dropOver: board.id === dragOverId}]"
                >
                    <span class="kanban-list-tabs__txt">
                        {{ board.name }}
                    </span>
                </drop>

            </div>

        </div>

        <div class="kanban-list__content">
            <tasks-kanban :currentBoard="currentBoard" />
        </div>

    </div>

</template>

<script>
    import {mapGetters}         from 'vuex'
    import {Drop}               from 'vue-drag-drop'

    import TasksKanban          from '@views/partcials/TasksKanban/TasksKanban'

    export default {
		components: {
			TasksKanban,
			Drop
		},
        data() {
            return {
                currentBoard: {},
                dragOverId: null
            };
        },
        computed: {
            ...mapGetters({
                getBoards:      'groups/getBoards',
                getFilters:     'filters/getFilters',
                getPriorities:  'priorities/getPriorities',
				getUserId:      'user/getUserId',
            }),
            getCurrentFilter() {
                if (this.$route.name === 'filter') {
                    return this.getFilters.find(filter => filter.id === +this.$route.params.id)
                }
            },
            getBoardsByFilter() {
                if (this.getCurrentFilter.priority_ids) {
                    return this.getBoards.filter(board =>
                        this.getCurrentFilter.priority_ids.some(filter =>
                            this.getPriorities.find(priority => priority.id === filter).board_id === board.id
                        )
                    );
                }

                if (this.getCurrentFilter.board_ids) {
                    return this.getBoards.filter(board => this.getCurrentFilter.board_ids.some(filter => filter === board.id));
                }

                if (this.getCurrentFilter.group_ids) {
                    return this.getBoards.filter(board => this.getCurrentFilter.group_ids.some(filter => filter === board.group_id))
                }

                return this.getBoards;
            }
        },
        watch: {
            getBoardsByFilter: function () {
                this.setCurrentBoard();
            }
        },
        mounted() {
            this.setCurrentBoard()
        },
        methods: {
            handleTaskDrop(board, [taskId, task, oldBoardId]) {

                let form = {...task};
                form.task_id =  task.id;
                form.board_id = board.id;

                this.dragOverId = null;

                if (board.id === task.board_id) {
                    return
                }

                if (form.group_id !== board.group_id) {
                    return this.$notify({type:'error', text: this.$t('cant_move_another_group')});
                }

                this.$api.task.updateTask(form, task);
            },
            handleDragOver(board) {
                if (this.currentBoard.id !== board.id) {
                    this.dragOverId = board.id
                }
            },
            handleDragLeave() {
                this.dragOverId = null
            },
            handleShowKanban(board) {
                this.currentBoard = board;
                this.getTasksListByBoardId(board.id)
            },
            setCurrentBoard() {
                if (this.getBoardsByFilter.length) {
                    this.currentBoard = this.getBoardsByFilter[0];
                } else {
                    this.currentBoard = {}
                }
            },
			getTasksListByBoardId(id) {
				let params = '';
            	if (this.getCurrentFilter.status === 0) {
					params = 'hide_done=true';
                }

				this.$api.board.getBoardById(id, params)
					.then(response => {
						if (response.board.tasks) {
							this.handlerTasks(response.board.tasks);
						}
					});
			},
			handlerTasks(tasks) {
            	let tasksList = [];
                const taskIds = this.getCurrentFilter.assigner_ids;

            	if (taskIds) {
                  tasks.filter(item => {
                    taskIds.forEach(assign => {
                      if (item.subscribers.task.includes(assign)) {
                        tasksList.push(item);
                      }
                    });
                  });
                } else {
                  tasksList = tasks;
                }

                this.$store.dispatch('task/setTasksList', tasksList);
            }
		},
    };
</script>

<style lang="scss">
    .kanban-list{
        padding: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .kanban-list-tabs{
        display: flex;
        border-bottom: 1px solid #5078f2;
        margin-bottom: 5px;
        overflow-y: hidden;
    }
    .kanban-list-tabs__header {
        padding: 5px;
        vertical-align: middle;
        border: 2px solid transparent;
        border-radius: 5px 5px 0 0;
        cursor: pointer;
        background-color: #fff;
        box-shadow: 0px -1px 1px 0 rgba(0, 0, 0, 0.2);
        height: 35px;
        overflow: hidden;
        max-width: 160px;
        text-align: center;
    }
    .kanban-list-tabs__item{ 
        margin-right: 5px;
        &:last-child{
            margin-right: 0;
        }
    }
    .kanban-list-tabs__header {
        &:hover, &:focus{
            background-color: #5078f2;
            color: #fff;
            box-shadow: none;
        }
    }
    .kanban-list-tabs__header.active{
        background-color: #5078f2;
        color: #fff;
        box-shadow: none;
    }
    .kanban-list-tabs__header.dropOver{
        background-color: #6ccecd;
        color: #fff;
    }
    .kanban-list__content{
        margin: 0;
        height: calc(100% - 48px);
    }
    .kanban-list-tabs__txt{
        pointer-events: none;
    }

</style>
