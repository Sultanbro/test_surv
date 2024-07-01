import { defineStore } from 'pinia'
import { fetchSettings, updateSettings } from '@/stores/api'


export const useSettingsStore = defineStore('settings', {
	state: () => ({
		isReady: false,
		isLoading: false,
		logo: ''
	}),
	actions: {
		async fetchSettings(type = 'company'){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchSettings(type)
				if(data.settings?.logo) this.logo = data.settings?.logo
				this.isReady = true
			}
			catch(error){
				console.error('fetchSettings', error)
			}
			this.isLoading = false
		},
		async updateSettings(body, options){
			try{
				const data = await updateSettings(body, options)
				if(data.settings?.logo) this.logo = data.settings?.logo
				if(data.logo) this.logo = data.logo
			}
			catch(error){
				console.error('updateSettings', error)
			}
		}
	}
})