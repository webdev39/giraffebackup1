const Login    = () => import(/* webpackChunkName: 'login */ '@pages/auth/Login');
const Register = () => import(/* webpackChunkName: 'register' */ '@pages/auth/Register');
const Reset    = () => import(/* webpackChunkName: 'reset' */ '@pages/auth/Reset');
const Restore  = () => import(/* webpackChunkName: 'restore' */ '@pages/auth/Restore');
const Invite   = () => import(/* webpackChunkName: 'invite' */ '@pages/auth/Invite');
const Confirm  = () => import(/* webpackChunkName: 'confirm' */ '@pages/auth/Confirm');
const Profile  = () => import(/* webpackChunkName: 'profile' */ '@pages/Profile');

const authFalse = { auth: false };
const authTrue = { auth: true };

export default [
    {
        path: '/login',
        component: Login,
        name: 'login',
        meta: {
            ...authFalse,
            title: 'Login',
        },
    },
    {
        path: '/register',
        component: Register,
        name: 'register',
        meta: {
            ...authFalse,
            title: 'Register',
        },
    },
    {
        path: '/reset-password',
        component: Reset,
        name: 'reset-password',
        meta: {
            ...authFalse,
            title: 'Reset Password',
        },
    },
    {
        path: '/restore-password',
        component: Restore,
        name: 'restore-password',
        meta: {
            ...authFalse,
            title: 'Restore Password',
        },
    },
    {
        path: '/confirm',
        component: Confirm,
        name: 'confirm',
        meta: {
            ...authFalse,
            title: 'Confirm',
        },
    },
    {
        path: '/invite',
        component: Invite,
        name: 'invite',
        meta: {
            ...authFalse,
            title: 'Invite',
        },
    },

    {
        path: '/profile',
        component: Profile,
        name: 'profile',
        meta: {
            ...authTrue,
            title: 'Profile',
        },
    }
]
