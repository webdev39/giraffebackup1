<template>
    <div class="task-item-title">
        <div class="checkbox-wrapper" style="display: inline-block;">
            <input type="checkbox" v-model="getTaskIsCompleted" @click.stop>
            <span class="checkbox checkbox_theme_blue"></span>
        </div>
        <span
            @click="showTaskDetails(task.id)"
            class="task-item-title-text"
            :style="{
                'text-decoration': getTaskIsCompleted ? 'line-through': 'none'
            }"
        >
            {{ task.name }}
        </span>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import find from "@helpers/findInGroups";

    export default {
        props: {
            task: {
                type: Object
            }
        },
        computed: {
            ...mapGetters({
                getMembers: 'members/getMembers',
                getPriorities: 'priorities/getPriorities',
                getUserId: 'user/getUserId',
            }),
            getTaskIsCompleted: {
                get() {
                    return this.task.done_by;
                },
                set(complete) {
                    this.taskIsCompleted = complete;
                    this.changeTaskWorkflow(this.task.id);
                    return this.taskIsCompleted
                }
            },
        },
        methods: {
            changeTaskWorkflow(taskId) {
                this.$api.task.changeWorkflowTask({
                    task_id: taskId,
                    is_done: this.taskIsCompleted
                }).then(() => {
                    if (this.$route.name === 'filter') {
                        this.$api.task
                            .getTasksByFilterId(this.$route.params.id)
                            .catch(err => {
                                this.$notify({type: 'error', text: err.response.data.message});
                            })
                    }
                }).catch(err => {
                    this.$notify({type: 'error', text: err.response.data.message});
                });
            },
            showTaskDetails(id) {
                let task = find.searchTaskById(this.$store.getters['groups/getStateGroups'], id);

                if (task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', task.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                this.$router.replace({query: {taskId: +id}});
            },
        }
    }
</script>

<style lang="scss">
    .task-item-title{
        display: flex;
        align-items: center;
        .task-item-title-text{
            cursor: pointer;
            margin-top: 2px;
            margin-left: 2px;
        }
    }
</style>