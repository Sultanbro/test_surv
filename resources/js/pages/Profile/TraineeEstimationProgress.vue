<template>
	<div
		class="TraineeEstimationProgress"
		:style="background"
	>
		<div class="TraineeEstimationProgress-text">
			{{ text }} ({{ count }})
			<div class="TraineeEstimationProgress-value">
				{{ percent }}%
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'TraineeEstimationProgress',
	components: {},
	props: {
		color: {
			type: String,
			default: () => '#DDE9FF'
		},
		text: {
			type: String,
			default: () => ''
		},
		count: {
			type: Number,
			default: 0
		},
		percent: {
			type: Number,
			default: 0
		},
	},
	computed: {
		background(){
			return `background: linear-gradient(to right, ${this.color} ${this.percent}%, #fff ${this.percent}%);`
		},
	}
}
</script>

<style lang="scss">
.TraineeEstimationProgress{
	position: relative;
	z-index: 1;

	text-align: center;

	&-text{
		position: relative;
		z-index: 3;

		text-align: left;
		font-size: 1.2rem;
		color: #62788b;
	}
	&-value{
		display: inline-block;
		margin-left: 1rem;
		font-weight: 600;
	}
}
</style>
