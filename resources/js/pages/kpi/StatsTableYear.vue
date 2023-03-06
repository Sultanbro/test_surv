<template>
	<div class="StatsTableYear">
		<div class="StatsTableYear-filters row">
			<b-col cols="3">
				<b-form-select
					v-model="year"
					:options="yearOptions"
				/>
			</b-col>
		</div>
		<table class="StatsTableYear-table j-table mt-4">
			<thead>
				<tr class="table-heading">
					<th class="first-column">
						Сотрудник\Отдел
					</th>
					<th class="text-center">
						Средний по году
					</th>
					<th
						v-for="month in $moment.months()"
						:key="month"
						class="text-center"
					>
						{{ month }}
					</th>
				</tr>
			</thead>
			<tbody>
				<tr
					v-for="kpi in stats"
					:key="kpi.id"
				>
					<td class="p-3">
						<i
							v-if="kpi.type === 1"
							class="fa fa-user mt-1 mr-1"
						/>
						<i
							v-else
							class="fa fa-users mt-1 mr-1"
						/>
						{{ kpi.title }}
					</td>
					<td class="text-center p-3">
						{{ kpi.avg }}
					</td>
					<td
						v-for="month, key in $moment.months()"
						:key="key"
						class="text-center p-3"
					>
						{{ kpi[key+1] || '' }}
					</td>
				</tr>
			</tbody>
		</table>
		<b-pagination
			v-model="page"
			:total-rows="statYear.rows"
			:per-page="statYear.limit"
			size="sm"
			class="mt-4"
		/>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useKPIStore } from '@/stores/KPI'
import { useYearOptions } from '@/composables/yearOptions'

export default {
	name: 'StatsTableYear',
	components: {},
	data(){
		const now = new Date()
		return {
			yearOptions: useYearOptions(),
			page: 1,
			year: now.getFullYear(),
		}
	},
	computed: {
		...mapState(useKPIStore, ['statYear']),
		stats(){
			const table = []
			Object.entries(this.statYear.data).forEach(([month, monthData]) => {
				monthData.forEach((kpi, index) => {
					if(!table[index]) table[index] = {
						id: `${kpi.target.type}-${kpi.target.id}`,
						title: kpi.target.name,
						type: kpi.target.type,
					}
					table[index][month] = kpi.avg
				})
			})
			table.forEach(row => {
				row.avg = 0
				let lastMonth = 0
				Object.keys(this.statYear.data).forEach(month => {
					lastMonth = parseInt(month)
					row.avg += row[month]
				})
				row.avg = row.avg / lastMonth
			})
			return table
		}
	},
	watch: {
		'statYear.page'(value){
			this.page = value
		},
		page(value){
			this.setStatYearPage(value)
		},
		'statYear.year'(value){
			this.year = value
		},
		yaer(value){
			this.setStatYearYear(value)
		},
	},
	created(){
		this.fetchStatYear(this.statYear.year, this.statYear.page, this.statYear.limit)
	},
	methods: {
		...mapActions(useKPIStore, [
			'fetchStatYear',
			'setStatYearPage',
			'setStatYearYear',
		])
	}
}
</script>

<style lang=scss>
.StatsTableYear{
	&-filters{}
	& &-table{
		.first-column{
			width: 20rem;
		}
	}
}
</style>
