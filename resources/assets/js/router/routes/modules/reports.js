const Reports = () => import(/* webpackChunkName: 'reports' */ '@pages/Reports');
const YearOverview = () => import(/* webpackChunkName: 'year-overview' */ '@pages/YearOverview');
const Bills = () => import(/* webpackChunkName: 'bills' */ '@pages/Bills');

const meta = { auth: true, report_name: true, group: true };

export default [
    {
        path: '/reports',
        component: Reports,
        name: 'reports',
        meta: {
            ...meta,
            title: 'Reports',
        },
    },
    {
        path: '/year-overview',
        component: YearOverview,
        name: 'year-overview',
        meta: {
            ...meta,
            title: 'Year Overview',
        },
    },
    {
        path: '/bills',
        component: Bills,
        name: 'bills',
        meta: {
            ...meta,
            title: 'Bills',
        },
    },
]
