import { defineStore } from 'pinia'
import moment from 'moment'
import {
	fetchProfileSalary,
	fetchProfileBalance,
	fetchProfileKpi,
	fetchProfilePremiums,
} from '@/stores/api'
import { calcSum } from '@/pages/kpi/kpis.js'


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
				const { data: balance } = await fetchProfileBalance(year, month)
				this.user_earnings = data.user_earnings
				// this.user_earnings.sumSalary = parseInt(this.user_earnings.sumSalary)
				this.user_earnings.sumSalary = Object.values(balance.salaries).reduce((result, day) => {
					// console.log('data', day)
					return result + (parseInt(day.value) || 0)
				}, 0)
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
		async fitchSalaryCrutch(year, month){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const { data } = await fetchProfileBalance(year, month)
				const { items } = await fetchProfileKpi(year, month)
				const premiums = await fetchProfilePremiums(year, month)

				this.user_earnings.sumSalary = Object.values(data.salaries).reduce((result, day) => result + (parseInt(day.value) || 0), 0)
				this.user_earnings.sumKpi = items.reduce((result, kpi) => {
					kpi.users.forEach(user => {
						user.items.forEach(userItem => {
							result += calcSum(userItem, kpi, userItem.percent)
						})
					});
					return result
				}, 0)
				this.user_earnings.sumBonuses = 0
				this.user_earnings.sumQuartalPremiums = premiums.reduce((result, premium) => {
					premium.forEach(el => {
						if(el.items?.sum && el.items?.to?.substring(0, 7) === moment(Date.now()).format('YYYY-MM')) result += el.items.sum
					})
					return result
				}, 0)
			}
			catch(error){
				console.error('fitchSalaryCrutch', error)
			}
			this.isLoading = false
		}
	}
})
