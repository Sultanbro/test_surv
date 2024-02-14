<template>
	<div class="ProfitTab">
		<!-- Ориентир -->
		<JobtronTable
			:fields="[{key: 0}, {key: 1}, {key: 2}, {key: 3}, {key: 4}]"
			:items="firstTable"
			headless
			class="ProfitTab-table my-5"
		>
			<template #thead>
				<!--  -->
			</template>
			<template #cell(1)="row">
				<div
					v-if="row.index === 3"
					class="ProfitTab-editable"
				>
					<input
						v-model="other"
						type="number"
						class="ProfitTab-input ProfitTab-padding text-center"
						@input="onChangeOther"
					>
					<div class="ProfitTab-editableValue ProfitTab-padding">
						{{ separateNumber(numberToCurrency(other)) }}
					</div>
				</div>
				<template v-else-if="row.index === 2">
					{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
				</template>
				<template v-else-if="row.index === 0">
					{{ row.value }}
					<img
						v-b-popover.click.blur.html="`Итоги ${row.value} дней`"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
						tabindex="-1"
						width="20"
					>
				</template>
				<template v-else>
					{{ row.value }}
				</template>
			</template>
			<template #cell(2)="row">
				<template v-if="row.index === 4 || row.index === 2">
					{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
				</template>
				<template v-else-if="row.index === 5">
					<div
						:class="resultClass"
						class="ProfitTab-unpad"
					>
						{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
					</div>
				</template>
				<template v-else>
					{{ row.value }}
				</template>
			</template>
			<template #cell(3)="row">
				{{ numberToCurrency(row.value) + (row.index === 2 ? '%' : '') }}
			</template>
			<template #cell(4)="row">
				{{ numberToCurrency(row.value) + ([2,5].includes(row.index) ? '%' : '') }}
				<img
					v-if="row.index === 5"
					v-b-popover.click.blur.html="`Рентабельность на сегодня`"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					tabindex="-1"
					width="20"
				>
			</template>
			<template #cell="row">
				<template v-if="row.index === 2">
					{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
				</template>
				<template v-else />
			</template>
		</JobtronTable>

		<!-- выручка/фот -->
		<JobtronTable
			:fields="secondFields"
			:items="[...secondTable, totalsSecond]"
			class="ProfitTab-table my-5"
		>
			<template #cell(revenue)="row">
				<div
					class=""
				>
					{{ separateNumber(numberToCurrency(row.value.now)) }}
				</div>
			</template>
			<template #cell(revenue2)="row">
				<div
					v-b-popover.click.blur.html="row.index !== secondTable.length ? [
						`Последння дата: ${row.item.revenue.lastPositiveDate}`,
						`За последнюю дату: ${row.item.revenue.lastPositive}`,
						`За ${daysPassed}: ${row.item.revenue.lastRev}`,
						`Расчет: ${daysInMonth - daysPassed}*${row.item.revenue.lastPositive}`
					].join('<br>') : ''"
					class=""
				>
					{{ separateNumber(row.item.revenue.predict) }}
				</div>
			</template>
			<template #cell(revenue3)="row">
				{{ row.item.revenue.total }}
			</template>
			<template #cell(fot)="row">
				<div
					v-b-popover.click.blur.html="row.index !== secondTable.length ? [
						`Работающие: ${row.value.actual}`,
						`Уволенные: ${row.value.fired}`,
						`Стажеры: ${row.value.trainee}`,
					].join('<br>') : ''"
					class=""
				>
					{{ separateNumber(numberToCurrency(row.value.sum)) }}
				</div>
			</template>
			<template #cell(fot2)="row">
				<div
					class=""
				>
					{{ separateNumber(numberToCurrency(row.item.fot.predict)) }}
				</div>
			</template>
			<template #cell(fot3)="row">
				<div
					class=""
				>
					{{ separateNumber(numberToCurrency(row.item.fot.predict + row.value.sum)) }}
				</div>
			</template>
			<template #cell(percent)="row">
				<div
					class="ProfitTab-unpad"
					:style="row.item.name ? `background: ${getCellColor(row.value)};` : ''"
				>
					{{ numberToCurrency(row.value) }}%
				</div>
			</template>
		</JobtronTable>

		<!-- план -->
		<JobtronTable
			:fields="thirdFields"
			:items="[...thirdTable, totalsThird]"
			class="ProfitTab-table my-5"
		>
			<template #cell(approved)="row">
				<div
					v-if="row.item.name"
					class="ProfitTab-editable"
				>
					<input
						v-model="row.item.approved"
						type="number"
						class="ProfitTab-input ProfitTab-padding text-center"
						@input="onChangeApprove(row.item.approved, row.item.id)"
					>
					<div class="ProfitTab-editableValue ProfitTab-padding">
						{{ separateNumber(numberToCurrency(row.value)) }}
					</div>
				</div>

				<template v-else>
					{{ separateNumber(numberToCurrency(row.value)) }}
				</template>
			</template>
			<template #cell(fact)="row">
				<div
					class="ProfitTab-unpad"
					:class="[row.item.fact < Number(row.item.plan) ? 'ProfitTab-bad' : 'ProfitTab-good']"
					:title="`Работающие: ${row.value.actual}, Уволенные: ${row.value.fired}, Стажеры: ${row.value.trainee}, Прогноз: ${row.value.sum + row.value.predict}`"
				>
					{{ separateNumber(numberToCurrency(row.value.sum)) }}
				</div>
			</template>
			<template #cell(plan)="row">
				<div
					class="ProfitTab-unpad"
					:class="[row.item.name ? (row.item.fact < Number(row.item.plan) ? 'ProfitTab-good' : 'ProfitTab-bad') : '']"
				>
					{{ separateNumber(numberToCurrency(row.value)) }}
				</div>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table.vue'

import { mapGetters } from 'vuex'
import { bus } from '@/bus'
import { calcGroupFOT } from './helper.js'
import { numberToCurrency, separateNumber } from '@/composables/format.js'
import {
	fetchSettings,
	updateSettings,
	fetchTop,
} from '@/stores/api.js'
// import { fetchRentabilityV2 } from '@/stores/api/analytics.js'


function percentMinMax(value, min, max){
	return (value - min) / (max - min)
}

const colors = [
	'f8696b',
	'f87b6e',
	'f98e72',
	'faa075',
	'fbb379',
	'fcc57c',
	'fdd880',
	'ffeb84',
	'e9e482',
	'd2de81',
	'bcd780',
	'a6d17e',
	'90cb7d',
	'79c47c',
	'63be7b',
]

export default {
	name: 'ProfitTab',
	components: {
		JobtronTable,
	},
	props: {
		year: {
			type: Number,
			default: 0
		},
		month: {
			type: Number,
			default: 0
		},
	},
	data(){
		return {
			ccGroups: [],
			admGroups: [],
			other: 0,
			secondFields: [
				{
					key: 'name',
					label: '',
				},
				{
					key: 'revenue',
					label: 'Выручка',
				},
				{
					key: 'fot',
					label: 'Факт ФОТ КЦ',
				},
				{
					key: 'percent',
					label: '',
				},
			],
			thirdFields: [
				{
					key: 'name',
					label: '',
				},
				{
					key: 'approved',
					label: 'ФОТ утвержденный',
				},
				{
					key: 'fact',
					label: 'факт ФОТ АДМ',
				},
				{
					key: 'plan',
					label: 'план ФОТ',
				},
			],
			secondTable: [],
			thirdTable: [],
			otherTimeout: null,
			approveTimeout: null,
		}
	},
	computed: {
		...mapGetters(['profileGroups']),
		daysInMonth(){
			return this.$moment([this.year, this.month]).daysInMonth()
		},
		daysPassed(){
			const now = new Date()
			if(now.getFullYear() === this.year && now.getMonth() === this.month) return now.getDate() - 1
			return this.daysInMonth
		},

		totalsSecond(){
			return {
				name: '',
				revenue: this.secondTable.reduce((result, row) => {
					result.now += Number(row.revenue.now)
					result.predict += Number(row.revenue.predict)
					result.total += Number(row.revenue.total)
					result.lastRev += Number(row.revenue.lastRev)
					result.lastPositive += Number(row.revenue.lastPositive)
					if(Number(row.revenue.lastPositiveDate) > result.lastPositiveDate) result.lastPositiveDate = Number(row.revenue.lastPositiveDate)
					return result
				}, {
					now: 0,
					predict: 0,
					total: 0,
					lastRev: 0,
					lastPositive: 0,
					lastPositiveDate: 0,
				}),
				fot: this.secondTable.reduce((result, row) => {
					result.sum += row.fot.sum
					result.actual += row.fot.actual
					result.fired += row.fot.fired
					result.trainee += row.fot.trainee
					result.predict += row.fot.predict
					return result
				}, {
					sum: 0,
					actual: 0,
					fired: 0,
					trainee: 0,
					predict: 0,
				}),
				percent: this.secondTable.length ? this.secondTable.reduce((result, row) => result + Number(row.percent), 0) / this.secondTable.length : 0,
			}
		},
		totalsThird(){
			return {
				name: '',
				approved: this.thirdTable.reduce((result, row) => result + Number(row.approved), 0),
				fact: this.thirdTable.reduce((result, row) => {
					result.sum += row.fact.sum
					result.actual += row.fact.actual
					result.fired += row.fact.fired
					result.trainee += row.fact.trainee
					result.predict += row.fact.predict
					return result
				}, {
					sum: 0,
					actual: 0,
					fired: 0,
					trainee: 0,
					predict: 0,
				}),
				plan: this.thirdTable.reduce((result, row) => result + Number(row.plan), 0),
			}
		},
		firstTable(){
			const revenue = this.totalsSecond.revenue.total
			const revenueNow = this.totalsSecond.revenue.now
			const exp = (this.totalsSecond.fot.sum + this.totalsSecond.fot.predict + this.totalsThird.fact.sum + this.totalsThird.fact.predict)
			const expenses = exp + Number(this.other)
			const profit = revenue - expenses
			const profitFact = Number(this.totalsSecond.revenue.now) - Number(this.totalsSecond.fot.sum) - this.totalsThird.fact.sum - Number(this.other)
			return [
				[
					'Ориентир ' + this.$moment([this.year, this.month]).format('MMMM'),
					this.daysPassed,
					this.daysInMonth,
					'',
					'',
				],
				[
					'Выручка',
					'Затраты',
					'Прибыль',
					'Маржа КЦ',
					'Рентабельность',
				],
				[
					revenue,
					expenses,
					profit,
					revenue ? ((revenue - (Number(this.totalsSecond.fot.sum) + Number(this.totalsSecond.fot.predict) )) / revenue) * 100 : 0,
					revenue ? profit / revenue * 100 : 0,
				],
				[
					'прочие затраты',
					this.other,
					'',
					'',
					'',
				],
				[
					'',
					'Profit PLAN на сегодня',
					Number(this.totalsSecond.revenue.now) - Number(this.totalsSecond.fot.sum) - this.totalsThird.plan - Number(this.other),
					'',
					'',
				],
				[
					'',
					'Profit FACT на сегодня',
					profitFact,
					'',
					revenueNow ? (profitFact / revenueNow) * 100 : 0,
				],
			]
		},
		secondPercents(){
			return this.secondTable.map(row => row.percent)
		},
		secondMin(){
			return Math.min(...this.secondPercents)
		},
		secondMax(){
			return Math.max(...this.secondPercents)
		},
		resultClass(){
			const plan = this.firstTable[4][2]
			const fact = this.firstTable[5][2]

			return plan < fact ? 'ProfitTab-good' : 'ProfitTab-bad'
		},
	},
	watch: {
		year(){
			this.fetchData()
		},
		month(){
			this.fetchData()
		},
	},
	created(){},
	mounted(){
		this.fetchData()
		bus.$on('tt-top-update', this.fetchData)

		if(window.admin){
			window.addAdminTool('profitAdvanced', this.profitAdvanced)
		}
	},
	beforeDestroy(){
		bus.$off('tt-top-update', this.fetchData)
	},
	methods: {
		separateNumber,
		numberToCurrency,
		async fetchDataTest(){
			this.other = 50000
			this.secondTable = []
			for(let i = 0; i < 15; ++i){
				this.secondTable.push({
					id: i,
					name: 'Отдел ' + i,
					revenue: {
						lastRev: 0,
						lastPositive: 0,
						lastPositiveDate: 0,
						now: 100000,
						predict: 100000,
						total: 200000,
					},
					fot: {
						sum: 100000,
						actual: 100000,
						fired: 0,
						trainee: 0,
						predict: 0,
					},
					percent: i
				})
			}

			this.thirdTable = []
			for(let i = 0; i < 15; ++i){
				this.thirdTable.push({
					id: i,
					name: 'Отдел ' + i,
					approved: 100000,
					fact: {
						sum: 100000,
						actual: 100000,
						fired: 0,
						trainee: 0,
						predict: 0,
					},
					plan: 99999
				})
			}
		},
		async fetchData(){
			if(this.$debug) return this.fetchDataTest()
			const loader = this.$loading.show()

			const date = `${this.year}_${this.month}`

			const {settings: ccGroups} = await fetchSettings('profit_cc_groups')
			// const defaultCCGroups = '[31, 71]'
			const defaultCCGroups = '[31, 42, 132, 136, 137, 142, 151]'
			this.ccGroups = JSON.parse(ccGroups.custom_profit_cc_groups === '0' ? defaultCCGroups : ccGroups.custom_profit_cc_groups || defaultCCGroups)

			const {settings: admGroups} = await fetchSettings('profit_adm_groups')
			// const defaultAdmGroups = '[23]'
			const defaultAdmGroups = '[23, 48, 102, 26]'
			this.admGroups = JSON.parse(admGroups.custom_profit_adm_groups === '0' ? defaultAdmGroups : admGroups.custom_profit_adm_groups || defaultAdmGroups) // 96 - OO

			const otherKey = 'profit_other_' + date
			const {settings: other} = await fetchSettings(otherKey)
			this.other = +other['custom_' + otherKey] || 0

			const admGroupsData = {}
			for(var group of this.admGroups){
				const groupKey = 'profit_adm_group_' + date + '_' + group
				const {settings} = await fetchSettings(groupKey)
				admGroupsData[group] = +settings['custom_' + groupKey] || 0
			}

			const { proceeds } = await fetchTop({
				year: this.year,
				month: this.month + 1,
			})

			const calcRevenue = (group, month) => {
				const result = {
					lastRev: 0,
					lastPositive: 0,
					lastPositiveDate: 0,
					now: 0,
					predict: 0,
					total: 0,
				}

				for(let i = 1; i <= this.daysPassed; ++i){
					const field = `${i > 9 ? i : '0' + i}.${month}`

					result.lastRev = Number(group[field] || 0)
					if(result.lastRev > 0) {
						result.lastPositive = result.lastRev
						result.lastPositiveDate = i
					}
					result.now += result.lastRev
					result.total += result.lastRev
				}

				result.last = result.lastRev
				if(this.daysPassed !== this.daysInMonth) {
					if(!result.lastRev) result.total += result.lastPositive

					for(let i = this.daysPassed + 1; i <= this.daysInMonth; ++i){
						result.predict += result.lastPositive
						result.total += result.lastPositive
					}
				}

				return result
			}

			const revenue = proceeds.records.reduce((result, group) => {
				if(!group.group_id) return result

				const month = this.month + 1 > 9 ? this.month + 1 : '0' + (this.month + 1)
				result[group.group_id] = calcRevenue(group, month)
				return result
			}, {})

			/* eslint-disable camelcase */
			const fot = {}
			for(var groupId of [...this.ccGroups, ...this.admGroups]){
				const group = this.profileGroups.find(group => group.id === groupId)
				var dataActual, dataTrainee, dataFired

				try {
					const {data} = await this.axios.post('/timetracking/salaries', {
						month: this.month + 1,
						year: this.year,
						group_id: groupId,
						user_types: 0,
					})
					dataActual = data
				}
				catch (error) {
					console.error(error)
					this.$toast.error('Не удалось зогрузить значения ФОТ действующих сотрудников для отдела ' + group?.name)
				}

				try {
					const {data} = await this.axios.post('/timetracking/salaries', {
						month: this.month + 1,
						year: this.year,
						group_id: groupId,
						user_types: 2,
					})
					dataTrainee = data
				}
				catch (error) {
					console.error(error)
					this.$toast.error('Не удалось зогрузить значения ФОТ стажеров для отдела ' + group?.name)
				}

				try {
					const {data} = await this.axios.post('/timetracking/salaries', {
						month: this.month + 1,
						year: this.year,
						group_id: groupId,
						user_types: 1,
					})
					dataFired = data
				}
				catch (error) {
					console.error(error)
					this.$toast.error('Не удалось зогрузить значения ФОТ уволенных сотрудников для отдела ' + group?.name)
				}

				fot[groupId] = {
					actual: dataActual ? calcGroupFOT(dataActual, this.daysPassed, this.daysInMonth) : 0,
					trainee: dataTrainee ? calcGroupFOT(dataTrainee, this.daysPassed, this.daysInMonth) : 0,
					fired: dataFired ? calcGroupFOT(dataFired, this.daysPassed, this.daysInMonth) : 0,
				}
			}
			/* eslint-enable camelcase */

			function sumFot(fot, daysPassed){
				const withoutKPI = fot.actual.bonus + fot.trainee.bonus + fot.fired.bonus
					+ fot.actual.total + fot.trainee.total + fot.fired.total
				const kpi = fot.actual.kpi + fot.trainee.kpi + fot.fired.kpi
				return {
					actual: daysPassed > 15 ? fot.actual.bonus + fot.actual.total : fot.actual.bonus + fot.actual.total + fot.actual.kpi,
					fired: daysPassed > 15 ? fot.fired.bonus + fot.fired.total : fot.fired.bonus + fot.fired.total + fot.fired.kpi,
					trainee: daysPassed > 15 ? fot.trainee.bonus + fot.trainee.total : fot.trainee.bonus + fot.trainee.total + fot.trainee.kpi,
					sum: daysPassed > 15 ? withoutKPI + kpi : withoutKPI,
					predict: fot.actual.predict + fot.trainee.predict
				}
			}

			this.secondTable = this.ccGroups.reduce((result, groupId) => {
				if(!fot[groupId]) return result
				const rowfot = sumFot(fot[groupId], this.daysPassed)
				const revNow = revenue[groupId]?.now || 0
				result.push({
					id: groupId,
					name: fot[groupId].actual.name,
					revenue: revenue[groupId] || {now: 0, predict: 0},
					fot: rowfot,
					percent: revNow ? ((revNow - rowfot.sum) / revNow) * 100 : 0,
				})
				return result
			}, [])

			this.thirdTable = this.admGroups.reduce((result, groupId) => {
				if(!fot[groupId]) return result
				const rowfot = sumFot(fot[groupId], this.daysPassed)
				result.push({
					id: groupId,
					name: fot[groupId].actual.name,
					approved: admGroupsData[groupId],
					fact: rowfot,
					allFot: fot[groupId],
					plan: admGroupsData[groupId] / this.daysInMonth * this.daysPassed
				})
				return result
			}, [])

			loader.hide()
		},

		getCellColor(value) {
			const perc = percentMinMax(value, this.secondMin, this.secondMax) * 100
			return '#' + colors[Math.round((colors.length - 1) * perc / 100)]
		},

		onChangeOther(){
			clearTimeout(this.otherTimeout)
			this.otherTimeout = setTimeout(() => {
				this.saveOther(this.other)
			}, 500)
		},

		onChangeApprove(value, groupId){
			clearTimeout(this.approveTimeout)
			this.approveTimeout = setTimeout(() => {
				this.saveApproved(value, groupId)
			}, 500)
		},

		async saveOther(value){
			const date = `${this.year}_${this.month}`
			const otherKey = 'profit_other_' + date
			await updateSettings({
				type: otherKey,
				[`custom_${otherKey}`]: value
			})
			this.$toast.success('Сохранено')
		},

		async saveApproved(value, groupId){
			const date = `${this.year}_${this.month}`
			const groupKey = 'profit_adm_group_' + date + '_' + groupId
			await updateSettings({
				type: groupKey,
				[`custom_${groupKey}`]: value
			})
			this.$toast.success('Сохранено')
		},

		profitAdvanced(){
			this.secondFields = [
				{
					key: 'name',
					label: '',
				},
				{
					key: 'revenue',
					label: 'Выручка',
				},
				{
					key: 'revenue2',
					label: 'Выручка прогноз',
				},
				{
					key: 'revenue3',
					label: 'Выручка всего',
				},
				{
					key: 'fot',
					label: 'Факт ФОТ КЦ',
				},
				{
					key: 'fot2',
					label: 'Прогноз ФОТ КЦ',
				},
				{
					key: 'fot3',
					label: 'Всего ФОТ КЦ',
				},
				{
					key: 'percent',
					label: '',
				},
			]
		},
	},
}
</script>

<style lang="scss">
.ProfitTab{
	$paddingX: 10px;
	$padding: 6px $paddingX;
	$margin: -6px -10px;

	display: flex;
  flex-flow: row wrap;
  gap: 20px;
  align-items: flex-start;

	&-table{
		width: auto;
	}

	&-unpad{
		padding: $padding;
		margin: $margin;
	}
	&-margin{
		margin: $margin;
	}
	&-padding{
		padding: $padding;
	}

	&-editable{
		margin: $margin;
		&:hover{
			.ProfitTab{
				&-editableValue{
					display: none;
				}
				&-input{
					display: block;
				}
			}
		}
	}
	// &-editableValue{}
	&-input{
		display: none;
		width: 150px;
		height: 1lh;
		&:focus{
			display: block;
			& ~ .ProfitTab{
				&-editableValue{
					display: none;
				}
			}
		}
	}

	&-full{
		width: calc(100% + $paddingX * 2);
		height: 1lh;
	}

	&-good{
		background-color: #63be7b;
	}
	&-bad{
		background-color: #f8696b;
	}

	.JobtronTable{
		&-td,
		&-th{
			padding: $padding;
		}
	}
}
</style>
