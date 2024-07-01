<template functional>
	<p class="AuthNote">
		<slot />
	</p>
</template>

<script>
export default {
	name: 'AuthNote',
	components: {},
	props: {},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.AuthNote{
	color: #737B8A;
	font-size: 12px;
	font-weight: 400;
	line-height: 16px;
	letter-spacing: 0.4px;
	text-align: left;
	a{
		color: #737B8A;
		text-decoration: underline;
	}
}
</style>
