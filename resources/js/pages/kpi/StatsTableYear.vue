<template>
	<div
		class="StatsTableYear"
		:class="{
			'v-loading': isLoading.year
		}"
	>
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
					<th />
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
				<template v-for="kpi in stats">
					<tr :key="kpi.id">
						<td
							class="p-3 pointer"
							@click="toggleKPI(kpi)"
						>
							<template v-if="kpi.type !== 1 && kpi.users.length">
								<i
									v-if="kpi.expanded"
									class="fa fa-minus mt-1"
								/>
								<i
									v-else
									class="fa fa-plus mt-1"
								/>
							</template>
						</td>
						<td class="p-3">
							<i
								v-if="kpi.type === 1"
								class="fa fa-user mt-1 mr-1"
							/>
							<i
								v-else-if="kpi.type === 2"
								class="fa fa-users mt-1 mr-1"
							/>
							<i
								v-else
								class="fa fa-briefcase mt-1 mr-1"
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
					<template v-if="kpi.expanded">
						<tr
							v-for="user in kpi.users"
							:key="`${kpi.id}-${user.id}`"
							class="StatsTableYear-subrow"
						>
							<td />
							<td class="p-3">
								<i class="fa fa-user mt-1 mr-1" />
								{{ user.name }}
							</td>
							<td class="text-center p-3">
								{{ user.avg || '' }}
							</td>
							<td
								v-for="month, key in $moment.months()"
								:key="key"
								class="text-center p-3"
							>
								{{ user[key+1] || '' }}
							</td>
						</tr>
					</template>
				</template>
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
		...mapState(useKPIStore, ['statYear', 'isLoading']),
		stats(){
			const table = []
			Object.entries(this.statYear.data).forEach(([month, monthData]) => {
				monthData.forEach((kpi, index) => {
					if(!table[index]) table[index] = {
						id: `${kpi.target.type}-${kpi.target.id}`,
						title: kpi.target.name,
						type: kpi.target.type,
						expanded: false,
						users: []
					}
					table[index][month] = kpi.avg
					kpi.users.forEach(user => {
						let userIndex = table[index].users.findIndex(existsUser => existsUser.id === user.id)
						if(!~userIndex){
							table[index].users.push({
								id: user.id,
								name: `${user.last_name} ${user.name}`,
							})
							userIndex = table[index].users.length - 1
						}
						table[index].users[userIndex][month] = user.avg_percent == parseInt(user.avg_percent) ? user.avg_percent : user.avg_percent.toFixed(2)
					})
				})
			})
			table.forEach(row => {
				row.avg = 0
				let lastMonth = 0
				Object.keys(this.statYear.data).forEach(month => {
					lastMonth = parseInt(month)
					row.avg += row[month]
				})
				row.avg = (row.avg / lastMonth).toFixed(2)

				row.users.forEach(user => {
					user.avg = 0
					let lastUserMonth = 0
					Object.keys(this.statYear.data).forEach(month => {
						lastUserMonth = parseInt(month)
						user.avg += user[month]
					})
					user.avg = (user.avg / lastUserMonth).toFixed(2)
				})
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
		]),
		toggleKPI(kpi){
			if(kpi.type === 1 || !kpi.users.length) return
			this.$set(kpi, 'expanded', !kpi.expanded)
			this.$forceUpdate()
		}
	}
}
</script>

<style lang=scss>
.StatsTableYear{
	position: relative;
	// &-filters{}
	& &-table{
		.first-column{
			width: 20rem;
		}
	}
	&-subrow{
		background-color: #F7FAFC;
	}
}
</style>
