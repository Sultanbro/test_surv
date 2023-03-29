import { defineStore } from 'pinia'
import {
	fetchCurrentPortal,
	updateCurrentPortal,
} from '@/stores/api'

function initialState(){
	return {
		portal: {
			id: 0,
			tenant_id: '',
			owner_id: 0,
			currency: 'kzt',
			created_at: null,
			updated_at: null,
			main_page_video: null,
			main_page_video_show_days_amount: 0,
			kpi_backlight: null
		}
	}
}

export const usePortalStore = defineStore('portal', {
	state: () => ({
		isLoading: false,
		// state here
		...initialState()
	}),
	actions: {
		async fetchPortal(refresh){
			if(this.isLoading) return
			if(!(!this.portal.id || refresh)) return
			this.isLoading = true
			try{
				const { data } = await fetchCurrentPortal()
				this.portal = data
			}
			catch(error){
				console.error('fetchPortal', error)
			}
			this.isLoading = false
		},
		async updatePortal(request){
			await updateCurrentPortal({
				// ...this.portal,
				...request,
			})
		}
	}
})
