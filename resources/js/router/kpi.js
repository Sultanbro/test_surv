export default {
	path: '/kpi',
	name: 'KPIViewV2',
	component: () => import(/* webpackChunkName: "KPIViewV2" */ '@/views/KPIViewV2'),
	meta: {
		title: 'KPI - показателей',
		menuItem: 'kpi',
	},
	children: [
		{
			path: '',
			name: 'KPIPage',
			component: () => import(/* webpackChunkName: "KPIPage" */ '@/pages/kpi/Kpi'),
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
		{
			path: 'bonus',
			name: 'KPIBonuses',
			component: () => import(/* webpackChunkName: "KPIBonuses" */ '@/pages/kpi/Bonuses.vue'),
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
		{
			path: 'premium',
			name: 'KPIQuartalPremium',
			component: () => import(/* webpackChunkName: "KPIQuartalPremium" */ '@/pages/kpi/QuartalPremium'),
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
		{
			path: 'statistics',
			name: 'KPIStatsV2',
			component: () => import(/* webpackChunkName: "KPIStatsV2" */ '@/pages/kpi/StatsV2'),
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
		{
			path: 'indicators',
			name: 'KPIIndicators',
			component: () => import(/* webpackChunkName: "KPIIndicators" */ '@/pages/kpi/Indicators'),
			meta: {
				title: 'KPI - показателей',
				menuItem: 'kpi',
			},
		},
	]
}
