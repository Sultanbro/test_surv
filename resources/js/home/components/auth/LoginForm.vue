<template>
	<form
		class="LoginForm"
		:action="action"
		method="POST"
		@submit.prevent="onSubmit"
	>
		<AuthTitle>
			{{ lang.title }}
		</AuthTitle>

		<AuthSubTitle>
			{{ lang.notexists }}
			<a
				href="/register"
				class="fw500"
			>
				{{ lang.register }}
			</a>
		</AuthSubTitle>

		<AuthMethod
			v-if="false"
			v-model="method"
			:items="authMethods"
		/>
		<div class="LoginForm-inputs">
			<AuthInput
				v-if="method === 'email'"
				key="email"
				v-model="email"
				:label="lang.email"
				type="email"
				placeholder="example@gmail.com"
				:error="errors.email"
			/>
			<template v-else>
				<AuthPhone
					key="phone"
					v-model="phone"
					:label="lang.phone"
					:error="errors.phone"
				/>
			</template>
			<AuthInput
				v-model="password"
				:label="lang.password"
				:type="showPassword ? 'text' : 'password'"
				:placeholder="lang.passwordPlaceholder"
				:error="errors.password"
			>
				<template #inner-after>
					<div
						class="LoginForm-showPassword"
						@click="showPassword = !showPassword"
					>
						<OpenEyeIcon v-if="showPassword" />
						<CloseEyeIcon v-else />
					</div>
				</template>
			</AuthInput>
		</div>
		<AuthSubTitle
			class="text-right"
			data-qqq="qweq"
		>
			<a
				href="javascript:void(0)"
				class="fw500"
				@click="$emit('forgot')"
			>
				{{ lang.forgot }}
			</a>
		</AuthSubTitle>

		<AuthSubmit>
			{{ lang.login }}
		</AuthSubmit>
	</form>
</template>

<script>
import AuthPhone from './AuthPhone.vue';
import AuthMethod from './AuthMethod.vue';
import AuthInput from './AuthInput.vue';
import AuthTitle from './AuthTitle.vue';
import AuthSubTitle from './AuthSubTitle.vue';
import AuthSubmit from './AuthSubmit.vue';
import OpenEyeIcon from '../../assets/img/auth/OpenEyeIcon.vue'
import CloseEyeIcon from '../../assets/img/auth/CloseEyeIcon.vue'

import * as LANG from './LoginForm.lang.js'

export default {
	name: 'LoginForm',
	components: {
		AuthMethod,
		AuthInput,
		AuthTitle,
		AuthSubTitle,
		AuthSubmit,
		AuthPhone,
		OpenEyeIcon,
		CloseEyeIcon
	},
	props: {
		action: {
			type: String,
			default: '/login',
		},
		errors: {
			type: Object,
			default: () => ({
				email: '',
				phone: '',
				password: '',
			})
		}
	},
	data(){
		return {
			method: 'email',
			email: '',
			phoneCode: '+7',
			phone: '',
			password: '',
			showPassword: '',
			contralDomain: ['jobtron'].includes(window.location.hostname.split('.')[0])
		}
	},
	computed: {
		lang(){
			return LANG[this.$root.$data.lang || 'ru']
		},
		authMethods(){
			return [
				{
					value: 'email',
					title: this.lang.switchemail,
				},
				{
					value: 'phone',
					title: this.lang.switchphone,
					// disabled: true,
				},
			]
		}
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onSubmit(){
			this.$emit('submit', {
				method: this.method,
				email: this.email,
				phone: this.phoneCode + this.phone,
				password: this.password,
			})
		}
	},
}
</script>

<style lang="scss">
.LoginForm{
	padding-top: 12px;
	&-inputs{
		display: flex;
		flex-flow: column nowrap;
		gap: 20px;
		margin-top: 20px;
	}
	&-showPassword{
    cursor: pointer;
		&:hover{
			filter: sepia(100%) hue-rotate(180deg);
		}
	}
}
</style>
