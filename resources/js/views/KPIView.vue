<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const KpiPages = () => import(/* webpackChunkName: "KpiPages" */ '@/pages/kpi/KpiPages')

export default {
	name: 'KPIView',
	components: {
		DefaultLayout,
		KpiPages,
	},
	data(){
		return {
			page: '',
			access: '',
		}
	},
	mounted(){
		useAsyncPageData('/kpi').then(data => {
			this.page = data.page
			this.access = data.access
		}).catch(error => {
			console.error('useAsyncPageData', error)
		})
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<KpiPages
				v-if="access"
				:access="access"
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
