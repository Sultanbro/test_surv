import VueRouter from 'vue-router'

import ProfileView from '@/views/ProfileView'
import CabinetView from '@/views/CabinetView'
import NewsView from '@/views/NewsView'
import KnowledgeBaseView from '@/views/KnowledgeBaseView'
import UpbooksView from '@/views/UpbooksView'
import PlaylistsView from '@/views/PlaylistsView'
import CoursesView from '@/views/CoursesView'
import TimetrackingView from '@/views/TimetrackingView'

import MyCoursesView from '@/views/MyCoursesView'
// import StructureView from '@/views/StructureView'
import EntertimeView from '@/views/EntertimeView'
import HRView from '@/views/HRView'
import TopView from '@/views/TopView'
import AnalyticsView from '@/views/AnalyticsView'
import SalaryView from '@/views/SalaryView'
import QualityControlView from '@/views/QualityControlView'
import MapView from '@/views/MapView'
import KPIView from '@/views/KPIView'

// Cтраницу настроек наверное тоже разделим если нужно
import SettingsView from '@/views/SettingsView'

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
                title: 'Новости',
            },
        },
        // {
        //     path: '/structure',
        //     name: 'StructureView',
        //     component: StructureView,
        //     meta: {
        //         title: 'Структура компании',
        //     },
        // },
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
            path: '/my-courses',
            name: 'MyCoursesView',
            component: MyCoursesView,
            meta: {
                title: 'Мои курсы',
            },
        },
        {
            path: '/timetracking/reports',
            name: 'TimetrackingView',
            component: TimetrackingView,
            meta: {
                title: 'Табель - Учет времени',
            },
        },
        {
            path: '/timetracking/top',
            name: 'TopView',
            component: TopView,
            meta: {
                title: 'ТОП',
            },
        },
        {
            path: '/timetracking/reports/enter-report',
            name: 'EntertimeView',
            component: EntertimeView,
            meta: {
                title: 'Время прихода - Учет времени',
            },
        },
        {
            path: '/timetracking/analytics',
            name: 'HRView',
            component: HRView,
            meta: {
                title: 'HR - аналитика рекрутинга',
            },
        },
        {
            path: '/timetracking/an',
            name: 'AnalyticsView',
            component: AnalyticsView,
            meta: {
                title: 'Аналитика отделов',
            },
        },
        {
            path: '/timetracking/salaries',
            name: 'SalaryView',
            component: SalaryView,
            meta: {
                title: 'Начисления',
            },
        },
        {
            path: '/timetracking/quality-control',
            name: 'QualityControlView',
            component: QualityControlView,
            meta: {
                title: 'Контроль качества',
            },
        },
        {
            path: '/maps',
            name: 'MapView',
            component: MapView,
            meta: {
                title: 'Карта сотрудников',
            },
        },
        {
            path: '/kpi',
            name: 'KPIView',
            component: KPIView,
            meta: {
                title: 'KPI - показателей',
            },
        },
        {
            path: '/timetracking/info',
            name: 'FAQView',
            component: () => import(/* webpackChunkName: "FAQView" */ '@/views/FAQView'),
            meta: {
                title: 'ЧАВО - FAQ',
            },
        },
        {
            path: '/timetracking/fines',
            name: 'FinesView',
            component: () => import(/* webpackChunkName: "FinesView" */ '@/views/FinesView'),
            meta: {
                title: 'Депремирование',
            },
        },
        {
            path: '/timetracking/settings',
            name: 'SettingsView',
            component: SettingsView,
            meta: {
                title: 'Настройки',
            },
        },
        {
            path: '/timetracking/edit-person',
            name: 'UserEditView',
            component: () => import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
            meta: {
                title: 'Редактирование профиля сотрудника',
            },
        },
    ],
})
export default router