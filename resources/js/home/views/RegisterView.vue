<template>
	<AuthLayout class="RegisterView">
		<template #form>
			<form
				action="/register"
				method="POST"
				class="RegisterView-form"
			>
				<AuthTitle>
					Регистрация в Jobtron.org
				</AuthTitle>
				<AuthSubTitle>
					У вас уже есть аккаунт?
					<router-link
						to="/login"
						class="fw500"
					>
						Войти
					</router-link>
				</AuthSubTitle>
				<AuthInput
					v-model="email"
					label="Email"
					type="email"
					placeholder="example@gmail.com"
					:error="errors.email"
				/>
				<AuthInput
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
					v-model="name"
					label="Имя"
					type="email"
					placeholder="Станислав"
					:error="errors.name"
				/>
				<AuthSelect
					v-model="currency"
					:options="currencyOptions"
					label="Валюта"
					text="При необходимости можно будет изменять в настройках"
				/>
				<AuthSubmit>
					Зарегистрироваться
				</AuthSubmit>
				<AuthNote>
					Создавая учетную запись, я подтверждаю, что принимаю
					<router-link to="/aggreement">
						Пользовательское соглашение
					</router-link>,
					ознакомлен с
					<router-link to="/offer">
						договором оферты
					</router-link>
					и
					<router-link to="/terms">
						Политикой конфеденциальности
					</router-link>.
				</AuthNote>
			</form>
		</template>
	</AuthLayout>
</template>

<script>
import AuthLayout from '../components/auth/AuthLayout.vue';
import AuthInput from '../components/auth/AuthInput.vue';
import AuthTitle from '../components/auth/AuthTitle.vue';
import AuthSubTitle from '../components/auth/AuthSubTitle.vue';
import AuthNote from '../components/auth/AuthNote.vue';
import AuthSubmit from '../components/auth/AuthSubmit.vue';
import AuthSelect from '../components/auth/AuthSelect.vue';

export default {
	name: 'RegisterView',
	components: {
		AuthLayout,
		AuthInput,
		AuthTitle,
		AuthSubTitle,
		AuthNote,
		AuthSubmit,
		AuthSelect,
	},
	props: {},
	data(){
		return {
			email: '',
			phone: '',
			name: '',
			currency: 'rub',

			errors: {},
			currencyOptions: [
				{
					value: 'rub',
					title: 'rub',
				},
				{
					value: 'kzt',
					title: 'kzt',
				},
				{
					value: 'usd',
					title: 'usd',
				},
			]
		}
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
//.RegisterView{}
</style>
