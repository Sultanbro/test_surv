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
						type="text"
						placeholder="Станислав"
						:error="errors.name"
					/>
					<AuthSelect
						v-model="currency"
						:options="currencyOptions"
						label="Валюта"
						:text="lang.canchange"
					/>
				</div>
				<AuthSubmit>
					{{ lang.register }}
				</AuthSubmit>
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
import AuthSubmit from '../components/auth/AuthSubmit.vue';
import AuthSelect from '../components/auth/AuthSelect.vue';
import AuthHeader from '../components/auth/AuthHeader.vue';
import AuthFooter from '../components/auth/AuthFooter.vue';
import AuthInfo from '../components/auth/AuthInfo.vue';

import axios from 'axios';
import * as LANG from './RegisterView.lang.js'

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
		AuthHeader,
		AuthFooter,
		AuthInfo,
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
	created(){},
	mounted(){},
	beforeDestroy(){},
	methods: {
		async onSubmit(){
			if(this.isLoading) return
			this.isLoading = true

			try {
				await axios.post('/register', {
					name: this.name,
					email: this.email,
					phone: this.phone,
					currency: this.currency,
				})
				this.isSended = true
			}
			catch (error) {
				const {status, /* data */} = error.response
				if(status === 422){
					// showErrors

				}
				else{
					// Не удалось создать кабинет, попробуйте позже
				}
				// reset capcha
			}

			this.isLoading = false
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
}
</style>
