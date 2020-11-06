<template>
    <div v-if="isLoggedIn">

        <div
            v-if="showCreate"
            class="row"
            style="margin:0; margin-bottom: 15px;"
        >

            <div class="col-xs-12"
            style="padding:0;"
            >

                <div class="input-create-task">

                    <div class="input-create-task__button-add">

                        <button
                             :title="$t('title_create_group')"
                             :disabled="isLoading"
                             @click="handleAddGroup"
                             id="add-group"
                             type="button"
                             class="btn"
                         >
                            <i class="icon-plus">
                                <svg class="icon font-color-green" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-plus"></use>
                                </svg>
                            </i>
                        </button>

                    </div>

                    <input
                        v-model.trim="nameNewGroup"
                        :disabled="isLoading"
                        :placeholder="$t('new_group_input')"
                        @keyup.enter="handleAddGroup"
                        id="group-title-input"
                        class="input-create-task__input"
                        type="text"
                        maxlength="150"
                        autofocus
                    >

                    <div class="input-create-task__setting">

                        <button
                            :title="$t('title_setting_group')"
                            :disabled="isLoading"
                            @click="showNewGroupDetails"
                            type="button"
                            class="btn"
                        >
                            <i class="oc icon-settings_2" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <div class="management">
            <div class="task-wrapper">
                <div class="management-group-item">
                    <div class="management-group-item__inner">
                        <div class="management-group-item__header">
                            <table class="board-header">
                                <tr>
                                    <th style="width: 500px;">
                                        <div class="board-header__column-name">
                                            <div class="board-header__name"></div>
                                        </div>
                                    </th>
                                    <th class="board-header__column-board-count">
                                        <div class="board-header__column-board-count-text">
                                            <span class="board-header__board-count"></span>
                                        </div>
                                    </th>
                                    <th class="board__column-bar">
                                        <div class="management-bar">
                                            <span>{{ $t('title_budget') }}</span>
                                            <span>{{ $t('used') }}</span>
                                        </div>
                                    </th>
                                    <th class="board__column-bar board__column-bar_open-task">
                                        <div class="management-bar">
                                            <div class="management-bar-text text-right" style="width: auto;">
                                                {{ $t('open') }}
                                            </div>
                                            <div class="management-bar-text" style="width: auto;">
                                                {{ $t('done') }}
                                            </div>
                                        </div>
                                    </th>
                                    <th class="board__column-bar-day">
                                        <div style="width: 150px;">
                                            {{ $t('new_task_activity') }}
                                        </div>
                                    </th>
                                    <th class="board__column-bar-day">
                                        <div style="width: 150px;">
                                            {{ $t('done_task_activity') }}
                                        </div>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <management-group-item
                    v-for="group in getActivedGroups"
                    :key="group.id"
                    :group="group"
                    :days="days"
                ></management-group-item>

                <div class="management-delimiter" @click="toggleShowArchive">
                    {{ $t('archived_group') }}
                </div>
                <template v-if="isShowArchive">
                    <management-group-item
                        v-for="group in getArchivedGroups"
                        :key="group.id"
                        :group="group"
                        :days="days" />
                </template>

            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import ManagementGroupItem  from '@views/partcials/Management/ManagementGroupsItem'

    export default {
		components: {
			'management-group-item': ManagementGroupItem
		},
        data() {
            return {
                nameNewGroup: '',
				getAll: [],
                days: [],
                isShowArchive: false,
            }
        },
        computed:{
			...mapGetters({
				getActivedGroups:  'groups/getActivedGroups',
				getArchivedGroups: 'groups/getArchivedGroups',
			}),
            showCreate() {
                return this.checkPermission('create-group');
            },
        },
        mounted() {
			// this.$api.group.getAllgroups();
			this.$api.group.getGroups();

			this.getDays();
			this.$store.dispatch('setPagePreloader', false);

            if (!this.checkPermission('read-all-groups', true)) {   /// THERE IS AN ERROR IN ROLE NAME, TODO: RENAME ROLE
                return this.$router.push({name: 'home'});
            }
        },
        methods: {
            toggleShowArchive() {
                this.isShowArchive = !this.isShowArchive;
            },
            getDays() {
                let range = this.$moment.range(this.$moment().subtract(29, 'days'), this.$moment());

                let lastDays = Array.from(range.by('day'));
                lastDays = lastDays.map(item => {
                    return { date: item.format('YYYY-MM-DD') }
                });

                this.days = lastDays;
            },
            handleAddGroup() {
                if (!this.nameNewGroup) {
                    this.$notify({type:'error', text: this.$t('no_group_name_error')});
                    return;
                }

                this.$api.group.createGroup({ name: this.nameNewGroup })
                    .then(response => {
                        this.$notify({type:'success', text: this.$t('create_group_success')});
                        this.clean();
                    })
                    .catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
            },
            clean() {
                this.nameNewGroup = '';
            },
            showNewGroupDetails() {
                if(this.nameNewGroup.length) {
                    this.$modal.show('group-setting-modal', { name: this.nameNewGroup })
                } else {
                    this.$notify({type:'info', text: this.$t('input_group_name')});
                }
            },
        }
    }
</script>

<style lang="scss">
    .input-create-task__button-add .icon-plus .icon {
        width:18px;
        height:18px;
    }
    .management-group-header{
        margin-bottom: 15px;
        background-color: #fff;
        /*height: 48px;*/
        table{
            width: 100%;
            height: 100%;
            th{
                padding: 10px;
            }
        }
    }

    .management-delimiter {
        cursor: pointer;
        width: 100%;
        padding: 20px;
        text-align: center;
        background: white;
        margin-bottom: 15px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.16), 0 2px 4px 0 rgba(0, 0, 0, 0.12);
        &:hover {
            color: #376aa7;
        }
    }
</style>
