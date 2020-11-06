<template>
    <div
        :class="['notification', { 'new-notification': notification.read_at === null }]"
    >
        <div class="notification-controls" >
            <button
                :class="['notification-controls-read-toggle', {read : notification.read_at}]"
                @click.stop="markRead(notification)"
            ></button>
        </div>

        <div class="notification-item__header">
            <theme-subscribers class="user-avatar-image" :style="{'background-image': 'url(' + notification.sender_avatar + ')'}">
                <span v-if="!notification.sender_avatar">{{ userInitials(notification) }}</span>
            </theme-subscribers>
            <div class="notification-header-info">
                <div class="notification-header-info-item">

                    <div class="notification__user">
                        {{notification.sender_name}} {{notification.sender_last_name }}
                    </div>

                    <div class="notification__action-type notifictaion__action-message" v-if="notification.action_message">{{ notification.action_message }}</div>

                    <!--<div
                        v-if="notification.action_type == 'done'"
                        class="notification__action-type"
                    >
                        {{ notification.message }}
                    </div>

                    <div
                        v-if="notification.action_type == 'rename'"
                        class="notification__action-type"
                    >
                        {{ $t('changed_name_of_task') }}
                    </div>

                    <div
                        v-if="notification.action_type == 'mention'"
                        class="notification__action-type"
                    >
                        {{ $t('mentioned_you') }}
                    </div>

                    <div
                        v-if="notification.action_type == 'subscribed' || notification.action_type == 'unsubscribed'"
                        class="notification__action-type"
                    >
                        {{ notification.action_type }} {{ notification.notifiable_user}}
                    </div>

                    <div
                        v-if="['changed priority'].includes(notification.action_type)"
                        class="notification__action-type"
                    >
                        {{ notification.action_type }}
                        {{ notification.message }}
                    </div>

                    <div
                        v-if="!['subscribed', 'done', 'unsubscribed', 'assigned', 'unassigned', 'mention', 'rename', 'changed priority'].includes(notification.action_type)"
                        class="notification__action-type"
                    >
                        {{ notification.action_type }}
                    </div>-->

                </div>

                <div
                    v-if="notification.task_name"
                    class="notification__task-name"
                >
                    <router-link :to="{ query: { taskId: notification.task_id } }">{{ notification.task_name }}</router-link><small class="timestamp">{{ notification.created_at_diff_for_humans }}</small>
                </div>

                <div
                    v-if="!notification.task_name"
                    class="notification__task-communication"
                >
                    <span>{{ $t('comment_from') }}</span>
                    <div class="notification__task-name"> {{ $t('communication') }} </div>
                    <small class="timestamp">{{ notification.created_at_diff_for_humans }}</small>
                </div>

                <div
                    v-if="!notification.task_name"
                    class="notification__task-message"
                >{{ $t('text_message') }}: <span v-html="notification.message.replace(/<[^>]*>?/gm, '')"></span></div>

            </div>
        </div>
    </div>
</template>

<script>
    import ThemeSubscribers from '@views/layouts/theme/ThemeSubscribers'

    export default {
        props: {
            notification: {
                type: Object,
            },
        },
        components: {
            ThemeSubscribers,
        },
        methods: {
            convertTranslateKey(key) {
               return this.$t(key.replace(/\s+/g, '_').toLowerCase())
            },
            userInitials(user) {
                return user ? `${user.sender_name[0]}${user.sender_last_name[0]}` : 'S';
            },
            markRead(notification) {
                if (notification.read_at === null) {
                    this.$api.notify.markNotificationRead(notification.id)
                } else {
                    this.$api.notify.markNotificationUnRead(notification.id)
                }
            },
		}
    }
</script>
