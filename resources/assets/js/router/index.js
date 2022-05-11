import VueRouter from 'vue-router'

import ProfilePage from './pages/ProfilePage'
import FaqPage from './pages/FaqPage'

export default new VueRouter({
    routes: [
        {
            path: '/index',
            component: ProfilePage
        },
        {
            path: '/faq',
            component: FaqPage
        }
    ]
})