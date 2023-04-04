import { defineStore } from 'pinia'
import {
	fetchNotifications,
	setNotificationsRead,
	setNotificationsReadAll,
} from '@/stores/api'

export const useNotificationsStore = defineStore('notifications', {
	state: () => ({
		isLoading: false,
		// state here
		read: null,
		unread: null,
		unreadQuantity: 0
	}),
	actions: {
		async fetchNotifications(){
			this.isLoading = true
			try {
				const data = await fetchNotifications()
				this.read = data.read
				this.unread = data.unread
				this.unreadQuantity = data.unreadQuantity
			}
			catch (error) {
				console.error('fetchNotifications', error)
			}
		},
		async setNotificationsRead(id){
			try{
				const { message } = await setNotificationsRead({
					user_notification_id: id,
				})
				if(message !== 'Success') return message

				const i = this.unread.findIndex(el => el.id === id)
				this.read.unshift(this.unread[i])
				this.unread.splice(i, 1)

				this.unreadQuantity--
			}
			catch(error){
				console.error('setNotificationsRead', error)
				return error
			}
		},
		async setNotificationsReadAll(){
			try{
				const { data } = await setNotificationsReadAll()
				if (data) {
					this.unread.forEach(el => {
						this.read.push(el)
					})

					this.unread = []
					this.unreadQuantity = 0

					return true
				}
			}
			catch(error){
				console.error('setNotificationsReadAll', error)
			}
			return false
		},
	}
})
