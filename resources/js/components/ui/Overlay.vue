<template>
	<div
		class="JobtronOverlay"
		:style="style"
		@click.self="$emit('close')"
	>
		<slot />
	</div>
</template>

<script>
export default {
	name: 'JobtronOverlay',
	components: {},
	props: {
		z: {
			type: Number,
			default: 1000000
		}
	},
	computed: {
		style(){
			return `z-index: ${this.z};`
		}
	}
}
</script>

<style lang="scss">
.JobtronOverlay{
	width: 100vw;
	height: 100vh;

	position: fixed;
	top: 0;
	right: 0;
	overflow: hidden;
	background-color: rgba(#000000, 0.25);
	backdrop-filter: blur(10px);
}
</style>
