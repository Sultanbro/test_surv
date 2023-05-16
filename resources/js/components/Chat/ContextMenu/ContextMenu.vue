<template>
	<div
		v-show="show"
		:style="style"
		class="ContextMenu"
		@mousedown.stop
		@contextmenu.prevent
	>
		<slot />
	</div>
</template>

<script>
export default {
	name: 'ContextMenu',
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
.ContextMenu{
	display: block;
	min-width: 100px;
	border-radius: 8px;
	padding: 10px 0;

	position: absolute;
	z-index: 1000;

	background-color: #fff;
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);

	visibility: hidden;
	opacity: 0;

	&_visible{
		visibility: visible;
		opacity: 1;
	}

	&-item{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;

		width: 100%;
		padding: 10px 20px;

		text-decoration: none;
		color: #0a0a0a;
		cursor: pointer;
		&:hover{
			color: #3361FF;
			background: #F6F8FF;
		}
		&_red,
		&_red:hover{
			color: #F8254B;
		}
	}
}
</style>
