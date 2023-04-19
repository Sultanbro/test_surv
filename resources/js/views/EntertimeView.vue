<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const TableComing = () => import(/* webpackChunkName: "TableComingPage" */ '@/pages/TableComing')

export default {
	name: 'EntertimeView',
	components: {
		DefaultLayout,
		ReportsNav,
		TableComing,
	},
	data(){
		return {
			groups: null,
			years: null,
			activeuserid: '',
			activeTab: 'nav-entertime-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/reports/enter-report').then(data => {
			this.groups = data.groups
			this.years = data.years
			this.activeuserid = '' + (data.activeuserid || '')
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<ReportsNav :active-tab="activeTab" />
			<TableComing
				v-show="activeuserid"
				:groups="groups"
				:years="years"
				:activeuserid="activeuserid"
			/>
		</div>
	</DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 7rem !important;
    }
}
</style>
