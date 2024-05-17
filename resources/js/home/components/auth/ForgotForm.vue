<template>
	<form
		class="ForgotForm"
		@submit.prevent="onSubmit"
	>
		<template v-if="isSended">
			<AuthTitle>
				{{ lang.sendedtitle }}
			</AuthTitle>
			<AuthSubTitle>
				{{ lang.sendedsubtitle1 }},
				{{ email }}
				{{ lang.sendedsubtitle2 }}
			</AuthSubTitle>
			<AuthGoToMailVue :email="email">
				{{ lang.goToMail }}
			</AuthGoToMailVue>
		</template>
		<template v-else>
			<AuthSubTitle>
				<a
					href="javascript:void(0)"
					class="fw500"
					@click="$emit('login')"
				>
					{{ lang.back }}
				</a>
			</AuthSubTitle>
			<AuthTitle>
				{{ lang.title }}
			</AuthTitle>
			<AuthSubTitle>
				{{ lang.subtitle }}
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
			<div class="ForgotForm-button">
				<AuthSubmit>
					{{ lang.submit }}
				</AuthSubmit>
			</div>
		</template>
	</form>
</template>

<script>
import AuthInput from './AuthInput.vue';
import AuthSubmit from './AuthSubmit.vue';
import AuthTitle from './AuthTitle.vue';
import AuthSubTitle from './AuthSubTitle.vue';
import AuthGoToMailVue from './AuthGoToMail.vue';

import axios from 'axios';
import * as LANG from './ForgotForm.lang.js';

export default {
	name: 'ForgotForm',
	components: {
		AuthInput,
		AuthSubmit,
		AuthTitle,
		AuthSubTitle,
		AuthGoToMailVue,
	},
	props: {},
	data() {
		return {
			email: '',
			errors: {
				email: '',
			},
			isSended: false,
			resendTimer: 0,
			resendIterval: null,
		};
	},
	computed: {
		lang() {
			return LANG[this.$root.$data.lang || 'ru'];
		},
	},
	watch: {},
	created() {},
	mounted() {
		this.resendIterval = setInterval(() => {
			this.resendTimer && --this.resendTimer;
		}, 1000);
	},
	beforeDestroy() {
		clearInterval(this.resendIterval);
	},
	methods: {
		async onSubmit() {
			this.isSended = true;
			// if (this.resendTimer) return;
			// this.isLoading = true;
			try {
				await axios.post(
					'/setting/reset',
					{
						email: this.email,
					},
					{
						headers: {
							From: this.email,
						},
					}
				);
				// if (data.success) {
				// 	this.isSended = true;
				// 	this.resendTimer = 60;
				// 	//toast? На вашу почту отправлен новый пароль!
				// 	return;
				// }
				// this.errors.email = "Вы не зарегистрированы в нашей системе!";
			} catch (error) {
				this.errors.email = 'Вы не зарегистрированы в нашей системе!';
			}
			// this.isLoading = false;
		},
	},
};
</script>

<style lang="scss">
.ForgotForm {
	&-inputs {
		margin-top: 20px;
	}
	&-button {
		margin-top: 32px;
	}
}
</style>
