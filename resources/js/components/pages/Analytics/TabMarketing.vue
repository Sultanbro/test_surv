<template>
	<div class="TabMarketing">
		<b-tabs>
			<b-tab
				key="100"
				title="Лидогенерация"
				card
			>
				<div class="pt-4">
					<b-tabs
						v-if="funnels.all"
						type="card"
						default-active-key="0"
					>
						<b-tab
							key="0"
							title="Сводная"
							card
						>
							<div class="row pt-4">
								<div class="col-8">
									<div class="TabMarketing-funnels">
										<TableFunnel
											:id="0"
											class="mb-4"
											:table="funnels['all']['all']"
											title="Сводная таблица"
											segment="segments"
											type="month"
											:date="date"
										/>
										<TableFunnel
											:id="1"
											class="mb-4"
											:table="funnels['all']['hh']"
											title="hh.ru"
											segment="hh"
											type="month"
											:date="date"
										/>
										<TableFunnel
											:id="2"
											class="mb-4"
											:table="funnels['all']['insta']"
											title="Job.bpartners.kz"
											segment="insta"
											type="month"
											:date="date"
										/>
									</div>
								</div>
								<!-- partner link creator -->
								<div class="col-4">
									<RefLinker />
								</div>
							</div>
						</b-tab>
						<b-tab
							v-for="(mon, i) in months"
							:key="i"
							:title="mon.month"
							card
						>
							<div class="pt-4">
								<TableFunnel
									:key="5 * 1000 * (Number(i) + 10 * Number(i))"
									class="mb-4"
									:table="funnels['month'][i]['hh']"
									title="hh.ru"
									segment="hh"
									type="week"
									:date="mon.date"
								/>
								<TableFunnel
									:key="6 * 1000 * (Number(i) + 10 * Number(i))"
									class="mb-4"
									:table="funnels['month'][i]['insta']"
									title="Job.bpartners.kz"
									segment="insta"
									type="week"
									:date="mon.date"
								/>
							</div>
						</b-tab>
					</b-tabs>
				</div>
			</b-tab>
			<b-tab
				key="101"
				title="Реферальная программа"
			>
				<RefStats class="mt-4" />
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useHRStore } from '@/stores/ReportsHR.js'

import RefLinker from '@/components/RefLinker' // рефералки
import TableFunnel from '@/components/tables/TableFunnel' // Воронка
import RefStats from './RefStats.vue'

export default {
	name: 'TabMarketing',
	components: {
		RefLinker,
		TableFunnel,
		RefStats,
	},
	props: {
		year: {
			type: Number,
			default: () => new Date().getFullYear()
		},
		month: {
			type: Number,
			default: () => new Date().getMonth()
		},
		refresh: {
			type: Number,
			default: 0
		},
		months: {
			type: Object,
			default: () => {}
		},
	},
	data(){
		return {
			currentDay: new Date().getDate()
		}
	},
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
			// funnels
			'funnels',
		]),
		date(){
			return `${this.year}-${(this.month > 8 ? '' : '0') + (this.month + 1)}-${this.currentDay > 9 ? this.currentDay : '0' + this.currentDay}`
		},
	},
	watch: {
		year(){ this.fetchData() },
		month(){ this.fetchData() },
		refresh(){ this.fetchData() },
	},
	mounted(){
		this.init()
	},
	methods: {
		...mapActions(useHRStore, [
			'fetchFunnels',
		]),
		init(){
			this.fetchData()
		},
		fetchData(){
			this.fetchFunnels({
				month: this.month + 1,
				year: this.year,
			})
		},
	}
}
</script>

<style lang="scss">
.TabMarketing{
	&-funnels{
		overflow-x: auto;
	}
}
</style>
