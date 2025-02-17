/* eslint-disable camelcase */

import { defineStore } from 'pinia';
import {
	fetchPricingManager,
	fetchOwnerInfo,
	fetchPricing,
	fetchPricingPromo,
	postPaymentData,
	fetchPaymentStatus,
} from '@/stores/api';
import { postPaymentExtendData } from './api/pricing';

function renameProps(obj, renames) {
	if (!obj) return null;
	return Object.keys(obj).reduce((result, key) => {
		if (renames[key]) result[renames[key]] = obj[key];
		else result[key] = obj[key];
		return result;
	}, {});
}

export const usePricingStore = defineStore('pricing', {
	state: () => ({
		isLoading: false,
		// state here
		manager: {},
		current: null,
		items: [],
		priceForOnePerson: {},
		priceForUser: null,
	}),
	actions: {
		async fetchManager() {
			this.isLoading = true;
			try {
				const data = await fetchPricingManager();
				this.manager = renameProps(data.data, { last_name: 'lastName' });
			} catch (error) {
				console.error('fetchPricingManager', error);
				this.manager = null;
			}
			// this.manager = {
			// 	id: 1,
			// 	photo: '/users_img/1708419167.png',
			// 	name: 'test',
			// 	lastName: 'test123',
			// 	phone: '123123123',
			// 	email: 'asdas@adsas.asd'
			// }
			this.isLoading = false;
		},
		async fetchCurrent() {
			this.isLoading = true;
			try {
				const { data } = await fetchOwnerInfo();
				this.current = data.tariff || {
					id: 0,
					owner_id: 0,
					tariff_id: 0,
					extra_user_limit: 0,
					expire_date: '',
					auto_payment: 0,
					payment_id: '',
					status: 'succeeded',
					service_for_payment: '',
					created_at: null,
					updated_at: null,
					tariff: {
						id: 0,
						kind: 'free',
						validity: 'monthly',
						users_limit: 5,
						price: 0,
						created_at: null,
						updated_at: null,
					},
				};
				// ^^ костыль чтоб для бесплатного тарифа хоть что-то показывало
			} catch (error) {
				console.error('fetchOwnerInfo', error);
			}
			this.isLoading = false;
		},
		async fetchPricing() {
			this.isLoading = true;
			try {
				const { data } = await fetchPricing();
				this.items = data.tariffs;
				this.priceForOnePerson = data.priceForOnePerson;
				this.priceForUser = data.priceForOnePerson;
			} catch (error) {
				console.error('fetchPricing', error);
			}
			this.isLoading = false;
		},
		async fetchPromo(code) {
			try {
				return await fetchPricingPromo(code);
			} catch (error) {
				console.error('fetchPricingPromo', error);
				return {};
			}
		},
		async fetchStatus(code) {
			try {
				const { data } = await fetchPaymentStatus(code);
				return data;
			} catch (error) {
				console.error('fetchPaymentStatus', error);
				return {};
			}
		},
		async postPaymentData(params) {
			const { data } = await postPaymentData(params);
			return data;
		},
		async postPaymentExtendData(params, id) {
			const { data } = await postPaymentExtendData(params, id);
			return data;
		},
	},
});
