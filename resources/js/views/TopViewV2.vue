<script>
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import RouterTabs from '@ui/RouterTabs.vue'
import GroupDateFilter from '@/components/pages/Reports/GroupDateFilter.vue'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')

import { mapState, mapActions } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import { useTopStore } from '@/stores/Top.js'
import { useYearOptions } from '@/composables/yearOptions'
import { bus } from '@/bus'

export default {
	name: 'TopView',
	components: {
		DefaultLayout,
		RouterTabs,
		GroupDateFilter,
		ReportsNav,
	},
	data(){
		return {
			data: null,
			activeTab: 'nav-top-tab',
			tabs: [
				{
					title: 'Полезность',
					value: '/timetracking/top',
				},
				{
					title: 'Маржа',
					value: '/timetracking/top/margin',
				},
				{
					title: 'Выручка',
					value: '/timetracking/top/revenue',
				},
				{
					title: 'Прогноз',
					value: '/timetracking/top/forecast',
				},
				{
					title: 'NPS',
					value: '/timetracking/top/nps',
				},
				...(['bp', 'test'].includes(location.hostname.split('.')[0]) ? [{
					title: 'Profit',
					value: '/timetracking/top/profit',
				}] : []),
			]
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
	},
	mounted(){},
	methods: {
		...mapActions(useTopStore, ['setDate']),
		onUpdateDate($event){
			this.setDate($event.year, $event.month)
			bus.$emit('tt-top-update', $event)
		}
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<ReportsNav :active-tab="activeTab" />
			<GroupDateFilter
				:years="years"
				refresh-on-change
				@refresh="onUpdateDate"
			/>
			<RouterTabs
				:tabs="tabs"
				:value="$route.path"
				class="top-tabs mt-4"
			>
				<router-view />
			</RouterTabs>
		</div>
	</DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 9rem !important;
    }
}
</style>

<style lang="scss">
.top-tabs{
	overflow: visible !important;
}
</style>
