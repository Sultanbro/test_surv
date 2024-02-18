/* global Laravel */
/* eslint-disable camelcase */

import { defineStore } from 'pinia'
import moment from 'moment'
import axios from 'axios'
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
import { calcCompleted, calcSum, parseKPI } from '@/pages/kpi/kpis.js'
import { calcTaxes } from '@/lib/salary.js'

const STORAGE_READED_KEY = 'profileSalaryReadedV2'
const LOCAL_CACHE_KEY = 'profileSalaryV2'

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
			let currencyRate = 1
			try {
				const { items, read, currency_rate: rate } = await fetchProfileKpi(year, month)
				kpis = items
				currencyRate = rate
				this.readed.kpis = read
			}
			catch (error) {
				console.error('fitchSalaryCrutch', error)
			}

			const targetables = {
				'App\\User': 1,
				'App\\Position': 2,
				'App\\ProfileGroup': 3,
			}

			kpis.sort((a, b) => targetables[a.targetable_type] - targetables[b.targetable_type])

			const sumKpi = parseInt(kpis.map(res => parseKPI(res)).reduce((result, kpi) => {
				kpi.users.forEach(user => {
					user.items.forEach(userItem => {
						result += calcSum(userItem, kpi, calcCompleted(userItem) / 100)
					})
				});
				return result
			}, 0) * currencyRate)
			const maxKpi = parseInt(kpis.reduce((result, kpi) => result + (parseInt(kpi.completed_100) || 0), 0) * currencyRate)

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
				this.readed.awards = data.nominations.read && data.certificates.read && data.accrual.read
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

			// taxes
			const total = sumSalary + sumKpi + sumBonuses
			let taxes = []
			let taxGroup = ''
			try {
				const { data } = await axios.get(`/taxes/${Laravel.userId}/user`)
				taxGroup = data.data?.name || ''
				taxes = data.data?.items || []
			}
			catch (error) {
				console.error(error)
			}
			const taxValue = total - calcTaxes(total, taxes, currencyRate)
			// let totalAfterTaxes = sumSalary + sumKpi + sumBonuses
			// let taxes = 0
			// salary.taxes.forEach(tax => {
			// 	if(tax.end_subtraction) return
			// 	totalAfterTaxes -= tax.pivot.is_percent ? Math.round(total * tax.value / 100) : tax.value
			// 	taxes += tax.pivot.is_percent ? Math.round(total * tax.value / 100) : tax.value
			// })
			// salary.taxes.forEach(tax => {
			// 	if(!tax.end_subtraction) return
			// 	taxes += tax.pivot.is_percent ? Math.round(totalAfterTaxes * tax.value / 100) : tax.value
			// })

			// set current values
			const earnings = {
				sumSalary,
				sumKpi,
				sumBonuses,
				sumQuartalPremiums,
				sumNominations: sumAwards,
				kpiMax: maxKpi,
				taxes,
				taxGroup,
				taxValue,
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
				taxes,
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
			const json = localStorage.getItem(LOCAL_CACHE_KEY)
			if(!json) return false
			const local = JSON.parse(json)
			return local.userId === Laravel.userId
		},
		loadLocal(){
			let json = localStorage.getItem(LOCAL_CACHE_KEY)
			if(!json){
				json = JSON.stringify({
					userId: Laravel.userId,
					user_earnings: {},
				})
				localStorage.setItem(LOCAL_CACHE_KEY, json)
			}
			const local = JSON.parse(json)
			this.user_earnings = local.user_earnings
		},
		saveLocal(){
			localStorage.setItem(LOCAL_CACHE_KEY, JSON.stringify({
				userId: Laravel.userId,
				user_earnings: this.user_earnings,
			}))
		},
	}
})
