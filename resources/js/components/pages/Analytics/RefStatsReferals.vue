<template>
	<div class="RefStatsReferals">
		<RefStatsReferalsTable
			:user-id="userId"
			:sorted-subs="sortedSubs"
			:layer="1"
			@sub-sort="$emit('sub-sort', $event)"
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
	},
	data(){
		return {}
	},
	computed: {},
	methods: {},
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
