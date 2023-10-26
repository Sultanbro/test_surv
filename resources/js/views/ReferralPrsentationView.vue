<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import VuePdfEmbed from 'vue-pdf-embed/dist/vue2-pdf-embed'

export default {
	name: 'ReferralPrsentationView',
	components: {
		DefaultLayout,
		VuePdfEmbed,
	},
	data(){
		return {}
	},
	mounted(){}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<vue-pdf-embed
				source="/storage/ilgiz-valinurov-recruting-na-100.pdf"
			/>
		</div>
	</DefaultLayout>
</template>
