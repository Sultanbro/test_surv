<template>
	<div class="workshop">
		<h1 class="workshop__title">
			Оплата: Практикума по созданию Базы Знаний
		</h1>
		<div class="workshop__wrapper">
			<h2>Оплатить картой</h2>
			<button>
				<a href="https://wl.walletone.com/checkout/refill/CreditCard/347953359/NewCard">
					<img
						class="workshopform__img"
						src="https://cdn-icons-png.flaticon.com/512/5352/5352016.png"
						alt=""
					>
					Картой банка не из Росиии
				</a>
			</button>
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
			<p>Оплачивая картой вы соглашаетесь с условями <strong>оферты</strong></p>

			<h2>Купить в рассрочку или в кредит</h2>
			<button>
				<a href="https://pay.kaspi.kz/pay/jjtpkyxq">
					Оплата Каспи
					<img
						class="workshopform__kaspi-img"
						src="https://static.cdn-kaspi.kz/kkz/mainmvc/reswp/img/start/myBank/Red.png"
						alt=""
					>
					<img
						class="workshopform__kaspi-img"
						src="https://static.cdn-kaspi.kz/kkz/mainmvc/reswp/img/start/myBank/Kredit.png"
						alt=""
					>
					<img
						class="workshopform__kaspi-img"
						src="https://papik.pro/grafic/uploads/posts/2023-04/thumbs/1681564231_papik-pro-p-kaspi-logotip-vektor-30.png"
						alt=""
					>
				</a>
			</button>
			<p>
				Обязательно указывайте в примечании свой <strong>номер телефона</strong>
			</p>
		</div>
	</div>
</template>

<script>
export default {
	name: 'WorkshopPage',
	data() {
		return {
			data: [],
		};
	},
	created() {},
	mounted() {
		this.$nextTick(() => {
			this.setMetaViewport();
		});
	},
	updated() {
		this.setMetaViewport();
	},
	methods: {
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
		// getAmount() {
		// 	return this.axios
		// 		.post(this.amountApi, {
		// 			phone: sessionStorage.getItem('phoneWorkShopForm'),
		// 			name: sessionStorage.getItem('phoneForm'),
		// 		})
		// 		.then((res) => {
		// 			this.createForm(res.data);
		// 		});
		// },
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
	align-items: center;
	font-size: 20px;
	.workshop__title {
		font-weight: bold;
		font-size: 35px;
	}
	&__wrapper {
		margin-top: 3%;
		h2 {
			margin-top: 3%;
			font-size: 20px;
			font-weight: bold;
		}
		h2:nth-of-type(2) {
			margin-top: 10%;
		}
		button {
			margin-top: 4%;
			width: 100%;
			font-weight: bold;
			padding: 2%;
			background-color: #fabf0f;
			border-radius: 10px;
			transition: all ease 100ms;
			.workshopform__img {
				width: 50px;
				height: 50px;
				border-radius: 50%;
			}
			.workshopform__kaspi-img {
				width: 56px;
				height: 42px;
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
			margin-top: 1%;
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
	}
}
</style>
