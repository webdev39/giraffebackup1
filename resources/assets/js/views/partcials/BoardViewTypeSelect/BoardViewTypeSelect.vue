<template>
    <div class="board-type-select">
        <select :disabled="isLoading" class="form-control form-theme_reset" v-model="getSelectViewType" >
            <option v-for="(viewType, index) in viewTypes" :value="viewType" :key="index">
                {{ $t(viewType.name.toLowerCase()) }}
            </option>
        </select>
        <info-placeholder :content="$t('here_you_can_change_the_view_of_your_tasks')" class="board-type-select__info-placeholder"></info-placeholder>
    </div>

</template>

<script>
    import { mapGetters }   from 'vuex'

    import find             from '@helpers/findInGroups'
    import infoPlaceholder  from '@views/components/infoPlaceholder/infoPlaceholder'

    export default {
        components: {
            infoPlaceholder
        },
        computed: {
            ...mapGetters({
                viewTypes: 'default/getViewTypes',
                getGroups: 'groups/getStateGroups',
                getUserViewTypes: 'user/getUserViewTypes',
            }),
            getUserViewType() {
                return this.getUserViewTypes.find(item => item.board_id === this.getCurrentBoard.id)
            },
            getUserViewTypeId () {
                if (this.getUserViewType) {
                    return this.getUserViewType.view_type_id
                }

                return false;
            },
            getCurrentBoard() {
                if (this.getGroups && this.$route.params.board_id) {
                    return find.searchBoardById(this.getGroups, Number(this.$route.params.board_id));
                }

                return null;
            },
            getCurrentGroup() {
                if (this.getGroups) {
                    let groupId;

                    if (this.getCurrentBoard) {
                        groupId = this.getCurrentBoard.group_id
                    } else {
                        groupId = Number(this.$route.params.group_id);
                    }

                    return find.searchGroupById(this.getGroups, groupId);
                }
            },
            getSelectViewType: {
                get() {
                    let viewTypeId;

                    if (this.getUserViewTypeId) {
                        viewTypeId = +this.getUserViewTypeId
                    } else {
                        viewTypeId = this.getCurrentBoard.view_type_id
                    }

                    return this.viewTypes.find(item => item.id === viewTypeId)
                },
                set(newValue) {
                    this.$store.dispatch('user/setUserViewTypes', {board_id: this.getCurrentBoard.id, view_type_id: newValue.id});
                }
            },
        }
    }
</script>
