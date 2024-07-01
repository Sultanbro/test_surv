<template>
	<div class="TabInterns">
		<TableSkype
			v-if="skypes.data"
			:month="monthInfo"
			:skypes="skypes.data"
			:groups="sgroups"
			:invite_groups="inviteGroups"
			:segments="segments"
		/>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useHRStore } from '@/stores/ReportsHR.js'

import TableSkype from '@/components/tables/TableSkype' // Стажеры

export default {
	name: 'TabInterns',
	components: {
		TableSkype,
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
			// Trainees
			'inviteGroups',
			'segments',
			'sgroups',
			'skypes',
		]),
		monthInfo(){
			const now = new Date()
			return {
				currentMonth: this.month || this.$moment(now).format('MMMM')
			}
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
			'fetchTrainees',
		]),
		init(){
			this.fetchData()
		},
		fetchData(){
			this.fetchTrainees({
				month: this.month + 1,
				year: this.year,
				limit: 2000, // временно т.к. переделывать пагинацию это отдельная история, нужно реализовать фильтры на беке
			})
		}
	}
}
</script>

<style lang="scss">
// .TabInterns{}
</style>
