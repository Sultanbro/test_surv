<template>
	<div class="RefStatsTable">
		<JobtronTable
			:fields="fields"
			:items="sorted"
			:tr-after-class-fn="rowAfterClass"
			class="RefStatsTable-table"
		>
			<template #header="{field}">
				<div
					class="RefStatsTable-header pointer"
					@click="setSort(field.key)"
				>
					{{ field.label }}
					<img
						v-if="field.hint"
						v-b-popover.hover="field.hint"
						src="/images/dist/profit-info.svg"
						class="img-info"
						alt="info icon"
					>
				</div>
			</template>
			<template #cell(switch)="{item}">
				<div
					v-if="item.users.length"
					class="RefStatsTable-switch pointer"
					@click="toggleAfter(item.id)"
				>
					<i
						v-if="uncollapsed.includes(item.id)"
						class="fa fa-minus-circle"
					/>
					<i
						v-else
						class="fa fa-plus-circle"
					/>
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
					class="RefStatsTable-money"
					:class="{
						'RefStatsTable-money_paid': value >= (item.month + item.monthRef)
					}"
				>
					{{ value }}
				</div>
			</template>
			<template #afterRow="firstLayerData">
				<transition name="RefStatsTable-scale">
					<div
						v-if="firstLayerData && firstLayerData.value.users.length && (uncollapsed.includes(firstLayerData.value.id) || single)"
						:key="firstLayerData.value.id"
						class="RefStatsTable-subtable"
						data-max="10"
					>
						<RefStatsReferals
							:user-id="firstLayerData.value.id"
							:sorted-subs="sortedSubs"
							:hint-comments="hintComments"
							@sub-sort="setSubSort"
							@payment-click="$emit('payment-click', $event)"
						/>
					</div>
				</transition>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import {
	tableFields,
} from './helper'

import JobtronTable from '@ui/Table.vue'
import RefStatsReferals from './RefStatsReferals.vue'

export default {
	name: 'RefStatsTable',
	components: {
		JobtronTable,
		RefStatsReferals,
	},
	props: {
		items:  {
			type: Array,
			default: () => [],
		},
		fields: {
			type: Array,
			default: () => tableFields,
		},
		single: {
			type: Boolean
		},
		hintComments: {
			type: Boolean
		},
	},
	data(){
		return {
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
		}
	},
	computed: {
		sorted(){
			return this.items.slice().sort((a, b) => {
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
			this.items.forEach(user => {
				sorted[user.id] = user.users.slice().sort((a, b) => {
					const aVal = a[this.sortSubCol]
					const bVal = b[this.sortSubCol]
					if(['title', 'status'].includes(this.sortSubCol)){
						return this.sortSubOrder === 'asc' ? this.sortFn.str(aVal || '', bVal || '') : this.sortFn.str(bVal || '', aVal || '')
					}
					return this.sortSubOrder === 'asc' ? this.sortFn.int(aVal?.sum || 0, bVal?.sum || 0) : this.sortFn.int(bVal?.sum || 0, aVal?.sum || 0)
				})
				user.users.forEach(user2 => {
					sorted[user2.id] = user2.users.slice().sort((a, b) => {
						const aVal = a[this.sortSubCol]
						const bVal = b[this.sortSubCol]
						if(['title', 'status'].includes(this.sortSubCol)){
							return this.sortSubOrder === 'asc' ? this.sortFn.str(aVal || '', bVal || '') : this.sortFn.str(bVal || '', aVal || '')
						}
						return this.sortSubOrder === 'asc' ? this.sortFn.int(aVal?.sum || 0, bVal?.sum || 0) : this.sortFn.int(bVal?.sum || 0, aVal?.sum || 0)
					})
					user2.users.forEach(user3 => {
						sorted[user3.id] = user3.users.slice().sort((a, b) => {
							const aVal = a[this.sortSubCol]
							const bVal = b[this.sortSubCol]
							if(['title', 'status'].includes(this.sortSubCol)){
								return this.sortSubOrder === 'asc' ? this.sortFn.str(aVal || '', bVal || '') : this.sortFn.str(bVal || '', aVal || '')
							}
							return this.sortSubOrder === 'asc' ? this.sortFn.int(aVal?.sum || 0, bVal?.sum || 0) : this.sortFn.int(bVal?.sum || 0, aVal?.sum || 0)
						})
					})
				})
			})
			return sorted
		},
	},
	methods: {
		toggleAfter(id){
			const index = this.uncollapsed.findIndex(uId => uId === id)
			if(~index){
				this.uncollapsed.splice(index, 1)
			}
			else{
				this.uncollapsed = []
				this.uncollapsed.push(id)
			}
		},
		rowAfterClass(row){
			return this.uncollapsed.includes(row.id) || this.single ? 'RefStats-afterRowActive' : ''
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
	},
}
</script>

<style lang="scss">
.RefStatsTable{
	$cellpadding: 8px 10px;
	$bgmargin: -8px -10px;

	&-table{
		width: 100px;
		table-layout: fixed;
	}
	&-header{
		user-select: none;
	}
	.JobtronTable-th,
	.JobtronTable-td{
		padding: $cellpadding;
		font-size: 12px;
		line-height: 1.1;
		opacity: 1;
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
		// display: none;
		// opacity: 0;
		transition: visibility 0.5s;
		visibility: hidden;
		> .JobtronTable-td{
			padding: 0;
			border-width: 0;
		}
		&.RefStats-afterRowActive{
			// display: table-row;
			// opacity: 1;
			visibility: visible;
			> .JobtronTable-td{
				padding: 8px 10px;
				border-width: 1px;
			}
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
		overflow-x: auto;
		position: relative;
	}

	&-title{
		width: 250px;
		min-width: 250px;
		max-width: 250px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-status{
		width: 100px;
		min-width: 100px;
		max-width: 100px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-profileLeads{
		width: 130px;
		.img-info{
			vertical-align: middle;
		}
	}
	&-leads{
		width: 80px;
	}
	&-profileDeals{
		width: 130px;
		.img-info{
			vertical-align: middle;
		}
	}
	&-deals{
		width: 80px;
	}
	&-deals{
		width: 80px;
	}
	&-profileCV1{
		width: 130px;
	}
	&-cv1{
		width: 90px;
	}
	&-accepted{
		width: 80px;
	}
	&-deals{
		width: 80px;
	}
	&-cv2{
		width: 112px;
	}
	&-deals{
		width: 80px;
	}
	&-total{
		width: 100px;
	}
	&-month{
		width: 82px;
	}
	&-monthRef{
		width: 90px;
	}
	&-paid{
		width: 100px;
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
		// padding: $cellpadding;
		margin: $bgmargin;
		font-size: 16px;
	}
	&-scale{
		&-enter-active,
		&-leave-active{
			transition: all .5s;
			transform-origin: top center;
		}
		&-enter,
		&-leave-to,
		&-leave-active{
			transform: scale(1, 0);
			opacity: 0;
		}
	}
}
</style>
