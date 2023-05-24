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
			if(this.hasLocal()){
				this.loadLocal()
				this.isReady = true
			}
			try{
				const data = await fetchProfileCourses(false)
				this.courses = data
				const data2 = await fetchProfileCourses(true)
				data2?.forEach(course => {
					this.results[course.id] = course.course_results
				});
				this.saveLocal()
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
		},
		hasLocal(){
			return !!localStorage.getItem('profileCourses')
		},
		loadLocal(){
			let json = localStorage.getItem('profileCourses')
			if(!json){
				json = JSON.stringify({
					courses: [],
					courseInfo: {},
					results: {},
				})
				localStorage.setItem('profileCourses', json)
			}
			const local = JSON.parse(json)
			this.courses = local.courses
			this.courseInfo = local.courseInfo
			this.results = local.results
		},
		saveLocal(){
			localStorage.setItem('profileCourses', JSON.stringify({
				courses: this.courses.map(course => {
					return {
						id: course.id,
						img: course.img,
						name: course.name,
					}
				}),
				courseInfo: Object.keys(this.courseInfo).reduce((info, key) => {
					info[key] = {
						progress: this.courseInfo[key].progress,
						items: this.courseInfo[key].items.map(item => ({
							status: item.status,
							item_model: item.item_model,
							completed_stages: item.completed_stages,
							all_stages: item.all_stages,
							title: item.title,
						})),
					}
					return info
				}, {}),
				results: Object.keys(this.results).reduce((results, key) => {
					if(!this.results[key][0]) return results
					results[key] = [{
						is_regressed: this.results[key][0].is_regressed
					}]
					return results
				}, {}),
			}))
		},
	}
})
