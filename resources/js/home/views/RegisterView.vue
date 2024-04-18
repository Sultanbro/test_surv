<template>
	<AuthLayout class="RegisterView">
		<template #form>
			<form
				action="/register"
				method="POST"
				class="RegisterView-form"
				@submit.prevent="onSubmit"
			>
				<AuthTitle>
					{{ lang.title }}
				</AuthTitle>
				<AuthSubTitle>
					{{ lang.exists }}
					<router-link
						to="/login"
						class="fw500"
					>
						{{ lang.login }}
					</router-link>
				</AuthSubTitle>
				<div class="RegisterView-inputs">
					<AuthInput
						v-model="email"
						label="Email"
						type="email"
						placeholder="example@gmail.com"
						:error="errors.email"
					/>
					<AuthInput
						v-model="phone"
						:label="lang.phone"
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
						:label="lang.name"
						type="text"
						:placeholder="lang.namePlaceholder"
						:error="errors.name"
					/>
					<AuthSelect
						v-model="currency"
						:options="currencyOptions"
						:label="lang.currency"
						:text="lang.canchange"
					/>
					<GRecaptcha
						v-if="useCapcha"
						:key="capchaKey"
						data-sitekey="6LeuEr8pAAAAAAvhivvwP88W3NW2ZCzYuJ65Mzam"
						:data-validate="onValidate"
						:data-callback="onSubmit"
						class="AuthSubmit"
					>
						{{ lang.register }}
					</GRecaptcha>
					<AuthSubmit v-else>
						{{ lang.register }}
					</AuthSubmit>
				</div>

				<AuthNote>
					{{ lang.agree1 }}
					<router-link to="/aggreement">
						{{ lang.aggreement }}
					</router-link>
					{{ lang.agree2 }}
					<router-link to="/offer">
						{{ lang.offer }}
					</router-link>
					{{ lang.agree3 }}
					<router-link to="/terms">
						{{ lang.terms }}
					</router-link>
					{{ lang.agree4 }}
				</AuthNote>
			</form>
		</template>

		<template
			v-if="isMobile"
			#form-header
		>
			<AuthHeader />
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
			<AuthHeader />
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
import AuthInput from '../components/auth/AuthInput.vue';
import AuthTitle from '../components/auth/AuthTitle.vue';
import AuthSubTitle from '../components/auth/AuthSubTitle.vue';
import AuthNote from '../components/auth/AuthNote.vue';
import AuthSelect from '../components/auth/AuthSelect.vue';
import AuthHeader from '../components/auth/AuthHeader.vue';
import AuthFooter from '../components/auth/AuthFooter.vue';
import AuthInfo from '../components/auth/AuthInfo.vue';
import AuthSubmit from '../components/auth/AuthSubmit.vue';

import GRecaptcha from '@finpo/vue2-recaptcha-invisible';

import axios from 'axios';
import * as LANG from './RegisterView.lang.js'

function emptyErrors(){
	return {
		email: '',
		phone: '',
		name: '',
		currency: '',
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
	name: 'RegisterView',
	components: {
		AuthLayout,
		AuthInput,
		AuthTitle,
		AuthSubTitle,
		AuthNote,
		AuthSelect,
		AuthHeader,
		AuthFooter,
		AuthInfo,
		GRecaptcha,
		AuthSubmit,
	},
	props: {},
	data(){
		return {
			email: '',
			phone: '',
			name: '',
			currency: 'rub',

			isLoading: false,
			isSended: true,
			errors: {},
			capchaKey: 1,
			useCapcha: true,
		}
	},
	computed: {
		isMobile(){
			return this.$viewportSize.width < 768
		},
		lang(){
			return LANG[this.$root.$data.lang || 'ru']
		},
		currencyOptions(){
			return [
				{
					value: 'rub',
					title: this.lang['rub'],
				},
				{
					value: 'kzt',
					title: this.lang['kzt'],
				},
				{
					value: 'usd',
					title: this.lang['usd'],
				},
			]
		},
	},
	watch: {},
	created(){
		if(window.Laravel?.userId) location.assign('/')
		const bitrix = document.querySelector('.b24-widget-button-shadow')
		if(bitrix?.parentNode) bitrix?.parentNode.remove()
	},
	mounted(){},
	beforeDestroy(){},
	methods: {
		async onSubmit(token){
			if(this.isLoading) return
			this.isLoading = true

			try {
				await axios.post('/register', {
					name: this.name,
					email: this.email,
					phone: this.phone,
					currency: this.currency,
					'g-recaptcha-response': token,
				})
				this.isSended = true
			}
			catch (error) {
				const {status, data} = error.response
				if(status === 422){
					this.errors = {
						...emptyErrors(),
						...parseErrors(data.errors),
					}
				}
				else{
					alert('Не удалось создать кабинет, попробуйте позже')
				}
				++this.capchaKey
			}

			this.isLoading = false
		},
		onValidate(a, b, c){
			console.error('onValidate', a, b, c)
		},
		onCallback(a, b, c){
			console.error('onCallback', a, b, c)
		},
	},
}
</script>

<style lang="scss">
.RegisterView{
	&-inputs{
		display: flex;
		flex-flow: column nowrap;
		gap: 20px;
	}
	.AuthSubmit{
		position: relative;
		button{
			width: 100%;

			position: absolute;
			z-index: 1;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;

			text-align: center;

			appearance: none;
			background-color: transparent;
			border: none;
			color: #fff;
		}
	}
}
</style>
