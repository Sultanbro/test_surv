import Vue from 'vue'
import { defineStore } from 'pinia'
import { fetchProfileCourses, fetchMyCourseInfo } from '@/stores/api'


export const useProfileCoursesStore = defineStore('profileCourses', {
	state: () => ({
		isReady: false,
		isLoading: false,
		isLoadingCourse: {},
		courses: [],
		courseInfo: {},
		results: {},
	}),
	actions: {
		addProgress(course){
			return {
				...course,
				progress: course.all_stages ? Number((course.completed_stages / course.all_stages) * 100).toFixed(1) : 0
			}
		},
		async fetchCourses(/* progress = false */){
			if(this.isLoading) return
			this.isLoading = true
			try{
				const data = await fetchProfileCourses(false)
				this.courses = data
				const data2 = await fetchProfileCourses(true)
				data2?.forEach(course => {
					this.results[course.id] = course.course_results
				});
				this.isReady = true
			}
			catch(error){
				console.error('fetchCourses', error)
			}
			this.isLoading = false
		},
		async fetchCourseInfo(id){
			if(this.isLoadingCourse[id]) return
			this.isLoadingCourse[id] = true
			try {
				const data = await fetchMyCourseInfo(id)
				Vue.set(this.courseInfo, id, this.addProgress(data))
				// this.courseInfo[id] = this.addProgress(data)
			}
			catch(error){
				console.error(error)
			}
			delete this.isLoadingCourse[id]
		}
	}
})