<template>
	<div class="ChatIconsDemo">
		<component
			v-for="comp, name in Icons"
			:key="name"
			:is="name"
			v-b-popover.hover.top="name"
			class="m-4"
		/>
	</div>
</template>

<script>
import * as Icons from './chat-icons'
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

<style>
.ChatIconsDemo{
	display: flex;
	flex-flow: row wrap;
	align-items: center;
	justify-content: center;

	position: fixed;
	z-index: 30000;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #fff;
}
</style>
