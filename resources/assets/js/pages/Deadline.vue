<template>
    <div class="task-list deadline-page">

        <transition name="fade">

            <div
                v-if="!loadingPage"
                class="task-list-holder"
            >
                <fixed-component>
                    <deadline-info/>
                    <task-create :redirect="false" />
                </fixed-component>

                <tasks-drag :filterFor="filter"/>
            </div>

        </transition>


    </div>
</template>

<script>
    import { mapGetters }     from 'vuex'

    import TaskListLoop     from '@views/partcials/TaskList/TaskListLoop'
    import TaskCreate       from '@views/components/task/TaskCreate'
    import TasksDrag        from "@views/partcials/TasksDrag/TasksDrag"
    import FixedComponent   from '@views/components/fixedComponent/FixedComponent'
    import DeadlineInfo     from '@views/partcials/DeadlineInfo/DeadlineInfo';

    export default {
		components: {
			TaskCreate,
			TaskListLoop,
			TasksDrag,
			FixedComponent,
			DeadlineInfo
		},
        data() {
			return {
				loadingPage: true
            }
        },
        computed: {
            ...mapGetters({
                getTasksDeadline: 'groups/getTasksDeadline'
            }),
            period() {
                return this.$route.params ? this.$route.params.period : null;
            },
            filter() {
                if (this.period === 'day') {
                    return "today";
                } else if (this.period === 'week') {
                    return "week";
                } else {
                    return null;
                }
            },
        },
        watch: {
            filter(period) {
                if (period) {
                    this.updateData();
                }
            },
        },
        mounted() {
            this.updateData();
			this.$store.dispatch('setPagePreloader', false);
		},
        beforeDestroy(){
            this.$store.dispatch('groups/clearTasksIds');
        },
        methods: {
			/**
             * Method for get tasks using id
			 * @param id
			 */
			getTasksListByIds(id) {
				this.loadingPage = true;

				this.$api.task.getTasksListByIds(Object.keys(id))
					.then(response => {
						this.$store.dispatch('task/setTasksList', response.tasks);

						if (response.tasks.length) {
							this.$store.dispatch('groups/setTasksListByBoardId', {
								tasks: response.tasks
							});
                        }
						this.loadingPage = false;
					});
			},
            updateData() {
                let tasksIds = {};

                this.getTasksDeadline[this.filter].filter(item => {
                    tasksIds[item.task_id] = item.sort_order
                });

                this.getTasksListByIds(tasksIds);
            }
        }
    }
</script>

<style lang="scss">
    .task-list {
        .page-preloader-holder {
            background-color: transparent;
        }
    }
</style>
