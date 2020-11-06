<!--Optimized-->
<template>
    <theme-navbar v-resize="resizeHeader" class="container-fluid" id="top-wrapper" ref="homeNavbar" :style="{'background-color': secondaryColor}">
        <div class="menu-navigation-icons" :class="{ 'order-1' : quickTimerStart, 'border-right-none' : quickTimerStart }">
            <button type="button" class="btn btn-menu-item btn-sm" @click.prevent="handleSidebarShow" :title="$t('menu')">
                <i class="icon-menu specialsize">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-menu"></use></svg>
                </i>
            </button>

            <button
                type="button"
                class="btn btn-menu-item btn-sm btn-menu-item-plus dropdown-toggle"
                id="dropdownMenuCreate"
                :title="$t('show_dropdown_create')"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                data-v-step="group_0"
            >
                <i class="icon-plus specialsize">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-plus"></use></svg>
                </i>
            </button>

            <div class="dropdown-menu left-navbar-dropdown-menu" aria-labelledby="dropdownMenuCreate" data-toggle="dropdown">
                <span
                    class="dropdown-item"
                    v-if="checkPermission('create-group')"
                    @click="$modal.show('group-setting-modal')"
                >
                    {{ $t('new_group') }}
                        <i class="icon-sitemap">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-sitemap">
                        </use>
                        </svg>
                        </i>
                </span>
                <span
                    class="dropdown-item"
                    v-if="checkPermission('create-board')"
                    @click="$modal.show('board-setting-modal')">
                    {{ $t('new_board') }}
                        <i class="icon-boards">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-boards">
                        </use>
                        </svg>
                        </i>
                </span>
                <span
                    class="dropdown-item"
                    @click="$modal.show('filter-setting-modal')">
                    {{ $t('new_filter') }}
                        <i class="icon-clipboard">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-clipboard">
                        </use>
                        </svg>
                        </i>
                </span>
            </div>

            <button  v-if="isTenant || checkPermission('read-all-groups')" type="button" class="btn-menu-item btn-sm hide-mobile" :title="$t('management')" @click.prevent="$router.push({name: 'management'})">
                <i class="menu-navigation__icon_groups" aria-hidden="true">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-groups"></use>
                    </svg>
                </i>
            </button>

            <div class="dropdown" >
                <button type="button"
                        id="timer-showhide-trigger"
                        class="btn btn-menu-item btn-sm"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        :title="$t('show_dropdown_report')"
                        v-if="!hideReportNavbarDropdown"
                >
                    <i class="icon-hexagon specialsize">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-hexagon">
                            </use>
                        </svg>
                    </i>
                </button>

                <div class="dropdown-menu report-navbar-dropdown-menu" aria-labelledby="timer-showhide-trigger" data-toggle="dropdown">
                    <router-link :to="{name:'reports'}" v-if="isReportDataPermission">
                        <a class="dropdown-item">{{ $t('reports') }}</a>
                    </router-link>

                    <router-link v-if="isShowLink" :to="{name:'year-overview'}">
                        <a class="dropdown-item">{{ $t('year_overview') }}</a>
                    </router-link>

                    <router-link v-if="isShowLink" :to="{name:'bills'}">
                        <a class="dropdown-item">{{ $t('bills') }}</a>
                    </router-link>

                    <router-link :to="{name:'manage-customers'}" v-if="isManageCustomersPermission">
                        <a id="client" style="cursor:pointer;" class="dropdown-item">
                            {{ $t("clients") }}
                        </a>
                    </router-link>

                    <a href="#" class="dropdown-item show-mobile hidden-sm hidden-md hidden-lg" @click="$router.push({name: 'manage-groups'})">{{ $t('manage_groups') }}</a>
                </div>
            </div>
            <div
                class="header-search"
                v-click-outside="hideSearch"
            >
                <button class="btn btn-menu-item header-search__button "
                        @click="triggerSearch">
                    <i class="icon-search specialsize">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-search">
                        </use>
                    </svg>
                    </i>
                </button>
                <new-search class="header-search__content" v-show="showSearch" />
            </div>
            <div
                style="position: relative"
                v-click-outside="hideNotificationModal"
            >
                <button
                    type="button"
                    id="messages"
                    class="btn btn-menu-item btn-sm"
                    @click="toggleNotificationModal"
                    :title="$t('notifications')"
                >
                    <i class="icon-message specialsize">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-message"></use>
                        </svg>
                    </i>
                    <span
                        v-if="getCountUnreadNotifications > 0"
                        class="number-notification"
                    >
                        {{ getCountUnreadNotifications }}
                    </span>
                </button>

                <notifications class="dropdown-menu" @hide="hideNotificationModal" v-if="showNotificationModal" />
            </div>

        </div>

        <div style="display: none;" class="col-xs-4 col-md-8">
            <button id="btn_show_current_timer" type="button" class="tracking-listing btn btn-transparent btn-md">
                <i class="fa fa-pause-circle" aria-hidden="true"></i>
            </button>
            <button id="btn_show_tracked_time" type="button" class="tracking-listing btn btn-transparent btn-md">
                <i class="fa fa-list-ul" aria-hidden="true"></i>
            </button>
        </div>

        <div class="header-tour">
            <theme-button-success
                :title="$t('view_site_tour')"
                type="button"
                class="btn header-tour-btn"
                @click.native="$emit('showTour')"
            >
                <i class="header-tour-icon">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-tour"></use></svg>
                </i>
            </theme-button-success>
        </div>

        <nav-timer v-if="checkPermission('time-tracking')"/>

        <div class="task-side-menu" :class="{ 'order-3' : quickTimerStart }">

            <div
                v-click-outside="closeNavbarNavigation"
                :title="getFullName"
                class="task-side-menu__btn-container"
            >
                <div
                    class="btn btn-lg btn-transparent"
                    @click="toggleNavbarNavigation"
                >
                    <button class="btn btn-member-loggedin" v-if="isMemberLoggedIn">
                        <i class="fa fa-user-secret" aria-hidden="true"></i>
                    </button>

                    <theme-subscribers class="menu-navbar-image" :style="{'background-image': 'url(' + userAvatar + ')'}" v-else>
                        <span v-if="!userAvatar">{{ userInitials }}</span>
                    </theme-subscribers>
                </div>

                <navigation v-if="showNavbarNavigation"/>
            </div>

            <button class="btn btn-navbar-logout" :title="$t('logout')" @click.prevent="$api.auth.logout()" >
                <i id="footer_nav_logout" class="icon-logout">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-logout"></use></svg>
                </i>
            </button>

        </div>

    </theme-navbar>
</template>
<script>
    import resize from 'vue-resize-directive'

    import { mapGetters }           from 'vuex'
    import clickOutside             from 'v-click-outside'
    import config                   from '@config'

    import NavTimer                 from '@views/components/navbar/NavTimer'
    import InstantSearch            from '@views/partcials/InstantSearch'
    import NewSearch                from '@views/partcials/NewSearch'
    import Notifications            from '@views/components/navbar/Notifications'
    import Navigation               from '@views/components/navbar/Navigation'

    import ThemeNavbar              from '@views/layouts/theme/ThemeNavbar'
    import ThemeSubscribers         from '@views/layouts/theme/ThemeSubscribers'
	import ThemeButtonSuccess       from "@assets/js/views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
		components: {
			NavTimer,
			InstantSearch,
			Notifications,
			Navigation,
			NewSearch,
			ThemeNavbar,
			ThemeSubscribers,
			ThemeButtonSuccess
		},
		props: {
			showSidebar: Boolean
		},
		directives: {
			'clickOutside': clickOutside.directive,
			resize,
		},
        data() {
            return {
                styleObj: {
                    display: 'none'
                },
                showSearch: false,
                showNotificationModal: false,
                showNavbarNavigation: false,
            }
        },
		watch:{
			$route (to, from){
				/**
				 * Close dropdownmenu after router change
				 * @type {Boolean}
				 */
				this.showNavbarNavigation = false;
			},
		},
		computed: {
            ...mapGetters({
                isMemberLoggedIn: 'isMemberLoggedIn',
                quickTimerStart: 'getQuickTimerState',
                groups: 'groups/getGroups',
                userAvatar: 'user/getAvatar',
                userInitials: 'user/getUserInitials',
                secondaryColor: 'user/getSecondaryColor',
                getFullName: 'user/getFullName',
                getTypesSidebar: 'sidebar/getTypesSidebar',
                getSidebarShow: 'sidebar/getSidebarShow',
                getCountUnreadNotifications: 'notify/getCountUnreadNotifications',
				getCurrentTour: 'getCurrentTour',
            }),
            isShowLink() {
                if (window.innerWidth < config.size.tablet) {
                    return false
                }

                return this.isReadBillingPermission;
            },
            isReportDataPermission() {
                return this.checkPermission('report-own-data') || this.checkPermission('report-other-data')
            },
            isReadBillingPermission() {
                return this.checkPermission('read-billing')
            },
            isManageCustomersPermission() {
                return this.checkPermission('manage-customers')
            },
            hideReportNavbarDropdown() {
                return !this.isReportDataPermission && !this.isReadBillingPermission && !this.isManageCustomersPermission
            }
        },
        created() {
            this.$event.$on('show-search', this.triggerSearch);
        },
        methods: {
            resizeHeader() {
                this.$event.$emit('update-header-height');
            },
            setSidebarStatus(status) {
                const val = this.getTypesSidebar.find(item => item.name === status);
                this.$store.dispatch('sidebar/setTypeShowSidebar', val);
            },
            handleSidebarShow() {
              if (window.innerWidth < 768 ) {
                  if (this.getSidebarShow.name === 'close') {
                      this.setSidebarStatus('open');
                  } else {
                      this.setSidebarStatus('close');
                  }
              } else {
                switch (this.getSidebarShow.name) {
                    case 'close':
                        this.setSidebarStatus('open');
                        break;
                    case 'open':
                        this.setSidebarStatus('small');
                        break;
                    case 'small':
                        this.setSidebarStatus('close');
                        break;
                }
              }
            },
            triggerSearch() {
                this.showSearch = true;
            },
            hideSearch(e) {
                if (this.showSearch && !e.target.classList.contains('tag')) {
                    this.showSearch = false;
                }
            },
            hideNotificationModal() {
                this.showNotificationModal = false;
            },
            toggleNotificationModal() {
                this.showNotificationModal = !this.showNotificationModal;
            },
            closeNavbarNavigation() {
                this.showNavbarNavigation = false;
            },
            toggleNavbarNavigation() {
                this.showNavbarNavigation = !this.showNavbarNavigation;
            },
        },
        beforeDestroy(){
            this.$event.$off('show-search');
        },
    }
</script>

<style lang="scss">
    .task-side-menu__btn-container {
        display: flex;
        align-items: center;
        justify-content: center;
            .btn {
                padding: 10px 10px;
            }
    }
    .menu-navbar-image {
        text-align: center
    }
    #messages {
        position: relative;

        .number-notification {
            background: #fa3e3e;
            color: #fff;
            padding: 4px 3px 2px 3px;
            text-align: center;
            font-size: 11px;
            line-height: 1;
            top: -3px;
            right: 0;
            min-height: 13px;
            position: absolute;
            border-radius: 4px;
        }
    }

    .btn-member-loggedin {
        font-size: 24px;
        color: #929292;
        border-radius: 50%;
        padding: 7px 7px 6px 10px;
        display: flex;
        align-items: center;
        background-color: #D6D6D7;

        @media (max-width: 550px) {
            padding: 4px;
        }
    }

   /* New Search */

    .header-search{
        position: relative;
        display: flex;
        z-index:99;
    }
    .header-search__button{
        position: relative;
        left: 0;
        z-index: 10;

    }
    .header-search__content{
        .multiselect__tags{
            min-height:36px;
            border-left: 0;
            border-right: 0;
            border-top: 0;
            padding-top: 0;
            padding-bottom:0;
            padding-left: 10px;
            // margin-left: -30px;
            border-radius:0px;
            border:none;
            @media (min-width: 1200px) {
                background: transparent;
            }
        }
        .multiselect__placeholder{
            margin-bottom: 0;
            padding-top:7px;
        }
        .multiselect__input{
            top: 8px;
            padding-left: 0;
        }
        .multiselect__select {
                height:30px;

                &:before {
                display:none;
            }

             &:after {
                    display: inline-block;
                    font-style: normal;
                    font-variant: normal;
                    text-rendering: auto;
                    -webkit-font-smoothing: antialiased;
                    font-family: FontAwesome ;
                    font-weight: 900;
                    content: "\f107";
                    margin-top:6px;
                    color:#cecece;
            }
        }

         .multiselect__content-wrapper {
            border:none;
            border-radius:0;
            -webkit-box-shadow: 0px 3px 5px 0 rgba(0, 0, 0, 0.2);
            box-shadow: 0px 3px 5px 0 rgba(0, 0, 0, 0.2);
        }
    }



    /* New Search end */

</style>
