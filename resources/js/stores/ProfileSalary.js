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
				this.user_earnings.sumSalary = parseInt(this.user_earnings.sumSalary)
				this.user_earnings.sumKpi = parseInt(this.user_earnings.sumKpi)
				this.user_earnings.sumBonuses = parseInt(this.user_earnings.sumBonuses)
				this.user_earnings.sumQuartalPremiums = parseInt(this.user_earnings.sumQuartalPremiums)
				this.user_earnings.sumNominations = parseInt(this.user_earnings.sumNominations)
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
