<template>
	<div
		class="PulseCard"
		:style="[`--card-pulse-size: ${size * 10}px`, `--card-pulse-color: ${color}`].join(';')"
		@click="$emit('click', $event)"
	>
		<div
			class="PulseCard-pulsor"
		/>
		<div class="PulseCard-content">
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	name: 'PulseCard',
	components: {},
	props: {
		color: {
			type: String,
			default: '#00aeef'
		},
		size: {
			type: Number,
			default: 1,
		},
	},
	computed: {},
}
</script>

<style lang="scss">
.PulseCard{
	position: relative;
	&-pulsor{
		border-radius: inherit;

		position: absolute;
		z-index: 1;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;

		transform-origin: center;
		animation: PulseCardPulse-1 infinite var(--card-pulse-interval, 1.5s);
		pointer-events: none;
		outline: 1px solid var(--card-pulse-color, #00aeef)
	}
	&-content{
		border-radius: inherit;
	}
}
@keyframes PulseCardPulse-1 {
	50% {
		outline-offset: 0;
		opacity: 1;
	}
	100% {
		outline-offset: var(--card-pulse-size, 10px);
		opacity: 0;
	}
}
@keyframes PulseCardPulse-2 {
	50% {
		outline-offset: 0;
		opacity: 1;
	}
	100% {
		outline-offset: 20px;
		opacity: 0;
	}
}

@keyframes PulseCardPulse-3 {
	50% {
		outline-offset: 0;
		opacity: 1;
	}
	100% {
		outline-offset: 30px;
		opacity: 0;
	}
}
</style>
