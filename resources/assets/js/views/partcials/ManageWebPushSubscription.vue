<template>
    <div class="col-xs-12 col-sm-offset-5 col-sm-5 col-lg-offset-5 col-lg-3 control-label text-align-right">
        <theme-button-success @click.native="togglePush" class="btn btn-md"  :disabled="isLoading">
            {{ isPushEnabled ? $t('disable_web_push_notifications') : $t('enable_web_push_notifications') }}
        </theme-button-success> 
        <button @click="testSubscription" class="btn btn-primary btn-md"  v-if="isPushEnabled">
            {{ $t('send_test_notification') }}
        </button>
    </div>
</template>

<script>
    import config               from '@config';
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        name: "manage-web-push-subscription",
        data: function () {
            return {
                isPushEnabled: false,
            }
        },
        components: {
            ThemeButtonSuccess
        },
        mounted() {
            this.registerServiceWorker();
        },
        methods: {
            /**
             * Register the service worker.
             */
            registerServiceWorker () {
                if (location.protocol !== 'https:'){
                    console.error('Please note that web push notifications require https to function');
                }
                if (!('serviceWorker' in navigator)) {
                    console.log('Service workers aren\'t supported in this browser.');
                    return
                }
                console.info('register and init');
                navigator.serviceWorker.register('/sw.js')
                    .then(() => this.initialiseServiceWorker())
            },

            initialiseServiceWorker () {
                if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
                    console.log('Notifications aren\'t supported.');
                    return
                }

                if (Notification.permission === 'denied') {
                    console.log('The user has blocked notifications.');
                    return
                }

                if (!('PushManager' in window)) {
                    console.log('Push messaging isn\'t supported.');
                    return
                }

                navigator.serviceWorker.ready.then(registration => {
                    registration.pushManager.getSubscription()
                        .then(subscription => {
                            this.pushButtonDisabled = false;

                            if (!subscription) {
                                return
                            }

                            this.updateSubscription(subscription);

                            this.isPushEnabled = true
                        })
                        .catch(e => {
                            console.log('Error during getSubscription()', e);
                        })
                })
            },

            /**
             * Subscribe for push notifications.
             */
            subscribe () {
                const vapidPublicKey = config.api.vapid_public_key;
                console.info('config', config);

                navigator.serviceWorker.ready.then(registration => {
                     // Debugging subscription for mobile apps
                    console.log(registration);
                    console.log(registration.pushManager);
                     // Debugging subscription for mobile apps

                    const options = { userVisibleOnly: true };

                    if (vapidPublicKey) {
                        options.applicationServerKey = this.urlBase64ToUint8Array(vapidPublicKey)
                    }

                    registration.pushManager.subscribe(options)
                        .then(subscription => {
                            this.isPushEnabled = true
                            this.pushButtonDisabled = false

                            this.updateSubscription(subscription).then(_ => {
								window.app.$notify({type:'success', text: 'Subscription Saved'});
							})
                        })
                        .catch(e => {
                            if (Notification.permission === 'denied') {
                                console.log('Permission for Notifications was denied');
                                this.pushButtonDisabled = true
                            } else {
                                console.log('Unable to subscribe to push.', e);
                                this.pushButtonDisabled = false
                            }
                        })
                })
            },

            /**
             * Unsubscribe from push notifications.
             */
            unsubscribe () {
                navigator.serviceWorker.ready.then(registration => {
                    registration.pushManager.getSubscription().then(subscription => {
                        if (!subscription) {
                            this.isPushEnabled = false
                            this.pushButtonDisabled = false
                            return
                        }

                        subscription.unsubscribe().then(() => {
                            this.deleteSubscription(subscription);

                            this.isPushEnabled = false;
                            this.pushButtonDisabled = false;
                        }).catch(e => {
                            console.log('Unsubscription error: ', e);
                            this.pushButtonDisabled = false;
                        })
                    }).catch(e => {
                        console.log('Error thrown while unsubscribing.', e);
                    })
                })
            },

            testSubscription() {
                this.$api.user.sendWelcomeNotification().then((res) => {
                }).catch(e => {
                    console.log('Sending error: ', e)
                });
            },

            /**
             * Toggle push notifications subscription.
             */
            togglePush () {
                if (this.isPushEnabled) {
                    this.unsubscribe()
                } else {
                    this.subscribe()
                }
            },

            /**
             * Send a request to the server to update user's subscription.
             *
             * @param {PushSubscription} subscription
             */
            updateSubscription (subscription) {
                const key = subscription.getKey('p256dh')
                const token = subscription.getKey('auth')

                const data = {
                    endpoint: subscription.endpoint,
                    key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                    token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
                }

                return this.$api.user.subscribeToWebPush(data);
            },

            /**
             * Send a requst to the server to delete user's subscription.
             *
             * @param {PushSubscription} subscription
             */
            deleteSubscription (subscription) {
                this.$api.user.unsubscribeFromWebPush({ endpoint: subscription.endpoint });
            },

            /**
             * Send a request to the server for a push notification.
             */
            sendNotification () {
                this.loading = true;

                axios.post('/notifications')
                    .then(() => { this.loading = false });
            },

            /**
             * https://github.com/Minishlink/physbook/blob/02a0d5d7ca0d5d2cc6d308a3a9b81244c63b3f14/app/Resources/public/js/app.js#L177
             *
             * @param  {String} base64String
             * @return {Uint8Array}
             */
            urlBase64ToUint8Array (base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/')

                const rawData = window.atob(base64)
                const outputArray = new Uint8Array(rawData.length)

                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i)
                }

                return outputArray
            }
        }
    }
</script>

<style scoped>

</style>
