<template functional>
	<p class="AuthSubTitle">
		<slot />
	</p>
</template>

<script>
export default {
	name: 'AuthSubTitle',
	props: {},
}
</script>

<style lang="scss">
.AuthSubTitle{
	margin: 0 0 12px;
	font-size: 16px;
	font-weight: 400;
	line-height: 24px;
	text-align: left;
}
</style>
