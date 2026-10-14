<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const Analytics = () => import(/* webpackChunkName: "HRPage" */ '@/pages/Analytics.vue')

export default {
	name: 'HRView',
	components: {
		DefaultLayout,
		ReportsNav,
		Analytics,
	},
	data(){
		return {
			groups: null,
			activeuserid: '0',
			activeTab: 'nav-profilex-tab',
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/analytics').then(data => {
			this.groups = data.groups || null
			this.activeuserid = '' + data.activeuserid
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
			<Analytics
				v-show="activeuserid"
				:groups="groups"
				:activeuserid="+activeuserid"
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
