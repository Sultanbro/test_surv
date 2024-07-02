<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import RouterTabs from '@ui/RouterTabs.vue'

const viewTabs = [
	{
		title: 'Статистика',
		value: '/kpi/statistics',
	},
]

const viewBPTabs = [
	{
		title: 'Показатели',
		value: '/kpi/indicators',
	},
]

const editTabs = [
	{
		title: 'KPI',
		value: '/kpi',
	},
	{
		title: 'Бонусы, %',
		value: '/kpi/bonus',
	},
	{
		title: 'Квартальная премия',
		value: '/kpi/premium',
	},
]

export default {
	name: 'KPIViewV2',
	components: {
		DefaultLayout,
		RouterTabs,
	},
	data(){
		return {
			access: '',
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0])
		}
	},
	computed: {
		tabs(){
			const result = []
			if(this.$can('kpi_edit')){
				result.push(...editTabs)
			}
			if(this.$can('kpi_view')){
				result.push(...viewTabs)
			}
			if(this.isBP){
				result.push(...viewBPTabs)
			}
			return result
		},
	},
	mounted(){
		const currentIndex = this.tabs.findIndex(tab => tab.value === this.$route.path)
		if(!~currentIndex && this.tabs.length){
			this.$router.push(this.tabs[0].value)
		}
	}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content kpi-pages">
			<RouterTabs
				:tabs="tabs"
				:value="$route.path"
				class="kpi-tabs mt-4"
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
	.kpi-tabs{
		overflow: visible !important;
	}

	.kpi-status-switch{
		position: relative;
	}
	.kpi-pages .kpi .kpi-status-switch{
		margin-left: auto;
	}
</style>
