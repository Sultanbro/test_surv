/* eslint-disable camelcase */

import { defineStore } from 'pinia';
import { fetchValidity } from '../pricing';

export const useValidityStore = defineStore('useValidityStore', {
	state: () => ({
		validity: [],
	}),
	actions: {
		async fetchValidityCorses() {
			const data = await fetchValidity();
			this.validity = data;
		},
	},
});
