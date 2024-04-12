<template>
	<form
		class="LoginForm"
		@submit.prevent="onSubmit"
	>
		<AuthTitle>
			Войти в Jobtron.org
		</AuthTitle>

		<AuthSubTitle>
			У вас нет аккаунта?
			<router-link
				to="/register"
				class="fw500"
			>
				Зарегистрироваться
			</router-link>
		</AuthSubTitle>

		<AuthMethod
			v-model="method"
			:items="authMethods"
		/>

		<div class="LoginForm-inputs">
			<AuthInput
				v-if="method === 'email'"
				key="email"
				v-model="email"
				label="Email"
				type="email"
				placeholder="example@gmail.com"
				:error="errors.email"
			/>
			<AuthInput
				v-else
				key="phone"
				v-model="phone"
				label="Телефон"
				type="phone"
				placeholder="000 000 00 00 "
				:error="errors.phone"
			>
				<template #inner-before>
					+7
				</template>
			</AuthInput>
			<AuthInput
				v-model="password"
				label="Пароль"
				:type="showPassword ? 'text' : 'password'"
				placeholder="Введите пароль"
				:error="errors.password"
			>
				<template #inner-after>
					<img
						:src="showPassword ? '/svg/EyeSlash2.svg' : '/svg/EyeSlash.svg'"
						class="LoginForm-showPassword"
						@click.stop="showPassword = !showPassword"
					>
				</template>
			</AuthInput>
		</div>
		<AuthSubTitle
			class="text-right"
		>
			<a
				href="javascript:void(0)"
				class="fw500"
				@click="$emit('forgot')"
			>
				Забыли пароль?
			</a>
		</AuthSubTitle>

		<AuthSubmit>
			Войти
		</AuthSubmit>
	</form>
</template>

<script>
import AuthMethod from './AuthMethod.vue';
import AuthInput from './AuthInput.vue';
import AuthTitle from './AuthTitle.vue';
import AuthSubTitle from './AuthSubTitle.vue';
import AuthSubmit from './AuthSubmit.vue';

export default {
	name: 'LoginForm',
	components: {
		AuthMethod,
		AuthInput,
		AuthTitle,
		AuthSubTitle,
		AuthSubmit,
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
			phoneCode: '',
			phone: '',
			password: '',
			showPassword: '',
		}
	},
	computed: {
		authMethods(){
			return [
				{
					value: 'email',
					title: 'Почта',
				},
				{
					value: 'phone',
					title: 'Номер телефона',
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
	}
	&-showPassword{
		&:hover{
			filter: sepia(100%) hue-rotate(180deg);
		}
	}
}
</style>
