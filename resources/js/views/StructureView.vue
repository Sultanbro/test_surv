<script>
import DefaultLayout from '@/layouts/DefaultLayout';
const StructurePage = () => import(/* webpackChunkName: "StructurePage" */ '@/pages/Structure/StructurePage.vue');

export default {
	name: 'StructureView',
	components: {
		DefaultLayout,
		StructurePage
	},
}
</script>

<template>
	<DefaultLayout class="no-padding">
		<div class="old__content">
			<StructurePage />
		</div>
	</DefaultLayout>
</template>
