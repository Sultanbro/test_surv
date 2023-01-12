import { defineStore } from 'pinia'
// import { fetchApi } from '@/stores/api' // replace fetchApi !!


export const useTemplateStore = defineStore('template', { // replace useTemplateStore, template !!
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
	}),
	actions: {
		async fetchApi(){
			this.isLoading = true
			try{
				// const data = await fetchApi()
				// update state here
				this.isReady = true
			}
			catch(error){
				console.error('fetchApi', error)
			}
			this.isLoading = false
		},
	}
})