<template>
	<div class="RefStats">
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
			<template #afterRow="firstLayerData">
				<div
					v-if="firstLayerData.value.users.length"
					class="RefStats-subtable"
				>
					<JobtronTable
						:fields="[
							{key: 'switch', label: ''},
							...subTableFields
						]"
						:items="sortedSubs[firstLayerData.value.id]"
						:tr-after-class-fn="rowAfterClass"
						class="RefStats-firstLayer"
					>
						<template #header="{field}">
							<div
								class="RefStats-header pointer"
								@click="setSubSort(field.key)"
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
						<template #afterRow="secondLayerData">
							<div
								v-if="secondLayerData.value.users.length"
								class="RefStats-subtable"
							>
								<JobtronTable
									:fields="[
										{key: 'switch', label: ''},
										...subTableFields
									]"
									:items="sortedSubs[secondLayerData.value.id]"
									:tr-after-class-fn="rowAfterClass"
									class="RefStats-secondLayer"
								>
									<template #header="{field}">
										<div
											class="RefStats-header pointer"
											@click="setSubSort(field.key)"
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
									<template #afterRow="thirdLayerData">
										<div
											v-if="thirdLayerData.value.users.length"
											class="RefStats-subtable"
										>
											<JobtronTable
												:fields="subTableFields"
												:items="sortedSubs[thirdLayerData.value.id]"
												:tr-after-class-fn="rowAfterClass"
												class="RefStats-thirdLayer"
											>
												<template #header="{field}">
													<div
														class="RefStats-header pointer"
														@click="setSubSort(field.key)"
													>
														{{ field.label }}
													</div>
												</template>
											</JobtronTable>
										</div>
									</template>
								</JobtronTable>
							</div>
						</template>
					</JobtronTable>
				</div>
			</template>
		</JobtronTable>
	</div>
</template>

<script>
import JobtronTable from '@ui/Table.vue'
import {
	tableFields,
	subTableFields,
	getFakeReferer,
} from './helper'

// const fakeData = {
// 	userPrice: 100,
// 	cvResultDealPercent: 85,
// 	cvDealUserPercent: 69,
// 	totalMonth: 1000,
// 	totalPayd: 800,
// }

export default {
	name: 'RefStats',
	components: {
		JobtronTable,
	},
	data(){
		return {
			users: [],
			tableFields,
			subTableFields,
			uncollapsed: [],
			sortCol: 'accepted',
			sortOrder: 'desc',
			sortSubCol: 'title',
			sortSubOrder: 'desc',
			sortFn: {
				str: (a, b) => a[this.sortCol].localeCompare(b[this.sortCol]),
				int: (a, b) => (parseInt(a[this.sortCol]) || 0) - (parseInt(b[this.sortCol]) || 0),
				float: (a, b) => (parseFloat(a[this.sortCol]) || 0) - (parseFloat(b[this.sortCol]) || 0),
			},
			sortSubFn: {
				str: (a, b) => a[this.sortSubCol].localeCompare(b[this.sortSubCol]),
				int: (a, b) => (parseInt(a[this.sortSubCol]) || 0) - (parseInt(b[this.sortSubCol]) || 0),
				float: (a, b) => (parseFloat(a[this.sortSubCol]) || 0) - (parseFloat(b[this.sortSubCol]) || 0),
			},
		}
	},
	computed: {
		sorted(){
			return this.users.slice().sort((a, b) => {
				if(['title', 'status'].includes(this.sortCol)){
					return this.sortOrder === 'asc' ? this.sortFn.str(a, b) : this.sortFn.str(b, a)
				}
				if(['leads', 'deals', 'accepted', 'total', 'month', 'monthRef', 'monthPayd'].includes(this.sortCol)){
					return this.sortOrder === 'asc' ? this.sortFn.int(a, b) : this.sortFn.int(b, a)
				}
				return this.sortOrder === 'asc' ? this.sortFn.float(a, b) : this.sortFn.float(b, a)
			})
		},
		sortedSubs(){
			const sorted = {}
			this.users.forEach(user => {
				sorted[user.id] = user.users.slice().sort((a, b) => {
					if(['title', 'status'].includes(this.sortSubCol)){
						return this.sortSubOrder === 'asc' ? this.sortSubFn.str(a, b) : this.sortSubFn.str(b, a)
					}
					return this.sortSubOrder === 'asc' ? this.sortSubFn.int(a, b) : this.sortSubFn.int(b, a)
				})
				user.users.forEach(user2 => {
					sorted[user2.id] = user2.users.slice().sort((a, b) => {
						if(['title', 'status'].includes(this.sortSubCol)){
							return this.sortSubOrder === 'asc' ? this.sortSubFn.str(a, b) : this.sortSubFn.str(b, a)
						}
						return this.sortSubOrder === 'asc' ? this.sortSubFn.int(a, b) : this.sortSubFn.int(b, a)
					})
					user2.users.forEach(user3 => {
						sorted[user3.id] = user3.users.slice().sort((a, b) => {
							if(['title', 'status'].includes(this.sortSubCol)){
								return this.sortSubOrder === 'asc' ? this.sortSubFn.str(a, b) : this.sortSubFn.str(b, a)
							}
							return this.sortSubOrder === 'asc' ? this.sortSubFn.int(a, b) : this.sortSubFn.int(b, a)
						})
					})
				})
			})
			return sorted
		}
	},
	mounted(){
		this.fetchData()
	},
	methods: {
		async fetchData(){
			const loader = this.$loading.show()
			this.users = [
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
				getFakeReferer(),
			]
			loader.hide()
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
	},
}
</script>

<style lang="scss">
.RefStats{
	overflow-x: auto;
	font-size: 14px;
	&-table{
		width: auto;
	}
	&-referalValue{
		width: 48px;
		min-width: 48px;
	}
	&-header{
		user-select: none;
	}
	.JobtronTable-th,
	.JobtronTable-td{
		padding: 8px 10px;
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
		margin: -8px -10px;
		padding-left: 15px;
	}
	&-firstLayer{
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
	&-secondLayer{
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
	&-thirdLayer{
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
