import { defineStore } from 'pinia'
import { fetchProfileCourses } from '@/stores/api'


export const useTemplateStore = defineStore('profileCourses', { // replace useTemplateStore, template !!
	state: () => ({
		isReady: false,
		isLoading: false,
		courses: [],
	}),
	actions: {
		async fetchCourses(){
			this.isLoading = true
			try{
				const data = await fetchProfileCourses(true)
				this.courses = data
				this.isReady = true
			}
			catch(error){
				console.error('fetchCourses', error)
			}
			this.isLoading = false
		},
	}
})