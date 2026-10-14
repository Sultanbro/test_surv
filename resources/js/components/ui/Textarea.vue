<template>
	<div
		class="JobtronTextarea"
		:class="{
			'JobtronTextarea_block': block
		}"
		:data-value="value"
	>
		<textarea
			class="JobtronTextarea-textarea"
			:value="value"
			@input="$emit('input', $event.target.value)"
		/>
	</div>
</template>

<script>
export default {
	name: 'JobtronTextarea',
	components: {},
	props: {
		value: {
			type: String,
			default: ''
		},
		block: Boolean,
	}
}
</script>

<style lang="scss">
.JobtronTextarea{
	display: inline-grid;
	align-items: stretch;
	vertical-align: top;

	padding: 0;
	margin: 0;
	border: none;

	position: relative;

	&_block{
		display: grid;
	}
	&:after,
	&-textarea{
		grid-area: 2 / 1;
		box-sizing: border-box;

		height: 100%;
		min-height: 1em;
		padding: 0.25em;
		margin: 0;
		border: none;

		font: inherit;
		resize: none;
		background: none;
		appearance: none;
	}

	&-textarea{
		overflow: auto;
	}

	&:after {
		content: attr(data-value) ' ';
		visibility: hidden;
		white-space: pre-wrap;
	}
}
</style>
