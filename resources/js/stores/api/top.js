import axios from 'axios'

export async function topArchiveUtility(request){
	const { data } = await axios.post('/top/utility-archive', request)
	return data
}
