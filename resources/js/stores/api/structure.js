import axios from 'axios'

export async function structureGet(){
	const {data} = await axios.get('/api/structure')
	return data
}

export async function structureCreate(request){
	const {data} = await axios.post('/api/structure/store', request)
	return data
}

export async function structureUpdate(id, request){
	const {data} = await axios.put(`/api/structure/${id}`, request)
	return data
}

export async function structureDelete(id){
	const {data} = await axios.delete(`/api/structure/${id}`)
	return data
}
