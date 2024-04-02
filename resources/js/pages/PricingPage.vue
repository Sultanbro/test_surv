<template>
	<div class="PricingPage py-4">
		<PricingManager />
		<PricingCurrent />
		<PricingRates
			:currency="currency"
			:selected-rate="selectedRate"
			@update="updateRate"
		/>

		<template v-if="items && items.length">
			<div class="PricingPage-currency mt-4">
				Валюта:
				<JobtronButton
					:fade="currency !== '₽'"
					@click="currency = '₽'"
				>
					₽
				</JobtronButton>
				<JobtronButton
					:fade="currency !== '₸'"
					@click="currency = '₸'"
				>
					₸
				</JobtronButton>
				<JobtronButton
					:fade="currency !== '$'"
					@click="currency = '$'"
				>
					$
				</JobtronButton>
			</div>
			<div
				v-if="selectedRate"
				class="PricingPage-users mt-4"
			>
				<div class="PricingPage-users-title">
					Количество пользователей:
				</div>
				<div class="PricingPage-users-form">
					<JobtronButton
						class="PricingPage-users-less"
						:disabled="users <= 0"
						@click="decreseUsers"
					>
						-
					</JobtronButton>
					<input
						v-model="users"
						type="number"
						class="PricingPage-users-input"
					>
					<JobtronButton
						class="PricingPage-users-more"
						@click="increseUsers"
					>
						+
					</JobtronButton>
				</div>
				<img
					v-b-popover.hover.right="'Если необходимо к тарифу можете добавить пользователей'"
					src="/images/dist/profit-info.svg"
					alt=""
				>
			</div>
			<div
				v-if="false && selectedRate"
				class="PricingPage-auto mt-4"
			>
				<b-form-checkbox
					v-model="autoPayment"
					switch
				>
					Авто оплата
				</b-form-checkbox>
			</div>
			<div
				v-if="selectedRate"
				class="PricingPage-total mt-4"
			>
				Итого к оплате: <span class="PricingPage-total-value">{{ $separateThousands(Math.round(total)) }} {{ currency }}</span> <JobtronButton @click="submitPayment">
					Оплатить
				</JobtronButton>
			</div>
			<hr class="my-4">
			<div
				v-if="isBP"
				class="PricingPage-promo mt-4"
			>
				<div
					v-if="promoData.code"
					class="PricingPage-promo-active"
				>
					Активирован промокод {{ $separateThousands(Math.round(promoData.value)) }} {{ currency }}
				</div>
				<template v-else>
					<div class="PricingPage-promo-title">
						Есть бонусный код?
					</div>
					<div class="PricingPage-promo-text">
						Активируйте его, чтобы получить бонус на первую оплату
					</div>
					<div class="PricingPage-promo-form mt-4">
						<input
							v-model="promo"
							type="text"
							class="PricingPage-promo-input form-control"
							placeholder="Код купона"
						>
						<JobtronButton
							:disabled="!promo || isPromoLoading"
							@click="activatePromo"
						>
							{{ isPromoLoading ? 'Активирую' : 'Активировать' }}
						</JobtronButton>
					</div>
				</template>
			</div>
		</template>
	</div>
</template>

<script>
/* global Laravel */
import { mapActions, mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'
import PricingManager from '@/components/pages/Pricing/PricingManager'
import PricingCurrent from '@/components/pages/Pricing/PricingCurrent'
import PricingRates from '@/components/pages/Pricing/PricingRates'
import JobtronButton from '@ui/Button'

export default {
	name: 'PricingPage',
	components: {
		PricingManager,
		PricingCurrent,
		PricingRates,
		JobtronButton,
	},
	data(){
		return {
			selectedRate: null,
			users: 0,
			period: '',
			autoPayment: true,
			currency: '₽',
			currencyTranslate: {
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'usd',
			},
			promo: '',
			promoData: {},
			isPromoLoading: false,
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0]),
		}
	},
	computed: {
		...mapState(usePricingStore, ['priceForUser', 'items']),
		additionalPrice(){
			if(!this.priceForUser) return 0
			return this.users * this.priceForUser[this.currencyCode] * (this.selectedRate.validity === 'monthly' ? 1 : 12)
		},
		total(){
			if(!this.selectedRate) return 0
			const total = this.selectedRate.multiCurrencyPrice[this.currencyCode] + this.additionalPrice
			return this.promoData?.value ? total - this.promoData.value : total
		},
		currencyCode(){
			return this.currencyTranslate[this.currency]
		},
	},

	created(){
		this.fetchCurrent(Laravel.userId)
		if(this.$route.query.status){
			this.fetchStatus().then(status => {
				if(status) return this.$toast.success('Платеж прошел успешно')
				this.$toast.error('Платеж прошел неуспешно')
			})
		}
	},

	methods: {
		...mapActions(usePricingStore, [
			'postPaymentData',
			'fetchPromo',
			'fetchCurrent',
			'fetchStatus',
		]),
		updateRate(value){
			this.selectedRate = value.rate
			this.period = value.rate.validity
		},
		decreseUsers(){
			if(this.users > 0) --this.users
		},
		increseUsers(){
			if(!this.selectedRate) return
			++this.users
		},
		async submitPayment(){
			if(!this.selectedRate) return
			if(this.currency !== '₽') return this.submitWalletOne()
			try{
				/* eslint-disable camelcase */
				const { data } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.selectedRate.id,
					extra_users_limit: this.users > 0 ? this.users : 0,
					auto_payment: this.autoPayment
				})
				/* eslint-enable camelcase */
				window.location.assign(data.url || data.redirect_url)
			}
			catch(error){
				console.error('submitPayment', error)
				this.$toast.error('Ошибка при попытке оплаты')
			}
		},
		async submitWalletOne(){
			try{
				/* eslint-disable camelcase */
				const { data } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.selectedRate.id,
					extra_users_limit: this.users > 0 ? this.users : 0,
					auto_payment: this.autoPayment
				})
				const form = document.createElement('form')
				form.method = 'post'
				form.action = data.url || data.redirect_url
				Object.keys(data.params).forEach(key => {
					const inp = document.createElement('input')
					inp.name = 'hidden'
					inp.value = data.params[key]
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
		async activatePromo(){
			this.isPromoLoading = true
			this.promoData = await this.fetchPromo(this.promo)
			this.isPromoLoading = false
		}
	},
};
</script>

<style lang="scss">
	.PricingPage-promo-title{
		position: relative;
	}
	.price-beta{
		position: absolute;
		top: 5px;
		left: 205px;
		padding: 3px 6px;
		border-radius: 4px;
		font-size: 12px;
		background-color: #cd2525;
		color: #fff;
		font-weight: 600;
		text-transform: uppercase;
	}
.PricingPage{
	line-height: 1.3;
	&-total,
	&-users{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 1rem;
	}
	&-users-form{
		display: inline-flex;
		flex-flow: row nowrap;
		align-items: stretch;
		border: 0.1rem solid #e8e8e8;
		border-radius: 0.6rem;
		font-size: 1.4rem;
	}
	&-users-input{
		flex: 1 1 100%;
		padding: 0 1rem;
		background-color: #F7FAFC;
		-moz-appearance: textfield;
		&:focus{
			background-color: #F7FAFC;
		}
		&::-webkit-outer-spin-button,
		&::-webkit-inner-spin-button{
			-webkit-appearance: none;
			margin: 0;
		}
	}
	// &-users-less,
	// &-users-more{
	// 	width: 4rem;
	// 	flex: 0 0 4rem;
	// 	padding: 0.5rem 1rem;
	// 	border: none;
	// 	background-color: #b6c2d6;
	// 	color: #777;
	// 	text-align: center;
	// 	font-weight: 700;
	// 	font-size: 1.3em;
	// 	&:disabled{
	// 		background-color: #aaa;
	// 	}
	// 	&:focus{
	// 		outline: none;
	// 	}
	// }
	// &-users-less{
	// 	border-radius: 0.6rem 0 0 0.6rem;
	// }
	// &-users-more{
	// 	border-radius: 0 0.6rem 0.6rem 0;
	// }

	&-currency{
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;
	}
	&-total-value{
		font-size: 1.3em;
	}
	&-promo-title{
		font-size: 1.3em;
	}
	&-promo-text{
		font-size: 0.8em;
		opacity: 0.5;
	}
	&-promo-input{
		display: inline-block;
		width: auto;
	}
}
</style>
