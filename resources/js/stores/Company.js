import { defineStore } from 'pinia'
import {
	fetchDictionaries,
	fetchCentralOwner,
} from '@/stores/api'


export const useCompanyStore = defineStore('company', {
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		dictionaries: {
			users: [],
			positions: [],
			profile_groups: [],
		},
		centralOwner: null
	}),
	actions: {
		async fetchDictionaries(refresh){
			if(this.isLoading) return
			if(this.isReady && !refresh) return
			this.isLoading = true
			try{
				const { data } = await fetchDictionaries()
				this.dictionaries = data
				this.isReady = true
			}
			catch(error){
				console.error('fetchDictionaries', error)
			}
			this.isLoading = false
		},
		async fetchCentralOwner(){
			try {
				const {data} = await fetchCentralOwner()
				this.centralOwner = data
			}
			catch (error) {
				console.error('fetchCentralOwner', error)
			}
		}
	},
})

// const store = useCompanyStore()
// store.fetchDictionaries()
