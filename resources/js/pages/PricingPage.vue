<template>
	<div class="PricingPage py-4">
		<PricingManager />
		<PricingCurrent />
		<PricingRates
			:currency="currency"
			@update="updateRate"
		/>

		<div class="PricingPage-users mt-4">
			<div class="PricingPage-users-title">
				Количество пользователей:
			</div>
			<div class="PricingPage-users-form">
				<button
					class="PricingPage-users-less"
					:disabled="users <= (selectedRate ? selectedRate.users_limit : 0)"
					@click="decreseUsers"
				>
					-
				</button>
				<input
					v-model="users"
					type="number"
					class="PricingPage-users-input"
				>
				<button
					class="PricingPage-users-more"
					@click="increseUsers"
				>
					+
				</button>
			</div>
			<img
				v-b-popover.hover.right="'Далеко-далеко за словесными горами в стране.'"
				src="/images/dist/profit-info.svg"
				alt=""
			>
		</div>
		<div class="PricingPage-currency mt-4">
			Валюта:
			<button
				class="btn"
				:class="{'btn-success': currency === '₽'}"
				@click="currency = '₽'"
			>
				₽
			</button>
			<button
				class="btn"
				:class="{'btn-success': currency === '₸'}"
				@click="currency = '₸'"
			>
				₸
			</button>
			<button
				class="btn"
				:class="{'btn-success': currency === '$'}"
				@click="currency = '$'"
			>
				$
			</button>
		</div>
		<div class="PricingPage-auto mt-4">
			<b-form-checkbox
				v-model="autoPayment"
				switch
			>
				Автооплата
			</b-form-checkbox>
		</div>
		<div class="PricingPage-total mt-4">
			Итого к оплате: <span class="PricingPage-total-value">{{ $separateThousands(Math.round(total / rates[currency])) }} {{ currency }}</span> <button
				class="btn btn-success"
				@click="submitPayment"
				:disabled="!selectedRate"
			>
				Оплатить
			</button>
		</div>
		<hr>
		<div class="PricingPage-promo mt-4">
			<div
				v-if="promoData.code"
				class="PricingPage-promo-active"
			>
				Активирован промокод {{ $separateThousands(Math.round(promoData.value / rates[currency])) }} {{ currency }}
			</div>
			<template v-else>
				<div class="PricingPage-promo-title">
					Есть бонусный код?
				</div>
				<div class="PricingPage-promo-text">
					Активируйте его чтобы получить бонус на первую оплату
				</div>
				<div class="PricingPage-promo-form mt-2">
					<input
						v-model="promo"
						type="text"
						class="PricingPage-promo-input form-control"
						placeholder="Код купона"
					>
					<button
						class="btn btn-success"
						:disabled="!promo || isPromoLoading"
						@click="activatePromo"
					>
						{{ isPromoLoading ? 'Активирую' : 'Активировать' }}
					</button>
				</div>
			</template>
		</div>
	</div>
</template>

<script>
/* global Laravel */
import { mapActions, mapState } from 'pinia'
import { usePricingStore } from '@/stores/Pricing'
import PricingManager from '@/components/pages/Pricing/PricingManager'
import PricingCurrent from '@/components/pages/Pricing/PricingCurrent'
import PricingRates from '@/components/pages/Pricing/PricingRates'

export default {
	name: 'PricingPage',
	components: {
		PricingManager,
		PricingCurrent,
		PricingRates,
	},
	data(){
		return {
			selectedRate: null,
			users: 0,
			userPrice: 200,
			period: '',
			autoPayment: true,
			currency: '₽',
			currencyTranslate: {
				'₽': 'rub',
				'₸': 'kzt',
				'$': 'dollar',
			},
			promo: '',
			promoData: {},
			isPromoLoading: false,
		}
	},
	computed: {
		...mapState(usePricingStore, ['rates']),
		additionalUsers(){
			if(!this.selectedRate) return 0
			return this.users - this.selectedRate.users
		},
		total(){
			if(!this.selectedRate) return 0
			const total = this.period === 'monthly' ? this.selectedRate.price + (this.additionalUsers * this.userPrice) : this.selectedRate.price + (this.additionalUsers * this.userPrice * 12)
			return this.promoData?.value ? total - this.promoData.value : total
		},
	},
	methods: {
		...mapActions(usePricingStore, [
			'postPaymentData',
			'fetchPromo',
			'fetchRates',
			'fetchCurrent',
		]),
		updateRate(value){
			this.selectedRate = value.rate
			this.period = value.rate.validity
			this.users = value.rate.users_limit
		},
		decreseUsers(){
			if(this.users > (this.selectedRate ? this.selectedRate.users_limit : 0)) --this.users
		},
		increseUsers(){
			if(!this.selectedRate) return
			++this.users
		},
		async submitPayment(){
			if(!this.selectedRate) return
			try{
				const url = await this.postPaymentData({
					currency: this.currencyTranslate[this.currency],
					tariff_id: this.selectedRate.id,
					extra_users_limit: this.additionalUsers,
					auto_payment: this.autoPayment
				})
				window.location.assign(url)
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
	created(){
		this.fetchRates()
		this.fetchCurrent(Laravel.userId)
	}
};
</script>

<style lang="scss">
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
	&-users-less,
	&-users-more{
		width: 4rem;
		flex: 0 0 4rem;
		padding: 0.5rem 1rem;
		border: none;
		background-color: #b6c2d6;
		color: #777;
		text-align: center;
		font-weight: 700;
		font-size: 1.3em;
		&:disabled{
			background-color: #aaa;
		}
		&:focus{
			outline: none;
		}
	}
	&-users-less{
		border-radius: 0.6rem 0 0 0.6rem;
	}
	&-users-more{
		border-radius: 0 0.6rem 0.6rem 0;
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
