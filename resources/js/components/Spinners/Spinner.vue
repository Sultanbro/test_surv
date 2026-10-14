<template>
	<div class="custom-loader" />
</template>

<script>
export default {
	name: 'CustomSpinner',
	data() {
		return {
			percentage: 0,
			intervalId: null,
		};
	},
	mounted() {
		this.startAnimation();
	},
	beforeDestroy() {
		clearInterval(this.intervalId);
	},
	methods: {
		startAnimation() {
			this.intervalId = setInterval(() => {
				this.percentage += 10;
				if (this.percentage >= 100) {
					this.percentage = 0;
				}
			}, 100);
		},
	},
};
</script>

<style scoped>
.custom-loader {
	width: 50px;
	aspect-ratio: 1;
	display: grid;
	border-radius: 50%;
	background:
			linear-gradient(0deg , rgb(21, 106, 232) 30%,#0000 0 70%, rgb(21, 106, 232) 0) 50%/8% 100%,
			linear-gradient(90deg, rgb(21, 106, 232) 30%,#0000 0 70%,rgb(0 0 0/75% ) 0) 50%/100% 8%;
	background-repeat: no-repeat;
	animation: l23 1s infinite steps(12);
		z-index: 999;
}
.custom-loader::before,
.custom-loader::after {
	content: "";
	grid-area: 1/1;
	border-radius: 50%;
	background: inherit;
	opacity: 0.915;
	transform: rotate(30deg);
}
.custom-loader::after {
	opacity: 0.83;
	transform: rotate(60deg);
}
@keyframes l23 {
	100% {transform: rotate(1turn)}
}

</style>
