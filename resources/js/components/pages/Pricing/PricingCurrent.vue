<template>
	<div class="PricingCurrent mt-4">
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Тариф:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ current.name }}
				</template>
				<b-skeleton v-else />
			</span>
		</div>
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Оплачен до:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ current.paid_up_to }}
				</template>
				<b-skeleton v-else />
			</span>
		</div>
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Пльзователей:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ current.users }}
				</template>
				<b-skeleton v-else />
			</span>
		</div>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'

export default {
	name: 'PricingCurrent',
	components: {},
	computed: {
		...mapState(usePricingStore, ['current']),
	},
}
</script>

<style lang="scss">
.PricingCurrent{
	&-label{
		opacity: 0.5;
	}
}
</style>
