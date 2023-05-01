<template>
	<div class="PricingCurrent mt-4">
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Тариф:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ names[current.tariff.kind] }}
				</template>
				<b-skeleton v-else />
			</span>
		</div>
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Оплачен до:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ current.expire_date }}
				</template>
				<b-skeleton v-else />
			</span>
		</div>
		<div class="PricingCurrent-row">
			<span class="PricingCurrent-label">Пользователей:</span>
			<span class="PricingCurrent-value">
				<template v-if="current">
					{{ totalUsers }}
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
	data(){
		return {
			names: {
				free: 'Бесплатный',
				base: 'База',
				standard: 'Стандарт',
				pro: 'PRO',
			},
		}
	},
	computed: {
		...mapState(usePricingStore, ['current', 'items']),
		totalUsers(){
			if(!this.current) return 0
			return (this.current.extra_user_limit || 0) + (this.current.tariff.users_limit || 0)
		}
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
