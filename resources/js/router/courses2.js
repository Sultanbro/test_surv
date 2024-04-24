export default {
	path: '/courses-v2',
	name: 'CoursesViewV2',
	component: () => import(/* webpackChunkName: "CoursesViewV2" */ '@/views/CoursesViewV2'),
	meta: {
		title: 'Курсы',
		menuItem: 'courses',
	},
	children: [
		{
			path: '',
			name: 'CoursesPage',
			component: () => import(/* webpackChunkName: "CoursesPage" */ '@/pages/courses/CoursesPage.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
		{
			path: '/assigned',
			name: 'CoursesAssigned',
			component: () => import(/* webpackChunkName: "CoursesAssigned" */ '@/pages/courses/CoursesAssigned.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
		{
			path: '/catalog',
			name: 'CoursesCatalog',
			component: () => import(/* webpackChunkName: "CoursesCatalog" */ '@/pages/courses/CoursesCatalog.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
	]
}
