<template>
	<div class="custom-loader" />
</template>

<style scoped>
.custom-loader {
	width: 25px;
	height: 25px;
	border-radius: 50%;
	background: conic-gradient(#0000 10%, #f4f4f4);
	-webkit-mask: radial-gradient(farthest-side, #0000 calc(100% - 4px), #000 0);
	animation: s3 1s infinite linear;
}
@keyframes s3 {
	to {
		transform: rotate(1turn);
	}
}
</style>
