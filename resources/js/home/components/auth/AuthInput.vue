<template>
	<label
		class="AuthInput"
		:class="{
			'AuthInput_error': error,
		}"
	>
		<span class="AuthInput-label">
			{{ label }}
		</span>
		<span class="AuthInput-wrapper">
			<slot name="inner-before" />
			<span class="AuthInput-inputWrapper">
				<slot name="input">
					<input
						:value="value"
						:type="type"
						:placeholder="placeholder"
						class="AuthInput-input"
						@input="$emit('input', $event.target.value)"
					>
				</slot>
			</span>
			<slot name="inner-after" />
		</span>
		<span
			v-if="error"
			class="AuthInput-error"
		>
			{{ error }}
		</span>
		<span
			v-else-if="text"
			class="AuthInput-text"
		>
			{{ text }}
		</span>
	</label>
</template>

<script>
export default {
	name: 'AuthInput',
	components: {},
	props: {
		value: {
			type: String,
			default: '',
		},
		label: {
			type: String,
			default: '',
		},
		type: {
			type: String,
			default: 'text',
		},
		placeholder: {
			type: String,
			default: '',
		},
		text: {
			type: String,
			default: '',
		},
		error: {
			type: String,
			default: '',
		},
	},
	data(){
		return {}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.AuthInput{
	&-label{
		display: block;
		margin-bottom: 4px;

		font-size: 14px;
		font-weight: 500;
		line-height: 20px;
		text-align: left;
		color: #737B8A;
	}
	&-wrapper{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;

		width: 100%;
		height: 48px;
		padding: 10px 16px 10px 16px;
		gap: 10px;
		border-radius: 8px;
		border: 1px solid #AFB5C0;
		background-color: #fff;
	}
	&-inputWrapper{
		flex: 1;
		display: block;
	}
	&-input{
		display: block;
		width: 100%;
		padding: 0;
		margin: 0;
		border: none;

		font-size: 16px;
		font-weight: 400;
		line-height: 20px;
		text-align: left;

		&:focus{
			outline: none;
		}
	}
	&-text,
	&-error{
		color: #737B8A;
		font-size: 12px;
		font-weight: 400;
		line-height: 16px;
		text-align: left;
	}
	&-error{
		color: #E13C3C;
	}

	&:hover{
		.AuthInput{
			&-wrapper{
				border-color: #60A5FA;
			}
		}
	}
	&:has(input:empty){
		.AuthInput{
			&-wrapper{
				border-color: #CDD1DB;
			}
		}
	}
	&:has(input:focus){
		.AuthInput{
			&-wrapper{
				border-color: #0C50FF;
			}
		}
	}
	&_error{
		.AuthInput{
			&-wrapper{
				border-color: #E13C3C;
			}
		}
	}
	&_success{
		.AuthInput{
			&-wrapper{
				border-color: #4FC168;
			}
		}
	}
}
</style>
