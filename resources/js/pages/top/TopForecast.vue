<template>
	<b-row class="TopForecast m-0">
		<b-col
			cols="12"
			md="8"
			class="p-0 mt-4"
		>
			<TopPredicts />
		</b-col>
	</b-row>
</template>

<script>
import TopPredicts from '@/components/pages/Top/TopPredicts'

export default {
	name: 'TopForecast',
	components: {
		TopPredicts,
	},
	props: {},
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
//.TopForecast{}
</style>
