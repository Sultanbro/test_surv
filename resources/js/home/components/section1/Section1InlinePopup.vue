<template>
	<span class="Section1InlinePopup">
		<span class="Section1InlinePopup-button">i</span>
		<span class="Section1InlinePopup-body">
			<slot />
		</span>
	</span>
</template>

<script>
export default {
	name: 'Section1InlinePopup',
	components: {},
}
</script>

<style lang="scss">
	.Section1InlinePopup{
		position: relative;
		&-button{
			display: inline-block;
			width: 1rem;
			height: 1rem;
			// cursor: pointer;
			border-radius: 5rem;
			background: #eee;
			font-size: 0.7rem;
			text-align: center;
			vertical-align: middle;
			user-select: none;
		}
		&-body{
			display: none;
			width: 17.8125rem;
			padding: 1rem;

			position: absolute;
			z-index: 5;
			top: 0;
			left: 100%;

			background-color: #fff;
			box-shadow: 0 0 2px rgba(#000, 0.5);
		}
		&:hover &-body{
			display: block;
		}
	}
</style>
