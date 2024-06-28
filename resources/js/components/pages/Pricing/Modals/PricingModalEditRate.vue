<template>
	<div class="pricing-buy-modal">
		<h1 class="pricing-buy-title">
			Управление тарифом
		</h1>
		<div class="pricing-buy-description">
			Вы можете подключить к своему тарифу дополнительных сотрудников за {{ count * Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}/мес
		</div>
		<div class="pricing-buy-added-people">
			<div class="pricing-buy-added-content">
				<p class="pricing-buy-added-title">
					Добавление пользователей*
				</p>
				<div class="pricing-buy-added-button-content">
					<button
						class="pricing-buy-added-button"
						@click="decrement"
					>
						<svg
							width="18"
							height="2"
							viewBox="0 0 18 2"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M0.75 1H17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</svg>
					</button>
					<input
						v-model="count"
						class="pricing-buy-added-input"
						type="number"
						value="0"
						min="0"
					>
					<button
						class="pricing-buy-added-button"
						@click="increment"
					>
						<svg
							width="18"
							height="18"
							viewBox="0 0 18 18"
							fill="none"
							xmlns="http://www.w3.org/2000/svg"
						>
							<path
								d="M0.75 9H17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
							<path
								d="M9 0.75V17.25"
								stroke="#333333"
								stroke-width="1.5"
								stroke-linecap="round"
								stroke-linejoin="round"
							/>
						</svg>
					</button>
				</div>
				<p class="pricing-buy-added-clue">
					*Подключаются на весь период оплаты тарифа
				</p>
			</div>
			<p class="pricing-buy-added-price">
				+{{ count * Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}  к оплате
			</p>
		</div>
		<div class="pricing-buy-promo-content" />
		<div class="pricing-buy-total-content">
			<div class="pricing-buy-total-count">
				<div class="pricing-buy-total-count-title">
					<p class="pricing-buy-total-count-price-people">
						Добавление пользователей
					</p>
				</div>
				<p class="pricing-buy-total-count-price">
					{{ count * Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}
				</p>
			</div>
			<p class="pricing-buy-added-clue pricing-buy-added-clue_top">
				{{ count }} пользователя на 24 дня оставшегося тарифа
			</p>
			<div class="pricing-buy-total">
				<p class="pricing-buy-total-title">
					Итого
				</p>
				<p class="pricing-buy-total-price">
					{{ $separateThousands(Math.round(total)) }} {{ currency }}
				</p>
			</div>
		</div>
		<p class="pricing-buy-description-total">
			Далее оплачивать добавленных сотрудников вы сможете при продлении тарифа
		</p>
		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				@click="editToBuyRate"
			>
				Перейти к оплате
			</button>
		</div>
	</div>
</template>

<script>
import {mapState} from 'pinia';
import {usePricingStore} from '../../../../stores/Pricing';

export default {
	name: 'PricingModalEditRate',
	props:{
		currency: {
			type: String,
			default: '₽'
		},
	},
	data() {
		return {
			count: 0,
			info: [],
		}
	},
	computed:{
		...mapState(usePricingStore, ['priceForOnePerson']),
		total(){
			// if (this.promoData?.value) {
			// 	price -= this.promoData.value;
			// }
			return  this.count * Math.round( this.priceForOnePerson[this.currencyCode]) ;
		},
		currencyCode(){
			return ({
				'₽': 'rub',
				'₸': 'kzt',
			})[this.currency]
		},
	},
	created(){
		this.infoFetch()
	},
	methods: {
		increment() {
			this.count++
		},
		decrement() {
			if (this.count > 0) this.count--
		},
		infoFetch(){
			this.axios('tariff/subscriptions').then((response) => {
				this.info = response.data.data
				this.managerPlainPhone()
			})
		},
		editToBuyRate(){
			try{
				if(this.currency !== '₽') return this.submitWalletOne()
				if (this.info.tariff) this.axios.post(`/tariff/subscriptions/${this.info.tariff.tariff_id}`, {
					currency: this.currencyCode,
					// eslint-disable-next-line camelcase
					extra_users_limit: this.count > 0 ? this.count : 0,
				}).then(res => {
					if (res && res.data.data) {
						window.location.assign(res.data.data.url);
					} else {
						console.error('URL not found in response', res);
					}
				})

			}
			catch(error){
				this.$toast.error('Ошибка при попытке оплаты')

			}
		},
		submitWalletOne(){
			try{
				if (this.info.tariff) this.axios.post(`/tariff/subscriptions/${this.info.tariff.subscriptions_id}`, {
					currency: this.currencyCode,
					// eslint-disable-next-line camelcase
					extra_users_limit: this.count > 0 ? this.count : 0,
				}).then(res => {
					if (res && res.data.data) {
						window.location.assign(res.data.data.url);
					} else {
						console.error('URL not found in response', res);
					}
				})
			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},
	},
};
</script>

<style lang="scss" scoped>
@media (min-width: 1600px) {
  .pricing-buy-added-content {
	max-width: 326px !important;
		width: 100%;
	display: flex;
	gap: 8px;
  }
	.pricing-buy-title{
		font-size: 28px !important;
	}
	.pricing-buy-description{
		font-size: 16px !important;
	}

	.pricing-buy-added-people{
		font-size: 16px !important;
	}
  .pricing-buy-added-title {
	font-size: 16px !important;
  }
  .pricing-buy-added-clue {
	font-size: 14px !important;
  }
  .pricing-buy-total-count-price-people {
	font-size: 16px !important;
  }
  .pricing-buy-total-count-price {
	font-size: 16px !important;
  }
  .pricing-buy-description-total {

	font-size: 14px !important;
  }

  .pricing-buy-modal {
	max-width: 564px !important;

	padding: 32px 32px 0 32px !important;
  }
  .pricing-buy-added-price {
		font-size: 16px !important;
	width: 287px !important;
  }
  .pricing-buy-total-title {
	font-size: 28px !important;
	line-height: 36px !important;
  }

  .pricing-buy-total-price {
	font-size: 28px !important;
	line-height: 36px !important;
  }
}
.PricingRates {
	&-options-content {
		display: flex;
		gap: 12px;
		flex-direction: column;
		align-items: flex-start;
		margin-bottom: 24px;
	}

	&-options-title {
		font-size: 16px;
		font-weight: 400;
	}

	&-options-button-group {
		display: flex;
		background-color: #f2f2f2;
		padding: 4px;
		border-radius: 8px;
	}

	&-options-button {
		background-color: #f2f2f2;
		padding: 7px 48px;
		border-radius: 8px;
	}

	&-options-button:focus {
		outline: none;
	}
}

.pricing-choice-tariff {
	display: flex;
	flex-direction: column;
	gap: 8px;
	max-width: 414px;
	margin-bottom: 24px;
}

.pricing-tariff-title {
	color: #737b8a;
	font-size: 16px;
}
.pricing-button-group {
	margin-top: auto;
	font-weight: 500;
	font-size: 16px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}
.pricing-button-connect {
	width: 100%;
	padding: 13px 0;
	text-align: center;
	border-radius: 8px;
	background-color: #0c50ff;
	color: white;
}
.pricing-button-connect:hover{
  background-color: #0d3bb2;

}
.pricing-button-later {
	width: 100%;
	padding: 13px 0;
	text-align: center;
	border-radius: 8px;
	background-color: #ededed;
}

.pricing-buy-modal {
	max-width: 404px;
	width: 100%;
	display: flex;
	flex-direction: column;
	height: 100vh;
	position: relative;
	bottom: 7%;
	padding: 32px 22px 0 22px;
}

.pricing-buy-description-total {
	max-width: 484px;
	color: #737b8a;
	font-size: 12px;
}

.pricing-buy-title {
	font-weight: 600;
	font-size: 20px;
	margin-bottom: 12px;
  color: #333333;

}

.pricing-buy-description {
	font-size: 13px;
  color: #333333;
	margin-bottom: 32px;
}

.pricing-buy-added-people {
	display: flex;
	align-items: center;
	font-size: 14px;
	gap: 14px;
  justify-content: space-between;
	margin-bottom: 24px;
}
.pricing-buy-added-content {
  max-width: 250px;
  width: 100%;
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.pricing-buy-added-title {
	color: #737b8a;
	font-size: 13px;
}

.pricing-buy-added-button-content {
  display: flex;
  gap: 8px;
}

.pricing-buy-added-button {
	background-color: #f2f2f2;
	border-radius: 8px;
	padding: 12px;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 48px;
	height: 48px;
}

.pricing-buy-added-input {
	padding: 14px 16px;
	border-radius: 8px;
	text-align: center;
	border: 1px solid #cdd1db;
	max-width: 88px;
}

.pricing-buy-added-clue {
	color: #737b8a;
	font-size: 12px;
}

.pricing-buy-added-clue_top {
  margin-top: 12px;
}

.pricing-buy-added-price {
	font-weight: 600;
	line-height: 24px;
	font-size: 13px;
  width: 100%;
  margin-bottom: 12px;
}

.pricing-buy-link-promo {
	cursor: pointer;
	color: #0c50ff;
	font-weight: 500;
	margin-bottom: 24px;
}

.pricing-buy-promo-content {
	display: flex;
	gap: 8px;
	margin-bottom: 24px;
}

.pricing-buy-promo-input {
	border-radius: 8px;
	border: 1px solid #afb5c0;
	padding: 14px 16px;
}

.pricing-buy-promo-button {
	background-color: #dbeafe;
	border-radius: 8px;
	padding: 13px 24px;
	color: #0c50ff;
}

.pricing-buy-total-content {
	background-color: #ededed;
	border-radius: 8px;
	padding: 24px;
	margin-bottom: 34px;
}

.pricing-buy-total-count {
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.pricing-buy-total-count-title {
}

.pricing-buy-total-count-price {
	font-size: 13px;
}

.pricing-buy-total {
  margin-top: 20px;
	display: flex;
	justify-content: space-between;
}

.pricing-buy-total-title {
	font-size: 20px;
	font-weight: 600;
	line-height: 30px;
}

.pricing-buy-total-price {
	font-size: 20px;
	font-weight: 600;
	line-height: 30px;
}

.activeOption {
	background-color: white;
	color: #1e40af;
}

.pricing-options-price {
	font-size: 12px;
}

.pricing-modal-promo-button {
	padding: 20px;
	border-radius: 12px;
	display: flex;
	align-items: center;
	outline: none;
	background-color: #f2f2f2;
}

.inpput-promo-active {
	background-color: #f2f2f2;
	outline: none;
}
.pricing-buy-total-count-price-description {
	color: #737b8a;
	font-size: 14px;
	padding-top: 6px;
	margin-bottom: 12px;
}

.pricing-buy-total-count-price-people {
font-size: 13px;
}
</style>
