<template>
	<div class="RefStatsReferals">
		<JobtronTable
			:fields="[
				{key: 'switch', label: ''},
				...subTableFields
			]"
			:items="sortedSubs[userId]"
			:tr-after-class-fn="rowAfterClass"
			class="RefStatsReferals-firstLayer"
		>
			<template #header="{field}">
				<div
					class="RefStatsReferals-header pointer"
					@click="$emit('sub-sort', '' + field.key)"
				>
					{{ field.label }}
				</div>
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
					:class="{
						'RefStatsReferals-money': value.value > 0,
						'RefStatsReferals-money_paid': value.value > 0 && value.paid,
						'pointer usn': $can('referal_edit'),
					}"
					:title="value.comment"
					@click="$emit('payment-click', {item, field})"
				>
					{{ value.value || '' }}
				</div>
			</template>
			<template #afterRow="secondLayerData">
				<div
					v-if="secondLayerData.value.users.length && uncollapsed.includes(secondLayerData.value.id)"
					class="RefStatsReferals-subtable"
				>
					<JobtronTable
						:fields="[
							{key: 'switch', label: ''},
							...subTableFields
						]"
						:items="sortedSubs[secondLayerData.value.id]"
						:tr-after-class-fn="rowAfterClass"
						class="RefStatsReferals-secondLayer"
					>
						<template #header="{field}">
							<div
								class="RefStatsReferals-header pointer"
								@click="$emit('sub-sort', '' + field.key)"
							>
								{{ field.label }}
							</div>
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
								:class="{
									'RefStatsReferals-money': value.value > 0,
									'RefStatsReferals-money_paid': value.value > 0 && value.paid,
									'pointer usn': $can('referal_edit'),
								}"
								:title="value.comment"
								@click="$emit('payment-click', {item, field})"
							>
								{{ value.value || '' }}
							</div>
						</template>
						<template #afterRow="thirdLayerData">
							<div
								v-if="thirdLayerData.value.users.length && uncollapsed.includes(thirdLayerData.value.id)"
								class="RefStatsReferals-subtable"
							>
								<JobtronTable
									:fields="subTableFields"
									:items="sortedSubs[thirdLayerData.value.id]"
									:tr-after-class-fn="rowAfterClass"
									class="RefStatsReferals-thirdLayer"
								>
									<template #header="{field}">
										<div
											class="RefStatsReferals-header pointer"
											@click="$emit('sub-sort', '' + field.key)"
										>
											{{ field.label }}
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
											:class="{
												'RefStatsReferals-money': value.value > 0,
												'RefStatsReferals-money_paid': value.value > 0 && value.paid,
												'pointer usn': $can('referal_edit'),
											}"
											:title="value.comment"
											@click="$emit('payment-click', {item, field})"
										>
											{{ value.value || '' }}
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
	subTableFields,
} from './helper'

export default {
	name: 'RefStatsReferals',
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
	},
	data(){
		return {
			subTableFields,
			uncollapsed: [],
		}
	},
	computed: {},
	methods: {
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
