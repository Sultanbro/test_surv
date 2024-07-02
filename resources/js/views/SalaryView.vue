<script>
/* eslint-disable camelcase */

import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const TableAccrual = () => import(/* webpackChunkName: "TableAccrualPage" */ '@/pages/TableAccrual')

export default {
	name: 'SalaryView',
	components: {
		DefaultLayout,
		ReportsNav,
		TableAccrual,
	},
	data(){
		return {
			groups: null,
			years: null,
			activeuserid: '',
			activeuserpos: 0,
			is_admin: false,
			can_edit: false,
			activeTab: 'nav-salary-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/salaries').then(data => {
			this.groups = data.groupss || null
			this.years = data.years || null
			this.activeuserid = '' + data.activeuserid
			this.activeuserpos = +data.activeuserpos
			this.is_admin = !!data.is_admin
			this.can_edit = !!data.can_edit
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
			<TableAccrual
				v-show="activeuserid"
				:groupss="groups"
				:years="years"
				:activeuserid="activeuserid"
				:activeuserpos="activeuserpos"
				:is_admin="is_admin"
				:can_edit="can_edit"
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
