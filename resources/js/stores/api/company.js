/** @module stores/api/company */
import axios from 'axios'

export async function fetchDictionaries(){
	const {data} = await axios.get('/dictionaries')
	return data
}

export async function fetchCentralOwner(){
	const {data} = await axios.get('/company/get-owner')
	return data
}
