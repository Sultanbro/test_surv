const key = ''

export function loadMapsApi(){
	if(window.ymaps) return
	const el = document.createElement('script')
	el.setAttribute('src', `https://api-maps.yandex.ru/2.1/?apikey=${key}&lang=ru_RU`)
	document.head.appendChild(el)
}
