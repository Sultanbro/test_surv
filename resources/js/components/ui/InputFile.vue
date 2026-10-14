<template>
	<label class="InputFile">
		<input
			type="file"
			:accept="accept"
			:multiple="multiple"
			class="InputFile-input"
			@change="$emit('change', $event.target.files)"
		>
		<slot />
	</label>
</template>

<script>
export default {
	name: 'InputFile',
	components: {},
	props: {
		accept: {
			type: String,
			default: ''
		},
		multiple: {
			type: Boolean,
			default: false
		},
	}
}
</script>

<style lang="scss">
.InputFile{
	cursor: pointer;
	&-input{
		display: none;
	}
}
</style>
