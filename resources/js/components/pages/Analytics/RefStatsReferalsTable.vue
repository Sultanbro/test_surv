<template>
	<JobtronTable
		:fields="subsCount(sortedSubs[userId]) && layer < 3 ? [
			{key: 'switch', label: '', rowspan: 2, thClass: 'RefStatsReferalsTable-switch', tdClass: 'RefStatsReferalsTable-switch'},
			...subFields
		] : subFields"
		:items="sortedSubs[userId]"
		:tr-after-class-fn="rowAfterClass"
		:tr-after-colspan="rowAfterColspan"
		class="RefStatsReferalsTable"
		:class="(['', 'RefStatsReferalsTable_firstLayer', 'RefStatsReferalsTable_secondLayer', 'RefStatsReferalsTable_thirdLayer'])[layer]"
	>
		<template
			v-if="layer < 2"
			#thead="{fields}"
		>
			<thead class="JobtronTable-head">
				<tr class="JobtronTable-row">
					<template v-for="field in fields">
						<th
							v-if="field.key.slice(-4) !== 'Week'"
							:key="field.key"
							class="JobtronTable-th"
							:class="field.thClass"
							:colspan="field.colspan"
							:rowspan="field.rowspan"
						>
							<div
								class="RefStatsReferalsTable-header pointer"
								@click="$emit('sub-sort', '' + field.key)"
							>
								{{ field.label }}
							</div>
						</th>
					</template>
					<th
						class="JobtronTable-th RefStatsReferalsTable-weeks"
						colspan="7"
					>
						Отработал недель
					</th>
				</tr>
				<tr class="JobtronTable-row">
					<template v-for="field in fields">
						<th
							v-if="field.key.slice(-4) === 'Week'"
							:key="field.key"
							class="JobtronTable-th"
							:class="field.thClass"
							:colspan="field.colspan"
							:rowspan="field.rowspan"
						>
							<div
								class="RefStatsReferalsTable-header pointer"
								@click="$emit('sub-sort', '' + field.key)"
							>
								{{ field.label }}
							</div>
						</th>
					</template>
				</tr>
			</thead>
		</template>
		<template #cell(switch)="{item}">
			<div
				v-if="item.users.length"
				class="RefStatsReferalsTable-switchCell pointer"
				@click="toggleAfter(item.id)"
			>
				{{ uncollapsed.includes(item.id) ? '-' : '+' }}
			</div>
		</template>
		<template #cell(title)="{value}">
			{{ value }}
		</template>
		<template #cell(status)="{value}">
			{{ value }}
		</template>
		<template #cell="{value, item, field}">
			<div
				v-if="value"
				:class="{
					'RefStatsReferalsTable-money': value.sum > 0,
					'RefStatsReferalsTable-money_paid': value.sum > 0 && value.paid,
					'pointer usn': $can('referal_edit'),
				}"
				:title="value.comment"
				@click="$emit('payment-click', {item, field})"
			>
				{{ value.sum || '' }}
			</div>
		</template>
		<template
			v-if="layer < 3"
			#afterRow="secondLayerData"
		>
			<div
				v-if="secondLayerData.value.users.length && uncollapsed.includes(secondLayerData.value.id)"
				class="RefStatsReferalsTable-subtable"
			>
				<RefStatsReferalsTable
					:user-id="secondLayerData.value.id"
					:sorted-subs="sortedSubs"
					:layer="layer + 1"
					@sub-sort="$emit('sub-sort', $event)"
					@payment-click="$emit('payment-click', $event)"
				/>
			</div>
		</template>
	</JobtronTable>
</template>

<script>
import JobtronTable from '@ui/Table.vue'
import {
	subTableFields,
	secondLayersFields,
} from './helper'

export default {
	name: 'RefStatsReferalsTable',
	components: {
		JobtronTable,
	},
	props: {
		userId: {
			type: Number,
			required: true,
		},
		sortedSubs: {
			type: Object,
			default: () => ({}),
		},
		layer: {
			type: Number,
			default: 1,
		},
	},
	data(){
		return {
			uncollapsed: [],
		}
	},
	computed: {
		maxWorkedDays(){
			let max = 0
			this.sortedSubs[this.userId].forEach(sub => {
				for(let i = 1; i < 16; ++i){
					if(sub[i]) max = Math.max(max, i)
				}
			})
			return max
		},
		subTableFields(){
			return subTableFields.map(field => {
				if(!field.days) return field
				if(parseInt(field.key) <= this.maxWorkedDays) return field
				return {
					...JSON.parse(JSON.stringify(field)),
					thClass: 'hidden',
					tdClass: 'hidden',
				}
			})
		},
		rowAfterColspan(){
			return this.layer > 1 ? 4 : 11 + this.maxWorkedDays
		},
		subFields(){
			return this.layer > 1 ? secondLayersFields : this.subTableFields
		},
	},
	mounted(){},
	methods: {
		subsCount(user){
			return user.reduce((result, user) => result + user.users.length, 0)
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
	},
}
</script>

<style lang="scss">
.RefStatsReferalsTable{
	$cellpadding: 8px 10px;
	$bgmargin: -8px -10px;

	table-layout: fixed;

	&-subtable{
		padding-left: 8px;
		margin: $bgmargin;
		position: relative;
	}

	&-header{
		user-select: none;
	}
	&-switch{
		width: 48px;

		position: sticky;
		z-index: 1;
		left: 0;

		~ .RefStatsReferalsTable{
			&-title{
				width: 250px;
				min-width: 250px;
				max-width: 250px;

				left: 48px;
			}
		}
	}
	&-title{
		width: 298px;
		min-width: 298px;
		max-width: 298px;

		position: sticky;
		z-index: 1;
		left: 0;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-status{
		width: 100px;
		min-width: 100px;
		max-width: 100px;

		overflow: hidden;

		position: sticky;
		z-index: 1;
		left: 298px;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-referalValue{
		width: 48px;
		min-width: 48px;
	}
	&-attest{
		min-width: 100px;
		// width: 100px;
	}
	&-weeks{
		width: 420px;
	}
	&-week{
		width: 60px;
	}
	&-week1{
		min-width: 120px;
	}
	&-money{
		padding: $cellpadding;
		margin: $bgmargin;
		background-color: #fdd;
		&_paid{
			background-color: #dfd;
		}
	}
	&-switchCell{
		padding: $cellpadding;
		margin: $bgmargin;
	}

	&_firstLayer{
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
	&_secondLayer{
		.RefStatsReferalsTable{
			&-switch{
				width: 40px;
				left: 8px;
				~ .RefStatsReferalsTable{
					&-title{
						width: 250px;
						min-width: 250px;
						max-width: 250px;
						left: 48px;
					}
				}
			}
			&-title{
				width: 290px;
				min-width: 290px;
				max-width: 290px;
				left: 8px;
			}
		}
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
	&_thirdLayer{
		.RefStatsReferalsTable{
			&-switch{
				width: 32px;
				left: 16px;
				~ .RefStatsReferalsTable{
					&-title{
						width: 250px;
						min-width: 250px;
						max-width: 250px;
						left: 48px;
					}
				}
			}
			&-title{
				width: 282px;
				min-width: 282px;
				max-width: 282px;
				left: 16px;
			}
		}
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
}
</style>
