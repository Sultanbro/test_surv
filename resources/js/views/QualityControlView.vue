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
		useAsyncPageData('/timetracking/reports').then(data => {
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
            <div class="row">
                <div class="col-md-12 mt-4 mb-3">
                    <ReportsNav :active-tab="activeTab"/>
                </div>
                <div class="col-md-12">
                    <TableQuality
                        v-show="groups"
                        :groups="groups"
                        :active_group="active_group"
                        :check="check"
                        :user="user"
                    />
                </div>
            </div>
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