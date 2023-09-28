const key = '782ab94b-9310-410b-84b0-9c942252cc65'

export function loadMapsApi(){
	if(window.ymaps) return
	const el = document.createElement('script')
	el.setAttribute('src', `https://api-maps.yandex.ru/2.1/?apikey=${key}&lang=ru_RU`)
	document.head.appendChild(el)
}
