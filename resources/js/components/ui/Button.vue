<template>
	<button
		class="JobtronButton"
		:class="{
			'JobtronButton_big': big,
			'JobtronButton_success': success,
			'JobtronButton_error': error,
		}"
		@click="$emit('click')"
	>
		<slot />
	</button>
</template>

<script>
export default {
	name: 'JobtronButton',
	components: {},
	props: {
		big: {
			type: Boolean
		},
		success: {
			type: Boolean
		},
		error: {
			type: Boolean
		},
	}
}
</script>

<style lang="scss">
.JobtronButton{
	display: inline-flex;

	align-items: center;
	justify-content: space-between;
	gap: 1.5rem;

	padding: 1.3rem 2.5rem;
	margin-top: auto;
	border: none;
	border-radius: 0.5rem;

	font-weight: 600;
	font-size: 1.2rem;
	// line-height: 1.4rem;
	line-height: 1;
	letter-spacing: -0.03em;
	color: #fff;

	background: #3361FF;
	cursor: pointer;
	user-select: none;
	&:hover:not([disabled]):not(.disabled){
		background: lighten(#3361FF, 10);
	}
	&:focus{
		outline: none;
	}
	&[disabled]{
		cursor: default;
		background-color: #777;
	}
	&_big{
		padding: 2.3rem 3rem;
		border-radius: 0.6rem;
		// line-height: 3rem;
	}
	&_success{
		background: #28a745;
		&:hover:not([disabled]):not(.disabled){
			background: #218838;
		}
	}
	&_error{
		background: #dc3545;
		&:hover:not([disabled]):not(.disabled){
			background: #c82333;
		}
	}
}
</style>
