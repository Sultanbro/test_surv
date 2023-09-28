import axios from 'axios'

export async function fetchAnalyticsMonthlyStats(request){
	const {data} = await axios.post('/timetracking/user-statistics-by-month', request)
	return data
}

export async function fetchAnalytics(request){
	const {data} = await axios.post('/timetracking/analytics-page/getanalytics', request)
	return data
}

export async function createAnalyticsActivity(request){
	const {data} = await axios.post('/timetracking/analytics/create-activity', request)
	return data
}

export async function deleteAnalyticsActivity(request){
	const {data} = await axios.post('/timetracking/analytics/delete_activity', request)
	return data
}

export async function createAnalyticsGroup(request){
	const {data} = await axios.post('/timetracking/analytics/new-group', request)
	return data
}

export async function archiveAnalyticsGroup(request){
	const {data} = await axios.post('/timetracking/analytics/archive_analytics', request)
	return data
}

export async function restoreAnalyticsGroup(request){
	const {data} = await axios.post('/timetracking/analytics/restore_analytics', request)
	return data
}

export async function updateAnalyticsOrder(request){
	const {data} = await axios.post('/timetracking/analytics/change_order', request)
	return data
}

export async function hideAnalyticsActivityUsers(request){
	const {data} = await axios.post('/timetracking/analytics/activity/remove/group', request)
	return data
}

export async function getAnalyticsActivityHiddenUsers(groupId){
	const {data} = await axios.get(`/timetracking/analytics/activity/removed/users/${groupId}`)
	const result = {}
	if(data.data){
		result.groupId = data.data.group_id
		delete data.data.group_id
		Object.keys(data.data).forEach(key => {
			const splitKey = key.split('_')
			if(splitKey[0] === 'group' && parseInt(splitKey[1])){
				result[parseInt(splitKey[1])] = JSON.parse(data.data[key])
			}
		})
	}
	return result
}
