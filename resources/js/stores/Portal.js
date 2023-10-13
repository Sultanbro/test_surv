/* global Laravel */
/* eslint-disable camelcase */

import { defineStore } from 'pinia'
import {
	fetchCurrentPortal,
	updateCurrentPortal,
} from '@/stores/api'

function initialState(){
	const tenant = location.hostname.split('.')[0]
	const tenants = Laravel.tenants || []
	if(tenants.includes('bp')) tenants.push('test')

	const isOwner = tenants.includes(tenant)
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
		},
		kpiBacklight: null,
		tenant,
		tenants,
		isMain: ['bp', 'test'].includes(tenant),
		isOwner,
		isAdmin: Laravel.is_admin || isOwner,
	}
}

function getBlankBacklight(min){
	return {
		startValue: min || 0,
		endValue: min || 0,
		prevMax: min || 0,
		color: ''
	}
}
function updateBacklight(value){
	if(!value || !value.length){
		return [getBlankBacklight()]
	}
	return value.map((item, index) => ({
		...item,
		prevMax: item.startValue,
		endValue: value[index + 1] ? value[index + 1].startValue : 100
	}))
}

export const usePortalStore = defineStore('portal', {
	state: () => ({
		isLoading: false,
		// state here
		...initialState(),
	}),
	actions: {
		async fetchPortal(refresh){
			if(this.isLoading) return
			if(!(!this.portal.id || refresh)) return
			this.isLoading = true
			try{
				const { data } = await fetchCurrentPortal()
				this.portal = data
				this.kpiBacklight = updateBacklight(this.portal.kpi_backlight)
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
		},
		getBlankBacklight,
		addBacklightColor(prev){
			this.kpiBacklight.push(getBlankBacklight(prev?.endValue || 0))
		},
		deleteBacklightColor(index){
			this.kpiBacklight.splice(index, 1)
			if(!this.kpiBacklight.length){
				this.addBacklightColor()
			}
		},
		updateBacklightColors(){
			return this.updatePortal({
				kpiBackLight: this.kpiBacklight.map(({startValue, color}) => ({
					start: startValue,
					color
				}))
			})
		},
		getBacklightForValue(value){
			if(!this.kpiBacklight || !this.kpiBacklight.length) return ''
			const num = +value
			const item = this.kpiBacklight.findLast(item => {
				return item.startValue <= num && num <= item.endValue
			})
			return item ? item.color : ''
		},
	}
})
