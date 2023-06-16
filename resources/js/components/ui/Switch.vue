<template>
	<span class="JobtronSwitch">
		<input
			type="checkbox"
			class="JobtronSwitch-input"
			:checked="value"
			@input="$emit('input', $event.target.checked)"
		>
		<span class="JobtronSwitch-switch" />
	</span>
</template>

<script>
export default {
	name: 'JobtronSwitch',
	components: {},
	props: {
		value: {
			type: Boolean
		}
	},
}
</script>

<style lang="scss">
.JobtronSwitch{
	display: inline-block;
	position: relative;

	&-switch{
		display: block;
		width: 48px;
		height: 30px;

		background: #8BABD8;
		border-radius: 20px;
		&:before{
			content: '';
			width: 20px;
			height: 20px;

			position: absolute;
			z-index: 3;
			top: 5px;
			left: 5px;

			background-color: #fff;
			border-radius: 20px;
			box-shadow: 0px 4px 4px rgba(50, 50, 71, 0.18), 0px 4px 8px rgba(50, 50, 71, 0.16);
		}
	}


	&-input{
		position: absolute;
		z-index: 5;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		opacity: 0;
		&:checked{
			~ .JobtronSwitch{
				&-switch{
					background: #3361FF;
					&:before{
						left: auto;
						right: 5px;
					}
				}
			}
		}
	}
}
</style>
