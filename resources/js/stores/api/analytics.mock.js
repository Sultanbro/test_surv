import fetchAnalyticsMonthlyStatsData from './mock/fetchAnalyticsMonthlyStats.json'
import fetchAnalyticsData from './mock/fetchAnalytics.json'
import createAnalyticsActivityData from './mock/createAnalyticsActivity.json'
import restoreAnalyticsGroupData from './mock/restoreAnalyticsGroup.json'

export async function fetchAnalyticsMonthlyStats(){
	return fetchAnalyticsMonthlyStatsData
}

export async function fetchAnalytics(){
	return fetchAnalyticsData
}

export async function createAnalyticsActivity(){
	return createAnalyticsActivityData
}

export async function deleteAnalyticsActivity(){
	return 0
}

export async function createAnalyticsGroup(){
	return 0
}

export async function archiveAnalyticsGroup(){
	return 0
}

export async function restoreAnalyticsGroup(){
	return restoreAnalyticsGroupData
}

export async function updateAnalyticsOrder(){
	return 0
}
