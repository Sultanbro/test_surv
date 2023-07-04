import { defineStore } from 'pinia'
import { fetchProfilePersonalInfo } from '@/stores/api'

export const usePersonalInfoStore = defineStore('personalInfo', {
	state: () => ({
		isReady: false,
		isLoading: false,
		user: {},
		position: {},
		groups: '',
		salary: '',
		workingDay: '',
		workingTime: '',
		schedule: '',
		currency: '',
		currencies: {},
	}),
	actions: {
		async fetchPersonalInfo() {
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchProfilePersonalInfo()
				this.user = data.user
				this.position = data.position
				this.groups = data.groups
				this.salary = data.salary
				this.workingDay = data.workingDay
				this.workingTime = data.workingTime
				this.schedule = data.schedule
				this.currency = data.currency
				this.currencies = data.currencies
				this.isReady = true
			}
			catch(error){
				console.error('fetchPersonalInfo', error)
			}
			this.isLoading = false
		},
	}
})
