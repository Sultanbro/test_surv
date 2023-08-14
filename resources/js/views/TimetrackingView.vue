<script>
/* eslint-disable camelcase */

import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const TableReport = () => import(/* webpackChunkName: "TableReportPage" */ '@/pages/TableReport')

export default {
	name: 'QualityControlView',
	components: {
		DefaultLayout,
		ReportsNav,
		TableReport,
	},
	data(){
		return {
			groups: null,
			fines: null,
			years: null,
			can_edit: false,
			activeuserid: '',
			activeuserpos: '',
			activeTab: 'nav-home-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/reports').then(data => {
			this.groups = data.groups || null
			this.fines = data.fines || null
			this.years = data.years || null
			this.can_edit = !!data.can_edit
			this.activeuserid = '' + data.activeuserid
			this.activeuserpos = '' + data.activeuserpos
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
			<TableReport
				v-show="activeuserid"
				:groups="groups"
				:fines="fines"
				:years="years"
				:can-edit="can_edit"
				:activeuserid="activeuserid"
				:activeuserpos="activeuserpos"
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
    padding-left: 9rem !important;
}
}
</style>
