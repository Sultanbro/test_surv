<template>
	<button
		class="AuthSubmit"
		@click="openEmailService"
	>
		<slot />
	</button>
</template>

<script>
export default {
	name: 'AuthGoToMail',
	props: {
		email: {
			type: String,
			required: true,
			default: ''
		}
	},
	methods: {
		openEmailService () {
			const domain = this.email.split('@')[1];
			let emailServiceURL = '';
			switch (domain) {
			case 'gmail.com':
				emailServiceURL = 'https://mail.google.com/';
				break;
			case 'yandex.ru':
				emailServiceURL = 'https://mail.yandex.ru/';
				break;
			case 'mail.ru':
				emailServiceURL = 'https://mail.ru/';
				break;
			default:
				alert('Технические затруднения')
				break;
			}
			if (emailServiceURL) {
				window.open(emailServiceURL, '_blank');
			}
		}
	},
}
</script>

<style lang="scss">
.AuthSubmit{
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 4px;

	width: 100%;
	height: 48px;
	padding: 16px;
	border: none;

	color: #fff;

	border-radius: 8px;
	background-color: #0C50FF;
	&:hover{
		background-color: #4A7CFD;
	}
	&:focus{
		outline: none;
		background-color: #4A7CFD;
	}
	&_disabled{
		background-color: #777;
		&:hover{
			background-color: #777;
		}
		button{
			cursor: wait;
		}
	}
}
</style>
