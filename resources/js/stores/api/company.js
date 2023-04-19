/** @module stores/api/company */
import axios from 'axios'

export async function fetchDictionaries(){
	const {data} = await axios.get('/dictionaries')
	return data
}
