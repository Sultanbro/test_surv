<template>
	<form
		class="workshopform"
		@submit.prevent="redirect"
	>
		<input
			v-model="phone"
			type="text"
			placeholder="Телефон"
		>
		<input
			v-model="sum"
			type="number"
			min="0"
			placeholder="Сумма выполнения"
		>
		<button type="submit">
			Оплатить
		</button>
	</form>
</template>

<script>
export default {
	data() {
		return {
			phone: '',
			sum: '',
		}
	},
	computed: {
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
		redirect() {
			sessionStorage.setItem('phoneForm', this.phone);
			sessionStorage.setItem('sumForm', this.sum);
			window.location.href = '/payworkshopknowledgebase';
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
	align-items: center;
	gap: 20px;
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
			padding: 4% !important;
			font-size: 28px !important;
		}
		button {
			padding: 5% !important;
			font-size: 28px !important;
		}
	}
}
</style>
