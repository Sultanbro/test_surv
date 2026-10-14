import { defineStore } from 'pinia'
import {
	fetchKPIStatYear,
} from '@/stores/api'

function initialState(){
	const now = new Date()
	const currentYear = now.getFullYear()
	// const currentMonth = now.getMonth()
	return {
		statYear: {
			data: [],
			lastPage: 0,
			rows: 0,
			page: 1,
			limit: 10,
			year: currentYear
		}
	}
}

export const useKPIStore = defineStore('kpi', {
	state: () => ({
		isLoading: {},
		// state here
		...initialState()
	}),
	actions: {
		async fetchStatYear(year = this.statYear.year, page = this.statYear.page, limit = this.statYear.limit){
			this.isLoading.year = true
			try{
				const {paginator} = await fetchKPIStatYear({
					year,
					page,
					limit,
				})
				this.statYear.data = paginator.data
				this.statYear.lastPage = paginator.last_page
				this.statYear.page = paginator.current_page
				this.statYear.rows = paginator.total
			}
			catch(error){
				console.error('fetchKPIStatYear', error)
			}
			this.isLoading.year = false
		},
		setStatYearPage(page){
			if(this.statYear.page === page) return
			this.statYear.page = page
			this.fetchStatYear()
		},
		setStatYearYear(year){
			if(this.statYear.year === year) return
			this.statYear.year = year
			this.fetchStatYear()
		}
	}
})
