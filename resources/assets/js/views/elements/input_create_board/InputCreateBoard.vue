<template>
    <div class="input-create-task">
        <div class="input-create-task__button-add">
            <button type="button" class="btn" @click="handleSave" :disabled="isLoading">
               <i class="icon-plus">
                    <svg class="icon font-color-green" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-plus"></use>
                    </svg>
                </i>
            </button>
        </div>
        <input class="input-create-task__input"
               type="text"
               v-model="newBoard.name"
               v-on:keyup.enter="handleSave"
               :placeholder="$t('create_task_title')">
        <div class="input-create-task__setting">
            <button type="button" class="btn" @click="showModalBoardSetting" :title="$t('title_setting_board')">
                <i aria-hidden="true" class="oc icon-settings_2"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            groupId: {type: Number}
        },
        data() {
            return {
                newBoard: {
                    name: ''
                },
            }
        },
        methods: {
            handleSave() {
                let data = {
                    group_id:       this.groupId,
                    name:           this.newBoard.name,
                    deadline :      null,
                    budgetTypeId :  "",
                    description :   "",
                    hardBudget :    "",
                    softBudget :    "",
                    viewTypeId :    1
                };

                if (!data.group_id || !data.name) {
                    if (!data.group_id) {
                        this.$notify({type:'error', text: this.errors['groupId']});
                    }
                    if (!data.name) {
                        this.$notify({type:'error', text: this.errors['name']});
                    }
                    return;
                }

                let self = this;

                this.$api.board.createBoard(data)
                    .then(res => {
                        this.cleanInput();
                    })
                    .catch(err => {
                        if (err.response.status === 422) {
                            if (err.response.data.errors) {
                                handleErrors(err.response.data.errors, this.errors, self, true);
                            }
                        } else {
                            self.$notify({type:'error', text: err.response.data.message});
                        }
                    });
            },
            cleanInput() {
                this.newBoard.name = '';
            },
            showModalBoardSetting() {
                this.$modal.show('board-setting-modal', {name: this.newBoard.name})
            }
        }
    }
</script>

<style lang="scss">
.input-create-task {
    .input-create-task__button-add {
        .btn {
            display: flex;
            justify-content: center;
            
            .icon-plus {
                display: flex;
            }
        }
    }
}
</style>
