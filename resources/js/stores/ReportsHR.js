import { defineStore } from 'pinia'
import {
	fetchHRRecruitment,
	fetchHRIndicators,
	fetchHRTrainees,
	fetchHRInternship,
	fetchHRFunnels,
	fetchHRDismiss,
} from '@/stores/api'

// import skypesMock from './skypes.json'


export const useHRStore = defineStore('hr', {
	state: () => ({
		isLoading: false,
		isReady: false,
		error: null,
		// recruiter
		recruiterStats: [],
		recruiterStatsLeads: [],
		recruiterStatsRates: {},
		// indicators
		indicatorsDate: '',
		indicators: null,
		records: [],
		hrs: [], // ????
		// Trainees
		inviteGroups: {},
		segments: {},
		sgroups: [],
		skypes: {},
		// Internship
		absentsFirst: [],
		absentsSecond: [],
		absentsThird: [],
		ocenkaSvod: [],
		traineeReport: [],
		// funnels
		funnels: {},
		// Dismiss
		causes: [],
		quiz: [],
		staff: [],
		staffByGroup: [],
		staffLongevity: [],
	}),
	actions: {
		async fetchRecruitment(params){
			this.isLoading = true
			try{
				const data = await fetchHRRecruitment(params)
				this.recruiterStats = data.recruiter_stats
				this.recruiterStatsLeads = data.recruiter_stats_leads
				this.recruiterStatsRates = data.recruiter_stats_rates
				this.error = data.error || data.message || null
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRRecruitment', error)
			}
			this.isLoading = false
		},
		async fetchIndicators(params){
			this.isLoading = true
			try{
				const data = await fetchHRIndicators(params)
				this.indicatorsDate = data.date
				this.indicators = data.indicators
				this.records = data.records
				this.hrs = data.hrs
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRIndicators', error)
			}
			this.isLoading = false
		},
		async fetchTrainees(params){
			this.isLoading = true
			try{
				const data = await fetchHRTrainees(params)
				this.inviteGroups = data.invite_groups
				this.segments = data.segments
				this.sgroups = data.sgroups
				this.skypes = data.skypes
				// this.skypes = skypesMock
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRTrainees', error)
			}
			this.isLoading = false
		},
		async fetchInternship(params){
			this.isLoading = true
			try{
				const data = await fetchHRInternship(params)
				this.absentsFirst = data.absents_first
				this.absentsSecond = data.absents_second
				this.absentsThird = data.absents_third
				this.ocenkaSvod = data.ocenka_svod
				this.traineeReport = data.trainee_report
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRInternship', error)
			}
			this.isLoading = false
		},
		async fetchFunnels(params){
			this.isLoading = true
			try{
				const data = await fetchHRFunnels(params)
				this.funnels = data.funnels
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRFunnels', error)
			}
			this.isLoading = false
		},
		async fetchDismiss(params){
			this.isLoading = true
			try{
				const data = await fetchHRDismiss(params)
				this.causes = data.causes
				this.quiz = data.quiz
				this.staff = data.staff
				this.staffByGroup = data.staff_by_group
				this.staffLongevity = data.staff_longevity
				this.isReady = true
			}
			catch(error){
				console.error('fetchHRDismiss', error)
			}
			this.isLoading = false
		},
	}
})
