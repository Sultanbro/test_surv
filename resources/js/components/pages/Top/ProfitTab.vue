<template>
	<div class="ProfitTab">
		<!-- Ориентир -->
		<JobtronTable
			:fields="[{key: 0}, {key: 1}, {key: 2}, {key: 3}, {key: 4}]"
			:items="firstTable"
			headless
			class="ProfitTab-table mt-4"
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
					>
					<div class="ProfitTab-editableValue ProfitTab-padding">
						{{ separateNumber(numberToCurrency(other)) }}
					</div>
				</div>
				<template v-else-if="row.index === 2">
					{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
				</template>
				<template v-else>
					{{ row.value }}
				</template>
			</template>
			<template #cell(2)="row">
				<template v-if="row.index > 3 || row.index === 2">
					{{ row.value ? separateNumber(numberToCurrency(row.value)) : '' }}
				</template>
				<template v-else>
					{{ row.value }}
				</template>
			</template>
			<template #cell(3)="row">
				{{ numberToCurrency(row.value) + (row.index === 2 ? '%' : '') }}
			</template>
			<template #cell(4)="row">
				{{ numberToCurrency(row.value) + (row.index === 2 ? '%' : '') }}
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
			class="ProfitTab-table mt-4"
		>
			<template #cell(revenue)="row">
				{{ separateNumber(numberToCurrency(row.value)) }}
			</template>
			<template #cell(fot)="row">
				{{ separateNumber(numberToCurrency(row.value)) }}
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
			class="ProfitTab-table mt-4"
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
					:class="[row.item.fact < Number(row.item.plan) ? 'ProfitTab-good' : 'ProfitTab-bad']"
				>
					{{ separateNumber(numberToCurrency(row.value)) }}
				</div>
			</template>
			<template #cell(plan)="row">
				<div
					class="ProfitTab-unpad"
					:class="[row.item.name ? (row.item.fact < Number(row.item.plan) ? 'ProfitTab-bad' : 'ProfitTab-good') : '']"
				>
					{{ separateNumber(numberToCurrency(row.value)) }}
				</div>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table.vue'
import { numberToCurrency, separateNumber } from '@/composables/format.js'

import {
	fetchSettings,
	// updateSettings,
} from '@/stores/api.js'

function percentMinMax(value, min, max){
	return (value - min) / (max - min)
}

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
		}
	},
	computed: {
		daysInMonth(){
			return this.$moment([this.year, this.month]).daysInMonth()
		},
		daysPassed(){
			const now = new Date()
			if(now.getFullYear() === this.year && now.getMonth() === this.month) return now.getDate()
			return this.daysInMonth
		},

		totalsSecond(){
			return {
				name: '',
				revenue: this.secondTable.reduce((result, row) => result + Number(row.revenue), 0),
				fot: this.secondTable.reduce((result, row) => result + Number(row.fot), 0),
				percent: this.secondTable.length ? this.secondTable.reduce((result, row) => result + Number(row.percent), 0) / this.secondTable.length : 0,
			}
		},
		totalsThird(){
			return {
				name: '',
				approved: this.thirdTable.reduce((result, row) => result + Number(row.approved), 0),
				fact: this.thirdTable.reduce((result, row) => result + Number(row.fact), 0),
				plan: this.thirdTable.reduce((result, row) => result + Number(row.plan), 0),
			}
		},
		firstTable(){
			const revenue = this.totalsSecond.revenue / this.daysPassed * this.daysInMonth
			const expenses = ((this.totalsSecond.fot + this.totalsThird.fact) / this.daysPassed * this.daysInMonth) + Number(this.other)
			const profit = revenue - expenses
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
					'Маржа',
					'Рентабельность',
				],
				[
					revenue,
					expenses,
					profit,
					revenue ? ((revenue - (Number(this.totalsSecond.fot) / this.daysPassed * this.daysInMonth)) / revenue) * 100 : 0,
					revenue ? (profit / revenue) * 100 : 0,
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
					Number(this.totalsSecond.revenue) - Number(this.totalsSecond.fot) - this.totalsThird.plan - Number(this.other),
					'',
					'',
				],
				[
					'',
					'Profit FACT на сегодня',
					Number(this.totalsSecond.revenue) - Number(this.totalsSecond.fot) - this.totalsThird.fact - Number(this.other),
					'',
					'',
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
	},
	methods: {
		separateNumber,
		numberToCurrency,
		async fetchData(){
			const loader = this.$loading.show()

			const {settings: ccGroups} = await fetchSettings('profit_cc_groups')
			this.ccGroups = JSON.parse(ccGroups.custom_profit_cc_groups || '[31, 42, 71, 132, 136, 137, 142, 151]')

			const {settings: admGroups} = await fetchSettings('profit_adm_groups')
			this.admGroups = JSON.parse(admGroups.custom_profit_adm_groups || '[23, 48, 102, 26]') // 96 - OO

			this.other = 50000

			this.secondTable = [
				{
					name: 'Отдел 1',
					revenue: 1393284,
					fot: 630638,
				},
				{
					name: 'Отдел 2',
					revenue: 234180,
					fot: 238316,
				},
				{
					name: 'Отдел 3',
					revenue: 119562,
					fot: 81377,
				},
			].map(row => ({
				...row,
				percent: ((row.revenue - row.fot) / row.revenue) * 100
			}))

			this.thirdTable = [
				{
					name: 'Отдел 4',
					approved: 3200000,
					fact: 250752,
				},
				{
					name: 'Отдел 5',
					approved: 1210000,
					fact: 90908,
				},
			].map(row => ({
				...row,
				plan: row.approved / this.daysInMonth * this.daysPassed
			}))

			loader.hide()
		},

		getCellColor(value) {
			const perc = percentMinMax(value, this.secondMin, this.secondMax) * 100
			let r, g, b = 0;
			if(perc < 50) {
				r = 235;
				g = Math.round(5.1 * perc);
				b = Math.round(113 - 1.13 * perc);
			}
			else {
				g = 225;
				r = Math.round(510 - 5.1 * perc);
			}
			const h = r * 0x10000 + g * 0x100 + b * 0x1;
			return '#' + ('000000' + h.toString(16)).slice(-6);
		},
	},
}
</script>

<style lang="scss">
.ProfitTab{
	$paddingX: 15px;
	$padding: 12px $paddingX;
	$margin: -12px -15px;

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
	&-editableValue{}
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
		background-color: rgb(0, 225, 0);
	}
	&-bad{
		background-color: rgb(235, 0, 113);
	}
}
</style>
