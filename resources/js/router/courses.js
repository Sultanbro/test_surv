export default {
	path: '/courses2',
	name: 'Courses2',
	component: () => import(/* webpackChunkName: "KPIViewV2" */ '@/views/CoursesView2'),
	meta: {
		title: 'Courses2',
		menuItem: 'Courses2',
	},
	children: [
		{
			path: '',
			name: 'Courses2page',
			component: () => import(/* webpackChunkName: "KPIPage" */ '@/pages/courses/CoursesPage'),
			meta: {
				title: 'Courses2page',
				menuItem: 'courses2',
			},
		},
		{
			path: 'assigned',
			name: 'assigned',
			component: () => import(/* webpackChunkName: "KPIPage" */ '@/pages/courses/AssignedCourses'),
			meta: {
				title: 'AssignedCourses',
				menuItem: 'assigned',
			},
		},
		{
			path: 'catalog',
			name: 'catalog',
			component: () => import(/* webpackChunkName: "KPIPage" */ '@/pages/courses/CoursesCatalog'),
			meta: {
				title: 'CoursesCatalog',
				menuItem: 'catalog',
			},
		},

	]
}
