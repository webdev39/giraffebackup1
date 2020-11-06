<template>
    <div class="subtasks-item">
        <template v-if="!isEdit">
            <label class="ocam-checkbox-label">
                <input type="checkbox" name="check" class="ocam-checkbox" v-model="isCompleted" @change="handleUpdateSubtaskStatus" :disabled="isLoading">

                <div :style="{'text-decoration': isCompleted ? 'line-through': 'none'}" class="label-text">
                    {{ subtask.name }}
                </div>
            </label>

            <button class="btn btn-edit" @click="setEditSubtask(true)" :disabled="isLoading">
                <i class="fa fa-pencil"></i>
            </button>

            <button class="btn btn-remove" @click="handleRemoveSubTask" :disabled="isLoading">
                <i class="fa fa-trash"></i>
            </button>
        </template>

        <template v-else>
            <textarea-autosize class="subtask-textarea" v-model="form.name" rows="1" />

            <button class="btn btn-cancel" @click="setEditSubtask(false)" :disabled="isLoading" >
                <i class="fa fa-times"></i>
            </button>

            <button class="btn btn-ok" @click="handleUpdateSubtaskName" :disabled="isLoading">
                <i class="fa fa-check"></i>
            </button>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            subtask: {
                type: Object
            }
        },
        data() {
            return {
                isEdit:         false,
                isCompleted:    this.subtask.is_completed,
                form: {
                    name: null,
                }
            };
        },
        methods: {
            handleChangeStatusDrag(status) {
                this.$emit('showEdit', status);
            },
            setEditSubtask(status) {
                this.handleChangeStatusDrag(status);
                this.isEdit    = status;
                this.form.name = this.subtask.name
            },
            handleUpdateSubtaskName() {
                this.$api.subtask.updateSubtask({
                    sub_task_id:    this.subtask.id,
                    task_id:        this.subtask.task_id,
                    name:           this.form.name
                }).then(() => {
                    this.setEditSubtask(false);
                    this.handleChangeStatusDrag(false);
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handleUpdateSubtaskStatus() {
                this.$api.subtask.changeStatusSubtask({
                    sub_task_id:    this.subtask.id,
                    task_id:        this.subtask.task_id,
                    is_completed:   this.isCompleted,
                }).then(() => {
                    this.handleChangeStatusDrag(false);
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            handleRemoveSubTask() {
                this.$modal.show('confirm-modal', {
                    title: 'Delete this SubTask',
                    body: 'Are you sure you want to delete this subtask?',
                    confirmCallback: () => {
                        this.$api.subtask.removeSubtask(this.subtask).catch(err => {
                            this.$notify({type:'error', text: err.response.data.message});
                        });
                    },
                })
            },
        }
    }
</script>

<style lang="scss">
    .subtasks-item {
        position: relative;
        margin-bottom: 8px;
        .ocam-checkbox-label {
        padding-left:25px;
    }
        input.ocam-checkbox + .label-text:before {
            top: 3px;
            left: 0px;
        }

        .subtask-textarea {
            width: calc(100% - 70px);
            border-radius: 5px;
            padding: 4px 10px;
            resize: none;
            overflow: hidden;
            height: 45px;
        }

        .btn {
            top: -2px;
            width: 28px;
            padding: 1px 6px;
            background: transparent;
            position: absolute;
            border: 1px solid rgb(221, 221, 221);
            color: #636b6f;
        }

        .btn-ok,
        .btn-edit {
            right: 6px;
            background:#fff;
        }

        .btn-remove,
        .btn-cancel {
            right: 36px;
            background:#fff;
        }

        .btn-remove,
        .btn-edit {
            display: none;
        }

        &:hover {
            .btn-remove,
            .btn-edit {
                display: block;
            }
        }
    }

    .input-create-details__input{
        padding-right: 40px;
    }
</style>
