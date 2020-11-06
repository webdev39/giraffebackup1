<template>
    <div id="manage-page" class="manage-groups">
        <theme-manage-container class="manage-container">
            <h1 class="manage-title">{{ $t('manage_groups') }}</h1>

            <group-create /> 

            <groups-list-item v-for="group in activedGroups" :key="group.id" :group="group" />

            <div class="manage-wrapper-groups row" role="button" @click="showArchiveGroups">
                {{ isShowArchiveGroup ? $t('hide_archived_groups') : $t('show_archived_groups')  }} ({{ countArchivedGroups }})
            </div>

            <groups-list-item v-for="group in archivedGroups" :key="group.id" :group="group" v-if="isShowArchiveGroup"/>
        </theme-manage-container>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import GroupsListItem       from '../../views/components/group/GroupListItem'
    import GroupCreate          from "../../views/components/group/GroupCreate"

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";

    export default {
        name: "manage-groups",
        data() {
            return {
                isShowArchiveGroup: false,
            }
        },
        components: {
            GroupCreate,
            GroupsListItem,
            ThemeManageContainer
        },
        computed:{
            ...mapGetters({
                activedGroups:  'groups/getActivedGroups',
                archivedGroups: 'groups/getArchivedGroups',
            }),
            countArchivedGroups() {
                return this.archivedGroups.length;
            }
        },
        mounted() {
            this.$api.group.getGroups();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            showArchiveGroups() {
                if(this.countArchivedGroups === 0){
                    return this.$notify({type:'error', text: this.$t('no_archived_group_yet')});
                }

                this.isShowArchiveGroup = !this.isShowArchiveGroup;
            },
        },
    }
</script>
