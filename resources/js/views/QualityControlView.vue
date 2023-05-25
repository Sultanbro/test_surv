<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const TableQuality = () => import(/* webpackChunkName: "TableQualityPage" */ '@/components/tables/TableQuality')

export default {
	name: 'QualityControlView',
	components: {
		DefaultLayout,
		ReportsNav,
		TableQuality,
	},
	data(){
		return {
			groups: null,
			active_group: '',
			check: '',
			user: null,
			activeTab: 'nav-quality-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/quality-control').then(data => {
			this.groups = data.groups || null
			this.active_group = '' + data.active_group
			this.check = '' + data.check
			this.user = data.user || null
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
			<TableQuality
				v-show="groups"
				:groups="groups"
				:active_group="active_group"
				:check="check"
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
