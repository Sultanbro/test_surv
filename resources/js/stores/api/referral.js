import axios from 'axios'

export async function referralUserStat(){
	const {data} = await axios.get('/referrals/user/statistics')
	return data.data
}

export async function referralStat(){
	const {data} = await axios.get('/referrals/statistics')
	return data.data
}
