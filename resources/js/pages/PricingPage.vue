<template>
	<div class="pricing-page-content">
		<JobtronOverlay
			v-if="currentModalId === 'priceModal'"
			@close="removeModal()"
		>
			<PricingModal>
				<PricingModalDefault />
			</PricingModal>
		</JobtronOverlay>
		<JobtronOverlay
			v-if="currentModalId === 'pricingToBuy'"
			@close="removeModal()"
		>
			<PricingModal>
				<PricingModalToBuy :currency="currency" />
			</PricingModal>
		</JobtronOverlay>
		<JobtronOverlay
			v-if="currentModalId === 'editRate'"
			@close="removeModal()"
		>
			<PricingModal>
				<PricingModalEditRate :currency="currency" />
			</PricingModal>
		</JobtronOverlay>

		<JobtronOverlay
			v-if="currentModalId === 'pricingToFree'"
			@close="removeModal()"
		>
			<PricingModalFreeLayout>
				<PricingToFree :currency="currency" />
			</PricingModalFreeLayout>
		</JobtronOverlay>
		<PriceTrialPeriod v-if="!trialPeriod" />
		<!--		<PriceTimeLimit is-default />-->
		<PriceSpace />
		<div class="PricingPage ">
			<PricingRates
				:currency="currency"
				:selected-rate="selectedRate"
				@update="updateRate"
				@updateCurrency="updateCurrency"
			/>
		</div>
		<PriceFAQ />
	</div>
</template>

<script>
/* global Laravel */
import { mapActions, mapState } from 'pinia';
import { usePricingStore } from '@/stores/Pricing';
import PricingRates from '@/components/pages/Pricing/PricingRates';
import PriceTrialPeriod from '../components/price/PriceTrialPeriod.vue';
import PriceFAQ from '../components/pages/Pricing/PriceFAQ.vue';
import PricingModal from '../components/pages/Pricing/PricingModal.vue';
import { useModalStore } from '../stores/Modal';
import JobtronOverlay from '../components/ui/Overlay.vue';
import PricingModalToBuy from '../components/pages/Pricing/Modals/PricingModalToBuy.vue';
import PricingModalDefault from '../components/pages/Pricing/Modals/PricingModalDefault.vue';
import { usePricingPeriodStore } from '../stores/PricingPeriod';
import PricingModalEditRate from '../components/pages/Pricing/Modals/PricingModalEditRate.vue';
import PriceSpace from '../components/price/PriceSpace.vue';
import PricingModalFreeLayout from '../components/pages/Pricing/Modals/PricingModalFreeLayout.vue';
import PricingToFree from '../components/pages/Pricing/Modals/PricingToFree.vue';

export default {
	name: 'PricingPage',
	components: {
		PricingToFree,
		PricingModalFreeLayout,
		PriceSpace,
		PricingModalDefault,
		PricingModalToBuy,
		JobtronOverlay,
		PricingModal,
		PriceFAQ,
		PriceTrialPeriod,
		PricingRates,
		PricingModalEditRate,
	},
	data() {
		return {
			selectedRate: null,
			users: 0,
			period: '',
			autoPayment: true,
			currency: '₽',
			currencyTranslate: {
				'₽': 'rub',
				'₸': 'kzt',
				$: 'usd',
			},
			promo: '',
			promoData: {},
			isPromoLoading: false,
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0]),
			freePeriod: false,
			trialPeriod: false
		};
	},

	computed: {
		...mapState(usePricingStore, ['priceForUser', 'items', 'current']),
		...mapState(useModalStore, ['currentModalId']),
		...mapState(usePricingPeriodStore, ['tariffStore', 'priceStore']),

		additionalPrice() {
			if (!this.priceForUser) return 0;
			return (
				this.users *
				this.priceForUser[this.currencyCode] *
				(this.selectedRate.validity === 'monthly' ? 1 : 12)
			);
		},
		total() {
			if (!this.selectedRate) return 0;
			const total =
				this.selectedRate.multiCurrencyPrice[this.currencyCode] +
				this.additionalPrice;
			return this.promoData?.value ? total - this.promoData.value : total;
		},
		currencyCode() {
			return this.currencyTranslate[this.currency];
		},
	},

	created() {
		this.fetchCurrent(Laravel.userId);
		if (this.$route.query.status) {
			this.fetchStatus().then((status) => {
				if (status) return this.$toast.success('Платеж прошел успешно');
				this.$toast.error('Платеж прошел неуспешно');
			});
		}
		this.trialPeriodFetch()
	},

	methods: {
		...mapActions(useModalStore, ['setCurrentModal', 'removeModalActive']),
		...mapActions(usePricingStore, [
			'postPaymentData',
			'fetchPromo',
			'fetchCurrent',
			'fetchStatus',
		]),
		removeModal() {
			this.removeModalActive();
		},
		trialPeriodFetch(){
			this.axios.get('/tariff/trial').then(res => {
				this.trialPeriod = res.data.data.has_trial
			})
		},
		updateRate(value) {
			this.selectedRate = value.rate;
			this.period = value.rate.validity;
		},
		updateFreePeriod(newPeriod) {
			this.freePeriod = newPeriod;
			this.$nextTick(() => {
				document.body.appendChild(this.$refs.pricingmodal);
			});
		},
		updateCurrency(newCurrency) {
			this.currency = newCurrency;
		},
		decreseUsers() {
			if (this.users > 0) --this.users;
		},
		increseUsers() {
			if (!this.selectedRate) return;
			++this.users;
		},
		async submitPayment() {
			if (!this.selectedRate) return;
			if (this.currency !== '₽') return this.submitWalletOne();
			try {
				/* eslint-disable camelcase */
				const { url } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.selectedRate.id,
					extra_users_limit: this.users > 0 ? this.users : 0,
					auto_payment: this.autoPayment,
				});
				/* eslint-enable camelcase */
				window.location.assign(url);
			} catch (error) {
				console.error('submitPayment', error);
				this.$toast.error('Ошибка при попытке оплаты');
			}
		},
		async submitWalletOne() {
			try {
				/* eslint-disable camelcase */
				const { url, params } = await this.postPaymentData({
					currency: this.currencyCode,
					tariff_id: this.selectedRate.id,
					extra_users_limit: this.users > 0 ? this.users : 0,
					auto_payment: this.autoPayment,
				});
				const form = document.createElement('form');
				form.method = 'post';
				form.action = url;
				Object.keys(params).forEach((key) => {
					const inp = document.createElement('input');
					inp.name = key;
					inp.value = params[key];
					form.appendChild(inp);
				});
				document.body.appendChild(form);
				form.submit();
			} catch (error) {
				console.error('submitPayment', error);
				this.$toast.error('Ошибка при попытке оплаты');
			}
		},
		async activatePromo() {
			this.isPromoLoading = true;
			this.promoData = await this.fetchPromo(this.promo);
			this.isPromoLoading = false;
		},
	},
};
</script>

<style lang="scss">
@media (min-width: 1600px) {
  .pricing-page-content {

	gap: 80px !important;

  }
}


.pricing-page-content {
	font-family: Inter,serif  !important;
  display: flex;
  flex-direction: column;
  gap: 40px;
  max-width: 1300px;
  width: 100%;
  margin-left: 30px;
	button:focus{
	outline: none !important;

  }
  * {
	font-family: inherit !important;

  }
}
.PricingPage-promo-title {
	position: relative;
  font-family: Inter,serif;
}
.price-beta {
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
.PricingPage {
	z-index: 3;
	line-height: 1.3;
	&-total,
	&-users {
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 1rem;
	}
	&-users-form {
		display: inline-flex;
		flex-flow: row nowrap;
		align-items: stretch;
		border: 0.1rem solid #e8e8e8;
		border-radius: 0.6rem;
		font-size: 1.4rem;
	}
	&-users-input {
		flex: 1 1 100%;
		padding: 0 1rem;
		background-color: #f7fafc;
		-moz-appearance: textfield;
		&:focus {
			background-color: #f7fafc;
		}
		&::-webkit-outer-spin-button,
		&::-webkit-inner-spin-button {
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

	&-currency {
		display: flex;
		align-items: center;
		justify-content: flex-start;
		gap: 10px;
	}
	&-total-value {
		font-size: 1.3em;
	}
	&-promo-title {
		font-size: 1.3em;
	}
	&-promo-text {
		font-size: 0.8em;
		opacity: 0.5;
	}
	&-promo-input {
		display: inline-block;
		width: auto;
	}
}
</style>
