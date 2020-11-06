const ManageRoles = () => import(/* webpackChunkName: 'manage-found' */ '@pages/manage/ManageRoles');
const ManagePipelines = () => import(/* webpackChunkName: 'manage-pipelines' */ '@pages/manage/ManagePipelines');
const ManageCustomers = () => import(/* webpackChunkName: 'manage-customers' */ '@pages/manage/ManageCustomers');
const ManageGroups = () => import(/* webpackChunkName: 'manage-groups' */ '@pages/manage/ManageGroups');
const ManageTenants = () => import(/* webpackChunkName: 'manage-tenants' */ '@pages/manage/ManageTenants');
const ManageUsers = () => import(/* webpackChunkName: 'manage-users' */ '@pages/manage/ManageUsers');
const ManageSystemSettings = () => import(/* webpackChunkName: 'manage-system-settings' */ '@pages/manage/ManageSystemSettings');

const auth = { auth: true };
const full = { auth: true, group: true };
export default [
    {
        path: '/manage',
        component: {
            render (c) { return c('router-view') }
        },
        children: [
            {
                path: '/clients',
                component: ManageCustomers,
                name: 'manage-customers',
                meta: {
                    ...auth,
                    title: 'Manage Customers',
                },
            }, {
                path: '/groups',
                component: ManageGroups,
                name: 'manage-groups',
                meta: {
                    ...full,
                    title: 'Manage Groups',
                },
            }, {
                path: '/users',
                component: ManageUsers,
                name: 'manage-users',
                meta: {
                    ...auth,
                    title: 'Manage Users',
                },
            }, {
                path: '/pipelines',
                component: ManagePipelines,
                name: 'manage-pipelines',
                meta: {
                    ...auth,
                    title: 'Manage Pipelines',
                },
            }, {
                path: '/roles',
                component: ManageRoles,
                name: 'manage-roles',
                meta: {
                    ...full,
                    title: 'Manage Roles',
                },
            }, {
                path: '/system-settings',
                component: ManageSystemSettings,
                name: 'manage-system-settings',
                meta: {
                    ...auth,
                    title: 'System settings',
                },
            },
        ]
    },
]
