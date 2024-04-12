<template functional>
	<h1 class="AuthTitle">
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
	text-align: left;
}
</style>
