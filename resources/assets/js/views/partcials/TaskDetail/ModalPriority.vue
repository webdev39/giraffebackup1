<template>
    <div class="details-modal priority-modal">
        <button type="button" class="btn btn-lg btn_details-modal_close">
            <i class="icon-close" @click="$emit('hide')">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                    xlink:href="#icon-close">
                    </use>
                </svg>
            </i>
        </button>
        <ul>
            <li class="priority-modal-item"
                v-for="(priority, index) in priorities"
                :key='index'
                @click="changeTaskPriority(priority.id)"
            >
                <i class="fa fa-square" aria-hidden="true" :style="{ color: priority.color}"></i>

                <span class="details-task-text">
                    {{ priority.name }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
    import { mapGetters }      from 'vuex'
	import moment from "moment";

    export default {
        name: "modal-priority",
        props: {
            task: {
                type: Object,
            },
        },
        computed: {
            ...mapGetters({
                getPriorities:  'priorities/getPriorities',
                getUserId:      'user/getUserId',
            }),
            priorities() {
                return this.getPriorities
                    .filter(priority => priority.board_id === this.task.board.id)
                    .sort((a,b) => a.sort_order - b.sort_order);
            },
        },
        methods: {
            changeTaskPriority(priorityId) {
                if (this.task.creator_id !== this.getUserId && !this.handlePermissionByGroupId('update-task', this.task.group_id)) {
                    return this.sendNotifyPermissionInfo('update-task');
                }

                let data = Object.assign({}, this.task, {
                	priority_id: priorityId,
                    task_id: this.task.id,
					sort_weight: moment().unix()
                });
                this.$api.task.updateTask(data).catch((err) => {
                    console.info('err', err);
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
        }
    }
</script>

<style lang="scss"></style>
