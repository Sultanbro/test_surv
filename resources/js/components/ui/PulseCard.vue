<template>
	<div
		class="PulseCard"
		@click="$emit('click', $event)"
	>
		<div class="PulseCard-pulsor" />
		<div class="PulseCard-content">
			<slot />
		</div>
	</div>
</template>

<script>
export default {
	name: 'PulseCard',
	components: {},
}
</script>

<style lang="scss">
.PulseCard{
	position: relative;
	&-pulsor{
		outline: 1px solid #00aeef;
		border-radius: inherit;

		position: absolute;
		z-index: 1;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;

		transform-origin: center;
		animation: PulseCardPulse infinite 1.5s;
		pointer-events: none;
	}
	&-content{
		border-radius: inherit;
	}
}
@keyframes PulseCardPulse {
	50% {
		outline-offset: 0;
		opacity: 1;
	}
	100% {
		outline-offset: 10px;
		opacity: 0;
	}
}
</style>
