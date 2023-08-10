import axios from 'axios'

function getDataFromScript(dataScript){
	try{
		const data = JSON.parse(dataScript.innerHTML.replace(/&quot;/g,'"'))
		dataScript.remove()
		return data
	}
	catch(err){
		console.error(err)
	}
}

export function useDataFromResponse(data){
	const doc = document.createElement('div')
	// const html = data.split('<body ontouchstart="">')[1]
	// doc.innerHTML = html.replace('</body>', '').replace('</html>', '')
	doc.innerHTML = data
	const dataScript = doc.querySelector('#async-page-data')
	if(dataScript) return getDataFromScript(dataScript)
}

export function useAsyncPageData(url){
	return new Promise((resolve, reject) => {
		const dataScript = document.getElementById('async-page-data')
		if(dataScript) return resolve(getDataFromScript(dataScript))
		axios.get(url).then(({ data }) => {
			const responseData = useDataFromResponse(data)
			if(responseData) return resolve(responseData)
			reject('No data')
		}).catch(reject)
	})
}
