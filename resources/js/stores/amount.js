import { defineStore } from 'pinia';

export const useAmountStore = defineStore('amountStore', {
	state: () => ({
		phone: '',
		sum: '',
	}),
	actions: {
		setUsername(phone, sum) {
			this.phone = phone;
			this.sum = sum;
		},
		getUsername() {
			return {
				phone: this.phone,
				sum: this.sum,
			};
		},
	},
});
