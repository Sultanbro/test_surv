export const settingsSubmenu = [
	{
		name: 'Сотрудники',
		icon: 'icon-nd-profile',
		to: '/timetracking/settings'
	},
	{
		name: 'Компания',
		icon: 'far fa-building ml-2',
		to: '/timetracking/settings?tab=2#nav-home',
		access: ['positions_view', 'groups_view'],
	},
	{
		name: 'Депремирования',
		icon: 'icon-nd-deduction',
		to: '/timetracking/settings?tab=4#nav-fines',
		access: ['fines_view'],
	},
	{
		name: 'Уведомления',
		icon: 'icon-nd-news',
		to: '/timetracking/settings?tab=10',
		access: ['notifications_view']
	},
	{
		name: 'Доступы',
		icon: 'icon-nd-salary',
		to: '/timetracking/settings?tab=6',
		access: ['permissions_view']
	},
	{
		name: 'Интеграции',
		icon: 'icon-nd-map',
		to: '/timetracking/settings?tab=8',
		access: 'isAdmin'
	},
	{
		name: 'Награды',
		icon: 'icon-nd-quality',
		to: '/timetracking/settings?tab=9#nav-awards',
		access: ['awards_view']
	},
]
