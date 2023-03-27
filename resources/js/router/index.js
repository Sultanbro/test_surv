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
// import StructureView from '@/views/StructureView'

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
				viewport: true,
				bodyClass: 'profile-page',
				menuItem: 'profile',
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
				viewport: true,
				menuItem: 'news',
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
				menuItem: 'kb',
			},
		},
		// upbooks.blade.php
		{
			path: '/admin/upbooks',
			name: 'UpbooksView',
			component: UpbooksView,
			meta: {
				title: 'Редактор книг',
				menuItem: 'courses',
			},
		},
		// admin/playlists/index.blade.php
		{
			path: '/video_playlists',
			name: 'PlaylistsView',
			component: PlaylistsView,
			meta: {
				title: 'Плейлисты - Видео обучение',
				menuItem: 'courses',
			},
		},
		// surv/courses.blade.php
		{
			path: '/courses',
			name: 'CoursesView',
			component: CoursesView,
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
		// admin/mycourse.blade.php
		{
			path: '/my-courses',
			name: 'MyCoursesView',
			component: MyCoursesView,
			meta: {
				title: 'Мои курсы',
				menuItem: 'courses',
			},
		},
		// admin/reports.blade.php
		{
			path: '/timetracking/reports',
			name: 'TimetrackingView',
			component: TimetrackingView,
			meta: {
				title: 'Табель - Учет времени',
				menuItem: 'reports',
			},
		},
		// admin/top.blade.php
		{
			path: '/timetracking/top',
			name: 'TopView',
			component: TopView,
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		// admin/enter-report.blade.php
		{
			path: '/timetracking/reports/enter-report',
			name: 'EntertimeView',
			component: EntertimeView,
			meta: {
				title: 'Время прихода - Учет времени',
				menuItem: 'reports',
			},
		},
		// admin/analytics.blade.php
		{
			path: '/timetracking/analytics',
			name: 'HRView',
			component: HRView,
			meta: {
				title: 'HR - аналитика рекрутинга',
				menuItem: 'reports',
			},
		},
		// admin/analytics-page.blade.php
		{
			path: '/timetracking/an',
			name: 'AnalyticsView',
			component: AnalyticsView,
			meta: {
				title: 'Аналитика отделов',
				menuItem: 'reports',
			},
		},
		// admin/salary.blade.php
		{
			path: '/timetracking/salaries',
			name: 'SalaryView',
			component: SalaryView,
			meta: {
				title: 'Начисления',
				menuItem: 'reports',
			},
		},
		// admin/quality_control.blade.php
		{
			path: '/timetracking/quality-control',
			name: 'QualityControlView',
			component: QualityControlView,
			meta: {
				title: 'Контроль качества',
				menuItem: 'reports',
			},
		},
		// surv/maps.blade.php
		{
			path: '/maps',
			name: 'MapView',
			component: MapView,
			meta: {
				title: 'Карта сотрудников',
				menuItem: 'maps',
			},
		},
		// kpi.blade.php
		{
			path: '/kpi',
			name: 'KPIView',
			component: KPIView,
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
		// admin/info.blade.php
		{
			path: '/timetracking/info',
			name: 'FAQView',
			component: () => import(/* webpackChunkName: "FAQView" */ '@/views/FAQView'),
			meta: {
				title: 'ЧАВО - FAQ',
				menuItem: 'faq',
			},
		},
		// admin/fines.blade.php
		{
			path: '/timetracking/fines',
			name: 'FinesView',
			component: () => import(/* webpackChunkName: "FinesView" */ '@/views/FinesView'),
			meta: {
				title: 'Депремирование',
				menuItem: 'fines',
			},
		},
		// admin/settingtimetracking.blade.php
		{
			path: '/timetracking/settings',
			name: 'SettingsView',
			component: SettingsView,
			meta: {
				title: 'Настройки',
				menuItem: 'settings',
			},
		},
		// admin/users/create.blade.php
		{
			path: '/timetracking/edit-person',
			name: 'UserEditView',
			component: () => import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
			meta: {
				title: 'Редактирование профиля сотрудника',
				menuItem: 'settings',
			},
		},
		// admin/users/create.blade.php
		{
			path: '/timetracking/create-person',
			name: 'UserCreateView',
			component: () => import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
			meta: {
				title: 'Создание профиля сотрудника',
				menuItem: 'settings',
			},
		},
		{
			path: '/pricing',
			name: 'PricingView',
			component: () => import(/* webpackChunkName: "PricingView" */ '@/views/PricingView'),
			meta: {
				title: 'PricingView',
			},
		},
		{
			path: '/structure',
			name: 'StructureView',
			component: () => import(/* webpackChunkName: "PricingView" */ '@/views/StructureView'),
			meta: {
				title: 'StructureView',
			},
		},
	],
})

const DEFAULT_TITLE = 'Jobtron.org';
const viewport = document.querySelector('meta[name="viewport"]');
router.beforeEach((to, from, next) => {
	viewport.content = ' ';
	if(to.meta.viewport){
		viewport.content = 'width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no';
	}
	next();
});
router.afterEach(to => {
	// Use next tick to handle router history correctly
	// see: https://github.com/vuejs/vue-router/issues/914#issuecomment-384477609
	Vue.nextTick(() => {
		document.title = to.meta.title || DEFAULT_TITLE
		document.body.className = to.meta.bodyClass || ''
	})
});

export default router
