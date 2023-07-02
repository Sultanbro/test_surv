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
