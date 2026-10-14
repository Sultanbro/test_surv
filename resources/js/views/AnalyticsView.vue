<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const AnalyticsPage = () => import(/* webpackChunkName: "AnalyticsPage" */ '@/pages/AnalyticsPage')

export default {
	name: 'AnalyticsView',
	components: {
		DefaultLayout,
		ReportsNav,
		AnalyticsPage,
	},
	data(){
		return {
			groups: '',
			activeuserid: 0,
			isAdmin: 0,
			activeTab: 'nav-profile-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/an').then(data => {
			this.groups = data.groups;
			this.activeuserid = data.activeuserid;
			this.isAdmin = !!data.isadmin;
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
			<AnalyticsPage
				v-show="activeuserid"
				:groups="groups"
				:activeuserid="activeuserid"
				:is-admin="isAdmin"
			/>
		</div>
	</DefaultLayout>
</template>

<style scoped>
.header__profile {
    display:none !important;
}
.table .form-control{
    height: auto!important;
    padding: 0 10px !important;
    border: none !important;
    background-color: transparent !important;
}
.table .form-control:active{
    border: none !important;
    background-color: transparent !important;
    box-shadow: none!important;
}
.table .form-control:focus{
    border: none !important;
    background-color: transparent !important;
    box-shadow: none!important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 9rem !important;
    }
}
</style>
