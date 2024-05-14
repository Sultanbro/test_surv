<template>
	<div class="workshop">
		<button @click="openPayForm">
			Оплата картой Казахстана
		</button>
	</div>
</template>

<script>
export default {
	name: 'WorkshopPage',
	methods: {
		openPayForm() {
			window.open(
				'https://wl.walletone.com/checkout/refill/CreditCard/347428001/NewCard',
				'_blank'
			);
		},
	},
};
</script>

<style scoped lang="scss">
.workshop {
	font-size: 25px;
	min-height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
	button {
		background-color: #33b4ff;
		padding: 1%;
		color: white;
		border-radius: 10px;
		transition: all ease 100ms;
		&:hover {
			background-color: #0193e8;
		}
		&:focus {
			outline: none;
		}
		a {
			color: white;
		}
	}
}
</style>
