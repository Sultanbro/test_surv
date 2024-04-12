<template>
	<AuthLayout class="LoginView">
		<template #form>
			<LoginForm
				v-if="mode === 'login'"
				:errors="loginErrors"
				:loading="isLoading"
				@submit="onSubmit"
				@forgot="mode = 'forgot'"
			/>
			<template v-else-if="mode === 'links'">
				<!-- select tenant -->
			</template>
			<template v-else-if="'forgot'">
				<ForgotForm
					@login="mode = 'login'"
				/>
			</template>
		</template>
	</AuthLayout>
</template>

<script>
import AuthLayout from '../components/auth/AuthLayout.vue';
import LoginForm from '../components/auth/LoginForm.vue';
import ForgotForm from '../components/auth/ForgotForm.vue';

import axios from 'axios'

function emptyErrors(){
	return {
		email: '',
		phone: '',
		password: '',
		// email: '',
	}
}
function parseErrors(errors){
	const fields = {
		username: 'email',
	}
	const result = {}
	for(var field in errors){
		result[fields[field] || field] = errors[field]
	}
	return result
}

export default {
	name: 'LoginView',
	components: {
		AuthLayout,
		LoginForm,
		ForgotForm,
	},
	props: {},
	data(){
		return {
			mode: 'login',
			isLoading: false,
			loginErrors: emptyErrors(),
			links: [],
		}
	},
	computed: {},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onSuccessLogin(data){
			if(data.link) return window.location.replace(data.link)
			if(data.links) return this.showLinks(data.links)
			// toast?
		},
		showLinks(links){
			this.links = links
			this.mode = 'links'
		},
		async onSubmit({method, email, phone, password}){
			this.isLoading = true
			try {
				const {data} = await axios.post('/login', {
					method,
					username: email,
					phone,
					password,
				})
				this.onSuccessLogin(data)
			}
			catch (error) {
				const { status, data } = error.response
				switch(status){
				case 422:
					this.loginErrors = {
						...emptyErrors(),
						...parseErrors(data.errors),
					}
					break;
				case 401:
					this.loginErrors = {
						...emptyErrors(),
						password: 'Введенный email или пароль не совпадает'
					}
					break;
				default:
					// toast?
				}
			}
			this.isLoading = false
		},
	},
}
</script>

<style lang="scss">
//.LoginView{}
</style>
