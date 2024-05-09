import axios from 'axios'
import { arrayUniqueKey } from '@/lib/array.js'
import moment from 'moment'


const AnV2Path = '/v2/analytics-page'

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
	// const {data} = await axios.post('/timetracking/analytics/new-group', request)
	const {data} = await axios.post(AnV2Path + '/create', request)
	return data.data
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
		delete data.data.group_id
		Object.keys(data.data).forEach(key => {
			const splitKey = key.split('_')
			const activityId = parseInt(splitKey[1])
			if(splitKey[0] === 'group' && activityId){
				result[activityId] = JSON.parse(data.data[key])
			}
		})
	}
	return result
}


function weeksOfMonth(year, month){
	const daysInMonth = moment(`${year}-${(month > 9 ? '0' : '') + month}`, 'YYYY-MM').daysInMonth()
	const date = new Date(year, month - 1, 1)
	const result = []
	for(let day = 1, week = []; day <= daysInMonth; ++day){
		week.push(day)
		date.setDate(day)
		if(!date.getDay()){
			result.push(week)
			week = []
		}
		else if(day === daysInMonth){
			result.push(week)
		}
	}
	return result
}

function isDefined(val){
	return typeof val !== 'undefined'
}

function getEmptyDecompositionValues(year, month){
	const result = {}
	const daysInMonth = moment(`${year}-${(month > 9 ? '0' : '') + month}`, 'YYYY-MM').daysInMonth()
	for(let day = 1; day <= daysInMonth; ++day){
		result[day] = {
			plan: null,
			fact: null,
		}
	}
	return result
}

const ActivityRecordsTable = {
	default(rec){
		const result = {
			id: rec.id,
			name: rec.name,
			lastname: rec.last_name,
			fullname: `${rec.last_name} ${rec.name}`,
			email: rec.email,
			fullTime: rec.full_time,
			fired: rec.fired || rec.status === 'fired' || rec.status === 'drop',
			isTrainee: rec.user_description ? rec.user_description.is_trainee : rec.isTrainee,
			appliedFrom: rec.applied_from,
			group: rec.group,
			plan: Number(rec.plan),
			userType: rec.user_type,
		}
		rec.statistics.forEach(stat => {
			result[stat.day] = Number(stat.value)
		})
		return result
	},
	collection(rec){
		const result = {
			id: rec.id,
			name: rec.name,
			lastname: rec.last_name,
			fullname: `${rec.last_name} ${rec.name}`,
			email: rec.email,
			fullTime: rec.full_time,
			fired: rec.fired || rec.status === 'fired' || rec.status === 'drop',
			isTrainee: rec.user_description ? rec.user_description.is_trainee : rec.isTrainee,
			appliedFrom: rec.applied_from,
			group: rec.group,
			plan: Number(rec.plan),
			userType: rec.user_type,
		}
		rec.statistics.forEach(stat => {
			result[stat.day] = Number(stat.value)
		})
		return result
	},
	quality(rec){
		const result = {
			id: rec.id,
			name: `${rec.last_name} ${rec.name}`,
			email: rec.email,
			total: 0,
			avg1: rec.avg1,
			avg2: rec.avg2,
			avg3: rec.avg3,
			avg4: rec.avg4,
			avg5: rec.avg5,
			userType: rec.user_type,
		}
		rec.week_qualities.forEach(stat => {
			result[stat.day] = Number(stat.total)
		})
		let weeks = 0
		ActivityRecordsTable.weeks.forEach((week, weekIndex) => {
			let avg = 0
			let count = 0
			week.forEach(day => {
				if(isDefined(result[day]) && result[day] > 0){
					avg += result[day]
					++count
				}
			})
			if(count > 0){
				const weekAvg = Math.round((avg / count) * 100) / 100
				result['avg' + (weekIndex + 1)] = weekAvg
				result.total += weekAvg
				++weeks
			}
		})

		if(weeks > 0) {
			result.total = Math.round((result.total / weeks) * 100) / 100
		}
		return result
	},
}

export async function fetchActivitiesV2(request){
	const {data} = await axios.get(AnV2Path + '/activities', {
		params: request
	})
	if(!data.data) throw new Error('AnalyticsV2: no activities')
	const activities = Array.isArray(data.data) ? data.data : Object.values(data.data)
	const weeks = weeksOfMonth(request.year, request.month)

	ActivityRecordsTable.year = request.year
	ActivityRecordsTable.month = request.month
	ActivityRecordsTable.weeks = weeks

	activities.forEach(act => {
		act.records = act.records.map(ActivityRecordsTable[act.type])
	})

	return {
		activities,
		weeks,
	}
}

export async function fetchDecompositionsV2(request){
	const {data} = await axios.get(AnV2Path + '/decompositions', {
		params: request
	})

	if(!data.data?.records) throw new Error('AnalyticsV2: no decompositions')
	const records = Array.isArray(data.data.records) ? data.data.records : Object.values(data.data.records)

	const result = records.reduce((result, rec) => {
		if(!rec.values || Array.isArray(rec.values)){
			rec.values = getEmptyDecompositionValues(request.year, request.month)
		}
		if(rec.group_id == data.data.group_id) result.push({
			...rec.values,
			id: rec.id,
			name: rec.name,
			// eslint-disable-next-line
			group_id: rec.group_id,
			date: rec.date,
			editable: false,
		})
		return result
	}, [])

	return result
}

export async function fetchPerformancesV2(request){
	const {data} = await axios.get(AnV2Path + '/performances', {
		params: request
	})

	if(!data.data) throw new Error('AnalyticsV2: no performances')

	const utility = data.data.utility || []
	const result = {
		utility: utility.map(item => ({
			...item,
			options: typeof item.options === 'string' ? JSON.parse(item.options) : item.options,
		})),
	}
	if(Array.isArray(data.data.rentability) && data.data.rentability.length){
		result.rentability = data.data.rentability.slice(-1)[0]
	}
	else if(!Array.isArray(data.data.rentability)){
		result.rentability = data.data.rentability
	}
	else{
		result.rentability = data.data.static_rentability
	}
	if(typeof result.rentability.options === 'string') result.rentability.options = JSON.parse(result.rentability.options)
	return result
}

export async function fetchFiredInfoV2(request){
	const {data} = await axios.get(AnV2Path + '/fired-info', {
		params: request
	})

	if(!data.data) throw new Error('AnalyticsV2: no fired info')
	return data.data
}

export async function fetchAnalyticsGroupsV2(request){
	const {data} = await axios.get(AnV2Path + '/groups', {
		params: request
	})

	if(!data.data?.groups) throw new Error('AnalyticsV2: no groups')
	return data.data.groups
}

export async function fetchAnalyticsV2(request){
	const {data} = await axios.get(AnV2Path + '/analytics', {
		params: request
	})

	if(!data.data?.table) throw new Error('AnalyticsV2: no analytics')
	/* eslint-disable camelcase */
	return {
		...data.data,
		table: data.data.table.map(row => Object.keys(row).reduce((result, key) => {
			const cell = row[key]
			result[key] = {
				...cell,
				value: cell.value || cell.show_value
			}
			return result
		}, {})),

		report_cards: {
			report_cards: arrayUniqueKey(data.data.report_cards?.report_cards || [])
		}
	}
	/* eslint-enable camelcase */
}

export async function fetchRentabilityV2(params){
	const { data } = await axios.get(AnV2Path + '/get-rentability', {params})
	const rent = data.data
	if(!Array.isArray(rent.speedometers)){
		rent.speedometers = Object.keys(rent.speedometers).map(key => {
			return rent.speedometers[key].slice(-1)[0]
		})
	}
	rent.speedometers = rent.speedometers.map(item => {
		const options = typeof item.options === 'string' ? JSON.parse(item.options) : item.options
		return {
			...item,
			options,
			sections: options?.staticLabels?.labels
		}
	})
	return {
		table: rent.table,
		speedometers: rent.speedometers,
		staticRent: rent.static_rentability,
	}
}
