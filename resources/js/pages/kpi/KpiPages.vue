<template>
	<div
		v-if="page"
		class="kpi-pages"
	>
		<div v-if="access === 'edit'">
			<b-tabs
				type="card"
				class="mt-4 kpi-tabs"
			>
				<b-tab
					:key="0"
					:active="page === 'kpi'"
					title="KPI"
					card
					@click="setPage('kpi')"
				>
					<KPI v-if="page === 'kpi'" />
				</b-tab>
				<b-tab
					:key="1"
					:active="page === 'bonus'"
					title="Бонусы, %"
					card
					@click="setPage('bonus')"
				>
					<Bonuses v-if="page === 'bonus'" />
				</b-tab>
				<b-tab
					:key="2"
					:active="page === 'premium'"
					title="Квартальная премия"
					card
					@click="setPage('premium')"
				>
					<QuartalPremium v-if="page === 'premium'" />
				</b-tab>
				<b-tab
					:key="3"
					:active="page === 'statistics'"
					title="Статистика"
					card
					@click="setPage('statistics')"
				>
					<StatsV2 v-if="page === 'statistics'" />
				</b-tab>
				<b-tab
					v-if="isBP"
					:key="4"
					:active="page === 'indicators'"
					title="Показатели"
					card
					@click="setPage('indicators')"
				>
					<Indicators v-if="page === 'indicators' && isBP" />
				</b-tab>
				<!-- <b-tab
					v-if="false"
					:key="5"
					:active="page === 'statistics'"
					title="Статистика1"
					card
				>
					<Stats v-if="active == 5" />
				</b-tab> -->
			</b-tabs>
		</div>

		<div v-else>
			<b-tabs
				type="card"
				class="mt-4"
				@activate-tab="(n,p,e) => active = n"
			>
				<b-tab
					:key="0"
					:active="page === 'statistics'"
					title="Статистика"
					card
					@click="setPage('statistics')"
				>
					<StatsV2 v-if="page === 'statistics'" />
				</b-tab>
				<b-tab
					v-if="isBP"
					:key="1"
					:active="page === 'indicators'"
					title="Показатели"
					card
					@click="setPage('indicators')"
				>
					<Indicators v-if="page === 'indicators' && isBP" />
				</b-tab>
			</b-tabs>
		</div>
	</div>
</template>

<script>
import KPI from '@/pages/kpi/Kpi'
import Bonuses from '@/pages/kpi/Bonuses.vue'
import QuartalPremium from '@/pages/kpi/QuartalPremium'
// const Stats = import(/* webpackChunkName: "KPIStatsV1" */ '@/pages/kpi/Stats')
// import Stats from '@/pages/kpi/Stats'
import StatsV2 from '@/pages/kpi/StatsV2'
import Indicators from '@/pages/kpi/Indicators'

export default {
	name: 'KPIPages',
	components: {
		KPI,
		Bonuses,
		QuartalPremium,
		// Stats,
		StatsV2,
		Indicators,
	},
	props: {
		access: {
			type: String,
			default: 'view'
		}
	},
	data() {
		return {
			active: 0,
			tenant: window.location.hostname.split('.')[0],
		}
	},
	computed: {
		page(){
			return this.$route.params.page || 'kpi'
		},
		isBP(){
			return ['bp', 'test'].includes(this.tenant)
		},
	},
	watch: {},
	created() {},
	mounted() {},
	methods: {
		init(){},
		setPage(page){
			if(page === 'kpi') return this.$router.push('/kpi')
			this.$router.push('/kpi/' + page)
		}
	}
}
</script>

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
