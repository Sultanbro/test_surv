import { defineStore } from 'pinia'
import * as API from '@/stores/api/referral'


export const useReferralStore = defineStore('referral', {
	state: () => ({
		isReady: false,
		isLoading: false,
		total: 0,
		month: 0,
		monthRef: 0,
		leads: 0,
		users: [],
		tops: [],
	}),
	actions: {
		async fetchUserStats(){
			this.isLoading = true

			try{
				const {
					tops,
					users,
					month,
					monthRef,
					total,
				} = await API.referralUserStat()
				this.total = total
				this.month = month
				this.monthRef = monthRef
				this.leads = (users[0]?.users || []).length
				this.users = users
				this.tops = tops
				this.isReady = true
			}
			catch(error){
				console.error('fetchUserStats', error)
			}
			this.isLoading = false
		},
	}
})

