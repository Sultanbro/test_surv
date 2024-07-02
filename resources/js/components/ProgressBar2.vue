<template>
	<div class="progress-bar mb-1">
		<div class="background-bar">
			<div class="info">
				<label>{{ label }}</label>
				<label class="percentage">{{ percentage }}%   </label>
			</div>
		</div>
		<transition
			appear
			@before-appear="beforeEnter"
			@after-appear="enter"
		>
			<div class="tracker-bar" />
		</transition>
	</div>
</template>

<script>
export default {
	name: 'ProgressBar2',
	props: {
		percentage: {
			type: Number,
			default: 0
		},
		label: {
			type: String,
			default: ''
		},
	},
	methods: {
		beforeEnter (el) {
			el.style.width = 0
		},
		enter (el) {
			el.style.width = `${this.percentage}%`
			el.style.transition = 'width 1s linear'
		}
	}
}
</script>
