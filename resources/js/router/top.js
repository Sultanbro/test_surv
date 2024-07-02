export default {
	path: '/timetracking/top',
	name: 'TopViewV2',
	component: () => import(/* webpackChunkName: "TopViewV2" */ '@/views/TopViewV2'),
	meta: {
		title: 'ТОП',
		menuItem: 'reports',
	},
	children: [
		{
			path: '',
			name: 'TopUtility',
			component: () => import(/* webpackChunkName: "TopUtility" */ '@/pages/top/TopUtility'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		{
			path: 'margin',
			name: 'TopMargin',
			component: () => import(/* webpackChunkName: "TopMargin" */ '@/pages/top/TopMargin'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		{
			path: 'revenue',
			name: 'TopRevenue',
			component: () => import(/* webpackChunkName: "TopRevenue" */ '@/pages/top/TopRevenue'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		{
			path: 'forecast',
			name: 'TopForecast',
			component: () => import(/* webpackChunkName: "TopForecast" */ '@/pages/top/TopForecast'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		{
			path: 'nps',
			name: 'TopNPS',
			component: () => import(/* webpackChunkName: "TopNPS" */ '@/pages/top/TopNPS'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
		{
			path: 'profit',
			name: 'TopProfit',
			component: () => import(/* webpackChunkName: "TopProfit" */ '@/pages/top/TopProfit'),
			meta: {
				title: 'ТОП',
				menuItem: 'reports',
			},
		},
	]
}
