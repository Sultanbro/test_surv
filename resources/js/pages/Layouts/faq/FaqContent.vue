<template>
	<div class="faq-content">
		<p class="faq-content-title">
			{{ active.title }}
		</p>
		<!-- eslint-disable vue/no-v-html -->
		<div
			class="faq-content-body"
			v-html="active.body"
		/>
		<!-- eslint-enable vue/no-v-html -->
	</div>
</template>


<script>
export default {
	name: 'FaqContent',
	props: {
		active: {
			type: Object,
			default: null
		}
	},
	data() {
		return {}
	},
	computed: {}
}
</script>

<style lang="scss">
.faq-content{
	width: 100%;
	height: calc(100vh - 130px);
	padding: 20px;
	overflow: auto;

	&-title{
		font-size: 20px;
		font-weight: 700;
		text-align: center;
		padding-bottom: 20px;
		margin-bottom: 20px;
		border-bottom: 1px solid #ddd;
	}
	&-body{
		font-size: 16px;
		line-height: 1.3;
	}
}
</style>
