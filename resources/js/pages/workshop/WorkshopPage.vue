<template>
	<div class="workshop">
		<h1 class="workshop__title">
			Оплата заказа: Практикум по созданию Базы Знаний
		</h1>
		<div class="workshop__wrapper">
			<h2>Оплатить картой</h2>
			<button @click="getAmount">
				<a>
					<img
						class="workshopform__img"
						src="https://cdn-icons-png.flaticon.com/512/5352/5352016.png"
						alt=""
					>
					Картой банка не из Росиии
				</a>
			</button>
			<p>Оплачивая картой вы соглашаетесь с условями <strong>оферты</strong></p>
			<button>
				<a href="https://proeducation.kz/e44NQ/">
					<img
						class="workshopform__img"
						src="https://gallery.yopriceville.com/downloadfullsize/send/10186"
						alt=""
					>
					Картой Российского банка
				</a>
			</button>
			<h2>Купить в рассрочку или в кредит</h2>
			<button>
				<a href="https://pay.kaspi.kz/pay/jjtpkyxq">
					<img
						class="workshopform__img"
						src="https://upload.wikimedia.org/wikipedia/ru/a/aa/Logo_of_Kaspi_bank.png"
						alt=""
					>
					Оплата Каспи
				</a>
			</button>
			<p>Обязательно указывайте в примечании свой <strong>номер телефона</strong></p>
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
		this.$nextTick(() => {
			this.setMetaViewport();
		});
	},
	updated() {
		this.setMetaViewport();
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
					amount: sessionStorage.getItem('sumForm'),
					phone: sessionStorage.getItem('phoneForm'),
				})
				.then((res) => {
					this.createForm(res.data);
				});
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
	margin: 2% 4% 0% 4%;
	display: flex;
	flex-direction: column;
	justify-content: center;
	font-size: 20px;
	.workshop__title {
		font-weight: bold;
		font-size: 35px;
	}
	&__wrapper {
		h2 {
			margin-top: 1%;
			font-size: 20px;
			font-weight: bold;
		}
		button {
			margin-top: 1%;
			width: 100%;
			font-weight: bold;
			padding: 1%;
			background-color: #fabf0f;
			border-radius: 10px;
			transition: all ease 100ms;
			display: flex;
			flex-wrap: wrap;
			z-index: 1;
			.workshopform__img {
				width: 50px;
				height: 50px;
				border-radius: 50%;
			}
			&:hover {
				background-color: #e8ae02;
			}
			&:focus {
				outline: none;
			}
			a {
				color: black;
				display: flex;
				align-items: center;
				gap: 5px;
			}
		}
		p {
			font-size: 12px;
			background-color: #f1d070f9;
			padding: 0% 1%;
			z-index: 0;
			border-radius: 0px 0px 9px 8px;
			-webkit-border-radius: 0px 0px 9px 8px;
			-moz-border-radius: 0px 0px 9px 8px;
			margin-top: -0.5%;
		}
	}
}

@media (max-width: 1000px) {
	.workshop {
		margin: 2% 4% 0% 4%;
		font-size: 20px;
		.workshop__title {
			font-size: 35px;
		}
		&__wrapper {
			h2 {
				margin-top: 7%;
				font-size: 20px;
			}
			button {
				margin-top: 4%;
				width: 100%;
				font-weight: bold;
				padding: 2%;
				&:hover {
					background-color: #e8ae02;
				}
				&:focus {
					outline: none;
				}
				a {
					color: black;
					display: flex;
					align-items: center;
					gap: 5px;
				}
			}
		}
		p {
			margin-top: -2%;
		}
	}
}
</style>
