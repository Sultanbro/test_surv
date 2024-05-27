<template>
	<form
		class="workshopform"
		@submit.prevent.stop="saveUserData"
	>
		<input
			v-model="name"
			type="text"
			placeholder="Ваше имя"
		>
		<input
			v-model="phone"
			type="text"
			placeholder="Укажите номер телефона"
		>
		<div
			v-if="error"
			class="workshopform__error"
		>
			Введите корректный номер телефона
		</div>
		<button type="submit">
			Оплатить
		</button>
	</form>
</template>

<script>
export default {
	name: 'WorkShopForm',
	data() {
		return {
			name: '',
			phone: '',
			phoneNumberRegex: /^\+?[1-9]\d{1,14}$/,
			error: false,
		};
	},
	mounted() {
		this.$nextTick(() => {
			this.setMetaViewport();
		});
	},
	updated() {
		this.setMetaViewport();
	},
	methods: {
		saveUserData() {
			if (this.validatePhone()) {
				const userSaveApi = 'https://jobtron.org/api/v1/invoices';

				this.axios.post(userSaveApi, {
					amount: '1',
					name: this.name,
					phone: this.phone,
				});
				
			} else {
				this.error = true;
			}
		},
		setMetaViewport() {
			const meta = document.createElement('meta');
			meta.setAttribute('name', 'viewport');
			meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
			document.head.appendChild(meta);
		},
		validatePhone() {
			if (this.phoneNumberRegex.test(this.phone) && this.phone.length >= 11) {
				return true;
			}
			return false;
		},
	},
};
</script>

<style scoped lang="scss">
.workshopform {
	max-width: 500px;
	margin: 0 auto;
	min-height: 100vh;
	display: flex;
	flex-direction: column;
	justify-content: center;
	gap: 20px;
	&__error {
		font-size: 15px;
		color: red;
	}
	input[type="text"],
	input[type="number"] {
		border-radius: 10px;
		font-size: 15px;
		padding: 3%;
		width: 90%;
		border: 1px solid #646464;
	}
	button {
		background-color: rgb(0, 145, 255);
		color: white;
		border-radius: 15px;
		padding: 3%;
		width: 90%;
		font-size: 15px;
		transition: all ease-in 100ms;
		&:hover {
			background-color: rgb(0, 122, 215);
		}
	}
}

@media (max-width: 430px) {
	.workshopform {
		margin: 1%;
		min-height: 100vh;
		display: flex;
		justify-content: center;
		align-items: center;
		input[type="text"],
		input[type="number"] {
			padding: 2% !important;
			font-size: 23px !important;
		}
		button {
			padding: 5% !important;
			font-size: 23px !important;
		}
	}
}
</style>
