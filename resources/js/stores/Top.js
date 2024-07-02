import { defineStore } from 'pinia'
// import { fetchApi } from '@/stores/api' // replace fetchApi !!
const now = new Date()

export const useTopStore = defineStore('top', { // replace useTemplateStore, template !!
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		year: now.getFullYear(),
		month: now.getMonth(),
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
		setDate(year, month){
			this.year = +year
			this.month = +month
		}
	}
})
