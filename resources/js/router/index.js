import Vue from 'vue'
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
        // newprofile.blade.php
        { path: '/profile', redirect: '/' },
        {
            path: '/',
            name: 'ProfileView',
            component: ProfileView,
            meta: {
                title: 'Мой профиль',
            },
        },
        // cabinet.blade.php
        {
            path: '/cabinet',
            name: 'CabinetView',
            component: CabinetView,
            meta: {
                title: 'Настройка кабинета',
            },
        },
        // news.blade.php
        {
            path: '/news',
            name: 'NewsView',
            component: NewsView,
            meta: {
                title: 'Новости',
            },
        },
        // ???.blade.php
        // {
        //     path: '/structure',
        //     name: 'StructureView',
        //     component: StructureView,
        //     meta: {
        //         title: 'Структура компании',
        //     },
        // },
        // surv/kb.blade.php
        {
            path: '/kb',
            name: 'KnowledgeBaseView',
            component: KnowledgeBaseView,
            meta: {
                title: 'База знаний',
            },
        },
        // upbooks.blade.php
        {
            path: '/admin/upbooks',
            name: 'UpbooksView',
            component: UpbooksView,
            meta: {
                title: 'Редактор книг',
            },
        },
        // admin/playlists/index.blade.php
        {
            path: '/video_playlists',
            name: 'PlaylistsView',
            component: PlaylistsView,
            meta: {
                title: 'Плейлисты - Видео обучение',
            },
        },
        // surv/courses.blade.php
        {
            path: '/courses',
            name: 'CoursesView',
            component: CoursesView,
            meta: {
                title: 'Курсы',
            },
        },
        // admin/mycourse.blade.php
        {
            path: '/my-courses',
            name: 'MyCoursesView',
            component: MyCoursesView,
            meta: {
                title: 'Мои курсы',
            },
        },
        // admin/reports.blade.php
        {
            path: '/timetracking/reports',
            name: 'TimetrackingView',
            component: TimetrackingView,
            meta: {
                title: 'Табель - Учет времени',
            },
        },
        // admin/top.blade.php
        {
            path: '/timetracking/top',
            name: 'TopView',
            component: TopView,
            meta: {
                title: 'ТОП',
            },
        },
        // admin/enter-report.blade.php
        {
            path: '/timetracking/reports/enter-report',
            name: 'EntertimeView',
            component: EntertimeView,
            meta: {
                title: 'Время прихода - Учет времени',
            },
        },
        // admin/analytics.blade.php
        {
            path: '/timetracking/analytics',
            name: 'HRView',
            component: HRView,
            meta: {
                title: 'HR - аналитика рекрутинга',
            },
        },
        // admin/analytics-page.blade.php
        {
            path: '/timetracking/an',
            name: 'AnalyticsView',
            component: AnalyticsView,
            meta: {
                title: 'Аналитика отделов',
            },
        },
        // admin/salary.blade.php
        {
            path: '/timetracking/salaries',
            name: 'SalaryView',
            component: SalaryView,
            meta: {
                title: 'Начисления',
            },
        },
        // admin/quality_control.blade.php
        {
            path: '/timetracking/quality-control',
            name: 'QualityControlView',
            component: QualityControlView,
            meta: {
                title: 'Контроль качества',
            },
        },
        // surv/maps.blade.php
        {
            path: '/maps',
            name: 'MapView',
            component: MapView,
            meta: {
                title: 'Карта сотрудников',
            },
        },
        // kpi.blade.php
        {
            path: '/kpi',
            name: 'KPIView',
            component: KPIView,
            meta: {
                title: 'KPI - показателей',
            },
        },
        // admin/info.blade.php
        {
            path: '/timetracking/info',
            name: 'FAQView',
            component: () => import(/* webpackChunkName: "FAQView" */ '@/views/FAQView'),
            meta: {
                title: 'ЧАВО - FAQ',
            },
        },
        // admin/fines.blade.php
        {
            path: '/timetracking/fines',
            name: 'FinesView',
            component: () => import(/* webpackChunkName: "FinesView" */ '@/views/FinesView'),
            meta: {
                title: 'Депремирование',
            },
        },
        // admin/settingtimetracking.blade.php
        {
            path: '/timetracking/settings',
            name: 'SettingsView',
            component: SettingsView,
            meta: {
                title: 'Настройки',
            },
        },
        // admin/users/create.blade.php
        {
            path: '/timetracking/edit-person',
            name: 'UserEditView',
            component: () => import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
            meta: {
                title: 'Редактирование профиля сотрудника',
            },
        },
        // admin/users/create.blade.php
        {
            path: '/timetracking/create-person',
            name: 'UserEditViewCreate',
            component: () => import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
            meta: {
                title: 'Создание профиля сотрудника',
            },
        },
    ],
})

const DEFAULT_TITLE = 'Jobtron.org'
router.afterEach((to, from) => {
    // Use next tick to handle router history correctly
    // see: https://github.com/vuejs/vue-router/issues/914#issuecomment-384477609
    Vue.nextTick(() => {
        document.title = to.meta.title || DEFAULT_TITLE
    })
})

export default router