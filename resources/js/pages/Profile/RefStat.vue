<template>
	<div
		v-if="users.length"
		id="RefStat"
		class="RefStat index block _anim _anim-no-hide"
		:class="{
			'v-loading': !isReady,
		}"
	>
		<div class="title index__title mt-6 mb-4">
			Реферальная программа «Business&nbsp;Friends»
		</div>

		<div
			v-if="refUser"
			class="RefStat-table index__table"
		>
			<div class="RefStat-diff">
				<div class="RefStat-self">
					<div class="RefStat-subtitle">
						Вы
					</div>
					<div class="RefStat-users">
						<div class="RefStat-user">
							<JobtronAvatar
								:image="`users_img/${user.img_url}`"
								:title="`${user.name} ${user.last_name}`"
							/>
							<div class="RefStat-userName">
								{{ user.name }}
							</div>
							<div class="RefStat-userLeads">
								Принято: {{ accepted }}
							</div>
						</div>
					</div>
				</div>
				<div class="RefStat-tops">
					<div class="RefStat-subtitle">
						Топчики
					</div>
					<div class="RefStat-users">
						<div
							v-for="topUser in tops"
							:key="topUser.id"
							class="RefStat-user"
						>
							<JobtronAvatar
								:image="topUser.avatar"
								:title="`${topUser.name} ${topUser.lastName}`"
							/>
							<div class="RefStat-userName">
								{{ topUser.name }}
							</div>
							<div class="RefStat-userLeads">
								{{ topUser.accepted }}
							</div>
						</div>
					</div>
				</div>
			</div>
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
import { mapState } from 'pinia'
import { useReferralStore } from '@/stores/Referral'
import {
	tableFields,
} from '@/components/pages/Analytics/helper'

import JobtronTable from '@ui/Table.vue'
import RefStatsReferals from '@/components/pages/Analytics/RefStatsReferals.vue'
import JobtronAvatar from '@ui/Avatar.vue'

export default {
	name: 'RefStat',
	components: {
		JobtronTable,
		RefStatsReferals,
		JobtronAvatar,
	},
	data(){
		return {
			tableFields,
			// uncollapsed: [],

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
		...mapState(useReferralStore, [
			'users',
			'tops',
			'accepted',
			'isReady',
		]),
		refUser(){
			/* global Laravel */
			if(!this.users) return null
			return this.users.find(user =>  user.id === Laravel.userId)
		},
		sortedSubs(){
			const sorted = {}
			this.users.forEach(user => {
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
		uncollapsed(){
			return this.users.map(user => user.id)
		},
	},
	mounted(){},
	methods: {
		toggleAfter(/* id */){
			// const index = this.uncollapsed.findIndex(uId => uId === id)
			// if(~index){
			// 	this.uncollapsed.splice(index, 1)
			// }
			// else{
			// 	this.uncollapsed.push(id)
			// }
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
		overflow-x: auto;
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
		.RefStat{
			&-subtable{
				padding-left: 15px;
			}
		}
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
	&-diff{
		display: flex;
		flex-flow: row nowrap;
		align-items: flex-start;
		gap: 40px;
		margin-bottom: 20px;
	}
	// &-self,
	// &-tops{}
	&-tops{
		flex: 1;
	}
	&-subtitle{
		margin-bottom: 20px;
		font-size: 20px;
		font-weight: 700;
		text-align: center;
	}
	&-users{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 20px;
	}
	&-user{
		display: flex;
		flex-flow: column nowrap;
		align-items: center;
		gap: 10px;
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

	.JobtronTable-head .JobtronTable-row:first-child .JobtronTable-th:first-child::before {
		background-color: #F8F9FD;
	}
}
.RefStats{
	&-title{
		width: 200px;
		min-width: 200px;
		max-width: 250px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
}
</style>
