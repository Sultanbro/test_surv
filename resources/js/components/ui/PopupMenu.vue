<template>
	<div
		class="PopupMenu"
		:class="['PopupMenu_'+position]"
		@mousedown.stop
		@contextmenu.prevent
	>
		<slot name="before" />
		<div
			v-if="maxHeight"
			class="PopupMenu-scroll"
			:style="`max-height: ${maxHeight};`"
		>
			<slot />
		</div>
		<template v-else>
			<slot />
		</template>
		<slot name="after" />
	</div>
</template>

<script>
export default {
	name: 'PopupMenu',
	props: {
		position: {
			type: String,
			default: 'bottomRight',
			validator(value) {
				// The value must match one of these strings
				return [
					'bottomRight',
					'bottomLeft',
					'topRight',
					'topLeft',
					'right',
					'rightBottom'
				].includes(value)
			}
		},
		maxHeight: {
			type: String,
			default: ''
		}
	}
}
</script>

<style lang="scss">
.PopupMenu{
	display: block;
	min-width: 100px;
	border-radius: 8px;
	padding: 10px 0;

	position: absolute;
	z-index: 1000;

	background-color: #fff;
	box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.05), 0px 15px 60px -40px rgba(45, 50, 90, 0.2);

	&_bottomRight{
		top: 100%;
		right: 0;
	}
	&_bottomLeft{
		top: 100%;
		left: 0;
	}
	&_topRight{
		right: 0;
		bottom: 100%;
	}
	&_topLeft{
		left: 0;
		bottom: 100%;
	}
	&_right{
		left: 100%;
		top: 50%;
		transform: translateY(-50%);
	}
	&_rightBottom{
		left: 100%;
		bottom: 0;
	}

	&-scroll{
		overflow-y: auto;
	}

	&-item{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;

		width: 100%;
		padding: 10px 20px;

		font-weight: 500;
		font-size: 14px;
		line-height: 1.3;
		letter-spacing: -0.03em;

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
