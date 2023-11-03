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
