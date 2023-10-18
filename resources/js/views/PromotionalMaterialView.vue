<script>
import DefaultLayout from '@/layouts/DefaultLayout'
const PromotionalMaterial = () => import(/* webpackChunkName: "PromotionalMaterial" */ '@/pages/PromotionalMaterial.vue')

export default {
	name: 'PromotionalMaterialView',
	components: {
		DefaultLayout,
		PromotionalMaterial,
	},
	data(){
		return {
			page: '',
		}
	},
	mounted(){}
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<PromotionalMaterial />
		</div>
	</DefaultLayout>
</template>
