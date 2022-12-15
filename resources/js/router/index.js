import VueRouter from 'vue-router'
import axios from 'axios'

import ProfileView from '@/views/ProfileView'
import CabinetView from '@/views/CabinetView'
import NewsView from '@/views/NewsView'
import KnowledgeBaseView from '@/views/KnowledgeBaseView'
import UpbooksView from '@/views/UpbooksView'
import PlaylistsView from '@/views/PlaylistsView'
import CoursesView from '@/views/CoursesView'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'ProfileView',
            component: ProfileView,
            meta: {
                title: 'Мой профиль',
            },
        },
        {
            path: '/cabinet',
            name: 'CabinetView',
            component: CabinetView,
            meta: {
                title: 'Настройка кабинета',
            },
        },
        {
            path: '/news',
            name: 'NewsView',
            component: NewsView,
            meta: {
                title: 'NEWS',
            },
        },
        {
            path: '/kb',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/admin/upbooks',
            name: 'UpbooksView',
            component: UpbooksView,
            meta: {
                title: 'Редактор книг',
            },
        },
        {
            path: '/video_playlists',
            name: 'PlaylistsView',
            component: PlaylistsView,
            meta: {
                title: 'Плейлисты - Видео обучение',
            },
        },
        {
            path: '/courses',
            name: 'CoursesView',
            component: CoursesView,
            meta: {
                title: 'Курсы',
            },
        },
        {
            path: '/timetracking/reports',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/top',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/reports/enter-report',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/analytics',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/an',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/salaries',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/quality-control',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/maps',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/kpi',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/info',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/fines',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/callibro/login',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/settings',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        {
            path: '/timetracking/edit-person',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
    ],
})
export default router