const TaskList = () => import(/* webpackChunkName: 'task-list' */ '@pages/TaskList');
const Filter = () => import(/* webpackChunkName: 'filter' */ '@pages/Filter');
const Deadline = () => import(/* webpackChunkName: 'deadline' */ '@pages/Deadline');
const Management = () => import(/* webpackChunkName: 'management' */ '@pages/Management');
const Communication = () => import(/* webpackChunkName: 'communication' */ '@views/layouts/task/communication/communication');
const Notifications = () => import(/* webpackChunkName: 'notifications' */ '@pages/Notifications');

const meta = { auth: true, group: true };

export default [
    {
        path: '/management',
        component: Management,
        name: 'management',
        meta: {
            ...meta,
            title: 'Management',
        },
    },
    {
        path: '/group/:group_id/board/:board_id',
        component: TaskList,
        name: 'board',
        meta,
    },
    {
        path: '/group/:group_id/communication',
        component: Communication,
        name: 'communication',
        meta: {
            ...meta,
            title: 'Communication',
        },

    },
    {
        path: '/deadline/:period',
        component: Deadline,
        name: 'deadline',
        meta: {
            ...meta,
            title: 'Deadline',
        },
    },
    {
        path: '/filter/:id',
        component: Filter,
        name: 'filter',
        meta: {
            ...meta,
            title: 'Filter',
        },
    },
    {
        path: '/notifications',
        component: Notifications,
        name: 'notifications',
        meta: {
            ...meta,
            title: 'Notifications',
        },
    },
];
