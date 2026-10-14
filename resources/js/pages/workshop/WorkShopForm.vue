<template>
	<div class="workshop">
		<img
			class="workshop__background"
			src="/images/bg-workshop.jpg"
			alt=""
		>
		<div class="workshop__wrapper">
			<h1 class="workshop__title">
				Оставить заявку на практикум
			</h1>
			<form
				class="workshop__form"
				@submit.prevent.stop="saveUserData"
			>
				<label>
					<UserNameIcon class="workshop__icon" />
					<input
						v-model="name"
						type="text"
						placeholder="Ваше имя"
					>
				</label>
				<label>
					<PhoneIcon class="workshop__icon" />
					<input
						v-model="phone"
						type="text"
						placeholder="Укажите номер телефона"
					>
				</label>
				<button type="submit">
					Отправить
				</button>
			</form>
		</div>
	</div>
</template>

<script>
/* eslint-disable camelcase */
import UserNameIcon from '../assets/icons/UserNameIcon.vue';
import PhoneIcon from '../assets/icons/PhoneIcon.vue';

export default {
	name: 'WorkShopForm',
	components: {
		UserNameIcon,
		PhoneIcon,
	},
	data() {
		return {
			name: '',
			phone: '',
		};
	},
	mounted() {
		this.setMetaViewport();
	},
	updated() {
		this.setMetaViewport();
	},
	methods: {
		async saveUserData() {
			const userSaveApi = 'https://jobtron.org/api/v1/invoices';

			const isSaveUser = this.axios.post(userSaveApi, {
				payer_name: this.name,
				payer_phone: this.phone,
				url: window.location.href,
				name: 'База знаний',
				provider: 'prodamus',
			});

			if (isSaveUser) {
				window.location.href = '/payworkshopknowledgebase';
			}
		},
		setMetaViewport() {
			const meta = document.createElement('meta');
			meta.setAttribute('name', 'viewport');
			meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
			document.head.appendChild(meta);
		},
	},
};
</script>

<style scoped lang="scss">
.workshop {
	min-height: 100vh;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	position: relative;

	&__background {
		z-index: 1;
		width: 1735px;
		height: 832px;
		opacity: 0.3;
		border-radius: 60px;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	&__wrapper {
		z-index: 2;
		background-color: white;
		border-radius: 40px;
		padding: 55px;
		.workshop__title {
			max-width: 424px;
			text-align: center;
			font-size: 45px;
			font-weight: bold;
			color: #5d73c2;
		}

		.workshop__form {
			display: flex;
			flex-direction: column;
			label {
				position: relative;
				.workshop__icon {
					position: absolute;
					top: 33px;
					left: 14px;
				}
				input {
					width: 100%;
					font-size: 16px;
					margin-top: 16px;
					border-radius: 8px;
					padding: 16px 16px 16px 43px;
					border: 2px solid #eef2f9;
				}
			}

			button {
				font-size: 14px;
				padding: 8px 16px 8px 16px;
				border-radius: 4px;
				font-weight: 600;
				color: white;
				height: 60px;
				margin-top: 32px;
				border-radius: 16px;
				background-color: #3b4c84;
			}
		}
	}
}

@media (max-width: 600px) {
	.workshop {
		&__background {
			display: none;
		}
		&__wrapper {
			border-radius: 0px;
			width: 100%;
			height: 100vh;
			.workshop__title {
				max-width: 100%;
			}
		}
	}
}
</style>
