<template>
    <div :class="['navigation-wrapper', {'navigation-calc-height' : quickTimerStart}]">

        <theme-sidebar class="navigation col-xs-2 col-md-3" :class="{'small-sidebar' : getSidebarShow.name === 'small', 'close-sidebar': getSidebarShow.name === 'close'}" :style="{'background-color': primaryColor}" >

            <transition name="transform-left-opacity">
                <div v-if="! pagePreloader" class="navigation-entry-wrapper navigation-links_theme_black">
                <div class="container-fluid">
                    <div class="row">
                        <router-link :to="{ name: 'deadline', params: { period: 'day' }}" id="your-day-menu" class="task-headline" >
                            <div class="navigation-entry-head" @click="hideSidebar" :class="{'router-link-active': $route.name === 'deadline' && $route.params.period === 'day' }">
                                <div class="navigation-entry-head-icon">
                                    <span class="navi-menue-icons">
                                        <i class="icon-calendar">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xlink:href="#icon-calendar">
                                            </use>
                                        </svg>
                                        </i>
                                    </span>
                                </div>
                                <div class="navigation-entry-head-content">
                                    <div class="navigation-entry-head-text">
                                        <span>{{ $t('your_day') }}</span>
                                    </div>
                                    <div class="navigation-entry-head-count">
                                        <theme-sidebar-deadline id="badge-your-day">
                                            <span>{{ getTasksDeadline.today.length }}</span>
                                        </theme-sidebar-deadline>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <router-link :to="{ name: 'deadline', params: { period: 'week' }}" id="your-week-menu" class="task-headline" >
                            <div class="navigation-entry-head" @click="hideSidebar" :class="{'router-link-active': $route.name === 'deadline' && $route.params.period === 'week' }">
                                <div class="navigation-entry-head-icon">
                                    <span class="navi-menue-icons">
                                        <i class="icon-calendar">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                            xlink:href="#icon-calendar">
                                            </use>
                                        </svg>
                                        </i>
                                    </span>
                                </div>
                                <div class="navigation-entry-head-content">
                                    <div class="navigation-entry-head-text" >
                                        <span>{{ $t('your_week') }}</span>
                                    </div>
                                    <div class="navigation-entry-head-count">
                                        <theme-sidebar-deadline id="badge-your-week">
                                            <span>{{ getTasksDeadline.week.length }}</span>
                                        </theme-sidebar-deadline>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                        <template v-for="filter in filters">
                            <router-link :to="{ name: 'filter', params: { id: filter.id }}" class="task-headline">
                                <div class="navigation-entry-head" @click="hideSidebar" :class="{'router-link-active': $route.name === 'filter' && $route.params.id === filter.id }">
                                    <div class="navigation-entry-head-icon">
                                        <span class="navi-menue-icons">
                                            <i class="icon-clipboard">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                xlink:href="#icon-clipboard">
                                                </use>
                                            </svg>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="navigation-entry-head-content">
                                        <div class="navigation-entry-head-text">
                                            <span>{{ filter.name }}</span>
                                        </div>
                                        <div class="navigation-entry-head-setting-icon">
                                            <span class="navi-menue-icons" :title="$t('title_setting_filter')" @click="$modal.show('filter-setting-modal', {filterId: filter.id})">
                                                <i class="icon-settings">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-settings">
                                                    </use>
                                                </svg>
                                                </i>
                                            </span>
                                        </div>
    <!--                                     <div class="navigation-entry-head-count" style="margin:0 1px 0 10px;">
                                            <span class="navi-menue-icons" :title="$t('title_remove_filter')" @click="showConfirmRemoveFilter(filter.id)">
                                                <i class="icon-close-circle">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-close-circle">
                                                    </use>
                                                </svg>
                                                </i>
                                            </span>
                                        </div> -->
                                    </div>
                                </div>
                            </router-link>
                        </template>
                        <div class="row own-devider"></div>
                        <div class="navigation-body">
                            <div class="groups-list">
                                <div class="" v-for="(group, index) in groups" :key="group.id">
                                    <div class="groups-list-item">
                                        <span class="navi-menue-icons">
                                            <i class="icon-sitemap">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-sitemap">
                                                    </use>
                                                </svg>
                                            </i>
                                        </span>
                                        <div class="group-list-content">
                                            <a class="board-name-val" @click="getCurrentOpenGroup(group)" data-toggle="collapse" :href="'#groupCollapse'+group.id"
                                                aria-expanded="false">
                                                <span :class="{'first-group' : index === 0}">{{group.name}}</span>
                                            </a>
                                            <div class="group-icon">
                                                <span class="navi-menue-icons navi-menue-icons_only_hover" style="cursor: pointer"  @click="pushCommunicationGroup(group.id)">
                                                    <i class="icon-list">
                                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                        xlink:href="#icon-list">
                                                        </use>
                                                    </svg>
                                                    </i>
                                                </span>

                                                <span
                                                    v-if="getGroupUnreadNotification[group.id]"
                                                    :class="['navi-menue-icons notify-count notify-count__group', { blue : showGroup[group.id]}]"
                                                >
                                                    {{ getGroupUnreadNotification[group.id].length }}
                                                </span>

                                                <span
                                                    data-v-step="group_1"
                                                    class="navi-menue-icons"
                                                    :title="$t('title_setting_group')"
                                                    :disabled="groupSettingModalShown"
                                                    @click="showGroupSettings(group.id)"
                                                    style="cursor: pointer"
                                                >
                                                        <i class="icon-settings">
                                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                xlink:href="#icon-settings">
                                                                </use>
                                                            </svg>
                                                        </i>
                                                </span> 
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        :id="'groupCollapse'+group.id"
                                        :class="{in: group.id == $route.params.group_id}"
                                        class="collapse collapse-boards" role="tabpanel" style="width: 100%;"
                                    >
                                        <drop
                                            v-for="board in group.boards"
                                            :key="board.id"
                                            @drop="handleTaskDrop(board.id, group.id, ...arguments)"
                                            @dragover="handleDragOver(board.id)"
                                            @dragleave="handleDragLeave(board.id)"
                                            class="drop board-item"
                                            :class="{'router-link-active': board.id == $route.params.board_id,'board-item-over': board.id == dragOverId }"
                                        >
                                            <div class="board-name">
                                                <div class="board-icon">
                                                    <span>
                                                        <i class="icon-boards">
                                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xlink:href="#icon-boards">
                                                            </use>
                                                        </svg>
                                                        </i>
                                                    </span>
                                                </div>
                                                <router-link
                                                    class="board-name-val"
                                                    :to="{ name: 'board', params: { group_id: group.id, board_id: board.id } }"
                                                    @click.native="hideSidebar(group)"
                                                >
                                                    {{board.name}}
                                                </router-link>
                                                <div class="board-settings-icon">
                                                    <i
                                                        @click="openBoardSettings(board.id, group.id)"
                                                        :title="$t('title_setting_board')"
                                                        class="icon-settings"
                                                        aria-hidden="true"
                                                    >
                                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xlink:href="#icon-settings">
                                                            </use>
                                                        </svg>
                                                    </i>
                                                </div>
                                                <div
                                                    v-if="getBoardUnreadNotification[board.id]"
                                                    class="notify-count"
                                                >
                                                    <span>{{ getBoardUnreadNotification[board.id].length }}</span>
                                                </div>
                                                <theme-sidebar-task-count
                                                    v-if="board.tasks_count"
                                                    class="open-task-count"
                                                >
                                                    <span>{{ board.tasks_count }}</span>
                                                </theme-sidebar-task-count>
                                            </div>
                                        </drop>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="container-fluid" v-if="showReportMenu" >
                    <div class="navigation-links navigation-links_theme_black">
                        <ul class="navigation-links__list">
                            <li class="navigation-links__item">
                                <router-link :to="{name:'reports'}" class="navigation-links__link">
                                {{ $t('reports') }}
                                </router-link>
                            </li>
                            <li class="navigation-links__item">
                                <router-link :to="{name:'year-overview'}" class="navigation-links__link">
                                {{ $t('year_overview') }}
                                </router-link>
                            </li>
                            <li class="navigation-links__item">
                                <router-link :to="{name:'bills'}" class="navigation-links__link">
                                {{ $t('bills') }}
                                </router-link>
                            </li>
                        </ul>
                    </div>
                    <div class="own-devider"></div>
                    <div class="navigation-links navigation-links_theme_black">
                        <ul class="navigation-links__list">
                            <li class="navigation-links__item">
                                <router-link :to="{name:'manage-customers'}" class="navigation-links__link">
                                {{ $t('clients') }}
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>-->
            </div>
            </transition>
        </theme-sidebar>
    </div>
</template>
<script>
    import {mapGetters}                     from 'vuex'
    import {Drop}                           from 'vue-drag-drop'

    import ThemeSidebar                     from '@views/layouts/theme/ThemeSidebar'
    import ThemeSidebarDeadline             from '@views/layouts/theme/ThemeSidebarDeadline'
    import ThemeSidebarTaskCount            from '@views/layouts/theme/ThemeSidebarTaskCount'

	import find                             from '@helpers/findInGroups'

    export default {
        components: {
            Drop,
            ThemeSidebar,
            ThemeSidebarDeadline,
            ThemeSidebarTaskCount
        },
        data() {
            return {
                groupSettingModalShown: false,
                dragOverId: null,
                showGroup: {}
            }
        },
        computed:{
            ...mapGetters({
                quickTimerStart: 'getQuickTimerState',
                groups: 'groups/getGroups',
                getTasksDeadline: 'groups/getTasksDeadline',
                filters: 'filters/getFilters',

                primaryColor: 'user/getPrimaryColor',
                getCurrentTheme: 'user/getCurrentTheme',

                getSidebarShow: 'sidebar/getSidebarShow',
                getTypesSidebar: 'sidebar/getTypesSidebar',
				pagePreloader: 'getPagePreloader',
				getCurrentTour: 'getCurrentTour',

				getUnreadMessages: 'notify/getUnreadMessages',
			}),
			getGroupUnreadNotification() {
				return this.$lodash.chain(this.getUnreadMessages)
					.map(item => {
						let task = find.searchTaskById(this.$store.getters['groups/getStateGroups'], item.task_id);
						if (this.showGroup[item.group_id]) {
							if (! item.task_id) {
                                return item;
							}
						} else {
							if (task || !item.task_id) {
							    return item;
                            }
                        }
					})
					.groupBy("group_id")
					.value();
            },
            getBoardUnreadNotification() {
				return this.$lodash.chain(this.getUnreadMessages)
					.map(item => {
						let task = find.searchTaskById(this.$store.getters['groups/getStateGroups'], item.task_id);
						if (task) {
							return item
						}
					})
					.groupBy("board_id")
					.value();
            }
        },
        created() {
        	if (this.$route.params.group_id) {
        		this.$set(this.showGroup, this.$route.params.group_id, true);
            }
        },
        methods: {
			getCurrentOpenGroup(group) {
				if (! this.showGroup.hasOwnProperty(group.id)) {
					this.$set(this.showGroup, group.id, false);
                }
                return this.$set(this.showGroup, group.id, ! this.showGroup[group.id]);
			},
			setHideAllOpenGroup(group) {
				for (let prop in this.showGroup) {
					if (Number(group.id) !== Number(prop)) {
					    this.$set(this.showGroup, prop, false);
                    }
				}
            },
            hideSidebar(group = null) {
                if (window.innerWidth < 768 ) {
                    this.$store.dispatch('sidebar/setTypeShowSidebar', this.getTypesSidebar.find(item => item.name === 'close'));
                }
                if (group) {
                    this.setHideAllOpenGroup(group);
                }
            },
            showConfirmRemoveFilter(filterId) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('delete_this_filter'),
                    body: this.$t('are_you_sure_you_want_to_delete_this_filter'),
                    confirmCallback: () => {
                        this.$api.filter.deleteFilter(filterId).catch((err) => {
                            this.defaultError(err.response);
                        })
                    },
                });
            },
            pushCommunicationGroup (groupId) {
                this.$router.push({ name: 'communication', params: { group_id: groupId }});
            },
            showGroupSettings (groupId) {
                if (!this.groupSettingModalShown) {
                    this.$modal.show('group-setting-modal', {groupId: groupId});
                }
            },
            openBoardSettings (board_id) {
                this.$modal.show('board-setting-modal', {boardId: board_id});
            },
            /*DRAG*/
            handleTaskDrop(boardId, groupId, [taskId, task, oldBoardId]) {
                let form = {...task};
                form.task_id =  task.id;
                form.board_id = boardId;
                this.dragOverId = null;

                if (form.group_id !== groupId) {
                    return this.$notify({type:'error', text: this.$t('cant_move_another_group')});
                }
                this.$api.task.updateTask(form, task);
            },
            handleDragOver(boardId) {
                this.dragOverId = boardId
            },
            handleDragLeave() {
                this.dragOverId = null
            },
        }
    }
</script>
<style scoped lang="scss">
    /*.navigation:not(.navigation_reports) {*/
        /*color: #fff;*/
    /*}*/
    .navigation.navigation_reports .own-devider{
        width:  20px;
        margin: 20px 0;
        border-top-style: dashed;
        border-top-color: #fff;
    }
    .sidebarPlaceholder.navigation_reports{
        background-color: #21292e;
    }
    .task-headline:hover, .task-headline:focus,
    .board-name-val:hover, .board-name-val:focus {
        /*color: rgba(255, 255, 255, 0.6) !important;*/
        color: inherit;
        opacity: 0.6;
        text-decoration: none;
    }
    .first-group {
        // white-space: normal !important;
    }
    .group-name > a[data-toggle="collapse"] > span {
        width: 100%;
        text-overflow: ellipsis;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
    }
    .board-name {
        padding: 0;
        width: 100% !important;
        /*height: 28px;*/
        display: -ms-inline-flexbox;
        display: inline-flex;
        justify-content: flex-start;
        position: relative;
    }
    .boards-list .board-name {
        width: 100%;
    }
    .board-icon {
        padding-top: 3px;
        flex: 0 0 auto;
    }
    .board-name-val {
        margin-right: auto;
        // margin-left: 10px;
        padding: 5px 0 5px 10px;
        flex: 1 1;
        text-overflow: ellipsis;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden !important;
    }
    .board-name:hover > .board-settings-icon {
        display: flex;
        align-items: center;
    }
    .navigation-entry-head .icon-settings{
        // display: none;
    }
    .navi-menue-icons{
        display: flex;
        // margin-right: 5px;
        align-items:center;
        &:last-child{
            margin-right: 0;
        }
    }
    .navi-menue-icons_only_hover{
        opacity: 0;
        margin-right:10px;
    }
    .is_touch .navi-menue-icons_only_hover{
        opacity: 1;
    }
    .groups-list-item:hover {
        .navi-menue-icons_only_hover {
            opacity: 1;
        }
    }
    .navigation-entry-head-setting-icon,
    .navigation-entry-head-count{
        display: flex;
        align-items: center;
        cursor: default;
    }
    .navigation-entry-head:hover .icon-settings{
        display: inline-block;
    }
    .board-settings-icon {
        display: none;
        flex: 0 0 auto;
        font-size: 20px;
        line-height: 20px;
        margin-right: 5px;
    }
    .board-settings-icon i {
        font-size: 1.4rem;
        margin-top: 1px;
         svg {
            width: 15px;
            height:15px;
     }
    }
    .open-task-count,
    .notify-count {
        //background-color: #6291c8;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        text-align: center;
        align-self: center;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        //color: #fff;
        font-size: 11px;
        line-height: 1;
        cursor: default;
    }
    .notify-count{
        background-color: #f73d3e;
        color: #fff;
    }
    .open-task-count {
        margin-left: 5px;
    }
    .notify-count__group{
        margin-right: 4px;

        &.blue {
            background-color: #0b4c9a;
        }
    }
    @media (max-width: 650px) {
        .open-task-count {
            right: 7px;
        }
    }
</style>
