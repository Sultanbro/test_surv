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
		referrals: [],
		tops: [],
	}),
	actions: {
		async fetchUserStats(){
			this.isLoading = true

			try{
				const {
					tops,
					referrals,
					mine,
					from_referrals: monthRef,
					absolute
				} = await API.referralUserStat()
				this.total = absolute
				this.month = mine
				this.monthRef = monthRef
				this.leads = referrals.length
				this.referrals = referrals
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

