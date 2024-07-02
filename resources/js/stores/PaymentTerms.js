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
			if(this.hasLocal()){
				this.loadLocal()
				this.isReady = true
			}
			try{
				const data = await fetchProfilePaymentTerms()
				this.groups = data.groups
				this.position = data.position
				this.isReady = true
				this.saveLocal()
			}
			catch(error){
				console.error('fetchPaymentTerms', error)
			}
			this.isLoading = false
		},
		hasLocal(){
			return !!localStorage.getItem('paymentTerms')
		},
		loadLocal(){
			let json = localStorage.getItem('paymentTerms')
			if(!json){
				json = JSON.stringify({
					groups: [],
					position: null,
				})
				localStorage.setItem('paymentTerms', json)
			}
			const local = JSON.parse(json)
			this.groups = local.groups
			this.position = local.position
		},
		saveLocal(){
			localStorage.setItem('paymentTerms', JSON.stringify({
				groups: this.groups,
				position: this.position,
			}))
		},
	}
})
