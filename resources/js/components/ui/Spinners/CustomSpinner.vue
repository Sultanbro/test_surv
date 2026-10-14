<template>
	<div class="loader" />
</template>

<script>
export default {
	name: 'CustomPriceSpinner'
}
</script>

<style scoped>
.loader {
	width: 25px;
	height: 25px;
	margin: 0;
	border-radius: 50%;
	border: 4px solid;
	border-color: #E4E4ED;
	border-right-color: #766DF4;
	animation: s2-data-v-d26aee9a 1s infinite linear;
}

@keyframes s2 {to{transform: rotate(1turn)}}
</style>
