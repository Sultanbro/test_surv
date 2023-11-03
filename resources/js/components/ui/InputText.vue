<template>
	<div
		class="InputText"
		:class="{
			'InputText_focus': focus
		}"
	>
		<input
			:value="value"
			type="text"
			class="InputText-input"
			:placeholder="placeholder"
			@input="$emit('input', $event.target.value)"
			@focus="onFocus"
			@blur="onBlur"
		>
		<div
			v-if="value || alwaysClear"
			class="InputText-clear"
			@click="onClear"
		>
			×
		</div>
	</div>
</template>

<script>
export default {
	name: 'InputText',
	components: {},
	props: {
		value: {
			type: String,
			default: ''
		},
		placeholder: {
			type: String,
			default: ''
		},
		alwaysClear: {
			type: Boolean
		},
	},
	data(){
		return {
			focus: false,
		}
	},
	methods: {
		onFocus(e){
			this.focus = true
			this.$emit('focus', e)
		},
		onBlur(e){
			this.focus = false
			this.$emit('blur', e)
		},
		onClear(){
			this.$emit('input', '')
			this.$emit('clear')
		}
	}
}
</script>

<style lang="scss">
.InputText{
	display: flex;
	flex-flow: row nowrap;
	align-items: center;

	border: 1px solid #777;
	&-input{
		flex: 1;
		padding: 4px 8px;
		border: none;
		font-size: inherit;
		background-color: transparent;
	}
	&-clear{
		cursor: pointer;
		padding: 4px;
		font-size: inherit;
		color: #777;
		&:hover{
			color: #000;
		}
	}
}
</style>
