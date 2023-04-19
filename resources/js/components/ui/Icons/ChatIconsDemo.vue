<template>
	<div
		class="ChatIconsDemo"
		@click.self.stop.prevent="$emit('close')"
	>
		<div
			v-for="comp, name in Icons"
			:key="name"
			:title="name"
			class="ChatIconsDemo-icon ChatIcon-parent"
		>
			<component
				:is="name"
				v-b-popover.hover.top="name"
			/>
		</div>
	</div>
</template>

<script>
import * as Icons from './index.js'
export default {
	name: 'ChatIconsDemo',
	components: {
		...Icons
	},
	data(){
		return {
			Icons
		}
	}
}
</script>

<style lang="scss">
.ChatIconsDemo{
	display: flex;
	flex-flow: row wrap;
	align-items: center;
	justify-content: center;

	padding: 2rem;

	position: fixed;
	z-index: 30000;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);

	background-color: #fff;
	box-shadow: 0 0 2px 2px rgba(#000, 0.5);

	&-icon{
		flex: 0 0 10%;
		padding: 2rem;
		text-align: center;

		&:hover{
			background-color: #def;
		}
	}
}
</style>
