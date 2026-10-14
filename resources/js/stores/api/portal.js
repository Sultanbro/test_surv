/** @module stores/api/partal */
import axios from 'axios'

/**
 * ...
 * @return {PortalRequest}
 */
export async function fetchCurrentPortal(){
	const { data } = await axios.get('/portal/current')
	return data
}

/**
 * ...
 * @return {PortalRequest}
 */
export async function updateCurrentPortal(request){
	const { data } = await axios.post('/portal/update', request)
	return data
}
