import { defineStore } from 'pinia'
import { fetchProfileSalary } from '@/stores/api'


export const useProfileSalaryStore = defineStore('profileSalary', {
	state: () => ({
		isReady: false,
		isLoading: false,
		user_earnings: {},
		has_qp: false,
	}),
	actions: {
		async fetchSalary(year, month){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchProfileSalary(year, month)
				this.user_earnings = data.user_earnings
				this.has_qp = data.has_qp
				this.isReady = true
			}
			catch(error){
				console.error('fetchSalary', error)
			}
			this.isLoading = false
		},
	}
})