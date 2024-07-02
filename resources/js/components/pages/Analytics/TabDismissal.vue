<template>
	<div class="TabDismissal">
		<b-tabs>
			<b-tab
				key="1"
				title="Причины и процент текучки"
				card
			>
				<div class="pt-4">
					<FilterStaffTurnover
						v-model="filterTurnover"
						class="mb-3"
						@input="fetchData"
					/>
					<TableStaffTurnover
						:filter="filterTurnover"
						:staff="staff"
						:causes="causes"
						:staff_longevity="staffLongevity"
						:staff_by_group="staffByGroup"
					/>
				</div>
			</b-tab>
			<b-tab
				key="2"
				title="Причины: Бот"
				card
			>
				<ReasonsBot :quiz="quiz" />
			</b-tab>
			<b-tab
				key="3"
				title="Причины увольнения"
				card
			>
				<div class="row">
					<div class="col-md-12 col-lg-6 d-flex align-items-center pt-4">
						<JobtronTable
							:fields="[{key: 'cause', label: 'Причины увольнения', colspan: 2, thClass: 'text-left', tdClass: 'text-left'}, {key: 'count', hide: true, tdClass:'text-center'}]"
							:items="absentsFirst"
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
import TableStaffTurnover from '@/components/tables/TableStaffTurnover.vue'
import FilterStaffTurnover from '@/components/pages/Analytics/FilterStaffTurnover.vue'
import ReasonsBot from '@/components/pages/Analytics/ReasonsBot'

export default {
	name: 'TabDismissal',
	components: {
		JobtronTable,
		TableStaffTurnover,
		FilterStaffTurnover,
		ReasonsBot,
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
			filterTurnover: {
				formula: 1,
				position: 0,
			},
		}
	},
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
			// Dismiss
			'absentsFirst',
			'causes',
			'quiz',
			'staff',
			'staffByGroup',
			'staffLongevity',
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
			'fetchDismiss',
		]),
		init(){
			this.fetchData()
		},
		fetchData(){
			const filtersFields = {
				position: 'position_id',
				formula: 'formula_type',
			}
			const params = {
				month: this.month + 1,
				year: this.year,
			}
			Object.keys(this.filterTurnover).forEach(key => {
				params[filtersFields[key]] = this.filterTurnover[key]
			})
			this.fetchDismiss(params)
		},
	}
}
</script>

<style lang="scss">
</style>
