import axios from 'axios'

function referalToUser(ref){
	const user = {
		id: Number(ref.id),
		title: `${ref.name} ${ref.last_name}`,
		status: ref.referrer_status,
	}

	const datetypes = ref.datetypes || {}
	const days = []
	for(let i = 0; i < 31; ++i){
		if(datetypes[i + 1]){
			days.push({
				...datetypes[i + 1],
				day: i + 1
			})
		}
	}
	days.forEach((day, index) => {
		if(index < 15) user[index + 1] = day
	})
	user.attest = datetypes['pass_certification'] || datetypes['pass certification']
	user.firstWeek = datetypes['1_week']
	user.secondWeek = datetypes['2_week']
	user.thirdWeek = datetypes['3_week']
	user.fourthWeek = datetypes['4_week']
	user.sixthWeek = datetypes['6_week']
	user.eighthWeek = datetypes['8_week']
	user.twelfthWeek = datetypes['12_week']
	user.users = ref.users?.map(referalToUser) || []
	return user
}

function referrerToUser(user){
	return {
		id: Number(user.id),
		title: `${user.name} ${user.last_name}`,
		status: user.referrer_status,
		leads: Number(user.leads || 0),
		deals: Number(user.deals || 0),
		leadsToDealPercent: Number(user.deal_lead_conversion_ratio || 0),
		accepted: Number(user.applieds || 0),
		dealToUserPercent: Number(user.appiled_deal_conversion_ratio || 0),
		total: Number(user.absolute_earned || 0),
		month: Number(user.month_earned || user.month_paid || 0),
		monthRef: Number(user.referrers_earned || user.referrers_paid || 0),
		monthPaid: Number(user.month_paid || 0),
		avatar: 'users_img/' + user.img_url,
		users: user.users?.map(referalToUser) || []
	}
}

export async function referralUserStat(){
	const {data} = await axios.get('/referrals/user/statistics')
	return {
		month: Number(data.data?.mine || 0),
		monthRef: Number(data.data?.from_referrals || 0),
		total: Number(data.data?.absolute || 0),
		tops: data.data?.top,
		users: data.data?.referrals?.map(referrerToUser) || [],
	}
}

export async function referralStat({year, month}){
	const mon = month + 1
	const params = {
		date: `${year}-${mon > 9 ? mon : '0' + mon}-01`
	}
	const {data} = await axios.get('/referrals/statistics', { params })
	return {
		userPrice: Number(data.data?.pivot?.employee_price || 0),
		cvResultDealPercent: Number(data.data?.pivot?.deal_lead_conversion || 0),
		cvDealUserPercent: Number(data.data?.pivot?.applied_deal_conversion || 0),
		users: data.data?.referrers?.map(referrerToUser) || [],
	}
}

export async function referralStatPay(userId, request){
	const data = await axios.post(`/referrals/paid/${userId}`, request)
	return data.message
}
