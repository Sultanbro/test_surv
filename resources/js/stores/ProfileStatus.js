import { defineStore } from 'pinia'
import { fetchProfileStatus, updateProfileStatus } from '@/stores/api'


export const useProfileStatusStore = defineStore('profileStatus', {
	state: () => ({
		isReady: false,
		isLoading: false,
		buttonStatus: 'init',
		status: 'stopped',
		groupsall: [],
		orders: [],
		zarplata: '',
		bonus: '',
		total_earned: '',
		balance: {
			loading: true
		},
		corp_book: null,
	}),
	actions: {
		async fetchStatus(){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchProfileStatus()
				this.status = data.status
				this.groupsall = data.groupsall
				this.orders = data.orders
				this.zarplata = data.zarplata
				this.bonus = data.bonus
				this.total_earned = data.total_earned
				this.balance = data.balance
				this.corp_book = data.corp_book
			}
			catch(error){
				console.error('fetchStatus', error)
			}
			this.isLoading = false
			this.isReady = true
		},
		async updateStatus(body){
			try{
				const data = await updateProfileStatus(body)
				this.status = data.status
				this.corp_book = data.corp_book
				// this.corp_book = data.corp_book.page
			}
			catch(error){
				console.error('updateStatus', error)
			}
		}
	}
})