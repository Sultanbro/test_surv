import { defineStore } from 'pinia';

export const usePricingPeriodStore = defineStore('priceStore', {
	state: () => ({
		tariffStore: 'free',
	}),
	actions: {
		connectedTariff(value) {
			this.tariffStore = value;
		},
	},
});
