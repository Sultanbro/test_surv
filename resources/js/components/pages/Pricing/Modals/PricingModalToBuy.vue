<template>
	<div class="pricing-buy-modal">
		<h1
			class="pricing-buy-title"
		>
			Оплата тарифа {{ priceStore.name }}
		</h1>
		<input :value="word">
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
					@click="$emit('updateOption', 1)"
				>
					<p class="pricing-options-price">
						1 месяц
					</p>
					<p class="pricing-options-price">
						{{ $separateThousands(Math.round(priceStore.monthly.multiCurrencyPrice[currencyCode])) || 0 }} {{ currency }}
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 3}"
					class="PricingRates-options-button"
					@click="$emit('updateOption', 3)"
				>
					<p class="pricing-options-price">
						3 месяца
					</p>
					<p class="pricing-options-price">
						{{ $separateThousands(Math.round(priceStore.threeMonthly.multiCurrencyPrice[currencyCode])) || 0 }} {{ currency }}
					</p>
				</button>
				<button
					:class="{'activeOption' : activeOption === 12}"
					class="PricingRates-options-button"
					@click="$emit('updateOption', 12)"
				>
					<div class="pricing-option-discount">
						<p class="pricing-options-price">
							Год
						</p>
						<div class="pricing-discount">
							-20%
						</div>
					</div>
					<p class="pricing-options-price">
						{{ $separateThousands(Math.round(priceStore.yearly.multiCurrencyPrice[currencyCode])) || 0 }} {{ currency }}
					</p>
				</button>
			</div>
		</div>
		<div class="pricing-choice-tariff">
			<div class="pricing-tariff-title">
				Подключить тариф для пространства
			</div>
			<DropdownPrice
				:selected-option="selectedOption"
				:options="options.data"
				placeholder="Выберите компанию"
				@update="updateSelected"
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
				+{{ sumPeople * Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}  к оплате
			</p>
		</div>
		<p
			v-if="!activePromo"
			class="pricing-buy-link-promo"
			@click="isActivePromo"
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
			<div class="pricing-buy-total-count-title">
				<div class="pricing-buy-total-count">
					<p class="pricing-buy-total-count-price-people">
						Добавление пользователей
					</p>
					<p class="pricing-buy-total-count-price">
						{{ sumPeople * Math.round(priceForOnePerson[currencyCode]) }} {{ currency }}
					</p>
				</div>
				<p class="pricing-buy-total-count-price-description">
					{{ sumPeople }} пользователя на {{ priceStore.name }} на {{ activeOption }} {{
						activeOption === 1 ? 'месяц' :
						activeOption === 3 ? 'месяца' :
						'месяцев'
					}}
				</p>
			</div>
			<div
				v-if="promoRate"
				class="pricing-promo"
			>
				<p class="pricing-promo-title">
					Скидка по промокоду
				</p>
				<p class="pricing-promo-text">
					{{ $separateThousands(Math.round(promoDiscount)) }} {{ currency }}
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
			v-if="tariffStore !== priceStore.name && tariffStore !== 'Бесплатный'"
			class="pricing-buy-description-total"
		>
			Вы перейдете на тариф {{ priceStore.name }}, сразу же после окончания у вас оплаченного периода тарифа
			{{ tariffStore }}
		</p>
		<div class="pricing-button-group">
			<button
				class="pricing-button-connect"
				@click="submitPayment"
			>
				Перейти к оплате <CustomPriceSpinner v-if="isLoading" />
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
import {useValidityStore} from '../../../../stores/api/pricing/validity';
import {useCurrentFetchStore} from '../../../../stores/currentFetch';
import CustomPriceSpinner from '../../../ui/Spinners/CustomSpinner.vue';

export default  {
	name: 'PricingModalToBuy',
	components: {CustomPriceSpinner, DropdownPrice},
	props: {
		currency: {
			type: String,
			default: '₽'
		},

		activeOption:{
			type: Number,
			default: 3
		},
	},
	data(){
		return{
			word:'asdasdasd',
			options: [],
			sumPeople: 0,
			promo: '',
			promoData: {},
			isPromoLoading: false,
			selectedRate: null,
			autoPayment: true,
			activePromo: false,
			data: [],
			selectedOption: null,
			promoFetch: [],
			promoRate: '',
			promoDiscount: 0,
			isLoading: false,
		}
	},

	computed:{
		...mapState(useModalStore, ['price']),
		...mapState(usePricingStore, ['priceForUser', 'items']),
		...mapState(usePricingPeriodStore, ['priceStore', 'tariffStore', 'tariffId']),
		...mapState(usePricingStore, ['priceForOnePerson']),
		...mapState(useValidityStore, ['validity', 'date']),
		...mapState(useCurrentFetchStore, ['current']),

		additionalPrice(){
			if(!this.priceForUser) return 0
			return this.users * this.priceForUser[this.currencyCode] * (this.selectedRate.validity === 'monthly' ? 1 : 12)
		},
		total(){
			if (!this.activeOption) return 0;

			let price = Number(this.getPrice(this.activeOption));
			let discount = 0
			if (this.promoRate) {
				discount = (Number(this.promoRate[0].rate) / 100) * price;
				// eslint-disable-next-line vue/no-side-effects-in-computed-properties
				this.promoDiscount= discount
				price -= discount;
			}

			return price +=   this.sumPeople * Math.round( this.priceForOnePerson[this.currencyCode]) ;
		},
		currencyCode(){
			return ({
				'₽': 'rub',
				'₸': 'kzt',
			})[this.currency]
		},

	},
	created(){
		this.fetchDateCorses()
		this.currentFetch()
		this.selectedOption = this.current
	},
	mounted() {
		this.getPriceData()
	},
	methods:{
		...mapActions(useModalStore, ['removeModalActive']),
		...mapActions(usePricingStore, ['fetchPromo', 'postPaymentData', 'postPaymentExtendData']),
		...mapActions(useValidityStore, ['fetchDateCorses']),
		updateSelected(option) {
			this.selectedOption = option;
		},
		currentFetch(){
			this.axios.get('/portal/current').then(res => {
				this.selectedOption = res.data.data.tenant_id
			})
		},
		async getPriceData() {
			try {
				this.axios.get('/tenants').then((response) => {
					this.options = response.data;
				})
			} catch (error) {
				console.error('Failed to fetch price data:', error);
			}
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
			this.isLoading = true
			if(!this.priceStore) return
			if(this.currency !== '₽') return this.submitWalletOne()

			try{
				/* eslint-disable camelcase */
				if (this.tariffStore === this.price && this.tariffId.payment_id !=='trial'){
					this.axios.post(`/tariff/subscriptions/${this.tariffId.id}/extend`, {
						currency: this.currencyCode,
						// eslint-disable-next-line camelcase
						extra_users_limit: this.count > 0 ? this.count : 0,
						tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
							this.activeOption === 12 ? this.priceStore.yearly.id :
								this.activeOption === 3 ? this.priceStore.threeMonthly.id : null,
						promo_code: this.promoRate[0]?.code || null,
					}).then(res => {

						if (res && res.data.data.url) {
							window.location.assign(res.data.data.url);
							this.isLoading = false

						} else {
							console.error('URL not found in response', res);
						}
					})
				}
				else{
					const { url } = await this.postPaymentData({
						currency: this.currencyCode,
						tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
							this.activeOption === 12 ? this.priceStore.yearly.id :
								this.activeOption === 3 ? this.priceStore.threeMonthly.id : null,
						extra_users_limit: this.sumPeople > 0 ? this.sumPeople : 0,
						auto_payment: this.autoPayment,
						tenant_id: this.selectedOption.id,
						promo_code: this.promoRate[0]?.code || null,
					})
					/* eslint-enable camelcase */
					window.location.assign(url)
					this.isLoading = false


				}

			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},
		async submitWalletOne(){
			try{
				/* eslint-disable camelcase */
				if (this.tariffStore === this.price  && this.tariffId.payment_id !=='trial'){
					const { url, params } = await this.postPaymentExtendData({
						currency: this.currencyCode,
						tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
							this.activeOption === 12 ? this.priceStore.yearly.id :
								this.activeOption === 3 ? this.priceStore.threeMonthly.id : null,
						extra_users_limit: this.sumPeople > 0 ? this.sumPeople : 0,
						auto_payment: this.autoPayment,
						tenant_id: this.selectedOption.id,
						promo_code: this.promoRate[0]?.code || null,
					}, this.tariffId.id);
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
				else{
					const { url, params } = await this.postPaymentData({
						currency: this.currencyCode,
						tariff_id: this.activeOption === 1 ? this.priceStore.monthly.id :
							this.activeOption === 12 ? this.priceStore.yearly.id :
								this.activeOption === 3 ? this.priceStore.threeMonthly.id : null,
						extra_users_limit: this.sumPeople > 0 ? this.sumPeople : 0,
						auto_payment: this.autoPayment,
						tenant_id: this.selectedOption.id,
						promo_code: this.promoRate[0]?.code || null,
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

					/* eslint-enable camelcase */
					this.isLoading = false

				}

			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},

		getPromo(){
			this.axios('promo-codes').then(res=> {
				this.promoFetch = res.data
			})
		},
		isActivePromo(){
			this.activePromo = true
			this.getPromo()
		},
		activatePromo(){
			try {
				this.promoRate = this.promoFetch.data.filter(item => item.code === this.promo)
				if (this.promoRate) 	this.$toast.success('промокод успешно введен')

				this.isPromoLoading = true;

			} catch (error) {
				console.error('Error fetching promo:', error);
				this.$toast.error('Ошибка при ввода промокода')
				this.activePromo = false
				this.promo = ''
			}
		},
		getPrice(option) {
			if (option === 1) {
				return this.priceStore.monthly.multiCurrencyPrice[this.currencyCode];
			} else if (option === 3) {
				return this.priceStore.threeMonthly.multiCurrencyPrice[this.currencyCode];
			} else if (option === 12) {
				return this.priceStore.yearly.multiCurrencyPrice[this.currencyCode];
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
	@media (min-width: 1600px) {
		.PricingRates {
		&-options-content {
			display: flex !important;
			gap: 12px !important;
			flex-direction: column !important;
			align-items: flex-start !important;
			margin-bottom: 24px !important;
		}

		&-options-title {
			font-size: 16px !important;
			font-weight: 400 !important;
		}

		&-options-button-group {
			display: flex !important;
			background-color: #f2f2f2 !important;
			padding: 4px !important;
			border-radius: 8px !important;
		}


		&-options-button {
			background-color: #f2f2f2 !important;
			padding: 7px 40px !important;
			border-radius: 8px !important;
			font-size: 16px !important;
		}

		&-options-button:focus {
			outline: none !important;
		}
		}
		.pricing-discount{
		font-size: 12px !important;
		}

		.pricing-choice-tariff {
		display: flex !important;
		flex-direction: column !important;
		gap: 8px !important;
		max-width: 414px !important;
		margin-bottom: 24px !important;
		}

		.pricing-tariff-title {
		color: #737b8a !important;
		font-size: 16px !important;
		}
	.PricingRates-options-title{
		font-size: 14px !important;
	}
		.pricing-button-group {
		margin-top: auto !important;
		padding: 0 0 0 0 !important;
		font-weight: 500 !important;
		font-size: 16px !important;
		display: flex !important;
		flex-direction: column !important;
		gap: 12px !important;
		}
		.pricing-button-connect {
		width: 100% !important;
		padding: 13px 0 !important;
		text-align: center !important;
		border-radius: 8px !important;
		}
		.pricing-button-later {
		width: 100% !important;
		padding: 13px 0 !important;
		text-align: center !important;
		border-radius: 8px !important;
		background-color: #ededed !important;
		}

		.pricing-buy-modal {
		width: 100% !important;
		display: flex !important;
		flex-direction: column !important;
		height: 100vh !important;
		position: relative !important;
		bottom: 7% !important;
		padding: 32px 32px 0 32px !important;
		}

		.pricing-buy-description-total {
		max-width: 484px !important;
		color: #737b8a !important;
		font-size: 14px !important;
		}

		.pricing-buy-title {
		font-weight: 600 !important;
		font-size: 28px !important;
		margin-bottom: 12px !important;
			margin-top: 0 !important;
		}

		.pricing-buy-description {
		font-size: 16px !important;
		color: #333333 !important;
		margin-bottom: 32px !important;
		}

		.pricing-buy-added-people {
		display: flex !important;
		align-items: center !important;
		margin-bottom: 24px !important;
		}
		.pricing-buy-added-content {
		width: 258px !important;
		display: flex !important;
		flex-direction: column !important;
		gap: 8px !important;
		}

		.pricing-buy-added-title {
		color: #737b8a !important;
		font-size: 16px !important;
		}

		.pricing-buy-added-button-content {
		display: flex !important;
		gap: 8px !important;
		}

		.pricing-buy-added-button {
		background-color: #f2f2f2 !important;
		border-radius: 8px !important;
		padding: 12px !important;
		display: flex !important;
		align-items: center !important;
		justify-content: center !important;
		width: 48px !important;
		height: 48px !important;
		}

		.pricing-buy-added-input {
		padding: 14px 16px !important;
		border-radius: 8px !important;
		border: 1px solid #cdd1db !important;
		max-width: 88px !important;
		}

		.pricing-buy-added-clue {
		color: #737b8a !important;
		font-size: 14px !important;
		}

		.pricing-buy-added-price {
			font-size: 16px !important;
		font-weight: 600 !important;
		line-height: 24px !important;
		}

		.pricing-buy-link-promo {
		cursor: pointer !important;
		color: #0c50ff !important;
		font-weight: 500 !important;
		margin-bottom: 24px !important;
			font-size: 16px !important;
		}

		.pricing-buy-promo-content {
		display: flex !important;
		gap: 8px !important;
		margin-bottom: 24px !important;
		}

		.pricing-buy-promo-input {
		border-radius: 8px !important;
		border: 1px solid #afb5c0 !important;
		padding: 14px 16px !important;
			font-size: 16px !important;
		}

		.pricing-buy-promo-button {
		background-color: #dbeafe !important;
		border-radius: 8px !important;
		padding: 13px 24px !important;
		color: #0c50ff !important;
			font-size: 16px !important;
		}

		.pricing-buy-total-content {
		background-color: #ededed !important;
		border-radius: 8px !important;
		padding: 24px !important;
		margin-bottom: 34px !important;
		}

		.pricing-buy-total-count {
		display: flex !important;
		justify-content: space-between !important;

		}

		.pricing-buy-total-count-title {
			font-size: 16px !important;
		}

		.pricing-buy-total-count-price {
			font-size: 16px !important;
		}

		.pricing-buy-total {
		display: flex !important;
		justify-content: space-between !important;
		}

		.pricing-buy-total-title {
		font-size: 28px !important;
		font-weight: 600 !important;
		line-height: 36px !important;
		}

		.pricing-buy-total-price {
		font-size: 28px !important;
		font-weight: 600 !important;
		line-height: 36px !important;
		}

		.activeOption {
		background-color: white !important;
		color: #1e40af !important;
		}

		.pricing-options-price {
		font-size: 13px !important;
		}

		.pricing-modal-promo-button {
		padding: 20px !important;
		border-radius: 12px !important;
		display: flex !important;
		align-items: center !important;
		outline: none !important;
		background-color: #f2f2f2 !important;
		}
			.pricing-promo-title{
				font-size: 16px !important;
			}
		.pricing-promo-text{
			font-size: 16px !important;
		}
		.inpput-promo-active {
		background-color: #f2f2f2 !important;
		outline: none !important;
		}

		.pricing-buy-total-count-price-description {
		color: #737b8a !important;
		font-size: 14px !important;
		padding-top: 6px !important;
		margin-bottom: 12px !important;
		}

		.pricing-buy-total-count-price-people {
			font-size: 16px !important;
		}
	}


	.PricingRates{
		overflow: hidden !important;
		&-options-content{
		display: flex;
		gap: 12px;
		flex-direction: column;
		align-items: flex-start;
		margin-bottom: 12px;
		}

		&-options-title{
		font-size: 14px;
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
		padding: 4px 20px ;
		border-radius: 8px;
			font-size: 14px;
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
		font-size: 13px;
	}
	.pricing-button-group{
		margin-top: auto;
		padding: 0 0 0 0;
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
	display: flex;
	justify-content: center;
	gap: 10px;
	align-items: center;
	background-color: #0C50FF;
	color: white;
	}
	.pricing-button-connect:hover{
		background-color: #0d3bb2;

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
		width: 404px;
		height: 100vh;
		position: relative;
		bottom: 7%;
		padding: 24px 24px 0 24px;
		font-family: Inter,serif !important;
		* {
		font-family: inherit !important;
		}
	}

	.pricing-buy-description-total{
		max-width: 404px;
		color: #737B8A;
		font-size: 12px;
	}

	.pricing-buy-title{
	font-weight: 600;
		font-size: 20px;
		margin-bottom: 12px;
		color: #333333;

		margin-top: 12px;
	}

	.pricing-buy-description{
	font-size: 12px;
		color: #333333;
		margin-bottom: 16px;
	}

	.pricing-buy-added-people{
		display: flex;
		align-items: center;
		margin-bottom: 12px;
	}
	.pricing-buy-added-content{
		width: 230px;
		display: flex;
		flex-direction: column;
		gap: 8px;
	}

	.pricing-buy-added-title{
		color: #737B8A;
		font-size: 13px;
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
		font-size: 12px;
	}

	.pricing-buy-added-price{
	font-weight: 600;
		font-size: 13px;
		line-height: 24px;
	}

	.pricing-buy-link-promo{
		cursor: pointer;
	color: #0C50FF;
		font-weight: 500;
		margin-bottom: 12px;
		font-size: 13px;
	}

	.pricing-buy-promo-content{
		display: flex;
		gap: 8px;
		margin-bottom: 12px;
	}

	.pricing-buy-promo-input{
	border-radius: 8px;
		border: 1px solid #AFB5C0;
		padding: 7px 8px;
		font-size: 13px;
	}

	.pricing-buy-promo-button{
	background-color: #DBEAFE;
		border-radius: 8px;
		padding: 6px 12px;
		font-size: 13px;
		color: #0C50FF;
	}

	.pricing-buy-total-content{
	background-color: #EDEDED;
		border-radius: 8px;
		padding: 12px;
		margin-bottom: 24px;
	}
	.pricing-discount{
		background-color: #FF5E5C;
		color: white;
		padding: 4px;
		font-size: 10px;
		font-weight: 400;
		border-radius: 8px;
	}

	.pricing-buy-total-count{
		margin-top: 12px;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.pricing-buy-total-count-title{
		font-size: 13px;
	}

	.pricing-buy-total-count-price{
		font-size: 13px;
	}

	.pricing-buy-total{
		display: flex;
		justify-content: space-between;
	}

	.pricing-buy-total-title{
	font-size: 20px;
		font-weight: 600;
		line-height: 32px;
	}

	.pricing-buy-total-price{
		font-size: 20px;
		font-weight: 600;
		line-height: 32px;
	}

	.activeOption{
		background-color: white;
		color: #1E40AF;
	}

	.pricing-options-price{
		font-size: 12px;
	}
	.pricing-option-discount{
		display: flex;
		gap: 6px;
	}

	.pricing-modal-promo-button{
		padding: 10px;
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
		font-size: 12px;
		padding-top: 6px;
		margin-bottom: 12px;
	}
	.PricingRates-options-title{
		font-size: 12px;
		color: #737B8A;
	}

	.pricing-buy-total-count-price-people{
		font-size: 13px;
	}
	.pricing-promo{
		display: flex;
		justify-content: space-between;
		margin-bottom: 10px;
	}
	.pricing-promo-title{
		font-size: 13px;
	}
	.pricing-promo-text{
		font-size: 13px;
		color:#FF5E5C;
	}
	</style>
