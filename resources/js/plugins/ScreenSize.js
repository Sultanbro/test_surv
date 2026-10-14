let size = null
let VC = null

function updateScreenSize(){
	if(!VC || !size) return
	const width = window.screen.width
	const height = window.screen.height

	VC.set(size, 'width', width)
	VC.set(size, 'height', height)
	VC.set(size, 'position', width > height ? 'landscape' : 'portrait')
}

function install(Vue){
	if(VC) return
	size = Vue.observable({
		width: 0,
		height: 0,
		position: 'portrait'
	})
	VC = Vue
	window.addEventListener('resize', updateScreenSize)
	updateScreenSize()
	Vue.prototype.$screenSize = size
}

export default {
	install
}
