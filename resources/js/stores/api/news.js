import axios from 'axios'

export async function newsCreate(formData){
	const { data } = await axios.post('/news', formData, {
		headers: {
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		}
	})

	return data
}

export async function newsUpdate(id, formData){
	formData.append('_method', 'put')
	const { data } = await axios.post(`/news/${id}`, formData, {
		headers: {
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		}
	})

	return data
}

export async function newsFetch(params){
	const { data } = await axios.get('/news/get', {params})
	return data.data
}

export async function newsNextPage(url){
	const { data } = await axios.get(url)
	return data.data
}
