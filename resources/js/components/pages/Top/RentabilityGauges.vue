<template>
	<div class="RentabilityGauges">
		<RentabilityGauge
			v-for="item, index in items"
			:key="index"
			:item="item"
			:activities="activities"
			@save="$emit('save', $event)"
		/>
	</div>
</template>

<script>
import RentabilityGauge from './RentabilityGauge.vue'
export default {
	name: 'RentabilityGauges',
	components: {
		RentabilityGauge,
	},
	props: {
		items: {
			type: Array,
			default: () => [],
		},
		activities: {
			type: Array,
			default: () => [],
		},
	},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	methods: {},
}
</script>

<style lang="scss">
.RentabilityGauges{
	display: flex;
	flex-flow: row wrap;
	gap: 10px;
}
</style>
