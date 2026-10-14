import axios from 'axios'
import fetchTimetrackingNPS from './mock/top/fetchTimetrackingNPS.json'

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

export async function fetchTopNPS(){
	return fetchTimetrackingNPS
}
