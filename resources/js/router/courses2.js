export default {
	path: '/courses2',
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
			component: () => import(/* webpackChunkName: "CoursesPage" */ '@/pages/Courses/CoursesPage.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
		{
			path: '/assigned',
			name: 'CoursesAssigned',
			component: () => import(/* webpackChunkName: "CoursesAssigned" */ '@/pages/Courses/CoursesAssigned.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
		{
			path: '/catalog',
			name: 'CoursesCatalog',
			component: () => import(/* webpackChunkName: "CoursesCatalog" */ '@/pages/Courses/CoursesCatalog.vue'),
			meta: {
				title: 'Курсы',
				menuItem: 'courses',
			},
		},
	]
}
