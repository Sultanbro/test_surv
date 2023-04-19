<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const ReportsNav = () => import(/* webpackChunkName: "ReportsNav" */ '@/components/layouts/ReportsNav.vue')
const TopPage = () => import(/* webpackChunkName: "TopPage" */ '@/pages/Top')

export default {
	name: 'TopView',
	components: {
		DefaultLayout,
		ReportsNav,
		TopPage,
	},
	data(){
		return {
			data: null,
			activeuserid: 0,
			activeTab: 'nav-top-tab'
		}
	},
	mounted(){
		useAsyncPageData('/timetracking/top').then(data => {
			this.data = data.data || null
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
			<TopPage
				v-show="activeuserid"
				:data="data"
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
        padding-left: 9rem !important;
    }
}
</style>
