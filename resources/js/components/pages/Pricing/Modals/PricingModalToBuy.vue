<template>
	<div class="pricing-buy-modal">
		<h1
			v-if="tariffStore === priceStore.name"
			class="pricing-buy-title"
		>
			Оплата тарифа {{ priceStore.name }}
		</h1>
		<h1
			v-if="tariffStore !== priceStore.name"
			class="pricing-buy-title"
		>
			Переход на тариф {{ priceStore.name }}
		</h1>
		<div class="pricing-buy-description">
			Выберите необходимые параметры
		</div>
		<div class="PricingRates-options-content">
			<div class="PricingRates-options-title">
				Cрок подключения
			</div>
			<div class="PricingRates-options-button-group">
				<button
					:class="{'activeOption' : activeOption === 1}"
					class="PricingRates-options-button"
					@click="handleClickOptions(1)"
				>
					<p>1 месяц</p>
					<p class="pricing-options-price">
						{{ $separateThousands(Math.round(priceStore.monthly.multiCurrencyPrice[currencyCode])) }} {{ currency }}
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 2}"
					class="PricingRates-options-button"
					@click="handleClickOptions(2)"
				>
					<p>3 месяца</p>
					<p class="pricing-options-price">
						52 908 ₽
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 12}"
					class="PricingRates-options-button"
					@click="handleClickOptions(12)"
				>
					<p>Год</p>
					<p class="pricing-options-price">
						{{ $separateThousands(Math.round(priceStore.annual.multiCurrencyPrice[currencyCode])) }} {{ currency }}
					</p>
				</button>
			</div>
		</div>
		<div class="pricing-choice-tariff">
			<div class="pricing-tariff-title">
				Подключить тариф для пространства
			</div>
			<DropdownPrice
				:options="options"
				placeholder="Выберите компанию"
			/>
		</div>
		<div class="pricing-buy-added-people">
			<div class="pricing-buy-added-content">
				<p class="pricing-buy-added-title">
					Добавление пользователей*
				</p>
				<div class="pricing-buy-added-button-content">
					<button
						class="pricing-buy-added-button"
						@click="removeSumPeople"
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
						:value="sumPeople"
						class="pricing-buy-added-input"
						type="number"
						@input="sumPeople = $event.target.value"
					>
					<button
						class="pricing-buy-added-button"
						@click="addedSumPeople"
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
				+200 ₽ к оплате
			</p>
		</div>
		<p
			v-if="!activePromo"
			class="pricing-buy-link-promo"
			@click="activePromo = true"
		>
			У меня есть промокод
		</p>
		<div
			v-if="activePromo"
			class="pricing-buy-promo-content"
		>
			<input
				v-model="promo"
				placeholder="Введите промокод"
				class="pricing-buy-promo-input"
				:class="{'inpput-promo-active': isPromoLoading}"
			>
			<button
				v-if="!isPromoLoading"
				class="pricing-buy-promo-button"
				@click="activatePromo"
			>
				Применить
			</button>
			<button
				v-else
				class="pricing-modal-promo-button"
				@click="cancelPromo"
			>
				<svg
					width="24"
					height="24"
					viewBox="0 0 24 24"
					fill="none"
					xmlns="http://www.w3.org/2000/svg"
				>
					<g clip-path="url(#clip0_88_2747)">
						<path
							d="M18.75 5.25L5.25 18.75"
							stroke="black"
							stroke-width="1.5"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
						<path
							d="M18.75 18.75L5.25 5.25"
							stroke="black"
							stroke-width="1.5"
							stroke-linecap="round"
							stroke-linejoin="round"
						/>
					</g>
					<defs>
						<clipPath id="clip0_88_2747">
							<rect
								width="24"
								height="24"
								fill="white"
							/>
						</clipPath>
					</defs>
				</svg>
			</button>
		</div>
		<div class="pricing-buy-total-content">
			<div class="pricing-buy-total-count">
				<p class="pricing-buy-total-count-title">
					{{ priceStore.name }}  на {{ activeOption }} {{
						activeOption === 1 ? 'месяц' :
						activeOption === 3 ? 'месяца' :
						'месяцев'
					}}
				</p>
				<p class="pricing-buy-total-count-price">
					{{ $separateThousands(Math.round(getPrice(activeOption))) }} {{ currency }}
				</p>
			</div>
			<div class="pricing-buy-total-count">
				<div class="pricing-buy-total-count-title">
					<p class="pricing-buy-total-count-price-people">
						Добавление пользователей
					</p>
					<p class="pricing-buy-total-count-price-description">
						{{ sumPeople }} пользователя на {{ priceStore.name }} на {{ activeOption }} {{
							activeOption === 1 ? 'месяц' :
							activeOption === 3 ? 'месяца' :
							'месяцев'
						}}
					</p>
				</div>

				<p class="pricing-buy-total-count-price">
					{{ sumPeople * 200 }} ₽
				</p>
			</div>
			<div class="pricing-buy-total">
				<p class="pricing-buy-total-title">
					Итого
				</p>
				<p class="pricing-buy-total-price">
					{{ $separateThousands(Math.round(total)) }} {{ currency }}
				</p>
			</div>
		</div>
		<p
			v-if="tariffStore !== priceStore.name"
			class="pricing-buy-description-total"
		>
			Вы перейдете на тариф стандарт 18.05.2024, сразу же после окончания у вас оплаченного периода тарифа Pro
		</p>
		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				@click="submitPayment"
			>
				Перейти к оплате
			</button>
		</div>
	</div>
</template>

<script>
import DropdownPrice from '../DropDown.vue';
import {mapActions, mapState} from 'pinia';
import {useModalStore} from '../../../../stores/Modal';
import {usePricingPeriodStore} from '../../../../stores/PricingPeriod';
import {usePricingStore} from '../../../../stores/Pricing';

export default  {
	name: 'PricingModalToBuy',
	components: {DropdownPrice},
	props: {
		currency: {
			type: String,
			default: '₽'
		},

	},
	data(){
		return{
			options: [{logo: '/images/price/logo.png', name: '1suol9rbcn'}, {logo: '/images/price/logo.png', name: 'ИП Самозанятость'}],
			activeOption: 1,
			sumPeople: 0,
			promo: '',
			promoData: {},
			isPromoLoading: false,
			selectedRate: null,
			autoPayment: true,
			activePromo: false
		}
	},

	computed:{
		...mapState(useModalStore, ['price']),
		...mapState(usePricingStore, ['priceForUser', 'items']),
		...mapState(usePricingPeriodStore, ['priceStore', 'tariffStore']),

		additionalPrice(){
			if(!this.priceForUser) return 0
			return this.users * this.priceForUser[this.currencyCode] * (this.selectedRate.validity === 'monthly' ? 1 : 12)
		},
		total(){
			if (!this.activeOption) return 0;

			let price;
			if (this.activeOption === 1) {
				price = this.priceStore.monthly.multiCurrencyPrice[this.currencyCode];
			} else if (this.activeOption === 12) {
				price = this.priceStore.annual.multiCurrencyPrice[this.currencyCode];
			} else {
				price = 0;
			}

			const additionalPrice = this.sumPeople * 200;
			let total = price + additionalPrice;
			if (this.promoData?.value) {
				total -= this.promoData.value;
			}
			return total;
		},
		currencyCode(){
			return ({
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'usd'
			})[this.currency]
		},
	},

	methods:{
		...mapActions(useModalStore, ['removeModalActive']),
		...mapActions(usePricingStore, ['fetchPromo', 'postPaymentData']),

		handleClickOptions(id){
			this.activeOption = id
		},
		closeModal(){
			this.removeModalActive()
		},
		addedSumPeople(){
			this.sumPeople+=1
		},
		removeSumPeople(){
			if (this.sumPeople >0) this.sumPeople-=1
		},
		async submitPayment(){
			if(!this.priceStore) return
			if(this.currency !== '₽') return this.submitWalletOne()
			try{
				/* eslint-disable camelcase */
				const { url } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
						this.activeOption === 12 ? this.priceStore.annual.id :
							this.activeOption === 3 ? this.priceStore.id : null,
					extra_users_limit: this.sumPeople > 0 ? this.sumPeople : 0,
					auto_payment: this.autoPayment
				})
				/* eslint-enable camelcase */
				window.location.assign(url)
			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},
		async submitWalletOne(){
			try{
				/* eslint-disable camelcase */
				const { url, params } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
						this.activeOption === 12 ? this.priceStore.annual.id :
							this.activeOption === 3 ? this.priceStore.id : null,
					extra_users_limit: this.sumPeople > 0 ? this.sumPeople : 0,
					auto_payment: this.autoPayment
				})
				const form = document.createElement('form')
				form.method = 'post'
				form.action = url
				Object.keys(params).forEach(key => {
					const inp = document.createElement('input')
					inp.name = key
					inp.value = params[key]
					form.appendChild(inp)
				})
				document.body.appendChild(form)
				form.submit()
			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},
		activatePromo(){
			try {

				this.promoData =  this.fetchPromo(this.promo);
			} catch (error) {
				console.error('Error fetching promo:', error);
			} finally {
				this.isPromoLoading = true;
			}
		},
		getPrice(option) {
			if (option === 1) {
				return this.priceStore.monthly.multiCurrencyPrice[this.currencyCode];
			} else if (option === 2) {
				return 52908;
			} else if (option === 3) {
				return this.priceStore.annual.multiCurrencyPrice[this.currencyCode];
			}
			return 0;
		},
		cancelPromo(){
			this.activePromo=false
			this.isPromoLoading= false
			this.promo = ''
		}
	}
}
</script>

<style lang="scss" scoped>
.PricingRates{
  &-options-content{
	display: flex;
	gap: 12px;
	flex-direction: column;
	align-items: flex-start;
	margin-bottom: 24px;
  }

  &-options-title{
	font-size: 16px;
	font-weight: 400;
  }

  &-options-button-group{
	display: flex;
	background-color: #F2F2F2;
	padding: 4px;
	border-radius: 8px;
  }

  &-options-button{
	background-color: #F2F2F2;
	padding: 7px 48px ;
	border-radius: 8px;
  }

  &-options-button:focus{
	outline: none;

  }
}

.pricing-choice-tariff{
  display: flex;
  flex-direction: column;
  gap: 8px;
	max-width: 414px;
  margin-bottom: 24px;
}

.pricing-tariff-title{
  color: #737B8A;
  font-size: 16px;
}
.pricing-button-group{
  margin-top: auto;
  padding: 0 0 32px 0;
  font-weight: 500;
  font-size: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.pricing-button-connect{
  width: 100%;
  padding: 13px 0;
  text-align: center;
  border-radius: 8px;
  background-color: #0C50FF;
  color: white;
}
.pricing-button-later{
  width: 100%;
  padding: 13px 0;
  text-align: center;
  border-radius: 8px;
  background-color: #EDEDED;
}

.pricing-buy-modal{
  display: flex;
  flex-direction: column;
	height: 100vh;
  position: relative;
  bottom: 7%;
  padding: 40px 40px 0 40px;
}

.pricing-buy-description-total{
	max-width: 484px;
	color: #737B8A;
	font-size: 14px;
}

.pricing-buy-title{
font-weight: 600;
	font-size: 28px;
	margin-bottom: 12px;
}

.pricing-buy-description{
font-size: 16px;
	color: #333333;
  margin-bottom: 32px;
}

.pricing-buy-added-people{
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 24px;
}
.pricing-buy-added-content{
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.pricing-buy-added-title{
  color: #737B8A;
  font-size: 16px;
}

.pricing-buy-added-button-content{
  display: flex;
	gap: 8px;
}

.pricing-buy-added-button{
background-color: #F2F2F2;
	border-radius: 8px;
	padding: 12px;
  display: flex;
  align-items: center;
	justify-content: center;
	width: 48px;
	height: 48px;
}

.pricing-buy-added-input{
padding: 14px 16px;
	border-radius: 8px;
	border: 1px solid #CDD1DB;
	max-width: 88px;
}

.pricing-buy-added-clue{
color: #737B8A;
	font-size: 14px;
}

.pricing-buy-added-price{
font-weight: 600;
	line-height: 24px;
}

.pricing-buy-link-promo{
	cursor: pointer;
color: #0C50FF;
	font-weight: 500;
  margin-bottom: 24px;
}

.pricing-buy-promo-content{
  display: flex;
	gap: 8px;
  margin-bottom: 24px;
}

.pricing-buy-promo-input{
border-radius: 8px;
	border: 1px solid #AFB5C0;
	padding: 14px 16px;
}

.pricing-buy-promo-button{
background-color: #DBEAFE;
	border-radius: 8px;
	padding: 13px 24px;
	color: #0C50FF;
}

.pricing-buy-total-content{
background-color: #EDEDED;
	border-radius: 8px;
	padding: 24px;
  margin-bottom: 34px;
}

.pricing-buy-total-count{
  display: flex;
	justify-content: space-between;
}

.pricing-buy-total-count-title{

}

.pricing-buy-total-count-price{

}

.pricing-buy-total{
  display: flex;
  justify-content: space-between;
}

.pricing-buy-total-title{
font-size: 28px;
	font-weight: 600;
	line-height: 36px;
}

.pricing-buy-total-price{
  font-size: 28px;
  font-weight: 600;
  line-height: 36px;
}

.activeOption{
	background-color: white;
	color: #1E40AF;
}

.pricing-options-price{
	font-size: 12px;
}

.pricing-modal-promo-button{
  padding: 20px;
  border-radius: 12px;
  display: flex;
  align-items: center;
	outline: none;
	background-color: #F2F2F2;
}

.inpput-promo-active{
	background-color: #F2F2F2;
  outline: none;
}
.pricing-buy-total-count-price-description{
	color: #737B8A;
	font-size: 14px;
	padding-top: 6px;
	margin-bottom: 12px;
}

.pricing-buy-total-count-price-people{
	margin-top: 12px;
}
</style>
