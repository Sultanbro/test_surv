import { defineStore } from 'pinia'
import { fetchDictionaries } from '@/stores/api'


export const useCompanyStore = defineStore('company', {
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		dictionaries: {
			users: [],
			positions: [],
			profile_groups: [],
		}
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
	}
})

// const store = useCompanyStore()
// store.fetchDictionaries()
