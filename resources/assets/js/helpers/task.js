import moment   from "moment/moment";
import store    from "@assets/js/store";

export default {
    checkJson(str) {
        if (typeof str !== 'string') return false;
        try {
            const result = JSON.parse(str);
            const type = Object.prototype.toString.call(result);
            return type === '[object Object]'
                || type === '[object Array]';
        } catch (err) {
            return false;
        }
    },

    updateCountTask(data) {
        const { draft, subscribers, planned_deadline, done_by, id } = data;

        if (draft !== null) {
            return;
        }
        const isSubscriber  = subscribers.task.some(subscriberId => subscriberId === store.getters['user/getUserId']);

        const prevDay       = !planned_deadline || moment().isAfter(planned_deadline, 'days');
        const prevWeek      = !planned_deadline || moment().isAfter(planned_deadline, 'isoWeek');

        const isCurrentDay  = !planned_deadline || prevDay  ||
            moment().isSame(moment.utc(planned_deadline, "YYYY-MM-DD HH:mm:ss").local().format("YYYY-MM-DD HH:mm:ss"), 'day');
        const isCurrentWeek = !planned_deadline || prevWeek ||
            moment().isSame(moment.utc(planned_deadline, "YYYY-MM-DD HH:mm:ss").local().format("YYYY-MM-DD HH:mm:ss"), 'isoWeek');

        const payloadToday = { period: 'today', task_id: id };
        const payloadWeek = { period: 'week', task_id: id };

        if (done_by || !isSubscriber || !isCurrentDay) {
            store.dispatch('groups/removeTaskDeadline', payloadToday);
            store.dispatch('task/removeTask', payloadToday);
        } else if (!done_by && isSubscriber && isCurrentDay) {
            store.dispatch('groups/addTaskDeadline', payloadToday);
        }

        if (done_by || !isSubscriber || !isCurrentWeek) {
            store.dispatch('groups/removeTaskDeadline', payloadWeek);
            store.dispatch('task/removeTask', payloadToday);
        } else if (!done_by && isSubscriber && isCurrentWeek) {
            store.dispatch('groups/addTaskDeadline', payloadWeek);
        }
    }
}
