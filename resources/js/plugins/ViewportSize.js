let size = null
let VC = null

function updateViewportSize(){
	if(!VC || !size) return
	VC.set(size, 'width', Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0))
	VC.set(size, 'height', Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0))
	VC.set(size, 'rem', parseFloat(getComputedStyle(document.documentElement).fontSize))
}

function install(Vue){
	if(VC) return
	size = Vue.observable({
		width: 0,
		height: 0,
		rem: 16
	})
	VC = Vue
	window.addEventListener('resize', updateViewportSize)
	updateViewportSize()
	Vue.prototype.$viewportSize = size
}

export default {
	install
}
