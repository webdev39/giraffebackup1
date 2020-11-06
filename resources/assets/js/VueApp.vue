<template>
    <div :class="['main-wrapper', classCurrentTour]" :style="{'font-family': `${getFontName}, sans-serif`}">

        <div class="main">
            <header-view
                v-if="isShowHeader"
                @showTour="tourWorker(true)"
            />

            <div class="main-content">
                <sidebar-view v-if="isShowSidebar" />

                <div
                    :class="backgroundClass"
                    :style="backgroundStyle"
                    class="content"
                >
                    <transition name="fade">
                        <task-detail v-if="isShowTaskDetails" :key="$route.query.taskId"/>
                    </transition>

                    <div class="content-inner">
                        <div class="content-inner-indent">

                            <transition name="fade" v-if="isShowPagePreloader">

                                <div
                                    v-if="pagePreloader"
                                    class="page-preloader-holder"
                                >
                                    <loader class="page-preloader"/>
                                </div>

                            </transition>

                            <router-view v-if="isMountRoute"/>

                        </div>
                    </div>
                </div>

                <UserReviews
                    v-if="isShowReviews && false"
                    :user="getUserProfile"/>

            </div>

        </div>

        <!-- Modules -->
        <notifications />
        <icon-sprite />
        <loader-wrapper />
        <external-modals />
        <transition name="fade">
            <Tour
                v-if="showTourHandler"
                :showTour="showTour"
                @hideTour="tourWorker(false)"
            />
        </transition>

    </div>
</template>

<script>
    import { mapGetters }   from "vuex";

    import LoaderWrapper    from "@views/layouts/LoaderWrapper";
    import SidebarView      from "@views/layouts/app/SidebarView";
    import HeaderView       from "@views/layouts/app/HeaderView";
    import ExternalModals   from "@views/layouts/app/ExternalModals";
    import Notifications    from "@views/components/notifications/Notifications";
    import IconSprite       from "@views/elements/icon_sprite/IconSprite";

    import TaskDetail       from '@views/layouts/task/TaskDetail';
	import Loader           from "@assets/js/views/components/loader/loader";
	import Tour             from "@views/components/tour/Tour";
	import UserReviews      from "@views/components/userReviews/UserReviews";

    export default {
		components:{
			Notifications,
			ExternalModals,
			HeaderView,
			SidebarView,
			IconSprite,
			LoaderWrapper,
			TaskDetail,
			Loader,
			UserReviews,
			Tour
		},
        data() {
			return {
				showTour: false,
            }
        },
		computed: {
            ...mapGetters({
                isAppLoading:       'loading/isAppLoading',
                getGroups:          'groups/getStateGroups',
                background:         'user/getBackground',
                getFontName:        'user/getFontName',
                getTypesSidebar:    'sidebar/getTypesSidebar',
                getUserProfile:     'user/getUserProfile',
				pagePreloader:      "getPagePreloader",
				getCurrentTour:     "getCurrentTour",
			}),
            isNotFound() {
                return this.$route.name === 'not-found';
            },
            isShowHeader() {
                return this.isLoggedIn && !this.isNotFound
            },
            isShowSidebar() {
                return this.isLoggedIn && !this.isNotFound
            },
			isShowReviews() {
				return this.isLoggedIn && !this.isNotFound
			},
			isShowPagePreloader() {
				return this.isLoggedIn && !this.isNotFound
			},
            classCurrentTour() {
                return 'tour-step-' + this.getCurrentTour.step;
            },
            backgroundClass() {
                if (this.isLoggedIn && !this.isNotFound) {
                    return { 'content_show-bg': true }
                }

                return {};
            },
            backgroundStyle() {
                if (this.isLoggedIn && this.background) {
                    return {'background-image': 'url(' + this.background + ')'};
                }

                return {};
            },
            isMountRoute () {
                if (this.$route.meta.group) {
					return this.getGroups.length > 0
                } else if (!this.$route.meta.auth) {
					return true;
                } else if (this.$route.meta.auth) {
					return this.isLoggedIn
                }

                return false
            },
            isShowTaskDetails() {
                return this.isLoggedIn && this.$route.query.taskId && this.getGroups.length > 0;
            },
			showTourHandler() {
            	if (!this.getUserProfile.tour && this.isShowPagePreloader) {
					return true
				}
				return this.showTour;
			}
        },
        created() {
            this.touchDetected();
            this.$store.dispatch('default/setDefaultData', window.Laravel);
			this.setShowSidebar();
            /**
             * DEBUGGING FCM
             */
            if (window.FlutterHost) {
                window.FlutterHost.postMessage('fcmToken')
            }




            window.Vue.rollbar.error('ERRRRRRRRRRqweRRRRsssOR');
            window.onFcmToken = (token) => {
                window.Vue.rollbar.error('ERRRRRRRRRRRRRROR on fcm');
                this.$store.dispatch('setDeviceToken', token);
            };

            /**
             * DEBUGGING FCM
             */

            if (this.$store.getters.getToken && !this.$store.getters.getLoggedIn) {
                let version = +window.localStorage.getItem('version');

                if (version === 0){
                    window.localStorage.setItem('version', process.env.VERSION);
                    version = process.env.VERSION;
                }

                if (version !== process.env.VERSION) {
                    this.$api.auth.logout();
                    return window.localStorage.setItem('version', process.env.VERSION)
                }

                this.$store.dispatch('loading/setAppLoading', true);

                this.$api.auth.authenticate().then(() => {
                    this.$store.dispatch('loading/setAppLoading', false);
                    this.$i18n.locale = this.getUserProfile.language.iso_639_1;
                    this.windowLaravelTranslate();
                }).finally(() => {
					this.$store.dispatch('loading/setAppLoading', false);
                });
            }

            this.$i18n.locale = 'en';

            window.addEventListener('resize', this.handleResize);

			window.onfocus = () => {
				if (this.$store.getters.getToken) {
					this.$api.notify.getUnreadNotifications();
					this.$api.timer.getTimers();
				}
			};

		},
		destroyed() {
            window.removeEventListener('resize', this.handleResize)
        },
        methods: {
		    windowLaravelTranslate() {
                window.Laravel.reports.criteria.timerange.map(item => {
                    item.name = this.$t(item.name.replace(/\s+/g, '_').toLowerCase());
                });
            },
			tourWorker(action) {
                this.showTour = action;
            },
            handleResize() {
                this.$event.$emit('update-window-screen-width', window.innerWidth);
            },
            setShowSidebar() {
                if (window.innerWidth < 768) {
                    this.$store.dispatch('sidebar/setTypeShowSidebar', this.getTypesSidebar.find(item => item.name === 'close'));
                } else {
                    this.$store.dispatch('sidebar/setTypeShowSidebar', this.getTypesSidebar.find(item => item.name === 'open'));
                }
            },
            touchDetected() {
                let msGesture       = window.navigator && window.navigator.msPointerEnabled && window.MSGesture,
                    touchSupport    = (("ontouchstart" in window) || msGesture || window.DocumentTouch && document instanceof DocumentTouch);

                if(touchSupport) {
                    document.getElementsByTagName('html')[0].classList.add("is_touch")
                }
            },
        }
    }
</script>

<style>
    .main-content {
        position: relative;
    }
    .content_show-bg {
        background: url(../images/bg.jpg) no-repeat center center fixed;
        background-size: cover;
    }
</style>
