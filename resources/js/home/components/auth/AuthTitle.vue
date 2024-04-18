<template functional>
	<h1
		v-bind="data.attrs"
		:class="['AuthTitle', data.staticClass]"
	>
		<slot />
	</h1>
</template>

<script>
export default {
	name: 'AuthTitle',
	props: {},
}
</script>

<style lang="scss">
.AuthTitle{
	margin: 0 0 8px;
	font-size: 28px;
	font-weight: 600;
	line-height: 36px;
}
</style>
