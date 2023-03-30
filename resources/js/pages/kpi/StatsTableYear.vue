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
			<b-col
				cols="9"
				class="d-fex aic"
			>
				<i
					v-if="isAdmin"
					class="fa fa-cogs btn ml-a"
					@click="isSettingsOpen = true"
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
							{{ kpi.avg | nonFixedFloat }}
						</td>
						<td
							v-for="month, key in $moment.months()"
							:key="key"
							class="text-center p-3"
							:style="`background-color: ${getBacklightForValue(kpi[key+1])}`"
						>
							{{ kpi[key+1] | nonFixedFloat }}
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
								{{ user.avg | nonFixedFloat }}
							</td>
							<td
								v-for="month, key in $moment.months()"
								:key="key"
								class="text-center p-3"
								:style="`background-color: ${getBacklightForValue(user[key+1])}`"
							>
								{{ user[key+1] | nonFixedFloat }}
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
		<SideBar
			:open="isSettingsOpen"
			width="50vw"
			title="Настройки"
			@close="isSettingsOpen = false"
		>
			<h3>Подсветка ячеек</h3>
			<div
				v-for="item, index in colors"
				:key="index"
				class="KPIBacklight-row"
			>
				от:
				<input
					type="number"
					:min="item.prevMax"
					:max="99"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.startValue"
				>
				до:
				<input
					type="number"
					:min="item.prevMax + 1"
					:max="100"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.endValue"
				>
				цвет:
				<input
					type="color"
					class="form-control input-surv KPIBacklight-input"
					v-model="item.color"
				>
				<i
					class="fa fa-trash btn btn-danger btn-icon"
					@click="deleteBacklightColor(index)"
				/>
			</div>
			<button
				class="btn btn-primary"
				@click="addBacklightColor(colors[colors.length-1])"
			>
				Добавить
			</button>
			<hr>
			<button
				class="btn btn-success"
				@click="updateBacklightColors"
			>
				Сохранить
			</button>
		</SideBar>
	</div>
</template>

<script>
import SideBar from '@/components/ui/Sidebar'
import { mapActions, mapState } from 'pinia'
import { useKPIStore } from '@/stores/KPI'
import { usePortalStore } from '@/stores/Portal'
import { useYearOptions } from '@/composables/yearOptions'

export default {
	name: 'StatsTableYear',
	components: {
		SideBar,
	},
	data(){
		const now = new Date()
		return {
			yearOptions: useYearOptions(),
			page: 1,
			year: now.getFullYear(),
			isSettingsOpen: false,
			colors: [this.getBlankBacklight()]
		}
	},
	computed: {
		...mapState(useKPIStore, ['statYear', 'isLoading']),
		...mapState(usePortalStore, ['portal']),
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
								name: `${user.last_name} ${user.name}`,
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
		isAdmin(){
			return this.$laravel.is_admin
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
		'portal.kpi_backlight'(value){
			this.syncColors(value)
		},
		yaer(value){
			this.setStatYearYear(value)
		},
	},
	created(){
		this.fetchStatYear(this.statYear.year, this.statYear.page, this.statYear.limit)
	},
	mounted(){
		this.syncColors(this.portal?.kpi_backlight)
	},
	methods: {
		...mapActions(useKPIStore, [
			'fetchStatYear',
			'setStatYearPage',
			'setStatYearYear',
		]),
		...mapActions(usePortalStore, [
			'fetchPortal',
			'updatePortal',
		]),
		toggleKPI(kpi){
			if(kpi.type === 1 || !kpi.users.length) return
			this.$set(kpi, 'expanded', !kpi.expanded)
			this.$forceUpdate()
		},
		getBlankBacklight(min){
			return {
				startValue: min || 0,
				endValue: min || 0,
				prevMax: min || 0,
				color: ''
			}
		},
		getBacklightForValue(value){
			if(!this.colors || !this.colors.length) return ''
			const num = +value
			const item = this.colors.findLast(item => {
				return item.startValue <= num && num <= item.endValue
			})
			return item ? item.color : ''
		},
		addBacklightColor(prev){
			this.colors.push(this.getBlankBacklight(prev?.endValue || 0))
		},
		deleteBacklightColor(index){
			this.colors.splice(index, 1)
			if(!this.colors.length){
				this.addBacklightColor()
			}
		},
		updateBacklightColors(){
			this.updatePortal({
				kpiBackLight: this.colors.map(({startValue, color}) => ({
					start: startValue,
					color
				}))
			})
		},
		syncColors(value){
			if(!value || !value.length){
				this.colors = [this.getBlankBacklight()]
				return
			}
			this.colors = value.map((item, index) => {
				return {
					...item,
					prevMax: item.startValue,
					endValue: value[index + 1] ? value[index + 1].startValue : 100
				}
			})
		}
	},
	filters: {
		nonFixedFloat(value){
			if(typeof value === 'undefined') return ''
			return parseInt(value) === value ? value : value.toFixed(2)
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
