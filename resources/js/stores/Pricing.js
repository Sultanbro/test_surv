/* global axios */
import { defineStore } from 'pinia'
import {
	fetchPricingManager,
	fetchOwnerInfo,
	fetchPricing,
	fetchPricingPromo,
	postPaymentData,
	fetchPaymentStatus,
} from '@/stores/api'

function renameProps(obj, renames){
	return Object.keys(obj).reduce((result, key) => {
		if(renames[key]) result[renames[key]] = obj[key]
		else result[key] = obj[key]
		return result
	}, {})
}

export const usePricingStore = defineStore('pricing', {
	state: () => ({
		isLoading: false,
		// state here
		manager: null,
		current: null,
		items: [],
		rates: {
			'₽': 1,
			'₸': 15.3915 / 100,
			'$': 70.5991,
		}
	}),
	actions: {
		async fetchRates(){
			const rates = await axios('https://www.cbr-xml-daily.ru/daily_json.js')
			this.rates = {
				'₽': 1,
				'₸': rates.data.Valute.KZT.Value / rates.data.Value.KZT.Nominal,
				'$': rates.data.Valute.USD.Value / rates.data.Value.USD.Nominal,
			}
		},
		async fetchManager(){
			this.isLoading = true
			try{
				const data = await fetchPricingManager()
				this.manager = renameProps(data, {last_name: 'lastName'})
			}
			catch(error){
				console.error('fetchPricingManager', error)
			}
			this.isLoading = false
		},
		async fetchCurrent(id){
			this.isLoading = true
			try{
				const { data } = await fetchOwnerInfo(id)
				this.current = data
			}
			catch(error){
				console.error('fetchOwnerInfo', error)
			}
			this.isLoading = false
		},
		async fetchPricing(){
			this.isLoading = true
			try{
				const { data } = await fetchPricing()
				this.items = data
			}
			catch(error){
				console.error('fetchPricing', error)
			}
			this.isLoading = false
		},
		async fetchPromo(code){
			try{
				return await fetchPricingPromo(code)
			}
			catch(error){
				console.error('fetchPricingPromo', error)
				return {}
			}
		},
		async fetchStatus(code){
			try{
				const { data } = await fetchPaymentStatus(code)
				return data
			}
			catch(error){
				console.error('fetchPaymentStatus', error)
				return {}
			}
		},
		async postPaymentData(params){
			const { data } = await postPaymentData(params)
			return data
		},
	}
})
