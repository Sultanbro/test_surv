<template>
	<JobtronTable
		:fields="subsCount(sortedSubs[userId]) ? [
			{key: 'switch', label: '', rowspan: 2},
			...subTableFields
		] : subTableFields"
		:items="sortedSubs[userId]"
		:tr-after-class-fn="rowAfterClass"
		:class="(['', 'RefStatsReferals-firstLayer', 'RefStatsReferals-secondLayer', 'RefStatsReferals-thirdLayer'])[layer]"
	>
		<template #thead="{fields}">
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
								class="RefStatsReferals-header pointer"
								@click="$emit('sub-sort', '' + field.key)"
							>
								{{ field.label }}
							</div>
						</th>
					</template>
					<th
						class="JobtronTable-th"
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
								class="RefStatsReferals-header pointer"
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
				class="RefStatsReferals-switch pointer"
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
					'RefStatsReferals-money': value.sum > 0,
					'RefStatsReferals-money_paid': value.sum > 0 && value.paid,
					'pointer usn': $can('referal_edit'),
				}"
				:title="value.comment"
				@click="$emit('payment-click', {item, field})"
			>
				{{ value.sum || '' }}
			</div>
		</template>
		<template #afterRow="secondLayerData">
			<div
				v-if="secondLayerData.value.users.length && uncollapsed.includes(secondLayerData.value.id)"
				class="RefStatsReferals-subtable"
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
	},
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
.RefStatsReferals{
	&-header{
		user-select: none;
	}
	&-title{
		width: 200px;
		min-width: 200px;
		max-width: 250px;

		overflow: hidden;

		white-space: nowrap;
		text-overflow: ellipsis;
	}
	&-referalValue{
		width: 48px;
		min-width: 48px;
	}
}
</style>
