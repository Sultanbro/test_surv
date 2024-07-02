<template>
	<div class="TabSecondStage">
		<b-tabs type="card">
			<b-tab
				key="1"
				title="Сводная"
				card
			>
				<TableTraineeSage2
					:ocenka-svod="ocenkaSvod"
					class="pt-4"
				/>
			</b-tab>
			<b-tab
				key="2"
				title="Оценка тренера"
				card
			>
				<SvodTable
					:trainee_report="traineeReport"
					:groups="groups"
					class="pt-5"
				/>
			</b-tab>
			<b-tab
				key="4"
				title="Отсутствие стажеров"
				card
			>
				<div class="row pt-4">
					<div class="col-md-4">
						<JobtronTable
							:fields="[{key: 'cause', label: 'Первый день', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
							:items="absentsFirst"
						/>
					</div>
					<div class="col-md-4">
						<JobtronTable
							:fields="[{key: 'cause', label: 'Второй день', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
							:items="absentsSecond"
						/>
					</div>
					<div class="col-md-4">
						<JobtronTable
							:fields="[{key: 'cause', label: 'После третьего дня', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
							:items="absentsSecond"
						/>
					</div>
				</div>
			</b-tab>
		</b-tabs>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useHRStore } from '@/stores/ReportsHR.js'

import JobtronTable from '@ui/Table'
import SvodTable from '@/components/SvodTable' //сводная таблица для аналитики
import TableTraineeSage2 from '@/components/tables/TableTraineeSage2' // Стажеры

export default {
	name: 'TabSecondStage',
	components: {
		JobtronTable,
		SvodTable,
		TableTraineeSage2,
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
		groups: {
			type: Array,
			default: () => []
		},
	},
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
			// Internship
			'absentsFirst',
			'absentsSecond',
			'absentsThird',
			'ocenkaSvod',
			'traineeReport',
		]),
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
			'fetchInternship',
		]),
		init(){
			this.fetchData()
		},
		fetchData(){
			this.fetchInternship({
				month: this.month + 1,
				year: this.year,
			})
		}
	}
}
</script>

<style lang="scss">
// .TabSecondStage{}
</style>
