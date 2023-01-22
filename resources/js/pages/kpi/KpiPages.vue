<template>
	<div
		v-if="page"
		class="kpi-pages"
	>
		<div v-if="access == 'edit'">
			<b-tabs
				type="card"
				class="mt-4"
				:value="active"
				@activate-tab="(n,p,e) => active = n"
			>
				<b-tab
					title="KPI"
					:key="0"
					card
				>
					<KPI v-if="active == 0" />
				</b-tab>
				<b-tab
					title="Бонусы"
					:key="1"
					card
				>
					<Bonuses v-if="active == 1" />
				</b-tab>
				<b-tab
					title="Квартальная премия"
					:key="2"
					card
				>
					<QuartalPremium v-if="active == 2" />
				</b-tab>
				<b-tab
					title="Статистика"
					:key="3"
					card
				>
					<Stats v-if="active == 3" />
				</b-tab>
				<b-tab
					title="Показатели"
					:key="4"
					card
				>
					<Indicators v-if="active == 4" />
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
					title="Статистика"
					:key="0"
					card
				>
					<Stats v-if="active == 0" />
				</b-tab>
				<b-tab
					title="Показатели"
					:key="1"
					card
				>
					<Indicators v-if="active == 1" />
				</b-tab>
			</b-tabs>
		</div>
	</div>
</template>

<script>
import KPI from '@/pages/kpi/Kpi'
import Bonuses from '@/pages/kpi/Bonuses'
import QuartalPremium from '@/pages/kpi/QuartalPremium'
import Stats from '@/pages/kpi/Stats'
import Indicators from '@/pages/kpi/Indicators'

export default {
	name: 'KPIPages',
	components: {
		KPI,
		Bonuses,
		QuartalPremium,
		Stats,
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