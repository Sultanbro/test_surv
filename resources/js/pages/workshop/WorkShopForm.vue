<template>
	<form
		class="workshopform"
		@submit.prevent.stop="saveUserData"
	>
		<h1 class="workshopform__title">
			Оставить заявку на практикум
		</h1>
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
		<button type="submit">
			Отправить
		</button>
	</form>
</template>

<script>
/* eslint-disable camelcase */
export default {
	name: 'WorkShopForm',
	data() {
		return {
			name: '',
			phone: '',
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
.workshopform {
	max-width: 500px;
	margin: 0 auto;
	min-height: 100vh;
	display: flex;
	flex-direction: column;
	justify-content: center;
	gap: 20px;

	h1 {
		text-align: center;
		font-size: 30px;
		font-weight: bold;
	}
	input[type="text"],
	input[type="number"] {
		border-radius: 10px;
		font-size: 15px;
		padding: 3%;
		border: 1px solid #646464;
	}
	button {
		background-color: rgb(0, 145, 255);
		color: white;
		border-radius: 15px;
		padding: 3%;
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
		input[type="text"],
		input[type="number"] {
			padding: 2% !important;
			font-size: 23px !important;
		}
		button {
			padding: 5% !important;
			font-size: 23px !important;
		}
		h1 {
			text-align: center;
		}
	}
}
</style>
