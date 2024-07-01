<template>
	<div class="RefStatsReferals">
		<RefStatsReferalsTable
			:user-id="userId"
			:sorted-subs="sortedSubs"
			:layer="1"
			:hint-comments="hintComments"
			:show-goups="showGoups"
			@sub-sort="$emit('sub-sort', $event)"
			@payment-click="$emit('payment-click', $event)"
		/>
	</div>
</template>

<script>
import RefStatsReferalsTable from './RefStatsReferalsTable.vue'

export default {
	name: 'RefStatsReferals',
	components: {
		RefStatsReferalsTable,
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
		hintComments: {
			type: Boolean
		},
		showGoups: {
			type: Boolean
		},
	},
	data(){
		return {}
	},
	computed: {},
	methods: {},
}
</script>

<style lang="scss">
// .RefStatsReferals{}
</style>
