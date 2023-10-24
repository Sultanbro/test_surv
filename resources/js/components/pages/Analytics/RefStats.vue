<template>
	<div class="RefStats">
		<div class="d-flex gap-4 ais mb-4">
			<RefStatsIndex
				:value="separateNumber(parseInt(userPrice) === userPrice ? userPrice : userPrice.toFixed(2))"
				label="Цена принятого сотрудника"
				unit="₸"
			/>
			<RefStatsIndex
				:value="separateNumber(parseInt(cvResultDealPercent) === cvResultDealPercent ? cvResultDealPercent : cvResultDealPercent.toFixed(2))"
				label="CV лид ➝ сделка"
				unit="%"
			/>
			<RefStatsIndex
				:value="separateNumber(parseInt(cvDealUserPercent) === cvDealUserPercent ? cvDealUserPercent : cvDealUserPercent.toFixed(2))"
				label="CV сделка ➝ сотрудник"
				unit="%"
			/>
			<RefStatsIndex
				:value="separateNumber(totalMonth)"
				label="Заработано"
				unit="₸"
			/>
			<RefStatsIndex
				:value="separateNumber(totalPaid)"
				label="Выплачено"
				unit="₸"
			/>
		</div>

		<JobtronTable
			:fields="[
				{key: 'switch', label: ''},
				...tableFields,
			]"
			:items="sorted"
			:tr-after-class-fn="rowAfterClass"
			class="RefStats-table"
		>
			<template #header="{field}">
				<div
					class="RefStats-header pointer"
					@click="setSort(field.key)"
				>
					{{ field.label }}
				</div>
			</template>
			<template #cell(switch)="{item}">
				<div
					v-if="item.users.length"
					class="RefStats-switch pointer"
					@click="toggleAfter(item.id)"
				>
					{{ uncollapsed.includes(item.id) ? '-' : '+' }}
				</div>
			</template>
			<template #cell(leadsToDealPercent)="{value}">
				{{ value }}%
			</template>
			<template #cell(dealToUserPercent)="{value}">
				{{ value }}%
			</template>
			<template #cell(monthPaid)="{item, value}">
				<div
					class="RefStats-money"
					:class="{
						'RefStats-money_paid': value >= (item.month + item.monthRef)
					}"
				>
					{{ value }}
				</div>
			</template>
			<template #afterRow="firstLayerData">
				<div
					v-if="firstLayerData.value.users.length && uncollapsed.includes(firstLayerData.value.id)"
					class="RefStats-subtable"
				>
					<RefStatsReferals
						:user-id="firstLayerData.value.id"
						:sorted-subs="sortedSubs"
						@payment-click="showPaymentDialog"
						@sub-sort="setSubSort"
					/>
				</div>
			</template>
		</JobtronTable>

		<b-modal
			v-model="paymentDialog.open"
			:title="paymentDialog.title"
			@ok="paymentSave"
		>
			<div class="d-flex aic gap-4 mb-4">
				<JobtronSwitch
					v-model="paymentDialog.paid"
				/>
				Оплачено
			</div>
			<div class="d-flex flex-column gap-4 mb-4">
				<b-form-textarea
					v-model="paymentDialog.comment"
					placeholder="комментарий..."
					rows="3"
					max-rows="6"
				/>
			</div>
		</b-modal>
	</div>
</template>

<script>
import {
	tableFields,
	// getFakeReferer,
} from './helper'
import {
	separateNumber,
} from '@/composables/format'
import * as API from '@/stores/api/referral'


import JobtronTable from '@ui/Table.vue'
import JobtronSwitch from '@ui/Switch.vue'
import RefStatsIndex from './RefStatsIndex.vue'
import RefStatsReferals from './RefStatsReferals.vue'


const now = new Date()
export default {
	name: 'RefStats',
	components: {
		JobtronTable,
		JobtronSwitch,
		RefStatsIndex,
		RefStatsReferals,
	},
	props: {
		filters: {
			type: Object,
			default: () => ({
				year: now.getFullYear(),
				month: now.getMonth(),
			})
		}
	},
	data(){
		return {
			userPrice: 0,
			cvResultDealPercent: 0,
			cvDealUserPercent: 0,

			users: [],
			tableFields,
			uncollapsed: [],

			sortCol: 'accepted',
			sortOrder: 'desc',
			sortSubCol: 'title',
			sortSubOrder: 'desc',
			sortFn: {
				str: (a, b) => a.localeCompare(b),
				int: (a, b) => (parseInt(a) || 0) - (parseInt(b) || 0),
				float: (a, b) => (parseFloat(a) || 0) - (parseFloat(b) || 0),
			},

			paymentDialog: {
				open: false,
				title: '',
				id: 0,
				key: '',
				paid: false,
				comment: '',
				sum: 0,
			},
		}
	},
	computed: {
		sorted(){
			return this.users.slice().sort((a, b) => {
				if(['title', 'status'].includes(this.sortCol)){
					return this.sortOrder === 'asc' ? this.sortFn.str(a[this.sortCol], b[this.sortCol]) : this.sortFn.str(b[this.sortCol], a[this.sortCol])
				}
				if(['leads', 'deals', 'accepted', 'total', 'month', 'monthRef', 'monthPayd'].includes(this.sortCol)){
					return this.sortOrder === 'asc' ? this.sortFn.int(a[this.sortCol], b[this.sortCol]) : this.sortFn.int(b[this.sortCol], a[this.sortCol])
				}
				return this.sortOrder === 'asc' ? this.sortFn.float(a[this.sortCol], b[this.sortCol]) : this.sortFn.float(b[this.sortCol], a[this.sortCol])
			})
		},
		sortedSubs(){
			const sorted = {}
			this.users.forEach(user => {
				sorted[user.id] = user.users.slice().sort((a, b) => {
					if(['title', 'status'].includes(this.sortSubCol)){
						return this.sortSubOrder === 'asc' ? this.sortFn.str(a[this.sortSubCol], b[this.sortSubCol]) : this.sortFn.str(b[this.sortSubCol], a[this.sortSubCol])
					}
					return this.sortSubOrder === 'asc' ? this.sortFn.int(a[this.sortSubCol].value, b[this.sortSubCol].value) : this.sortFn.int(b[this.sortSubCol].value, a[this.sortSubCol].value)
				})
				user.users.forEach(user2 => {
					sorted[user2.id] = user2.users.slice().sort((a, b) => {
						if(['title', 'status'].includes(this.sortSubCol)){
							return this.sortSubOrder === 'asc' ? this.sortFn.str(a[this.sortSubCol], b[this.sortSubCol]) : this.sortFn.str(b[this.sortSubCol], a[this.sortSubCol])
						}
						return this.sortSubOrder === 'asc' ? this.sortFn.int(a[this.sortSubCol].value, b[this.sortSubCol].value) : this.sortFn.int(b[this.sortSubCol].value, a[this.sortSubCol].value)
					})
					user2.users.forEach(user3 => {
						sorted[user3.id] = user3.users.slice().sort((a, b) => {
							if(['title', 'status'].includes(this.sortSubCol)){
								return this.sortSubOrder === 'asc' ? this.sortFn.str(a[this.sortSubCol], b[this.sortSubCol]) : this.sortFn.str(b[this.sortSubCol], a[this.sortSubCol])
							}
							return this.sortSubOrder === 'asc' ? this.sortFn.int(a[this.sortSubCol].value, b[this.sortSubCol].value) : this.sortFn.int(b[this.sortSubCol].value, a[this.sortSubCol].value)
						})
					})
				})
			})
			return sorted
		},
		allReferals(){
			const referals = {}
			this.users.forEach(user => {
				user.users.forEach(ref1 => {
					referals[ref1.id] = ref1
					ref1.users.forEach(ref2 => {
						referals[ref2.id] = ref2
						ref2.users.forEach(ref3 => {
							referals[ref3.id] = ref3
						})
					})
				})
			})
			return referals
		},
		totalMonth(){
			return this.users.reduce((result, user) => result + user.month + user.monthRef, 0)
		},
		totalPaid(){
			return this.users.reduce((result, user) => result + user.monthPaid, 0)
		},
	},
	watch: {
		filters: {
			deep: true,
			handler(){
				this.fetchData()
			}
		}
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		separateNumber,
		async fetchData(){
			const loader = this.$loading.show()

			try {
				const data = await API.referralStat(this.filters)
				this.userPrice = data.userPrice
				this.cvResultDealPercent = data.cvResultDealPercent
				this.cvDealUserPercent = data.cvDealUserPercent
				this.users = data.users
				loader.hide()
			}
			catch (error) {
				loader.hide()
				console.error(error)
				this.$toast.error('Не удалось получить статистику реферальной программы')
				window.onerror && window.onerror(error)
			}
		},
		toggleAfter(id){
			const index = this.uncollapsed.findIndex(uId => uId === id)
			if(~index){
				this.uncollapsed.splice(index, 1)
			}
			else{
				this.uncollapsed.push(id)
			}
		},
		rowAfterClass(row){
			return this.uncollapsed.includes(row.id) ? 'RefStats-afterRowActive' : ''
		},
		setSort(key){
			if(key === 'switch') return
			if(key === this.sortCol) {
				this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
				return
			}
			this.sortOrder = key === 'title' ? 'asc' : 'desc'
			this.sortCol = key
		},
		setSubSort(key){
			if(key === 'switch') return
			if(key === this.sortSubCol) {
				this.sortSubOrder = this.sortSubOrder === 'asc' ? 'desc' : 'asc'
				return
			}
			this.sortSubOrder = key === 'title' ? 'asc' : 'desc'
			this.sortSubCol = key
		},
		showPaymentDialog({item, field}){
			if(!this.$can('referal_edit')) return
			this.paymentDialog.title = `Редактирование оплаты ${item.title} ${field.key}`
			this.paymentDialog.id = item.id
			this.paymentDialog.key = field.key
			this.paymentDialog.paid = item[field.key].paid
			this.paymentDialog.transactionId = item[field.key].id
			this.paymentDialog.comment = item[field.key].comment
			this.paymentDialog.open = true
		},
		async paymentSave(){
			try {
				await API.referralStatPay(this.paymentDialog.id, {
					id: this.paymentDialog.transactionId,
					type: this.field2type('' + this.paymentDialog.key),
					paid: this.paymentDialog.paid,
					commnet: this.paymentDialog.comment,
				})
				this.paymentDialog.open = false
				this.allReferals[this.paymentDialog.id][this.paymentDialog.key].paid = true
				this.allReferals[this.paymentDialog.id][this.paymentDialog.key].comment = this.paymentDialog.comment
				this.$toast.success('Сохранено')
			}
			catch (error) {
				console.error(error)
				this.$toast.error('Не сохранено')
				window.onerror && window.onerror(error)
			}
		},
		field2type(field){
			if(field === 'attest') return 3
			if(field.substring(field.length - 4) === 'Week') return 2
			return 1
		}
	},
}
</script>

<style lang="scss">
.RefStats{
	$cellpadding: 8px 10px;
	$bgmargin: -8px -10px;

	overflow-x: auto;
	font-size: 14px;
	position: relative;

	&-table{
		width: auto;
	}
	&-header{
		user-select: none;
	}
	.JobtronTable-th,
	.JobtronTable-td{
		padding: $cellpadding;
		font-size: 12px;
		line-height: 1.1;
		opacity: 0.9;
	}
	.JobtronTable-row:not(.JobtronTable-afterRow){
		&:hover{
			.JobtronTable-th,
			.JobtronTable-td{
				opacity: 1;
			}
		}
	}
	.JobtronTable-afterRow{
		display: none;
		&.RefStats-afterRowActive{
			display: table-row;
		}
		.JobtronTable,
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child,
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:last-child{
			border-radius: 0;
		}
		.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child::before{
			display: none;
		}
	}
	&-subtable{
		margin: $bgmargin;
		padding-left: 15px;
	}

	&-title{
		width: 200px;
		min-width: 200px;
		max-width: 250px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-money{
		padding: $cellpadding;
		margin: $bgmargin;
		background-color: #fdd;
		&_paid{
			background-color: #dfd;
		}
	}
	&-switch{
		padding: $cellpadding;
		margin: $bgmargin;
	}

	.RefStatsReferals{
		&-subtable{
			margin: $bgmargin;
			padding-left: 15px;
		}
	}
	.RefStatsReferals-firstLayer{
		> .JobtronTable-head,
		> .JobtronTable-body{
			> tr:not(.JobtronTable-afterRow){
				.JobtronTable-th,
				.JobtronTable-td{
					border-color: darken(#E7EAEA, 5);
				}
				.JobtronTable-th{
					background-color: darken(#f8f9fd, 5);
				}
				.JobtronTable-td{
					background-color: #dde9ff;
				}
			}
		}
	}
	.RefStatsReferals-secondLayer{
		> .JobtronTable-head,
		> .JobtronTable-body{
			> tr:not(.JobtronTable-afterRow){
				.JobtronTable-th,
				.JobtronTable-td{
					border-color: darken(#E7EAEA, 10);
				}
				.JobtronTable-th{
					background-color: darken(#f8f9fd, 10);
				}
				.JobtronTable-td{
					background-color: darken(#dde9ff, 5);
				}
			}
		}

	}
	.RefStatsReferals-thirdLayer{
		> .JobtronTable-head,
		> .JobtronTable-body{
			> tr:not(.JobtronTable-afterRow){
				.JobtronTable-th,
				.JobtronTable-td{
					border-color: darken(#E7EAEA, 15);
				}
				.JobtronTable-th{
					background-color: darken(#f8f9fd, 15);
				}
				.JobtronTable-td{
					background-color: darken(#dde9ff, 10);
				}
			}
		}
	}
	.RefStatsReferals-money{
		padding: $cellpadding;
		margin: $bgmargin;
		background-color: #fdd;
		&_paid{
			background-color: #dfd;
		}
	}
	.RefStatsReferals-switch{
		padding: $cellpadding;
		margin: $bgmargin;
	}
}
</style>
