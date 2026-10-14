<script>
export default {
	name: 'ReportsNav',
	props: {
		activeTab: {
			type: String,
			default: ''
		}
	},
	data(){
		return {
			tenant: window.location.hostname.split('.')[0],
			tabs: [
				{
					id: 'nav-top-tab',
					path: '/timetracking/top',
					title: 'TOП',
					access: 'top_view',
					bp: true
				},
				{
					id: 'nav-home-tab',
					path: '/timetracking/reports',
					title: 'Табель',
					access: 'tabel_view'
				},
				{
					id: 'nav-entertime-tab',
					path: '/timetracking/reports/enter-report',
					title: 'Время прихода',
					access: 'entertime_view'
				},
				{
					id: 'nav-profilex-tab',
					path: '/timetracking/analytics',
					title: 'HR',
					access: 'hr_view',
					bp: true
				},
				{
					id: 'nav-profile-tab',
					path: '/timetracking/an',
					title: 'Аналитика',
					access: 'analytics_view'
				},
				{
					id: 'nav-salary-tab',
					path: '/timetracking/salaries',
					title: 'Начисления',
					access: 'salaries_view'
				},
				{
					id: 'nav-quality-tab',
					path: '/timetracking/quality-control',
					title: 'ОКК',
					access: 'quality_view'
				},
			],
		}
	}
}
</script>
<template>
	<nav class="my-4">
		<ul
			id="nav-tab"
			class="nav nav-tabs"
		>
			<template v-for="tab in tabs">
				<li
					v-if="!(!$can(tab.access) || (tab.bp && tenant !== 'bp'))"
					:key="tab.id"
					class="nav-item"
				>
					<router-link
						:id="tab.id"
						:to="tab.path"
						class="nav-link"
						:class="{active: tab.id === activeTab}"
					>
						{{ tab.title }}
					</router-link>
				</li>
			</template>
		</ul>
	</nav>
</template>
