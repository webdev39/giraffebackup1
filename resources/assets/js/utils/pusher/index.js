import Echo         from 'laravel-echo'
import pusher       from 'pusher-js'

import api          from '@api'
import config       from '@config'
import store        from '@store'
import router       from "@router";
window.io = require('socket.io-client');

class Index {

    constructor() {
        this.echo = null;
    }

    // pusher
    connect() {
        if (this.echo === null) {
            this.connectSocketIo();
        }

        return this;
    }

    // Sockets use pusher
    connectPusher() {
        this.echo = new Echo({
            broadcaster:    config.pusher.broadcaster,
            authEndpoint:   config.url + '/' + config.api.pusher.auth_url,
            key:            config.api.pusher.key,
            cluster:        config.api.pusher.cluster,
            encrypted:      true,
            auth: {
                headers: {
                    Authorization: 'Bearer ' + store.state.token
                }
            },
        });

        this.echo.connector.pusher.connection.bind('connected', event => this.constructor.connected(event));
        this.echo.connector.pusher.connection.bind('disconnected', () => this.constructor.disconnected());
    }

    connectSocketIo() {
        this.echo = new Echo({
            broadcaster:    config.api.socket_io.broadcaster,
            authEndpoint:   config.url + '/' + config.api.socket_io.auth_url,
            host:           config.url + ':' + config.api.socket_io.port,
            key:            config.api.socket_io.key,
            auth: {
                headers: {
                    Authorization: 'Bearer ' + store.state.token
                }
            },
        });
    }

    disconnect() {
        if (this.echo !== null) {
            this.echo.disconnect();
            this.echo = null;
        }

        return this;
    }

    run() {
        let channel = this.echo.private('user.' + store.state.user.id);

        /** notification **/
        channel.notification(notification => {
            const data = Object.assign({}, notification.data, {
                read_at:    null,
                created_at: notification.created_at,
                id:         notification.id
            });

            store.dispatch('notify/addMessage', data);
            store.dispatch('notify/addUnreadMessages', data);
        });

        /** timer event **/
        channel.listen('.timer.start', (event) => {
            api.timer.getTimers();
        }).listen('.timer.pause', (event) => {
            api.timer.getTimers();
        }).listen('.timer.continue', (event) => {
            api.timer.getTimers();
        }).listen('.timer.stop', (event) => {
            api.timer.getTimers();
        }).listen('.timer.update', (event) => {
            api.timer.getTimer(event.id);
        });

        this.listenChangeTask();
    }

    leavingCommunication(roomId) {
        this.echo.leave('communication.' + roomId);
    }

    listenCommunication(roomId) {
        let channel = this.echo.channel('communication.' + roomId);

        channel.listen(`.message.send`, event => {
            if (event.user.id !== store.getters['user/getUserId']) {
                let comment = event.model;
                comment.user = event.user;

                if (comment.parent_id) {
                    store.dispatch('actions/addReply', event.model);
                    return;
                }

                event.model.new_comment = true;
                if (event.model.task) {
                    event.model.task_title = event.model.task.name;
                }

                store.dispatch('actions/addActions', event.model);
            }
        });

        channel.listen(`.comment.deleted`, (event) => {
            if (event.user.id !== store.getters['user/getUserId']) {
                store.dispatch('actions/removeAction', event.model);
            }
        });
    }

    listenChangeTask() {
        let channel = this.echo.channel('tasks');

        let checkCanUpdateTask = (event, callback) => {
            let currentRouteQuery = router.currentRoute.query;

            if (event.subscribers_to_task.includes(store.getters['user/getUserId'])) {
                callback();
            } else {
                if (currentRouteQuery.hasOwnProperty('taskId') && Number(currentRouteQuery.taskId) === Number(event.model.id)) {
                    callback();
                }
            }
        };

        channel.listen('.task.changed', event => {
            checkCanUpdateTask(event, () => {
                if (! event.model.draft) {
                    api.task.getTaskById(event.model.id);
                    api.task.getTasksDeadline();
                }
            });
        });

        channel.listen('.task.created', event => {
            if (event.subscribers_to_task.includes(store.getters['user/getUserId'])) {
                api.task.getTaskById(event.model.id)
            }
        });

        channel.listen('.activity-log.changed', event => {
            checkCanUpdateTask(event, () => {
                if (Number(event.model.id) === Number(store.getters['task/getTaskId']) && ! event.model.draft) {
                    api.actions.getActionsByTaskId(event.model.id, 1, {
                        assigned: [],
                        filters: [],
                        columns: [],
                    }, true)
                }
            });
        });

    }

    static connected(event) {
        if (store.debug) {
            console.log('connected', event);
        }
    }

    static disconnected() {
        if (store.debug) {
            console.log('disconnected');
        }
    }
}

export default new Index();
