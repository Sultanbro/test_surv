import { defineStore } from 'pinia'
import moment from 'moment'
import {
	fetchProfileSalary,
	fetchProfileBalance,
	fetchProfileKpi,
	fetchProfilePremiums,
	fetchProfileBonuses,
	fetchProfilePersonalInfo,
	fetchPosibleBonuses,
	fetchProfileAwards,
} from '@/stores/api'
import { calcSum } from '@/pages/kpi/kpis.js'


const now = new Date()
export const useProfileSalaryStore = defineStore('profileSalary', {
	state: () => ({
		isReady: false,
		isLoading: false,
		user_earnings: {},
		has_qp: true,
		monthly: {},
		profile: null,
		readed: {
			kpis: null,
			bonuses: null,
			premiums: null,
			awards: null
		},
		possibleBonuses: null,
		currentKey: `${now.getFullYear()}-${now.getMonth() + 1}`,
		unreadCount: {
			kpis: 0,
			bonuses: 0,
			premiums: 0,
			awards: 0
		}
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
				// fetch data
				const { data: salary } = await fetchProfileBalance(year, month)
				const { items: kpis } = await fetchProfileKpi(year, month)
				const premiums = await fetchProfilePremiums(year, month)
				const { data: bonuses } = await fetchProfileBonuses(year, month)
				if(!this.profile){
					this.profile = await fetchProfilePersonalInfo()
				}
				const awards = {
					nominations: await fetchProfileAwards('nomination'),
					certificates: await fetchProfileAwards('certificate'),
					accrual: await fetchProfileAwards('accrual'),
				}

				// calc values
				const sumSalary = Object.values(salary.salaries).reduce((result, day) => result + (parseInt(day.value) || 0), 0)
				const sumKpi = kpis.reduce((result, kpi) => {
					kpi.users.forEach(user => {
						user.items.forEach(userItem => {
							result += calcSum(userItem, kpi, userItem.percent)
						})
					});
					return result
				}, 0)
				const sumBonuses = bonuses.history.reduce((result, bonus) => result + (parseInt(bonus.sum) || 0), 0)
				const sumQuartalPremiums = premiums.reduce((result, premium) => {
					premium.forEach(el => {
						if(el.items?.sum && el.items?.to?.substring(0, 7) === moment(Date.now()).format('YYYY-MM')) result += el.items.sum
					})
					return result
				}, 0)
				const sumAwards = Object.values(awards).reduce((result, type) => {
					type.forEach(award => {
						if(award.my && award.my.length) return result + award.my.length
					})
					return result
				}, 0)

				// calc max
				const maxKpi = kpis.reduce((result, kpi) => result + (parseInt(kpi.completed_100) || 0), 0)
				const maxSalary = parseInt(this.profile.salary.replace(/\s/g, '')) || 0

				// set current values
				const earnings = {
					sumSalary,
					sumKpi,
					sumBonuses,
					sumQuartalPremiums,
					sumNominations: sumAwards,
					kpiMax: maxKpi,
					oklad: maxSalary
				}
				this.user_earnings = earnings
				// this.user_earnings.sumSalary = sumSalary
				// this.user_earnings.sumKpi = sumKpi
				// this.user_earnings.sumBonuses = sumBonuses
				// this.user_earnings.sumQuartalPremiums = sumQuartalPremiums
				// this.user_earnings.sumNominations = 0
				// this.user_earnings.kpiMax = maxKpi
				// this.user_earnings.oklad = maxSalary

				// save for month
				const key = `${year}-${month}`
				this.monthly[key] = {
					salary,
					kpis,
					premiums,
					awards,

					sumSalary,
					sumKpi,
					sumBonuses,
					sumQuartalPremiums,
					sumAwards,
					maxKpi,
					maxSalary,
				}

				this.isReady = true

				// Можно запрышивать уже после основных данных т.к. нужны только для проверки
				if(!this.possibleBonuses){
					this.possibleBonuses = await fetchPosibleBonuses()
				}

				// check new premiums
				if(this.currentKey === key){
					this.checkReaded()
				}
			}
			catch(error){
				console.error('fitchSalaryCrutch', error)
			}
			this.isLoading = false
		},
		checkReaded(){
			const data = this.monthly[this.currentKey]
			if(!data) return

			// kpi
			this.unreadCount.kpis = data.kpis.reduce((result, kpi) => {
				kpi.users.forEach(user => {
					user.items.forEach(userKpi => {
						const readed = this.readed.kpis.find(r => r.id === userKpi.id)
						if(!readed) return ++result
						if(readed.updated_at !== (userKpi.updated_at || userKpi.created_at)) return ++result
					})
				})
				return result
			}, 0)

			// bonuses
			this.unreadCount.bonuses = this.possibleBonuses.reduce((result, group) => {
				group.items.forEach(bonus => {
					const readed = this.readed.bonuses.find(r => r.id === bonus.id)
					if(!readed) return ++result
					if(readed.updated_at !== (bonus.updated_at || bonus.created_at)) return ++result
				})
				return result
			}, 0)

			// premiums
			this.unreadCount.premiums = data.premiums.reduce((result, type) => {
				type.forEach(premium => {
					if(!premium?.items) return
					const readed = this.readed.premiums.find(r => r.id === premium.items.activity_id)
					if(!readed) return ++result
				})
				return result
			}, 0)

			// awards
			this.unreadCount.awards = Object.keys(data.awards).reduce((result, type) => {
				data.awards[type].forEach(award => {
					award.available.forEach(available => {
						const readed = this.readed.awards.find(r => r.id === available.id)
						if(!readed) return ++result
						if(readed.updated_at !== (available.updated_at || available.created_at)) return ++result
					})
				})
				return result
			}, 0)
		},
		loadReadedPremiums(){
			let readedJSON = localStorage.getItem('profileSalaryReaded')
			if(!readedJSON){
				readedJSON = JSON.stringify({
					kpis: [],
					bonuses: [],
					premiums: [],
					awards: [],
				})
				localStorage.setItem('profileSalaryReaded', readedJSON)
			}
			this.readed = JSON.parse(readedJSON)
		},
		saveReadedPremiums(){
			localStorage.setItem('profileSalaryReaded', JSON.stringify(this.readed))
		},
		setReadedKpis(){
			const data = this.monthly[this.currentKey]
			if(!data) return

			data.kpis.forEach(kpi => {
				kpi.users.forEach(user => {
					user.items.forEach(userKpi => {
						const readed = this.readed.kpis.find(r => r.id === userKpi.id)
						if(!readed) return this.readed.kpis.push({
							id: userKpi.id,
							updated_at: userKpi.updated_at || userKpi.created_at
						})
						if(readed.updated_at !== (userKpi.updated_at || userKpi.created_at)){
							readed.updated_at = userKpi.updated_at || userKpi.created_at
						}
					})
				})
			})

			this.unreadCount.kpis = 0
			this.saveReadedPremiums()
		},
		setReadedBonuses(){
			const data = this.monthly[this.currentKey]
			if(!data) return

			this.possibleBonuses.forEach(group => {
				group.items.forEach(bonus => {
					const readed = this.readed.bonuses.find(r => r.id === bonus.id)
					if(!readed) return this.readed.bonuses.push({
						id: bonus.id,
						updated_at: bonus.updated_at || bonus.created_at
					})
					if(readed.updated_at !== (bonus.updated_at || bonus.created_at)){
						readed.updated_at = bonus.updated_at || bonus.created_at
					}
				})
			})

			this.unreadCount.bonuses = 0
			this.saveReadedPremiums()
		},
		setReadedPremiums(){
			const data = this.monthly[this.currentKey]
			if(!data) return

			data.premiums.forEach(type => {
				type.forEach(premium => {
					if(!premium?.items) return
					const readed = this.readed.premiums.find(r => r.id === premium.items.activity_id)
					if(!readed) return this.readed.premiums.push({
						id: premium.items.activity_id,
					})
				})
			})

			this.unreadCount.premiums = 0
			this.saveReadedPremiums()
		},
		setReadedAwards(){
			const data = this.monthly[this.currentKey]
			if(!data) return

			Object.keys(data.awards).forEach(type => {
				data.awards[type].forEach(award => {
					award.available.forEach(available => {
						const readed = this.readed.awards.find(r => r.id === available.id)
						if(!readed) return this.readed.awards.push({
							id: available.id,
							updated_at: available.updated_at || available.created_at
						})
						if(readed.updated_at !== (available.updated_at || available.created_at)){
							readed.updated_at = available.updated_at || available.created_at
						}
					})
				})
			})

			this.unreadCount.awards = 0
			this.saveReadedPremiums()
		}
	}
})
