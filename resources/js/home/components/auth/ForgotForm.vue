<template>
	<form
		class="ForgotForm"
		@submit.prevent="onSubmit"
	>
		<template v-if="isSended">
			<AuthTitle>
				Отправили письмо с инструкциями
			</AuthTitle>
			<AuthSubTitle>
				Мы выслали письмо на почту {{ email }}, перейдите по ссылке в письме, чтобы сбросить пароль. Если письмо не пришло, проверьте папку спам.
			</AuthSubTitle>
			<AuthSubmit
				:disabled="resendTimer"
			>
				Повторить отправку
			</AuthSubmit>
			<AuthSubTitle
				v-if="resendTimer"
			>
				Повторно отправить письмо можно через: {{ resendTimer }} сек
			</AuthSubTitle>
		</template>
		<AuthSubTitle>
			<a
				href="javascript:void(0)"
				class="fw500"
				@click="$emit('login')"
			>
				Вернуться ко входу
			</a>
		</AuthSubTitle>
		<AuthTitle>
			Восстановление пароля
		</AuthTitle>
		<AuthSubTitle>
			Укажите email, который вы указывали при регистрации
		</AuthSubTitle>
		<div class="ForgotForm-inputs">
			<AuthInput
				v-model="email"
				label="Email"
				type="email"
				placeholder="example@gmail.com"
				:error="errors.email"
			/>
		</div>
		<AuthSubmit>
			Продолжить
		</AuthSubmit>
	</form>
</template>

<script>
import AuthInput from './AuthInput.vue';
import AuthSubmit from './AuthSubmit.vue';
import AuthTitle from './AuthTitle.vue';
import AuthSubTitle from './AuthSubTitle.vue';
import axios from 'axios';
export default {
	name: 'ForgotForm',
	components: {
		AuthInput,
		AuthSubmit,
		AuthTitle,
		AuthSubTitle,
	},
	props: {},
	data(){
		return {
			email: '',
			errors: {
				email: '',
			},
			isLoading: false,
			isSended: false,
			resendTimer: 0,
			resendIterval: null,
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){
		this.resendIterval = setInterval(() => {
			this.resendTimer && --this.resendTimer
		}, 1000)
	},
	beforeDestroy(){
		clearInterval(this.resendIterval)
	},
	methods: {
		async onSubmit(){
			if(this.resendTimer) return
			this.isLoading = true
			try {
				const {data} = await axios.post('/setting/reset', {
					email: this.email,
				})
				if(data.success) {
					this.isSended = true
					this.resendTimer = 60
					//toast? На вашу почту отправлен новый пароль!
					return
				}
				this.errors.email = 'Вы не зарегистрированы в нашей системе!'
			}
			catch (error) {
				this.errors.email = 'Вы не зарегистрированы в нашей системе!'
			}
			this.isLoading = false
		},
	},
}
</script>

<style lang="scss">
.ForgotForm{
	&-inputs{
		margin-top: 20px;
	}
}
</style>
