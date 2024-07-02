<template>
	<div
		class="FlipCard"
		:class="{'FlipCard_flipped': flipped}"
	>
		<div class="FlipCard-inner">
			<div class="FlipCard-front">
				<slot name="front" />
			</div>
			<div class="FlipCard-back">
				<slot name="back" />
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'FlipCard',
	components: {},
	props: {
		flipped: {
			type: Boolean,
			default: false
		}
	}
}
</script>

<style lang="scss">
.FlipCard{
	perspective: 1000px;
	&-inner{
		display: grid;
		align-items: stretch;
		vertical-align: top;

		padding: 0;
		margin: 0;
		border: none;

		position: relative;

		transition: transform 0.8s;
		transform-style: preserve-3d;
	}

	&-front,
	&-back{
		grid-area: 2 / 1;
		box-sizing: border-box;

		width: 100%;
		height: 100%;

		position: relative;
		background: none;
		appearance: none;
		// -webkit-backface-visibility: hidden; /* Safari */
		backface-visibility: hidden;
	}

	&-back{
		transform: rotateY(180deg);
	}

	&_flipped{
		.FlipCard-inner{
			transform: rotateY(180deg);
		}
	}
}
</style>
