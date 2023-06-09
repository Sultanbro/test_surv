<template>
	<div class="TabDismissal">
		<b-tabs>
			<b-tab
				title="Причины и процент текучки"
				key="1"
				card
			>
				<div class="pt-4">
					<TableStaffTurnover
						:staff="staff"
						:causes="causes"
						:staff_longevity="staffLongevity"
						:staff_by_group="staffByGroup"
					/>
				</div>
			</b-tab>
			<b-tab
				title="Причины: Бот"
				key="2"
				card
			>
				<ReasonsBot :quiz="quiz" />
			</b-tab>
			<b-tab
				title="Причины увольнения"
				key="3"
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
import ReasonsBot from '@/components/pages/Analytics/ReasonsBot'

export default {
	name: 'TabDismissal',
	components: {
		JobtronTable,
		TableStaffTurnover,
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
	computed: {
		...mapState(useHRStore, [
			'isLoading',
			'isReady',
			'error',
			// Dismiss
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
			this.fetchDismiss({
				month: this.month + 1,
				year: this.year,
			})
		},
	}
}
</script>

<style lang="scss">
</style>
