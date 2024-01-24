<template>
	<div class="TopProfit">
		<ProfitTab
			:year="+year"
			:month="+month"
		/>
	</div>
</template>

<script>
import ProfitTab from '@/components/pages/Top/ProfitTab'

import { mapState } from 'pinia'
import { useTopStore } from '@/stores/Top.js'

export default {
	name: 'TopProfit',
	components: {
		ProfitTab,
	},
	props: {},
	data(){
		return {}
	},
	computed: {
		...mapState(useTopStore, ['year', 'month']),
	},
	watch: {},
	created(){},
	mounted(){},
	methods: {},
}
</script>

<style lang="scss">
//.TopProfit{}
</style>
