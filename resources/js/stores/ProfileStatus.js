import { defineStore } from 'pinia'
import {
	fetchProfileStatus,
	updateProfileStatus,
	pushOvertime,
} from '@/stores/api'


export const useProfileStatusStore = defineStore('profileStatus', {
	state: () => ({
		isReady: false,
		isLoading: false,
		buttonStatus: 'init',
		status: 'stopped',
		message: '',
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
				const data = await updateProfileStatus(body);
				if(data.status){
					this.status = data.status;
					this.message = data.error ? data.error.message : null;
				}
				else{
					this.status = 'workdone'
					this.message = 'Вы не можете начать рабочий день в выходной'
				}
				if(data.error){
					this.status = 'workdone';
				}
				this.corp_book = data.corp_book
				// this.corp_book = data.corp_book.page
			}
			catch(error){
				this.message = error;
				console.error('updateStatus', error)
			}
		},
		// Приходится делать тут т.к. компонент Questions меняет пропсы
		resetCorpBookAnswers(){
			if(!this.corp_book) return
			if(!(this.corp_book.questions && this.corp_book.questions.length)) return
			this.corp_book.questions.forEach(question => {
				question.result = null
				question.variants.forEach(variant => {
					delete variant.checked
				})
			})
		},
		async pushOvertime(request){
			return await pushOvertime(request)
		}
	}
})
