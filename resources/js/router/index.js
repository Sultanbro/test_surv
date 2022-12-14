import VueRouter from 'vue-router'

export default new VueRouter({
    routes: [
        {
            path: '/',
            component: () => import(/* webpackChunkName: "ProfilePage" */ '@/pages/Profile/ProfilePage'),
        },
        {
            path: '/news',
            component: () => import(/* webpackChunkName: "NewsView" */ '@/views/NewsView.vue'),
        },
    ],
})