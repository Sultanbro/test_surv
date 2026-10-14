<template>
	<div class="TopNPS">
		<NPS
			:activeuserid="+$laravel.userId"
			:show_header="false"
		/>
	</div>
</template>

<script>
import NPS from '@/components/tables/NPS' // Оценка руководителей
export default {
	name: 'TopNPS',
	components: {
		NPS,
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
//.TopNPS{}
</style>
