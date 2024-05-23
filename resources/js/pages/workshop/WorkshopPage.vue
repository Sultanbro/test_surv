<template>
	<div class="workshop">
		<h1 class="workshop__title">
			Оплата заказа
		</h1>
		<div class="wrapper">
			<div style="margin-top: 5%">
				<h2 style="font-size: 20px">
					Оплатить картой
				</h2>
				<div style="display: flex; flex-direction: column">
					<button @click="getAmount">
						<a>
							<img
								width="40"
								height="40"
								src="https://cdn-icons-png.flaticon.com/512/5352/5352016.png"
								alt=""
							>
							Картой банка не из Росиии
						</a>
					</button>
					<button style="margin-top: 2%">
						<a href="https://proeducation.kz/e44NQ/">
							<img
								width="40"
								height="40"
								src="https://gallery.yopriceville.com/downloadfullsize/send/10186"
								alt=""
							>
							Картой Российского банка
						</a>
					</button>
				</div>
			</div>
			<button style="margin-top: 2%">
				<a href="https://pay.kaspi.kz/pay/jjtpkyxq">
					<img
						width="40"
						height="40"
						src="https://cdn-icons-png.flaticon.com/512/262/262042.png"
						alt=""
					>
					Оплата Каспи Рассрочка
				</a>
			</button>
			<button style="margin-top: 2%">
				<a
					href="https://wl.walletone.com/checkout/refill/CreditCard/347773203/NewCard"
				>
					<img
						width="40"
						height="40"
						src="https://cdn-icons-png.flaticon.com/512/262/262042.png"
						alt=""
					>
					Тест
				</a>
			</button>
		</div>
	</div>
</template>

<script>
import { useAmountStore } from '../../stores/amount';
import { mapActions } from 'pinia';

export default {
	name: 'WorkshopPage',
	data() {
		return {
			amountApi: 'https://jobtron.org/api/payments/invoices/wallet-one',
			data: [],
		};
	},
	created() {
		this.openPayForm();
	},
	mounted() {
		this.openPayForm();
	},
	methods: {
		...mapActions(useAmountStore, ['getUsername']),
		openPayForm(link) {
			window.open(link, '_blank');
		},
		createForm(res) {
			const form = document.createElement('form');
			form.style.display = 'none';
			form.method = 'post';
			form.action = res.url;
			Object.keys(res.params).forEach((key) => {
				const inp = document.createElement('input');
				inp.name = key;
				inp.value = res.params[key];
				form.appendChild(inp);
			});
			document.body.appendChild(form);
			form.submit();
		},
		getAmount() {
			return this.axios
				.post(this.amountApi, {
					amount: 100,
					phone: sessionStorage.getItem('phoneForm'),
					sum: sessionStorage.getItem('sumForm'),
				})
				.then((res) => {
					this.createForm(res.data);
				});
		},
	},
};
</script>

<style scoped lang="scss">
@import url("./styles.scss");
</style>
