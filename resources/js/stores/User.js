import { defineStore } from 'pinia'
import axios from 'axios'


export const useUserStore = defineStore('user', {
	state: () => ({
		isReady: false,
		isLoading: false,
		// state here
		user: {},
	}),
	actions: {
		async fetchUser(force){
			if(this.isReady && !force) return
			if(this.isLoading) return
			this.isLoading = true
			try{
				const {data} = await axios.get('/messenger/api/v2/chats')
				this.user = data.user
				this.isReady = true
			}
			catch(error){
				console.error('fetchUser', error)
			}
			this.isLoading = false
		},
	}
})
