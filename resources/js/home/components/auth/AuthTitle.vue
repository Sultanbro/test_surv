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
};
</script>

<style lang="scss">
.AuthTitle {
	margin: 0 0 8px;
	font-size: 26px;
	font-weight: 600;
	line-height: 36px;
}

@media (max-width: 1200px) {
	.AuthTitle {
		font-weight: 600;
		font-size: 20px;
		line-height: 26px;
	}
}
</style>
