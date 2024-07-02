/* eslint-disable camelcase */

import { defineStore } from 'pinia';
import { fetchValidity } from '../pricing';

export const useValidityStore = defineStore('useValidityStore', {
	state: () => ({
		validity: [],
		date: '',
	}),
	actions: {
		async fetchValidityCorses() {
			const data = await fetchValidity();
			this.validity = data.data;
			const newDate = new Date(this.validity.expired_at);
			const currentDate = new Date();
			const timeDiff = newDate - currentDate;
			const daysDiff = timeDiff / (1000 * 60 * 60 * 24);
			this.validity = daysDiff;
		},
		async fetchDateCorses() {
			const data = await fetchValidity();
			this.date = data.data.validity.expired_at || '';
		},
	},
});
