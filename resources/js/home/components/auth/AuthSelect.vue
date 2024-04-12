<template>
	<label
		class="AuthSelect"
		:class="{
			'AuthSelect_error': error,
			'AuthSelect_selected': selected,
		}"
	>
		<span class="AuthSelect-label">
			{{ label }}
		</span>
		<span class="AuthSelect-wrapper">
			<slot name="inner-before" />
			<span class="AuthSelect-inputWrapper">
				<slot name="input">
					<div class="AuthSelect-selected">
						{{ selected ? selected.title : placeholder }}
					</div>
					<select
						:value="value"
						class="AuthSelect-input"
						@input="$emit('input', $event.target.value)"
					>
						<option
							v-for="opt in options"
							:key="opt.value"
							:value="opt.value"
						>
							{{ opt.title }}
						</option>
					</select>
				</slot>
			</span>
			<slot name="inner-after" />
		</span>
		<span
			v-if="error"
			class="AuthSelect-error"
		>
			{{ error }}
		</span>
		<span
			v-else-if="text"
			class="AuthSelect-text"
		>
			{{ text }}
		</span>
	</label>
</template>

<script>
export default {
	name: 'AuthSelect',
	components: {},
	props: {
		value: {
			type: String,
			default: '',
		},
		options: {
			type: Array,
			default: () => [],
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
	computed: {
		selected(){
			return this.options.find(opt => opt.value === this.value)
		}
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {},
}
</script>

<style lang="scss">
.AuthSelect{
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
		gap: 10px;

		width: 100%;
		height: 48px;
		padding: 10px 16px 10px 16px;
		border: 1px solid #CDD1DB;
		background-color: #fff;

		position: relative;

		border-radius: 8px;
	}
	&-inputWrapper{
		flex: 1;
		display: block;
	}
	&-selected{
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
	&-input{
		width: 100%;
		opacity: 0;
		position: absolute;
		z-index: 1;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
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
		.AuthSelect{
			&-wrapper{
				border-color: #60A5FA;
			}
		}
	}
	&:has(:focus){
		.AuthSelect{
			&-wrapper{
				border-color: #0C50FF;
			}
		}
	}
	&_selected{
		.AuthSelect{
			&-wrapper{
				border-color: #AFB5C0;
			}
		}
	}
	&_error{
		.AuthSelect{
			&-wrapper{
				border-color: #E13C3C;
			}
		}
	}
	&_success{
		.AuthSelect{
			&-wrapper{
				border-color: #4FC168;
			}
		}
	}
}
</style>
