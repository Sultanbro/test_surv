<template>
	<div class="TabPivot">
		<TableRecruiterStats
			:data="recruiterStats"
			:days-in-month="new Date().getDate()"
			:rates="recruiterStatsRates"
			:year="year"
			:month="month + 1"
			:leads_data="recruiterStatsLeads"
			:editable="true"
			@changeDay="setDay"
		/>
		<div class="mb-5" />
		<Recruting
			v-if="indicators"
			:is-analytics-page="true"
			:records="indicators"
		/>
		<div class="mb-5" />
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useHRStore } from '@/stores/ReportsHR.js'

import TableRecruiterStats from '@/components/analytics/TableRecruiterStats' // Почасовая таблица рекрутинга
import Recruting from '@/components/analytics/Recruting' // сводная информация рекрутинг

export default {
	name: 'TabPivot',
	components: {
		TableRecruiterStats,
		Recruting,
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
		}
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
			// recruiter
			'recruiterStats',
			'recruiterStatsLeads',
			'recruiterStatsRates',
			// indicators
			'indicatorsDate',
			'indicators',
			'records',
			'hrs', // ????
		]),
	},
	watch: {
		year(){ this.fetchData() },
		month(){ this.fetchData() },
		refresh(){ this.fetchData() },
		currentDay(){
			this.fetchRecruitment({
				day: this.currentDay,
				month: this.month + 1,
				year: this.year,
			})
		},
	},
	mounted(){
		this.init()
	},
	methods: {
		...mapActions(useHRStore, [
			'fetchRecruitment',
			'fetchIndicators',
		]),
		init(){
			this.fetchData()
		},
		fetchData(){
			this.fetchRecruitment({
				day: this.currentDay,
				month: this.month + 1,
				year: this.year,
			})
			this.fetchIndicators({
				month: this.month + 1,
				year: this.year,
			})
		},
		setDay(value){
			this.currentDay = value
		},
	}
}
</script>

<style lang="scss">
// .TabPivot{}
</style>
