<template>
	<div
		id="RefStat"
		class="RefStat index block _anim _anim-no-hide"
		:class="{
			'v-loading': loading,
		}"
	>
		<div class="title index__title mt-5">
			Реферальная программа «Business Family»
			<b-badge variant="warning">
				Demo
			</b-badge>
		</div>

		<!-- <div class="subtitle index__subtitle">
			Сравните Ваши показатели с другими сотрудниками
		</div> -->

		<div
			v-if="refUser"
			class="RefStat-table index__table"
		>
			<div class="ovx">
				<JobtronTable
					:fields="tableFields"
					:items="users"
					:tr-after-class-fn="rowAfterClass"
				>
					<template #header="{field}">
						<div
							class="RefStat-header pointer"
							@click="setSort(field.key)"
						>
							{{ field.label }}
						</div>
					</template>
					<template #cell(switch)="{item}">
						<div
							v-if="item.users.length"
							class="RefStat-switch pointer"
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
							class="RefStat-money"
							:class="{
								'RefStat-money_paid': value >= (item.month + item.monthRef)
							}"
						>
							{{ value }}
						</div>
					</template>
					<template #afterRow="firstLayerData">
						<div
							v-if="firstLayerData.value.users.length && uncollapsed.includes(firstLayerData.value.id)"
							class="RefStat-subtable"
						>
							<RefStatsReferals
								:user-id="firstLayerData.value.id"
								:sorted-subs="sortedSubs"
								@sub-sort="setSubSort"
							/>
						</div>
					</template>
				</JobtronTable>
			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import JobtronTable from '@ui/Table.vue'
import RefStatsReferals from '@/components/pages/Analytics/RefStatsReferals.vue'
import {
	tableFields,
	getFakeReferer,
} from '@/components/pages/Analytics/helper'

export default {
	name: 'RefStat',
	components: {
		JobtronTable,
		RefStatsReferals,
	},
	data(){
		return {
			loading: true,

			refUser: null,
			users: [],
			tableFields,
			uncollapsed: [],

			sortSubCol: 'title',
			sortSubOrder: 'desc',
			sortFn: {
				str: (a, b) => a.localeCompare(b),
				int: (a, b) => (parseInt(a) || 0) - (parseInt(b) || 0),
				float: (a, b) => (parseFloat(a) || 0) - (parseFloat(b) || 0),
			},
		}
	},
	computed: {
		...mapGetters(['user']),
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
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true
			this.refUser = {
				...getFakeReferer(),
				title: `${this.user.name} ${this.user.last_name}`
			}
			this.users = [
				this.refUser
			]
			this.uncollapsed.push(this.refUser.id)
			this.loading = false
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
		setSubSort(key){
			if(key === 'switch') return
			if(key === this.sortSubCol) {
				this.sortSubOrder = this.sortSubOrder === 'asc' ? 'desc' : 'asc'
				return
			}
			this.sortSubOrder = key === 'title' ? 'asc' : 'desc'
			this.sortSubCol = key
		},
	},
}
</script>

<style lang="scss">
.RefStat{
	$cellpadding: 8px 10px;
	$bgmargin: -8px -10px;

	font-size: 14px;

	&-table{
		// overflow-x: auto;
		> .JobtronTable{
			width: auto;
		}
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
