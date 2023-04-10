import { defineStore } from 'pinia'
import {
	fetchWorkChartList,
} from '@/stores/api'


export const useWorkChartStore = defineStore('workChart', {
	state: () => ({
		isLoading: false,
		// state here
		workChartList: null
	}),
	actions: {
		async fetchWorkChartList(){
			this.isLoading = true
			try{
				const {data} = await fetchWorkChartList()
				this.workChartList = data
			}
			catch(error){
				console.error('fetchWorkChartList', error)
			}
			this.isLoading = false
		},
	}
})
