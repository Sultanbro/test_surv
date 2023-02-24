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
		priceForUser: null,
	}),
	actions: {
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
				this.items = data.tariffs
				this.priceForUser = data.priceForOnePerson
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
