/* global Laravel */
import { defineStore } from 'pinia'
import moment from 'moment'
import {
	fetchProfileSalary,
	fetchProfileBalance,
	fetchProfileKpi,
	fetchProfilePremiums,
	fetchProfileBonuses,
	fetchPosibleBonuses,
	fetchProfileAwards,
	setReadedKPI,
	setReadedBonus,
	setReadedPremium,
	setReadedAward,
	fetchAvailableBonuses,
} from '@/stores/api'
import { calcSum } from '@/pages/kpi/kpis.js'

const STORAGE_READED_KEY = 'profileSalaryReadedV2'

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
			kpis: true,
			bonuses: true,
			premiums: true,
			awards: true
		},
		possibleBonuses: null,
		currentKey: `${now.getFullYear()}-${now.getMonth()}`,
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

			// local
			if(this.hasLocal()){
				this.loadLocal()
				this.isReady = true
			}

			// salary
			let salary = {
				salaries: {}
			}
			try {
				const { data } = await fetchProfileBalance(year, month)
				salary = data
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}
			const sumSalary = Object.values(salary.salaries).reduce((result, day) => result + (parseInt(day.value) || 0), 0)
			this.user_earnings.sumSalary = sumSalary

			// kpi
			let kpis = []
			try {
				const { items, read } = await fetchProfileKpi(year, month)
				kpis = items
				this.readed.kpis = read
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}
			const sumKpi = kpis.reduce((result, kpi) => {
				kpi.users.forEach(user => {
					user.items.forEach(userItem => {
						result += calcSum(userItem, kpi, userItem.percent / 100)
					})
				});
				return result
			}, 0)
			const maxKpi = kpis.reduce((result, kpi) => result + (parseInt(kpi.completed_100) || 0), 0)

			// bonus
			let bonuses = {
				history: []
			}
			try {
				const { data } = await fetchProfileBonuses(year, month)
				bonuses = data
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}
			const sumBonuses = bonuses.history.reduce((result, bonus) => result + (parseInt(bonus.sum) || 0), 0)
			this.checkReadedBonuses()

			// premium
			let premiums = []
			try {
				const { data, read } = await fetchProfilePremiums(year, month)
				premiums = data
				this.readed.premiums = read
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}
			const sumQuartalPremiums = premiums.reduce((result, premium) => {
				premium.forEach(el => {
					if(el.items?.sum && el.items?.to?.substring(0, 7) === moment(Date.now()).format('YYYY-MM')) result += el.items.sum
				})
				return result
			}, 0)

			// awards
			let awards = {}
			try {
				const data = {
					nominations: await fetchProfileAwards('nomination'),
					certificates: await fetchProfileAwards('certificate'),
					accrual: await fetchProfileAwards('accrual'),
				}
				awards = data
				this.readed.awards = data.nominations.data.read
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}
			const sumAwards = Object.values(awards).reduce((result, type) => {
				type.data.forEach(award => {
					if(award.my && award.my.length) return result + award.my.length
				})
				return result
			}, 0)

			// set current values
			const earnings = {
				sumSalary,
				sumKpi,
				sumBonuses,
				sumQuartalPremiums,
				sumNominations: sumAwards,
				kpiMax: maxKpi,
			}
			this.user_earnings = earnings

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
			}

			this.isReady = true
			this.saveLocal()

			// Можно запрышивать уже после основных данных т.к. нужны только для проверки
			if(!this.possibleBonuses){
				try {
					this.possibleBonuses = await fetchPosibleBonuses()
				}
				catch (error) {
					console.error('fitchSalaryCrutch', error)
				}
			}

			this.isLoading = false
		},
		async checkReadedBonuses(){
			try {
				const { read } = await fetchAvailableBonuses()
				this.readed.bonuses = read
			}
			catch (error) {
				console.error('checkReadedBonuses', error)
			}
		},
		loadReadedPremiums(){
			let readedJSON = localStorage.getItem(STORAGE_READED_KEY)
			if(!readedJSON){
				readedJSON = JSON.stringify({
					kpis: true,
					bonuses: true,
					premiums: true,
					awards: true,
				})
				localStorage.setItem(STORAGE_READED_KEY, readedJSON)
			}
			this.readed = JSON.parse(readedJSON)
		},
		saveReadedPremiums(){
			localStorage.setItem(STORAGE_READED_KEY, JSON.stringify(this.readed))
		},
		setReadedKpis(){
			setReadedKPI()
			this.readed.kpis = true
			this.saveReadedPremiums()
		},
		setReadedBonuses(){
			setReadedBonus()
			this.readed.bonuses = true
			this.saveReadedPremiums()
		},
		setReadedPremiums(){
			setReadedPremium()
			this.readed.premiums = true
			this.saveReadedPremiums()
		},
		setReadedAwards(){
			setReadedAward()
			this.readed.awards = true
			this.saveReadedPremiums()
		},
		hasLocal(){
			const json = localStorage.getItem('profileSalary')
			if(!json) return false
			const local = JSON.parse(json)
			return local.userId === Laravel.userId
		},
		loadLocal(){
			let json = localStorage.getItem('profileSalary')
			if(!json){
				json = JSON.stringify({
					userId: Laravel.userId,
					user_earnings: {},
				})
				localStorage.setItem('profileSalary', json)
			}
			const local = JSON.parse(json)
			this.user_earnings = local.user_earnings
		},
		saveLocal(){
			localStorage.setItem('profileSalary', JSON.stringify({
				userId: Laravel.userId,
				user_earnings: this.user_earnings,
			}))
		},
	}
})
