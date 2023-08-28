<template>
	<div
		class="StatsTableYear"
		:class="{
			'v-loading': isLoading.year
		}"
	>
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
				<template v-for="kpi, index in stats">
					<tr :key="kpi.id">
						<td
							class="p-3 pointer"
							@click="toggleKPI(kpi)"
						>
							<div class="StatsTableYear-firstCol">
								<span>{{ index + 1 }}</span>
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
							</div>
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
						<td
							class="text-center p-3"
							:style="`background-color: ${getBacklightForValue(kpi.avg)}`"
						>
							{{ kpi.avg | nonFixedFloat }}<sub v-if="typeof kpi.avg !== 'undefined'">%</sub>
						</td>
						<td
							v-for="month, key in $moment.months()"
							:key="key"
							class="text-center p-3"
							:style="`background-color: ${getBacklightForValue(kpi[key+1])}`"
						>
							{{ kpi[key+1] | nonFixedFloat }}<sub v-if="typeof kpi[key+1] !== 'undefined'">%</sub>
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
							<td
								class="text-center p-3"
								:style="`background-color: ${getBacklightForValue(user.avg)}`"
							>
								{{ user.avg | nonFixedFloat }}<sub v-if="typeof user[key+1] !== 'undefined'">%</sub>
							</td>
							<td
								v-for="month, key in $moment.months()"
								:key="key"
								class="text-center p-3"
								:style="`background-color: ${getBacklightForValue(user[key+1])}`"
							>
								{{ user[key+1] | nonFixedFloat }}<sub v-if="typeof user[key+1] !== 'undefined'">%</sub>
							</td>
						</tr>
					</template>
				</template>
			</tbody>
		</table>
		<b-col>
			<b-row>
				<b-col class="d-flex aic">
					<b-pagination
						v-model="page"
						:total-rows="statYear.rows"
						:per-page="statYear.limit"
						size="sm"
						class="mt-4"
					/>
				</b-col>
				<b-col
					class="d-flex aic"
					cols="3"
				>
					<b-form-select
						v-model="statYear.limit"
						:options="[10, 20, 50, 100]"
					/>
				</b-col>
			</b-row>
		</b-col>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useKPIStore } from '@/stores/KPI'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'

const now = new Date()

export default {
	name: 'StatsTableYear',
	components: {},
	filters: {
		nonFixedFloat(value){
			if(typeof value === 'undefined') return ''
			return parseInt(value) === value ? value : value.toFixed(2)
		}
	},
	props:{
		year:{
			type: Number,
			default: now.getFullYear(),
		}
	},
	data(){
		return {
			page: 1,
		}
	},
	computed: {
		...mapState(useKPIStore, ['statYear', 'isLoading']),
		...mapState(usePortalStore, ['portal']),
		yearOptions(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		stats(){
			const table = []
			Object.entries(this.statYear.data).forEach(([month, monthData]) => {
				monthData.forEach(kpi => {
					const id = `${kpi.target.type}-${kpi.target.id}`
					let index = table.findIndex(existsKPI => existsKPI.id == id)
					if(!~index) {
						table.push({
							id: id,
							title: kpi.target.name,
							type: kpi.target.type,
							expanded: false,
							users: []
						})
						index = table.length - 1
					}
					table[index][month] = kpi.avg
					kpi.users.forEach(user => {
						let userIndex = table[index].users.findIndex(existsUser => existsUser.id === user.id)
						if(!~userIndex){
							table[index].users.push({
								id: user.id,
								name: `${user.name}`,
							})
							userIndex = table[index].users.length - 1
						}
						table[index].users[userIndex][month] = user.avg_percent
					})
				})
			})
			table.forEach(row => {
				row.avg = 0
				let monthCount = 0
				Object.keys(row).forEach(month => {
					const intMonth = parseInt(month)
					if(Number.isNaN(intMonth)) return
					if(typeof row[month] === 'undefined') return
					row.avg += row[month]
					++monthCount
				})
				row.avg = monthCount ? row.avg / monthCount : 0

				row.users.forEach(user => {
					user.avg = 0
					let userMonthCount = 0
					Object.keys(user).forEach(month => {
						const intUserMonth = parseInt(month)
						if(Number.isNaN(intUserMonth)) return
						if(typeof user[month] === 'undefined') return
						user.avg += user[month]
						++userMonthCount
					})
					user.avg = userMonthCount ? user.avg / userMonthCount : 0
				})
			})
			return table
		},
	},
	watch: {
		'statYear.page'(value){
			this.page = value
		},
		page(value){
			this.setStatYearPage(value)
		},
		year(){
			this.fetchStatYear(this.year)
		},
		'statYear.limit'(){
			this.fetchStatYear(this.year)
		},
	},
	created(){
		this.fetchStatYear(this.year, this.statYear.page, this.statYear.limit)
	},
	mounted(){},
	methods: {
		...mapActions(useKPIStore, [
			'fetchStatYear',
			'setStatYearPage',
			'setStatYearYear',
		]),
		...mapActions(usePortalStore, ['getBacklightForValue']),
		toggleKPI(kpi){
			if(kpi.type === 1 || !kpi.users.length) return
			this.$set(kpi, 'expanded', !kpi.expanded)
			this.$forceUpdate()
		},
	},
}
</script>

<style lang="scss">
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
	&-firstCol{
		display: flex;
		justify-content: space-between;
	}
}
.KPIBacklight{
	&-row{
		display: flex;
		align-items: center;
		gap: 0.5rem;
		margin-bottom: 1rem;
	}
	&-input{
		display: inline-flex;
		width: auto;
		&[type="color"]{
			padding: 0 !important;
			min-width: 4rem;
		}
	}
}
</style>
