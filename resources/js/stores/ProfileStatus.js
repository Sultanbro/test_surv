import { defineStore } from 'pinia'
import { fetchProfileStatus } from '@/stores/api'


export const useProfileStatusStore = defineStore('ttstatus', {
	state: () => ({
		isReady: false,
		isLoading: false,
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
				this.isReady = true
			}
			catch(error){
				console.error('fetchStatus', error)
			}
			this.isLoading = false
		},
	}
})