import VueRouter from 'vue-router';
import { checkVersion } from '@/stores/api.js';
import kpi from './kpi.js';
import top from './top.js';
import courses2 from './courses2.js';

import ProfileView from '@/views/ProfileView';
import CabinetView from '@/views/CabinetView';
import NewsView from '@/views/NewsView';
import KnowledgeBaseView from '@/views/KnowledgeBaseView';
import UpbooksView from '@/views/UpbooksView';
import PlaylistsView from '@/views/PlaylistsView';
import CoursesView from '@/views/CoursesView';
import TimetrackingView from '@/views/TimetrackingView';

import MyCoursesView from '@/views/MyCoursesView';
// import StructureView from '@/views/StructureView'
import EntertimeView from '@/views/EntertimeView';
import HRView from '@/views/HRView';
// import TopView from '@/views/TopView'
import AnalyticsView from '@/views/AnalyticsView';
import SalaryView from '@/views/SalaryView';
import QualityControlView from '@/views/QualityControlView';
import MapView from '@/views/MapView';
import PromotionalMaterialView from '@/views/PromotionalMaterialView';
import ReferralPrsentationView from '@/views/ReferralPrsentationView';

import WorkshopPage from '../pages/workshop/WorkshopPage.vue';
import WorkshopForm from '../pages/workshop/WorkShopForm.vue';

import MkKnowBase from '../pages/mkKnowBase/MkKnowBase.vue';

import Articles from '../pages/articles/ArticlesView.vue';
import ArticlesContent from '../pages/articles/ArticlesContent.vue';

// Cтраницу настроек наверное тоже разделим если нужно
import SettingsView from '@/views/SettingsView';
// import StructureView from '@/views/StructureView'

const VIEWPORT_DEFAULT = 'width=1360, initial-scale=1';
const VIEWPORT_ADAPTIVE =
	'width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no';

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
				viewport: VIEWPORT_ADAPTIVE,
				bodyClass: 'profile-page',
				menuItem: 'profile',
			},
		},
		{
			path: '/profile/promotional-material',
			name: 'PromotionalMaterialView',
			component: PromotionalMaterialView,
			meta: {
				title: 'Рекламный материал',
			},
		},
		{
			path: '/profile/referral-prsentation',
			name: 'ReferralPrsentationView',
			component: ReferralPrsentationView,
			meta: {
				title: 'Рекламный материал',
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
				viewport: VIEWPORT_ADAPTIVE,
				menuItem: 'news',
			},
		},
		// admin/books.blade.php
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
			alias: ['/admin/upbooks/:id'],
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
			alias: [
				'/video_playlists/:category',
				'/video_playlists/:category/:playlist',
				'/video_playlists/:category/:playlist/:video',
			],
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
		courses2,
		// admin/mycourse.blade.php
		{
			path: '/my-courses',
			name: 'MyCoursesView',
			component: MyCoursesView,
			meta: {
				title: 'Мои курсы',
				menuItem: 'profile',
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
		// {
		// 	path: '/timetracking/top',
		// 	name: 'TopView',
		// 	component: TopView,
		// 	meta: {
		// 		title: 'ТОП',
		// 		menuItem: 'reports',
		// 	},
		// },
		top,
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
		kpi,
		// courses,
		// admin/info.blade.php
		{
			path: '/timetracking/info',
			name: 'FAQView',
			component: () =>
				import(/* webpackChunkName: "FAQView" */ '@/views/FAQView'),
			meta: {
				title: 'ЧАВО - FAQ',
				menuItem: 'faq',
			},
		},
		// admin/fines.blade.php
		{
			path: '/timetracking/fines',
			name: 'FinesView',
			component: () =>
				import(/* webpackChunkName: "FinesView" */ '@/views/FinesView'),
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
			component: () =>
				import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
			meta: {
				title: 'Редактирование профиля сотрудника',
				menuItem: 'settings',
			},
		},
		// admin/users/create.blade.php
		{
			path: '/timetracking/create-person',
			name: 'UserCreateView',
			component: () =>
				import(/* webpackChunkName: "UserEditView" */ '@/views/UserEditView'),
			meta: {
				title: 'Создание профиля сотрудника',
				menuItem: 'settings',
			},
		},
		{
			path: '/pricing',
			name: 'PricingView',
			component: () =>
				import(/* webpackChunkName: "PricingView" */ '@/views/PricingView'),
			meta: {
				title: 'PricingView',
			},
		},
		{
			path: '/structure',
			name: 'StructureView',
			component: () =>
				import(/* webpackChunkName: "StructureView" */ '@/views/StructureView'),
			meta: {
				title: 'StructureView',
				menuItem: 'structure',
			},
		},
		{
			path: '/signature/verification',
			name: 'SignatureVerification',
			component: () =>
				import(
					/* webpackChunkName: "SignatureVerification" */ '@/views/SignatureVerification'
				),
			meta: {
				title: 'SignatureVerification',
			},
		},
		{
			path: '/signature/view',
			name: 'SignatureView',
			component: () =>
				import(/* webpackChunkName: "SignatureView" */ '@/views/SignatureView'),
			meta: {
				title: 'SignatureView',
			},
		},
		{
			path: '/awards/fix-preview',
			name: 'FixAwardView',
			component: () =>
				import(/* webpackChunkName: "FixAwardView" */ '@/views/FixAwardView'),
			meta: {
				title: 'FixAwardView',
			},
		},
		{
			path: '/payworkshopknowledgebase',
			name: 'Workshop',
			component: WorkshopPage,
			meta: {
				title: 'Страница оплаты',
			},
		},
		{
			path: '/payworkshopknowledgebaseform',
			name: 'WorkshopForm',
			component: WorkshopForm,
			meta: {
				title: 'Форма перед оплатой',
			},
		},
		{
			path: '/mkKnowBase',
			name: 'MkKnowBase',
			component: MkKnowBase,
			meta: {
				title: 'МК База знаний',
			},
		},
		{
			path: '/articles',
			name: 'Articles',
			component: Articles,
			meta: {
				title: 'Статьи',
			},
		},
		{
			path: '/article/:id',
			name: 'Article',
			alias: ['/article/:id'],
			component: ArticlesContent,
			meta: {
				title: 'Статья',
			},
		},
	],
});

const viewport = document.querySelector('meta[name="viewport"]');
router.beforeEach((to, from, next) => {
	checkVersion().then((newVersion) => {
		if (newVersion) location.assign(to.fullPath || to.path);
	});
	viewport.content = to.meta.viewport || VIEWPORT_DEFAULT;
	next();
});
export default router;
