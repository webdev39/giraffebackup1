<template>
    <draggable
        v-model="groupTasks"
        group="kanban"
        class="drag-inner-list"
        @change="changeTask"
        @sort="onSortCallback"
        :handle="getDragClass"
        :disabled="disabledDrag"
    >
        <drag
            v-for="(task, key) in groupTasks"
            :hideImageHtml="false"
            :key="task.id"
            :transferData="[task.id, task, task.board_id]"
        >
            <tasks-kanban-item :task="task"/>
        </drag>
    </draggable>
</template>

<script>
    import { mapGetters }         from 'vuex'
    import { Drag, Drop }         from 'vue-drag-drop'
    import draggable              from 'vuedraggable'

    import tasksSorting         from '@mixins/tasksSorting'
    import TasksKanbanItem      from './TasksKanbanItem'

    export default {
        name: 'TasksKanbanPriorityList',
        components: {
            TasksKanbanItem,
            draggable,
            Drag,
            Drop
        },
        mixins: [
            tasksSorting,
        ],
        props: {
          tasks: {
              type: Object
          },
          priority: {
              type: Object
          }
        },
        computed: {
            ...mapGetters({
                getSelectSortType:  'groups/getSelectSortType',
            }),
            isSortWeight() {
                return this.getSelectSortType.name === 'sort_weight';
            },
            disabledDrag() {
                return !this.isSortWeight;
            },
            getDragClass() {
                return /is_touch/.test(document.getElementsByTagName('html')[0].className) ? '.icon-move' : '.drag-item-content'
            },
            groupTasks: {
                get() {
                    let notSorted = this.tasks[`${this.priority.id}`].tasks;

                    if (this.isSortWeight) {
                        return notSorted.sort((a, b) => {
                            return  b.sort_weight - a.sort_weight;
                        });
                    } else {
                        return notSorted
                    }
                },
                set(value) {
                    this.newTasks = value;
                }
            },
        },
        data() {
            return {
                newTasks: [],
                changeType: null,
            };
        },
        methods: {
            changeTask(data) {
                let objKeys = Object.keys(data);

                this.$emit('change', {
                    type:           objKeys[0],
                    task:           data[objKeys[0]].element,
                    tasks:          this.newTasks,
                    priority_id:    this.priority.id
                });

                this.changeType = objKeys[0];
                this.currentTask = data[objKeys[0]].element;
            },
            onSortCallback (evt) {
                /**
                 * Mixin tasksSorting
                 */
                this.sortTasks(this.groupTasks, evt, this.changeType, this.currentTask);
            },
        },
    };
</script>


