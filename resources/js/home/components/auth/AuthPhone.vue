<template>
	<div
		class="AuthPhone"
		:class="{
			'AuthPhone_error': error,
		}"
	>
		<span class="AuthInput-label">
			{{ label }}
		</span>
		<VuePhoneNumberInput
			v-model="phone"
			default-country-code="KZ"
			:preferred-countries="['KZ', 'RU', 'KG']"
			class="AuthPhone-wrapper"
			@update="onUpdate"
		/>
		<span
			v-if="error"
			class="AuthPhone-error"
		>
			{{ error }}
		</span>
		<span
			v-else-if="text"
			class="AuthPhone-text"
		>
			{{ text }}
		</span>
	</div>
</template>

<script>
import VuePhoneNumberInput from 'vue-phone-number-input';
import 'vue-phone-number-input/dist/vue-phone-number-input.css';

export default {
	name: 'AuthPhone',
	components: {
		VuePhoneNumberInput,
	},
	props: {
		value: {
			type: String,
			default: ''
		},
		label: {
			type: String,
			default: '',
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
		return {
			phone: this.value
		}
	},
	computed: {},
	watch: {
		value(){
			this.phone = this.value
		}
	},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onUpdate($event){
			this.$emit('input', $event.e164)
		}
	},
}
</script>

<style lang="scss">
.AuthPhone{
	.country-selector__country-flag,
	.country-selector__toggle,
	.input-tel__label,
	.country-selector__label{
		display: none;
	}
	.input-tel__input{
		border: none;
	}
	.country-selector{
		height: 48px;
	}
	.country-selector__input{
		width: 64px;
		height: 48px;
		padding: 10px 0 10px 16px !important;
		border: none;
		font-size: 16px;
		// color: #333;
		line-height: 1.25;
		box-shadow: none !important;
		background-color: transparent;
	}
	.select-country-container{
		width: auto !important;
		min-width: auto !important;
		flex: 0 0 64px !important;
	}
	.input-tel__input{
		height: 48px;
		font-size: 16px;
		// color: #333;
		line-height: 1.25;
		padding-top: 0 !important;
		box-shadow: none !important;
	}

	&-wrapper{
		border-radius: 8px;
		border: 1px solid #AFB5C0;
		background-color: #fff;
	}

	&-label{
		display: block;
		margin-bottom: 4px;

		font-size: 14px;
		font-weight: 500;
		line-height: 20px;
		text-align: left;
		color: #737B8A;
	}

	&:hover{
		.AuthPhone{
			&-wrapper{
				border-color: #60A5FA;
			}
		}
	}
	&:has(input:empty){
		.AuthPhone{
			&-wrapper{
				border-color: #CDD1DB;
			}
		}
	}
	&:has(input:focus){
		.AuthPhone{
			&-wrapper{
				border-color: #0C50FF;
			}
		}
	}
	&_error{
		.AuthPhone{
			&-wrapper{
				border-color: #E13C3C;
			}
		}
	}
	&_success{
		.AuthPhone{
			&-wrapper{
				border-color: #4FC168;
			}
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
}
</style>
