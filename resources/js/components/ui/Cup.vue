<template>
	<img
		v-if="src"
		:src="src"
		alt="icon"
		class="JobtronCup"
		:class="{
			'JobtronCup_rotate': rotate
		}"
	>
</template>

<script>
const images = [
	'',
	'first-place.png',
	'second-place.png',
	'third-place.png',
]

export default {
	name: 'JobtronCup',
	components: {},
	props: {
		place: {
			type: Number,
			default: 1,
		},
		rotate: {
			type: Boolean,
			default: false
		}
	},
	computed: {
		src(){
			if(this.place < 1 || this.place > 3) return ''
			return '/images/dist/' + images[this.place]
		}
	}
}
</script>

<style lang="scss">
.JobtronCup{
	width: 15px;
	height: 20px;
	&_rotate{
		animation: rotateCup 2.5s cubic-bezier(.78,0,.26,.79) infinite normal;
	}
}
@keyframes rotateCup {
	0% {
		transform: rotateY(0);
	}
	100% {
		transform: rotateY(360deg);
	}
}
</style>
