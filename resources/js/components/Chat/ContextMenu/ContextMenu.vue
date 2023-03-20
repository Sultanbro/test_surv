<template>
	<div
		v-show="show"
		:style="style"
		class="messenger__context-menu"
		@mousedown.stop
		@contextmenu.prevent
	>
		<slot />
	</div>
</template>

<script>
export default {
	props: {
		show: Boolean,
		x: {
			type: Number,
			default: 0,
		},
		y: {
			type: Number,
			default: 0,
		},
		parentElement: {
			type: HTMLElement,
			default: null
		}
	},
	computed: {
		style(){
			if(!this.parentElement) return {top: 0, left: 0}
			const messengerWindowRect = this.parentElement.getBoundingClientRect()
			const x = this.x - messengerWindowRect.left
			const y = this.y - messengerWindowRect.top
			const offsetHeight = this.$el.offsetHeight
			const offsetWidth = this.$el.offsetWidth

			return {
				top: (y + offsetHeight > messengerWindowRect.height
					? messengerWindowRect.height - offsetHeight
					: y) + 'px',
				left: (x + offsetWidth + 50 > messengerWindowRect.width
					? messengerWindowRect.width - offsetWidth
					: x) + 'px'
			}
		}
	}
}
</script>

<style lang="scss">
.messenger__context{
	&-menu{
		display: block;
		min-width: 100px;
		border: 1px solid #e5e5e5;
		border-radius: 4px;

		position: absolute;
		z-index: 1000;

		background-color: #fff;
		box-shadow: 0 2px 8px rgba(#000, .15);
	}
	a,
	&-item{
		display: block;
		padding: 10px 10px;
		text-decoration: none;
		color: #0a0a0a;
		&:hover{
			background-color: #f5f5f5;
		}
	}
}
</style>
