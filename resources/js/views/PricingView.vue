<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import PricingPage from '@/pages/PricingPage'

export default {
	name: 'PricingView',
	components: {
		DefaultLayout,
		PricingPage,
	},
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<PricingPage />
		</div>
	</DefaultLayout>
</template>
