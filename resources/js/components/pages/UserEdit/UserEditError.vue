<script>
export default {
	name: 'UserEditError',
	props: {
		errors: {
			type: Object,
			default: () => ({})
		},
		name: {
			type: String,
			default: ''
		}
	},
	computed: {
		error(){
			return this.errors[this.name]
		},
		text(){
			if(!this.error) return ''
			if(Array.isArray(this.error)) return this.error.join('\n')
			return this.error
		}
	}
}
</script>

<template>
	<div
		v-if="error"
		class="UserEditError"
	>
		{{ text }}
	</div>
</template>

<style lang="scss">
.UserEditError{
	color: red;
}
</style>
