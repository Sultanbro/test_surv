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
				<SelectTenant
					:links="links"
				/>
			</template>
			<template v-else-if="'forgot'">
				<ForgotForm
					@login="mode = 'login'"
				/>
			</template>
		</template>

		<template
			v-if="isMobile"
			#form-header
		>
			<AuthHeader back />
		</template>
		<template
			v-if="isMobile"
			#form-footer
		>
			<AuthFooter />
		</template>

		<template
			v-if="!isMobile"
			#info
		>
			<AuthInfo />
		</template>
		<template
			v-if="!isMobile"
			#info-header
		>
			<AuthHeader back />
		</template>
		<template
			v-if="!isMobile"
			#info-footer
		>
			<AuthFooter />
		</template>
	</AuthLayout>
</template>

<script>
import AuthLayout from '../components/auth/AuthLayout.vue';
import LoginForm from '../components/auth/LoginForm.vue';
import ForgotForm from '../components/auth/ForgotForm.vue';
import AuthHeader from '../components/auth/AuthHeader.vue';
import AuthFooter from '../components/auth/AuthFooter.vue';
import AuthInfo from '../components/auth/AuthInfo.vue';
import SelectTenant from '../components/auth/SelectTenant.vue';

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
		AuthHeader,
		AuthFooter,
		AuthInfo,
		SelectTenant,
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
	computed: {
		isMobile(){
			return this.$viewportSize.width < 768
		}
	},
	watch: {},
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		onSuccessLogin(data){
			console
			if(Object.keys(data).includes('link')) return window.location.replace(data.link)
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
				const response = await fetch('/login', {
					method: 'POST',
					cache: 'no-cache',
					headers: {
						'Content-Type': 'application/json',
					},
					redirect: 'manual',
					body: JSON.stringify({
						method,
						username: email,
						phone,
						password,
					})
				})
				if(response.type === 'opaqueredirect') return location.assign('/')
				if(!response.ok) {
					// toast?
					return
				}
				const data = await response.json()
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
				case 302:
					alert('302')
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
