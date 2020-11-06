import api      from '@api'
import store    from '@store'
import router   from '@router'
import pusher   from '@utils/pusher'
import sentry   from '@utils/sentry'

export default {
    login(data) {
        localStorage.removeItem('memberToken');

        if (data.token) {
            try {
                api.filter.getFilters();

                const canTrack = data.permissions.find(perm => perm.name === 'time-tracking');

                if (canTrack) {
                    api.timer.getTimers();
                }

                api.group.getGroups();
                api.tag.getTags();

                store.dispatch('setTokenAndLoggedIn',           data.token);
                store.dispatch('user/setProfile',               data.profile);
                store.dispatch('user/setTheme',                 data.colorSchemes);
                store.dispatch('user/setThemeId',               data.profile.selected_color_scheme_id || 16);
                store.dispatch('members/setMembers',            data.members);

                store.dispatch('priorities/setPriorities',      data.priorities);
                store.dispatch('permissions/setPermissions',    data.permissions);
                store.dispatch('permissions/setAllPermissions', data.allPermissions);

                store.dispatch('notify/setUnreadMessages',      data.unread_notifications);

                store.dispatch('groups/setTasksDeadline',       data.tasks_deadline);

                store.dispatch('settings/setSettings',          data.settings);

                window.app.$i18n.locale = store.getters['user/getUserProfile'].language.iso_639_1;

                if (pusher.echo === null) {
                    pusher.connect().run();
                }

                return true;
            } catch (e) {
                sentry.captureException(e);
            }
        }

        return false;
    },
    logout(notify = false) {

        if (store.getters['isMemberLoggedIn']) {
            return location.reload();
        }

        pusher.disconnect();

        store.dispatch('setTokenAndLoggedIn', null);
        store.dispatch('setDeviceToken', null);
        store.dispatch('user/clearProfile');

        router.push({name: 'login'});

        store.dispatch('resetStore');

        if (notify) {
            window.app.$notify({type:'error', text: window.app.$t('the_token_expired_please_login')});
        }

        return true;
    }
}
