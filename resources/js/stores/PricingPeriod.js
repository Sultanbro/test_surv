import { defineStore } from 'pinia';

export const usePricingPeriodStore = defineStore('priceStore', {
	state: () => ({
		tariffStore: 'Бесплатный',
		priceStore: null,
		tariffId: null,
	}),
	actions: {
		connectedTariff(value) {
			this.tariffStore = value;
		},
		setTariffId(value) {
			this.tariffId = value;
		},
		addedPrice(value) {
			this.priceStore = value;
		},
	},
});
