import axios from 'axios'
import { obj2request } from '@/composables/request.js'

export async function topArchiveUtility(request){
	const { data } = await axios.post('/top/utility-archive', request)
	return data
}

export async function fetchTop(request){
	const { data } = await axios.post('/timetracking/top', request)
	return data
}

export async function fetchArchiveUtility(){
	const { data } = await axios.get('/top/utility/list')
	return data
}
export async function fetchArchiveRentability(){
	const { data } = await axios.get('/top/rentability/list')
	return data
}
export async function fetchArchiveProceeds(){
	const { data } = await axios.get('/top/proceeds/list')
	return data
}

export async function switchArchiveTop(request){
	const { data } = await axios.post('/top/switch', request)
	return data
}

export async function fetchTopNPS(request){
	const { data } = await axios.post('/timetracking/nps', request)
	return data
}

export async function fetchTopPredicts(){
	const { data } = await axios.get('/v2/analytics-page/predicts/')
	return data.data
}

const updateTopPredictsReplaces = {
	groupId: 'group_id'
}
export async function updateTopPredicts(request){
	const { data } = await axios.post('/timetracking/top/save_group_plan', obj2request(request, updateTopPredictsReplaces))
	return data.data
}
