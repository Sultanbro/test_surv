<template functional>
	<p
		v-bind="data.attrs"
		:class="['AuthSubTitle', data.staticClass]"
	>
		<slot />
	</p>
</template>

<script>
export default {
	name: 'AuthSubTitle',
	props: {},
};
</script>

<style lang="scss">
.AuthSubTitle {
	// margin: 0 0 12px;
  margin-right: 35px;
	font-size: 14px;
	font-weight: 400;
	line-height: 24px;

	a {
		text-decoration: none;
		color: #0c50ff;
		&:hover {
			color: #4a7cfd;
		}
	}
}
</style>
