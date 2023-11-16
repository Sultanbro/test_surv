<template>
	<div
		class="InputText"
		:class="{
			'InputText_focus': focus,
			'InputText_small': small,
			'InputText_empty': !value,
			'InputText_primary': primary,
		}"
	>
		<slot name="before" />
		<input
			:value="value"
			type="text"
			class="InputText-input"
			:placeholder="placeholder"
			:readonly="readonly"
			@input="$emit('input', $event.target.value)"
			@focus="onFocus"
			@blur="onBlur"
			@click="$emit('click', $event)"
		>
		<div
			v-if="clear && (value || alwaysClear)"
			class="InputText-clear"
			@click="onClear"
		>
			×
		</div>
		<slot name="after" />
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
		clear: {
			type: Boolean
		},
		small: {
			type: Boolean
		},
		primary: {
			type: Boolean
		},
		readonly: {
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
	gap: 12px;

	padding: 0 15px;

	border-radius: 8px;
	background-color: #F7FAFC;

	&-input{
		flex: 1;
		padding: 15px 0;
		border: none;
		font-size: 15px;
		line-height: 20px;
		background-color: transparent;
		&:first-child{
			padding-left: 12px;
		}
		&::placeholder {
			color: #8DA0C1;
		}
	}
	&-clear{
		cursor: pointer;
		padding: 15px 8px;
		font-size: 15px;
		line-height: 20px;
		color: #777;
		&:hover{
			color: #000;
		}
	}
	&_small{
		padding: 0 8px;
		.InputText{
			&-input{
				padding: 8px 0;
				&:first-child{
					padding-left: 12px;
				}
			}
			&-clear{
				padding: 8px;
			}
		}
	}
	&_primary{
		background-color: #fff;
		box-shadow: 0px 8px 16px 4px rgba(131, 178, 233, 0.14),
			0px 0px 1px 0px rgba(21, 106, 232, 0.20);
	}
}
</style>
