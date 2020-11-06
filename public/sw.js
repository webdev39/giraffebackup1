(() => {
    'use strict'

    // import http from '@utils/http'

    const WebPush = {
        init () {
            self.addEventListener('push', this.notificationPush.bind(this))
            self.addEventListener('notificationclick', this.notificationClick.bind(this))
            self.addEventListener('notificationclose', this.notificationClose.bind(this))
        },

        /**
         * Handle notification push event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/push
         *
         * @param {NotificationEvent} event
         */
        notificationPush (event) {
            if (!(self.Notification && self.Notification.permission === 'granted')) {
                return
            }

            // https://developer.mozilla.org/en-US/docs/Web/API/PushMessageData
            if (event.data) {
                event.waitUntil(
                    this.sendNotification(event.data.json())
                )
            }
        },

        /**
         * Handle notification click event.
         *
         * https://developer.mozilla.org/en-US/docs/Web/Events/notificationclick
         *
         * @param {NotificationEvent} event
         */
        notificationClick (event) {
           // event.preventDefault(); // prevent the browser from focusing the Notification's tab
            console.info(event);
            const action = event.notification.actions[0] === undefined ? '' : event.notification.actions[0].action;
            console.info('notificationClick action', action, event);
            let url = action.length
                ? action
                : '/?json='+JSON.stringify(event.notification.data);

            console.info('url', url);
            self.clients.openWindow(url);
        },

        /**
         * Handle notification close event (Chrome 50+, Firefox 55+).
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerGlobalScope/onnotificationclose
         *
         * @param {NotificationEvent} event
         */
        notificationClose (event) {
            self.registration.pushManager.getSubscription().then(subscription => {
                if (subscription) {
                    this.dismissNotification(event, subscription)
                }
            })
        },

        /**
         * Send notification to the user.
         *
         * https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorkerRegistration/showNotification
         *
         * @param {PushMessageData|Object} data
         */
        sendNotification (data) {
            console.info('sendNotification data', data);
            return self.registration.showNotification(data.title, { body: data.body, url: 'https://google.com', actions: data.actions})
        },

        /**
         * Send request to server to dismiss a notification.
         *
         * @param  {NotificationEvent} event
         * @param  {String} subscription.endpoint
         * @return {Response}
         */
        dismissNotification ({ notification }, { endpoint }) {
            if (!notification.data || !notification.data.id) {
                return
            }

            const data = new FormData()
            data.append('endpoint', endpoint)

            // Send a request to the server to mark the notification as read.
            // http.post(`/notifications/${notification.data.id}/dismiss`, data);
        }
    }

    WebPush.init()
})()
