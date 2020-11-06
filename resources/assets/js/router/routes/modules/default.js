const NotFound = () => import(/* webpackChunkName: 'not-found' */ '@pages/NotFound');

export default [{
    path: '/home',
    redirect: '/deadline/day',
    name: 'home',
    meta: { auth: true }
},
{
    path: '/',
    redirect: '/deadline/day',
    meta: { auth: true }
},
{
    path: '/oauth',
    name: 'oauth',
},
{
    path: '/not-found',
    component: NotFound,
    name: 'not-found'
},
{
    path: '/video/wrong-url',
    name: 'wronf-url'
},
{
    path: '/*',
        redirect: {
        name: 'not-found'
    }
}];