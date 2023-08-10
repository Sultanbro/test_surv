<template>
	<div
		v-if="page"
		class="kpi-pages"
	>
		<div v-if="access == 'edit'">
			<b-tabs
				type="card"
				class="mt-4 kpi-tabs"
				:value="active"
				@activate-tab="(n,p,e) => active = n"
			>
				<b-tab
					:key="0"
					title="KPI"
					card
				>
					<KPI v-if="active == 0" />
				</b-tab>
				<b-tab
					:key="1"
					title="Бонусы, %"
					card
				>
					<Bonuses v-if="active == 1" />
				</b-tab>
				<b-tab
					:key="2"
					title="Квартальная премия"
					card
				>
					<QuartalPremium v-if="active == 2" />
				</b-tab>
				<b-tab
					:key="3"
					title="Статистика"
					card
				>
					<StatsV2 v-if="active == 3" />
				</b-tab>
				<b-tab
					v-if="tenant === 'bp'"
					:key="4"
					title="Показатели"
					card
				>
					<Indicators v-if="active == 4 && tenant === 'bp'" />
				</b-tab>
				<b-tab
					v-if="false"
					:key="5"
					title="Статистика1"
					card
				>
					<Stats v-if="active == 5" />
				</b-tab>
			</b-tabs>
		</div>

		<div v-else>
			<b-tabs
				type="card"
				class="mt-4"
				:value="active"
				@activate-tab="(n,p,e) => active = n"
			>
				<b-tab
					:key="0"
					title="Статистика"
					card
				>
					<StatsV2 v-if="active == 0" />
				</b-tab>
				<b-tab
					v-if="tenant === 'bp'"
					:key="1"
					title="Показатели"
					card
				>
					<Indicators v-if="active == 1 && tenant === 'bp'" />
				</b-tab>
			</b-tabs>
		</div>
	</div>
</template>

<script>
import KPI from '@/pages/kpi/Kpi'
import Bonuses from '@/pages/kpi/Bonuses.vue'
import QuartalPremium from '@/pages/kpi/QuartalPremium'
const Stats = import(/* webpackChunkName: "KPIStatsV1" */ '@/pages/kpi/Stats')
// import Stats from '@/pages/kpi/Stats'
import StatsV2 from '@/pages/kpi/StatsV2'
import Indicators from '@/pages/kpi/Indicators'

export default {
	name: 'KPIPages',
	components: {
		KPI,
		Bonuses,
		QuartalPremium,
		Stats,
		StatsV2,
		Indicators,
	},
	props: {
		page: {
			type: String,
			default: 'kpi'
		},
		access: {
			default: 'view'
		}
	},
	data() {
		return {
			active: 0,
			tenant: window.location.hostname.split('.')[0],
		}
	},
	watch:{
		page(){
			this.init()
		}
	},
	created() {
	},
	mounted() {
		let uri = window.location.search.substring(1);
		let params = new URLSearchParams(uri);
		if(params.get('target')){
			// может быть проблемой для spa
			window.history.pushState({}, document.title, '/' + 'kpi');
		}
	},
	methods: {
		init(){
			// this.fetchData()
			let uri = window.location.search.substring(1);
			let params = new URLSearchParams(uri);
			this.active = params.get('target') ? 3 : 0;
		},

		changeStatus(item, e){
			this.axios.post('/bonus/set/status', {
				premium_id: item.id,
				is_active: e
			}).then(() => {
				this.$toast.success('Статус изменен')
			}).catch(() => {
				this.$toast.error('Статус не изменен')
			})
		},
		fetchData() {
			let loader = this.$loading.show();

			this.axios.post('/kpi/' + this.page, {
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
			}).then(() => {

				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},
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
