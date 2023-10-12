import axios from 'axios'

export async function fetchKBBooks(){
	const {data} = await axios.get('/kb/get')
	return data.books
}

export async function fetchKBArchived(){
	const {data} = await axios.get('/kb/get-archived')
	return data.books
}

export async function fetchKBBook(id){
	const {data} = await axios.post('/kb/tree', {id})
	return data
}

export async function deleteKBBook(id){
	const {data} = await axios.post('/kb/page/delete-section', {id})
	return data
}

export async function restoreKBBook(id){
	const {data} = await axios.post('/kb/page/restore-section', {id})
	return data
}

export async function searchKBBook(text){
	const {data} = await axios.post('kb/search', {text})
	return data
}

export async function createKBBook(name){
	const {data} = await axios.post('/kb/page/add-section', {name})
	return data
}
export async function updateKBBook(request){
	const {data} = await axios.post('/kb/page/update-section', request)
	return data
}

export async function updateKBOrder(request){
	const {data} = await axios.post('/kb/page/save-order', request)
	return data
}

export async function fetchKBAccess(id){
	const {data} = await axios.post('/kb/page/get-access', {id})
	return data
}

export async function fetchGlossary(){
	const {data} = await axios.get('/glossary/get')
	return data
}

export async function saveGlossaryTerm(word){
	const {data} = await axios.post('/glossary/save', {word})
	return data
}

export async function deleteGlossaryTerm(id){
	const {data} = await axios.post('/glossary/delete', {id})
	return data
}

export async function fetchGlossaryAccess(){
	const {data} = await axios.post('/glossary/get-access')
	return data.who_can_edit
}

export async function updateGlossaryAccess(targets){
	const {data} = await axios.post('/glossary/update-access', {
		// eslint-disable-next-line camelcase
		who_can_edit: targets
	})
	return data
}
