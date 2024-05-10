import { defineStore } from 'pinia';

export const usePricingPeriodStore = defineStore('priceStore', {
	state: () => ({
		tariffStore: 'Бесплатный',
		priceStore: null,
	}),
	actions: {
		connectedTariff(value) {
			this.tariffStore = value;
		},

		addedPrice(value) {
			this.priceStore = value;
		},
	},
});
