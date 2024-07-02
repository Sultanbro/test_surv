import { defineStore } from 'pinia';

export const useModalStore = defineStore('modalStore', {
	state: () => ({
		currentModalId: null,
		price: null,
	}),
	actions: {
		setCurrentModal(modalId) {
			this.currentModalId = modalId;
		},
		setPrice(price) {
			this.price = price;
		},
		removePrice() {
			this.price = null;
		},
		removeModalActive() {
			this.currentModalId = null;
		},
	},
});
