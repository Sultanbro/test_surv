<template>
	<div id="app">
		<RouterView class="app-content" />
	</div>
</template>

<script>

export default {
	name: 'App',
}
</script>
<style lang="scss">
@import 'assets/scss/app.variables.scss';
@import 'assets/scss/app.global.scss';
.app-content{
	width: 100%;
}
</style>
