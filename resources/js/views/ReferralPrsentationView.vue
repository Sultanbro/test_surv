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
				source="/static/BusinessFriends.pdf"
			/>
		</div>
	</DefaultLayout>
</template>
