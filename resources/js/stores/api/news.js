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

function question2QNA(question){
	return {
		id: question.id,
		multiAnswer: !!question.multi_answer,
		question: question.question,
		order: question.order,
		answers: question.answers || [],
	}
}

export async function newsFetch(params){
	const { data } = await axios.get('/news/get', {params})
	return {
		pagination: data.data.pagination,
		// eslint-disable-next-line
		pinned_articles: data.data.pinned_articles?.map(article => {
			return {
				...article,
				questions: (article.questions || []).map(question2QNA)
			}
		}),
		articles: data.data.articles?.map(article => {
			return {
				...article,
				questions: (article.questions || []).map(question2QNA)
			}
		})
	}
}

export async function newsNextPage(url){
	const { data } = await axios.get(url)
	return data.data
}

export async function newsLike(id){
	const { data } = await axios.post(`/news/${id}/like`)
	return data.data
}

export async function newsViews(id){
	const { data } = await axios.post(`/news/${id}/views`)
	return data.data
}

export async function newsFavourite(id){
	const { data } = await axios.post(`/news/${id}/favourite`)
	return data.data
}

export async function newsPin(id){
	const { data } = await axios.post(`/news/${id}/pin`)
	return data.data
}

export async function newsVote(id, request){
	const { data } = await axios.post(`/news/${id}/vote`, request)
	return data.data
}

// переделать на request
export async function newsComment(id, formData){
	const { data } = await axios.post(`/news/${id}/comments`, formData)
	return data.data
}

export async function newsDelete(id){
	const { data } = await axios.delete(`/news/${id}`)
	return data.data
}

export async function newsCommentsFetch(id){
	const { data } = await axios.get(`/news/${id}/comments`)
	return data.data
}

export async function newsCommentsLike(postId, id){
	const { data } = await axios.post(`news/${postId}/comments/${id}/like`)
	return data.data
}

export async function newsCommentsReaction(postId, id, formData){
	const { data } = await axios.post(`news/${postId}/comments/${id}/reaction`, formData)
	return data.data
}

export async function newsCommentsDelete(postId, id){
	const { data } = await axios.delete(`news/${postId}/comments/${id}`)
	return data.data
}
