import axios from 'axios'

function addFavorites(items, favorites){
	items.forEach(item => {
		if(favorites.includes(item.id)) item.isFavorite = true
		if(item.children) addFavorites(item.children, favorites)
		item.factCategory = item.is_category
	})
	return items
}

/* eslint-disable camelcase */
function catInPage(items, parent = {is_category: 1}){
	items.forEach(item => {
		item.is_category = parent.is_category ? item.is_category : 0
		if(item.children) catInPage(item.children, item)
	})
	return items
}
/* eslint-enable camelcase */

function nest(items, id = null, link = 'parent_id'){
	return items
		.filter(item => item[link] === id)
		.map(item => ({ ...item, children: nest(items, item.id) }))
}

function booksTree(books){
	books.forEach(book => {
		// eslint-disable-next-line camelcase
		book.parent_id = book.parent_id || null
		book.canEdit = !!book.can_edit
	})
	const map = books.reduce((map, book) => {
		map[book.id] = structuredClone(book)
		return map
	}, {})

	return {
		map,
		tree: nest(books),
		flat: books,
		orphans: books.filter(book => book.parent_id && !map[book.parent_id])
	}
}
function booksOpen(books){
	books.forEach(book => {
		// book.opened = true
		book.canRead = true
	})
	return books
}

function canRead(tree){
	tree.forEach(book => {
		book.canRead = true
		if(book.children) canRead(book.children)
	})
}

function tree2flat(tree, books = []){
	tree.forEach(book => {
		books.push(book)
		if(book.children) tree2flat(book.children, books)
	})
	return books
}

export async function fetchKBBooks(){
	const {data} = await axios.get('/kb/get')
	return booksTree(booksOpen(data.books || []))
}

export async function fetchKBBooksV2(){
	const {data} = await axios.post('/kb/user-tree')
	const tree = data.data || []
	canRead(tree)
	const flat = tree2flat(tree, [])
	return {
		tree,
		flat,
		map: flat.reduce((result, book) => {
			result[book.id] = book
			return result
		}, {})
	}
}

export async function fetchKBArchived(){
	const {data} = await axios.get('/kb/get-archived')
	return data.books
}

export async function fetchKBBook(id){
	const {data} = await axios.post('/kb/tree', {id})
	return {
		...data,
		trees: catInPage(addFavorites(data.trees, data.favourite_ids))
	}
}

export async function deleteKBBook(id){
	const {data} = await axios.post('/kb/page/delete-section', {id})
	return data
}

export async function restoreKBBook(id){
	const {data} = await axios.post('/kb/page/restore-section', {id})
	return data
}

export async function searchKBBook(request){
	const {data} = await axios.post('kb/search', request)
	return data
}

export async function createKBBook(request){
	const {data} = await axios.post('/kb/page/add-section', request)
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

	return {
		whoCanRead: data.who_can_read || [],
		whoCanEdit: data.who_can_edit || [],
		whoCanReadPairs: data.who_can_read_pairs || [],
		whoCanEditPairs: data.who_can_edit_pairs || [],
	}
}

// если массив id
export async function fetchKBAccesses(id){
	const {data} = await axios.post('/kb/page/get-access', {id})

	return Object.keys(data).reduce((result, id) => {
		result[id] = {
			whoCanRead: data[id].who_can_read || [],
			whoCanEdit: data[id].who_can_edit || [],
			whoCanReadPairs: data[id].who_can_read_pairs || [],
			whoCanEditPairs: data[id].who_can_edit_pairs || [],
		}
		return result
	}, {})
}

export async function addKBPage(id){
	const {data} = await axios.post('/kb/page/create', {id})
	return data
}

/* favorites */
export async function fetchKBFavorites(){
	const {data} = await axios.get('/kb/get-favourites')
	return {
		items: data.items.map(page => {
			return {
				...page,
				canRead: true,
				canEdit: false,
				opened: true,
				isFavorite: true,
				book: {
					id: page.topParent_id,
					opened: true,
					canRead: true,
					canEdit: false,
				}
			}
		})
	}
}
export async function toggleKBPageFavorite(id, request){
	const {data} = await axios.post(`/kb/toggle-favorite/${id}`, request)
	return data
}
/* favorites */

/* glossary */
export async function fetchGlossary(){
	const {data} = await axios.get('/glossary/get')
	return data
}

export async function saveGlossaryTerm(id, word){
	const {data} = await axios.post('/glossary/save', {id, word})
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
/* glossary */
