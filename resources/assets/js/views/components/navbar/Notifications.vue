<template>
    <div class="container" id="notifications-box" ref="notificationsBox">
        <div style="height:100%">
            <div class="notifications-header">
                <div class="notifications-header__title">
                    {{ $t('notifications') }}
                </div>
                <div class="notifications-header__controls">
                    <a class="notifications-header__controls-item" href="#" v-on:click.prevent="showAll()" v-if="!inPage" >{{ $t('show_all') }}</a>
                    <a class="notifications-header__controls-item" href="#" v-on:click.prevent="allMarkRead()">{{ $t('mark_all_as_read') }}</a>
                </div>
                <button type="button" class="btn btn-lg btn_notification-box_close">
                    <i class="icon-close" @click="$emit('hide')">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-close">
                        </use>
                    </svg>
                    </i>
                </button>
            </div>
            <div class="notification-body">
                <div class="notification-tabs" v-if="inPage">
                    <div
                        v-for="tab in tabs"
                        :class="['notification-tab__control-item', {'notification-tab__control-item__active': tab.name === selectTab.name}]"
                        @click="handleSetTab(tab)"
                    >
                        {{ tab.label }}
                    </div>

                </div>

                <template v-if="inPage">
                    <div v-if="!isMessagesEmpty"
                        class="notificaiton-body-messages">
                        <template
                            v-if="selectTab.name === 'unread'"
                        >
                        <div class="" v-if="getNotificationByType[selectTab.name].length === 0">
                                <div class="empty-notifications">
                                    <i class="icon-no-messages">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-no-messages">
                                        </use>
                                    </svg>
                                    </i>
                                    <span>{{ $t('not_have_unread')}}</span>
                                </div>
                                
                        </div>
                            <div
                                v-for="(list, index) in setGroupTask(getNotificationByType[selectTab.name])"
                                :key="index"
                            >
                                <div v-if="list.length > 1">
                                    <div class="notification-task-name">
                                        <span @click="showTask(list[0])">{{ list[0].task_name }}</span> has {{ list.length }} updates
                                    </div>
                                    <div
                                        v-for="notification in list"
                                        :key="notification.id"
                                        @click="showTask(notification)"
                                    >
                                        <NotificationsList
                                            :notification="notification"
                                            class="notification-task-group"
                                        />
                                    </div>
                                </div>
                                <div v-if="list.length === 1">
                                    <div
                                        v-for="notification in list"
                                        :key="notification.id"
                                        @click="showTask(notification)"
                                    >
                                        <NotificationsList :notification="notification"/>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template
                            v-if="selectTab.name === 'read' || selectTab.name === 'all'"
                        >

                        <div class="" v-if="getNotificationByType[selectTab.name].length === 0">
                                <div class="empty-notifications">
                                    <i class="icon-no-messages">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-no-messages">
                                        </use>
                                    </svg>
                                    </i>
                                    <span>{{ $t('not_have_read')}}</span>
                                </div>
                        </div>
                        
                            <div
                                v-for="(notification, index) in getNotificationByType[selectTab.name]"
                                :key="index"
                            >
                                <NotificationsList :notification="notification"/>
                            </div>
                        </template>

                        <template
                            v-if="selectTab.name === 'ownLog'"
                        >
                        <div class="" v-if="ownLog.list.lenght === 0">
                                <div class="empty-notifications">
                                    <i class="icon-no-messages">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-no-messages">
                                        </use>
                                    </svg>
                                    </i>
                                    <span>{{ $t('not_have_own_log')}}</span>
                                </div>
                        </div>
                            <div
                                v-for="(item, index) in ownLog.list"
                                :key="index"
                                class="activity-log-item"
                            >

                                <div class="activity-log-item-image">
                                    <div
                                        v-if="getAvatar"
                                        class="activity-log-holder"
                                    >
                                        <img :src="getAvatar" alt="" class="activity-log-img">
                                    </div>

                                    <theme-subscribers
                                            v-if="!getAvatar"
                                            class="menu-navbar-image"
                                            :style="{'background-image': 'url(' + getAvatar + ')'}"
                                    >
                                        <span>{{ userInitials }}</span>
                                    </theme-subscribers>

                                </div>

                                <template
                                    v-if="isJsonComment(item.body)"
                                >
                                    <div class="activity-log-content">
                                        <router-link
                                                v-if="item.task_name"
                                                :to="{ query: { taskId: item.task_id } }"
                                                class="activity-log-task"
                                        >
                                            {{ item.task_name }}
                                        </router-link>
                                        <div>{{ parseJsonComment(item.body).sender }}</div>
                                        <div class="task-detail-activity-action">{{ parseJsonComment(item.body).action }}</div>
                                        <div class="activity-log-time">
                                            <small class="timestamp">{{ item.created_at_diff_for_humans }}</small>
                                        </div>

                                        <div
                                            v-html="parseJsonComment(item.body).new"
                                            class="task-detail-activity-desc"
                                        ></div>
                                        <button
                                                @click="showOldDesc = !showOldDesc"
                                                class="task-detail-activity-button"
                                        >{{ !showOldDesc ? 'Show previous description' : 'Hide' }}</button>
                                        <div
                                            v-if="showOldDesc"
                                            v-html="parseJsonComment(item.body).old"
                                            class="task-detail-activity-desc"
                                        ></div>
                                    </div>
                                </template>

                                <template
                                    v-if="!isJsonComment(item.body)"
                                >
                                    <div class="activity-log-content">
                                        <router-link
                                            v-if="item.task_name"
                                            :to="{ query: { taskId: item.task_id } }"
                                            class="activity-log-task"
                                        >
                                            {{ item.task_name }}
                                        </router-link>
                                        <div v-if="!item.task_name">Task not found</div>
                                        <div v-html="item.body" class="activity-log-info"></div>
                                        <div class="activity-log-time">
                                            <small class="timestamp">{{ item.created_at_diff_for_humans }}</small>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </template>
                    </div>
                </template>

                <template v-else>
                    <div v-if="!isMessagesEmpty">
                        <div
                            v-for="(notification, index) in getNotifications"
                            :key="notification.id"
                            @click="showTask(notification)"
                        >
                            <NotificationsList :notification="notification"/>
                        </div>
                    </div>
                </template>

                <div class="notificaiton-body-messages" v-if="isMessagesEmpty">
                    <div class="empty-notifications">
                    <i class="icon-no-messages">
                     <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                         <use xmlns:xlink="http://www.w3.org/1999/xlink"
                         xlink:href="#icon-no-messages">
                         </use>
                     </svg>
                     </i>
                     <span>{{$t('not_have_new_notifications')}}</span>
                    </div>
                </div>

                <div class="notification" v-if="isFetchNotify">
                    <content-loading :absolute="styleLoading.absolute" :autosize="styleLoading.autosize" :loading="isFetchNotify">
                        <div class="empty-notifications" v-if="isMessagesEmpty && !isFetchNotify">
                            {{ $t('not_have_new_messages')}}
                        </div>
                    </content-loading>
                </div>
            </div>

            <div class="notification-footer">
                <a class="notifications-footer__controls-item"
                   href="#"
                   v-on:click.prevent="showAll()"
                   @click="$emit('hide')"
                >
                    {{ $t('show_older_notifications') }}...
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import ContentLoading       from '@views/components/ContentLoading'
    import NotificationsList    from '@views/components/notifications/NotificationsList'

	import ThemeSubscribers     from '@views/layouts/theme/ThemeSubscribers'
	import task                 from '@helpers/task';

    export default {
        name: "notifications-header",
		components: {
			ContentLoading,
			NotificationsList,
			ThemeSubscribers
		},
		props: {
			scrollContainer: {
				type: String,
			},
			inPage: {
				type: Boolean,
				default: false
			},
		},
        data() {
            return {
                isFetchNotify: false,
                styleLoading: {
                    'absolute': true,
                    'autosize': true
                },
                selectTab: { name: 'unread', label: this.$t('unread')},
                tabs: [
                    { name: 'unread', label: this.$t('unread')},
                    { name: 'read', label: this.$t('read')},
                    { name: 'ownLog', label: this.$t('own_log')},
                    { name: 'all', label: this.$t('all')},
                ],
                ownLog: {
                	list: [],
					current_page: 0,
					total_pages: 1,
                },
				showOldDesc: false
            }
        },
        computed: {
            ...mapGetters({
                getNotifications: 'notify/getMessages',
				getUnreadMessages: 'notify/getUnreadMessages',
                getMembers: 'members/getMembers',
				getUserId: 'user/getUserId',
				userInitials: 'user/getUserInitials',
				getAvatar: 'user/getAvatar'
            }),
            getNotificationByType() {
                let notify = {
                    unread: this.getUnreadMessages,
                    read: [],
                    ownLog: this.ownLog.list,
                    all: this.getNotifications,
                };

                this.getNotifications.map((item) => {
                    item.read_at ? notify.read.push(item) : '';
                });

                return notify
            },
            isMessagesEmpty() {
                return this.getNotifications.length === 0
            },
        },
        mounted () {
            this.$nextTick(function () {
            	this.getOwnLog();
                this.initNotification();
                this.nextNotifications();
            });
        },
        methods: {
			isJsonComment(comment) {
				return task.checkJson(comment);
			},
			parseJsonComment(comment) {
				return JSON.parse(comment);
			},
			getOwnLog() {
                this.$api.log.getActivityLogs(this.ownLog.current_page).then(response => {
					this.ownLog = {
						list: this.ownLog.list.concat(response.data),
						current_page: response.pagination.current_page + 1,
						total_pages: response.pagination.total_pages,
                    };
                });
            },
			setGroupTask(list) {
			    let newList = [],
                    prevTaskId = 0;

			    list.forEach(item => {
			    	if (item.task_id !== prevTaskId) {
						newList.push([item]);
						prevTaskId = item.task_id;
                    } else {
			    		newList[newList.length - 1].push(item)
                    }
                });

			    return newList;
            },
            handleSetTab(tab) {
                this.selectTab = tab;
            },
            initNotification () {
                if (this.$store.getters['notify/getCurrentPage'] !== 0) {
                    return
                }

                if (this.isFetchNotify) {
                    return
                }

                this.fetchNotifications();
            },
            nextNotifications() {
                let content;

                if (this.scrollContainer) {
                    content = document.querySelector(this.scrollContainer);
                } else {
                    content = this.$refs.notificationsBox;
                }
                content.onscroll = () => {
                    if (Math.ceil(content.scrollTop + content.clientHeight) >= content.children[0].scrollHeight && this.$route.name === 'notifications') {
                    	// This need for stop send query
                    	if (this.selectTab.name === 'unread' && this.getNotificationByType['unread'] < this.getNotificationByType['all']) {
                    		return;
                        }
                        this.uploadNotify();
                    }
                }
            },
            fetchNotifications () {
                this.isFetchNotify = true;

                this.$api.notify.getNotifications()
                    .finally(() => {
                        this.isFetchNotify = false;
                    });
            },
            uploadNotify() {
                if (this.isFetchNotify) {
                    return
                }

                if (this.selectTab.name !== 'ownLog' && this.$store.getters['notify/getLastPage'] > this.$store.getters['notify/getCurrentPage']) {
                    this.fetchNotifications();
                }
				if (this.selectTab.name === 'ownLog' && this.ownLog.total_pages > this.ownLog.current_page) {
					this.getOwnLog();
				}
            },
            allMarkRead() {
                this.$api.notify.markAllNotificationsRead()
            },
            showAll() {
                this.$emit('hide');
                this.$router.push({ name: 'notifications' });
            },
			showTask(notification) {
				if (notification.read_at === null) {
					this.$api.notify.markNotificationRead(notification.id)
				}

				// this.$emit('hide');

				if (notification.task_id) {
					if (this.$route.query.taskId !== notification.task_id) {
					    return this.$router.replace({query: {taskId: notification.task_id}});
                    }
					return;
				}

				if (notification.board_id){
					return this.$modal.show('board-setting-modal', {boardId: notification.board_id});
				}

				if (notification.group_id){
					if (!notification.task_id) {
						return this.$router.push({ name: 'communication', params: { group_id: notification.group_id } });
                    }

					if (!this.groupSettingModalShown) {
						if (this.handlePermissionByGroupId('read-group', notification.group_id)) {
							return this.$modal.show('group-setting-modal', {groupId: notification.group_id});
						} else {
							return this.sendNotifyPermissionInfo('read-group');
						}
					}
				}
			},

		},
    }
</script>

<style lang="scss">
    #notifications-box {
        width: 100vw;
        max-width:760px;
        height: calc(100vh - 72px);
        left:-144px;
        font-size: 12px;
        padding: 0;
        line-height: 1.4;
        display: block !important;
        top: 40px;
        border:none;
        box-shadow: 0 2px 4px 0 rgba(0,0,0,.16), 0 2px 8px 0 rgba(0,0,0,.12);
        border-radius: 5px;

        @media (min-width: 550px) {
            left:-189px;
        }
        @media (min-width: 750px) {
            left:-140px;
            height: calc(85vh - 72px);
            width:650px;
        }

        .notifications-header {
            margin:0;
            overflow: hidden;
            display: inline-flex;
            width: 100%;
            position:relative;
        }

        .notifications-header__title{
            font-weight: bold;
        }

        .notifications-header__controls{
            margin-left: auto;
            align-items: center;
            justify-content: center;
            display: flex;
            margin-right:15px;
            @media (min-width: 400px) {
                margin-right:30px;
            }
        }

        .notifications-header__controls-item{
            margin-right: 15px;
           @media (min-width: 400px) {
                margin-right:30px;
            }
                &:hover {
                color:#62a8ea;
            }
            &:last-child{
                margin-right: 0;
            }
        }

        .notification {
            position: relative;
            min-height: 72px;

            &-task {

                &-name {
                    padding: 10px 15px;
                    font-size: 16px;
                    font-weight: 600;
                    background: #dddfe2;
                    span {
                        color: #3669a7;
                        cursor: pointer;
                    }
                }

            }
        }

        .notifications-header {
            padding: 10px 30px 10px 10px;
            // box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
        }

        .notification {
            padding: 10px 30px 10px 10px;
            border: solid 1px #dddfe2;
            
        }

        .notification-body {
            position: relative;
            margin:0;
            height: calc(100% - 72px);
            overflow: auto;
        }

        .scroll-view {
            touch-action: none;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: 0 15px;
            overflow: hidden;

            & :last-child {
                border: none;
            }
        }


        .new-notification {
            background-color: #f1f1f1;

            &.notification-task-group {
                padding-left: 40px;
            }

        }

        .empty-notification-message {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            height: 100%;
            text-align: center;
            display: block;
            color: #b2b2b2;
            background: #fff;
            line-height: 39px;
            padding: 0 15px;
            border-radius: 5px;
        }
        .notification {
            cursor: pointer;
            font-size:14px;
            transition: .2s all;
            &:hover {
                border-color: #b1b1b1;
                /*border-left:5px solid #bbbbbb;*/
                border-right:none;
                box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
            }
            p {
                margin: 0;
                overflow: hidden;
               text-overflow: ellipsis;
               display: -webkit-box;
               line-height: 16px;
               max-height: 32px;
               -webkit-line-clamp: 2;
               -webkit-box-orient: vertical;
               margin-bottom: 5px;
               margin-left:35px;

            }

            .timestamp {
                color: #a2a2a2;
                 margin-left: 10px;
            }
        }

        .empty-notifications {
            text-align: center;
            padding: 80px 0;
            font-size: 14px;
            color: #444;
                .icon-no-messages {
                    display: flex;
                    justify-content: center;
                        .icon {
                            width: 60px;
                            height: 60px;
                            fill: #cecece;
                        }
                }
                span {
                    font-size: 18px;
                    color:#cecece;
                }
        }
        .notification-controls{
            position: absolute;
            right: 15px;
            top:50%;
            z-index: 1;
            transform: translateY(-50%);
        }
        .notification-controls-read-toggle{
            width: 15px;
            height: 15px;
            border: 1px solid #ff8aa6;
            background-color:#ff8aa6;
            border-radius: 50%;
            transition: .3s background-color ease, transform .3s ease;

            &.read{
                box-sizing: content-box;
                padding: 0;
                width: 10px;
                height: 10px;
                background-color: #fff;
                border: 2px solid #6f6f6f;

                &:hover {
                    background-color: #ff8aa6;
                    transform: scale(1.1);
                }
            }

            &:hover {
                background-color: #fff;
                transform: scale(1.1);
            }
        }
        .notification-item__header{
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            .user-avatar-image{
                margin-right: 8px;
                background-size: cover;
            }
        }
        .notification__user{
            margin-right: 5px;
            color: #a2a2a2;
            font-weight: bold;
        }

        .notification__action-type{
            color: #a2a2a2;
        }
        .notification__task-name{
            font-weight: bold;
            color: #3669a7;
        }
        .notification__task-communication{
            display: flex;
            align-items: center;

            .notification__task-name {
                margin: 0 3px;
            }
        }
        .notification-header-info-item{
            display: flex;
            flex-wrap: wrap;
            padding-right: 38px;
        }
         .notification-footer {
            border-top: solid 1px #dddfe2;
        }
        .notification-footer a {
            display:block;
            text-align:center;
            padding: 10px 20px 10px
        }

          .notifications-footer__controls-item{
                &:hover {
                color:#62a8ea;
            }
        }

        .btn_notification-box_close {
            position: absolute;
            right: 0;
            top:0;
            background-color: transparent;
            border: none;
            padding: 5px 12px;
            padding-bottom: 0;
            -webkit-box-shadow: none;
            box-shadow: none;
            &:hover {
                    .icon-close {
                        .icon {
                            fill: #62a8ea;
                         }
                }
            }
                .icon-close {
                 display:block;
                    .icon {
                        width:11px;
                        height:11px;
                    }
                }
         }

    }

    .notifications-page {
        #notifications-box {
            box-shadow: none;
            .notifications-header {
                box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12); 
            }
            .notifications-header__controls {
            margin-right:0;
        }
            .btn_notification-box_close {
                    padding:11px 12px;
                    display:none;
            }
            .new-notification {
                &:after {
                    display:none;
                }
            }

            .notification-footer {
                display:none;
             }

             .read-notification:after {
                 display: none;
             }

             .notification-body {
                 background: transparent;
             }
             .notification {
                 background: #fff;
             }

              .new-notification {
                background-color: #f1f1f1;
                // border-color: #ebccd1;
                .timestamp {
                    color: #7b7b7b;
                }

 
        }
        .notification-tabs {
            display:flex;
              .notification-tab__control-item {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    background: #fff;
                    padding: 10px 5px;
                    min-width: 60px;
                    text-align: center;
                    color:#3669a7;
                    font-weight: 700;
                    border-top-left-radius: 3px;
                    border-top-right-radius: 3px;
                    margin: 0 2px;
                        &:hover {
                            background: #f1f1f1;
                            border-top: 3px solid #b7b7b7;

                        }
                        &:first-child {
                            margin-left: 0;
                        }
                        &:last-child {
                            margin-right: 0;
                        }  
                }
                .notification-tab__control-item__active {
                    border-top: 3px solid #6291c8;
                }

             @media (min-width: 567px) {
                display: block;
                    .notification-tab__control-item {
                        display: inline-block;
                    }
             }
         }
         .notificaiton-body-messages {
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
            border-radius: 5px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            overflow: hidden;
            background: #fff;
            .empty-notifications {
                .icon-no-messages {
                    display: flex;
                    justify-content: center;
                        .icon {
                            width: 60px;
                            height: 60px;
                            fill: #cecece;
                        }
                }
                span {
                    font-size: 18px;
                    color:#cecece;
                }
            }
         }
        }
    }

    .activity-log {

        &-content {
            width: 100%;
            font-size: 14px;
            margin-left: 15px;
        }

        &-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: #fff;
            border: solid 1px #dddfe2;
            padding: 10px 30px 10px 10px;

            &-image {
                width: 50px;

                .menu-navbar-image {
                    width: 50px;
                    height: 50px;
                    line-height: 50px;
                }
            }
        }

        &-holder {
            width: 100%;
            max-width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
        }

        &-img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        &-task {
            display: inline-block;
            margin-bottom: 5px;
            font-size: 15px;
            font-weight: 600;
            color: #376aa7;
            text-transform: uppercase;
            border-bottom: 2px solid transparent;
            transition: border-bottom-color 0.3s cubic-bezier(0.23, 1, 0.32, 1);

            &:hover,
            &:focus {
                color: #376aa7;
                border-bottom-color: #376aa7;
                text-decoration: none !important;
            }
        }
        &-info {
            margin-bottom: 5px;
            font-size: 14px;

            img {
                display: inline-block;
                width: 100px;
                height: 100px;
                margin: 5px;
                border-radius: 10px;
                object-fit: cover;
            }
        }

        &-time {
            color: #a2a2a2;
            font-size: 14px;
        }
    }
</style>
