import { defineStore } from 'pinia'
import { fetchProfilePaymentTerms } from '@/stores/api'


export const usePaymentTermsStore = defineStore('paymentTerms', {
	state: () => ({
		isReady: false,
		isLoading: false,
		groups: [],
		position: null,
	}),
	actions: {
		async fetchPaymentTerms(){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchProfilePaymentTerms()
				this.groups = data.groups
				this.position = data.position
				this.isReady = true
			}
			catch(error){
				console.error('fetchPaymentTerms', error)
			}
			this.isLoading = false
		},
	}
})