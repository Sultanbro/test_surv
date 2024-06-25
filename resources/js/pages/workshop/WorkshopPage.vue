<template>
	<div
		v-if="!isShowQrCodeKaspi"
		class="workshop"
	>
		<img
			class="workshop__background"
			src="/images/bg-workshop.jpg"
			alt=""
		>
		<div class="workshop__wrapper">
			<div class="workshop__title">
				<h1>Оплатите практикум</h1>
				<h2>“Cоздание базы знаний”</h2>
			</div>
			<div class="workshop__payment-method">
				<div
					class="workshop__payment"
					@click="movedPayForm"
				>
					<div class="workshop__payment-wrapper">
						<div class="workshop__payment-name">
							<div class="workshop__payment-title">
								<div>
									<PaymentCardIcon />
									<p>Оплата картой любой страны</p>
								</div>
								<div>
									<VisaCardIcon />
									<MasterCardIcon />
									<OnionPayCardIcon />
								</div>
							</div>
						</div>
						<label class="checkbox-container">
							<input
								type="checkbox"
								checked
							>
							<span class="checkmark" />
							<p>Оплачивая картой, вы соглащаетесь с условиями оферты</p>
						</label>
					</div>
				</div>
			</div>
			<div
				class="workshop__payment-method"
				@click="toggleQrCode"
			>
				<label class="workshop__payment">
					<div class="workshop__payment-wrapper">
						<div class="workshop__payment-name">
							<div class="workshop__payment-title">
								<div>
									<InstallmentIcon />
									<p>Рассрочка или кредит</p>
								</div>
							</div>
						</div>
						<div class="workshop__kaspi">
							<div class="workshop__kaspi-red">
								<img
									src="/images/kaspi-red-logo.png"
									alt=""
								>
							</div>
							<div class="workshop__kaspi-installment">
								<KaspiLogoIcon />
								рассрочка
							</div>
							<div class="workshop__kaspi-gold">
								<img
									src="/images/kaspi-gold-logo.png"
									alt=""
								>
							</div>
							<div class="workshop__kaspi-info">
								<InfoIcon />
								<p>Обязательно указывайте в примечании свой номер</p>
							</div>
						</div>
					</div>
				</label>
			</div>
		</div>
	</div>
	<div
		v-else
		class="workshop"
	>
		<img
			src="/images/qr-codee.png"
			alt=""
		>
	</div>
</template>

<script>
import PaymentCardIcon from '../assets/icons/PaymentCardIcon.vue';
import InstallmentIcon from '../assets/icons/InstallmentIcon.vue';

import MasterCardIcon from '../assets/icons/MasterCardIcon.vue';
import OnionPayCardIcon from '../assets/icons/OnionPayCardIcon.vue';
import VisaCardIcon from '../assets/icons/VisaCardIcon.vue';

import KaspiLogoIcon from '../assets/icons/KaspiLogoIcon.vue';

import InfoIcon from '../assets/icons/InfoIcon.vue';

export default {
	name: 'WorkshopPage',
	components: {
		PaymentCardIcon,
		MasterCardIcon,
		OnionPayCardIcon,
		VisaCardIcon,
		InstallmentIcon,
		KaspiLogoIcon,
		InfoIcon,
	},
	data() {
		return {
			isShowQrCodeKaspi: false,
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
		toggleQrCode() {
			this.isShowQrCodeKaspi = true;
		},
		setMetaViewport() {
			const meta = document.createElement('meta');
			meta.setAttribute('name', 'viewport');
			meta.setAttribute('content', 'width=device-width, initial-scale=1.0');
			document.head.appendChild(meta);
		},
		movedPayForm() {
			window.location.href =
				'https://jobtron.proeducation.kz/?invoice_id=f6c2bfb0a1f94b027f5f9bf88c29f35d&paylink=1';
		},
	},
};
</script>

<style scoped lang="scss">
%installmentButton {
	border: 1px solid #658cda33;
	border-radius: 4.86px;
	width: 91.18px;
	height: 37.08px;
	background-color: #ffffff;
	display: flex;
	justify-content: center;
	align-items: center;
	overflow: hidden;
}

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
		opacity: 0.33;
		border-radius: 60px;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	&__wrapper {
		z-index: 2;
		.workshop__title {
			text-align: center;
			font-size: 45px;
			color: #5d73c2;
			font-size: 35px;
			font-weight: 600;
			h1 {
				font-size: 45px;
				font-weight: bold;
			}
		}

		.workshop__payment-method {
			margin-top: 4px;
			background-color: #ffffff;
			border-radius: 20px;
			&:hover {
				outline: 1px solid #156ae8;
			}
			.workshop__payment {
				margin-top: 16px;
				cursor: pointer;
				display: flex;
				padding: 4%;
				width: 569px;
				.workshop__payment-checkbox {
					-webkit-appearance: none;
					width: 25px;
					min-width: 25px;
					height: 25px;
					min-height: 20px;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					border-radius: 50%;
					background-color: #eef6ffcc;
					border: 1px solid #eff5fc;
					position: relative;
					cursor: pointer;
					margin-right: 10px;
					transition: background-color 0.3s ease;
				}

				.workshop__payment-checkbox:checked {
					background-color: #3781ef;
					border-color: #3781ef;
				}

				.workshop__payment-checkbox:checked:after {
					content: "";
					display: block;
					position: absolute;
					top: 6px;
					left: 9px;
					width: 5px;
					height: 11px;
					border: solid #fff;
					border-width: 0 2px 2px 0;
					opacity: 1;
					transform: rotate(40deg);
				}

				.workshop__payment-checkbox:hover {
					border-color: #ccc;
				}
				.workshop__payment-wrapper {
					width: 100%;
					.workshop__payment-name {
						background-color: #ffffff;
						box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;
						display: flex;
						justify-content: space-between;
						flex-direction: column;
						padding: 3%;
						border-radius: 8px;
						width: 100%;
						.workshop__payment-title {
							display: flex;
							justify-content: space-between;
							div {
								display: flex;
								p {
									font-size: 16px;
									padding-left: 5px;
								}
							}
						}
					}
					label {
						margin-top: 2%;
						display: flex;
						gap: 5px;
						p {
							font-size: 12px;
							padding-left: 5%;
						}
					}

					.workshop__kaspi {
						margin-top: 8px;
						display: flex;
						justify-content: space-between;
						align-items: center;
						flex-wrap: wrap;
						.workshop__kaspi-red {
							@extend %installmentButton;
							overflow: hidden;
							img {
								width: 79px;
								height: 46px;
							}
						}

						.workshop__kaspi-installment {
							@extend %installmentButton;
							flex-direction: column;
						}

						.workshop__kaspi-gold {
							@extend %installmentButton;
							img {
								width: 74.14px;
								height: 28.7px;
							}
						}

						.workshop__kaspi-info {
							display: flex;
							align-items: start;
							gap: 2%;
							p {
								width: 157px;
								font-weight: 400;
							}
						}
					}
				}

				.checkbox-container {
					display: inline-block;
					position: relative;
				}

				.checkbox-container input {
					position: absolute;
					opacity: 0;
					cursor: pointer;
					width: 0;
					height: 0;
				}

				.checkmark {
					position: absolute;
					top: 0px;
					left: 0px;
					height: 16px;
					width: 16px;
					background-color: #ffffff;
					border: 1px solid #000;
					cursor: pointer;
					border-radius: 3px;
				}

				.checkbox-container input:checked ~ .checkmark {
					background-color: white;
				}

				.checkbox-container input:checked ~ .checkmark:after {
					display: block;
				}

				.checkmark:after {
					content: "";
					position: absolute;
					display: none;
					left: 6px;
					top: 3px;
					width: 3px;
					height: 7px;
					border: solid #000;
					border-width: 0 1px 1px 0;
					transform: rotate(45deg);
				}
			}
		}
	}
}

@media (max-width: 1000px) {
	.workshop {
		background-color: #eeeeee;
		&__background {
			width: 100%;
			height: 100vh;
			border-radius: 0px;
		}
		&__wrapper {
			.workshop__payment-method {
				margin: 3%;
				.workshop__payment {
					width: auto;
					.workshop__payment-wrapper {
						label {
							p {
								margin-left: 3%;
							}
						}
						.workshop__kaspi {
							.workshop__kaspi-info {
								margin-top: 1%;
								p {
									width: 100%;
									font-weight: 400;
								}
							}
						}
					}
				}
			}
		}
	}
}
</style>
