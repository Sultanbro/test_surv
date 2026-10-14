const lockers = new Set()
const locked = [document.documentElement, document.body]

function lock(){
	locked.forEach(el => {
		el.classList.add('v-scroll-locked')
	})
}

function unlock(){
	locked.forEach(el => {
		el.classList.remove('v-scroll-locked')
	})
}

function inserted(el, binding){
	if(binding.value){
		if(!lockers.size) lock()
		lockers.add(el)
	}
}

function componentUpdated(el, binding){
	if(binding.value){
		if(!lockers.size) lock()
		lockers.add(el)
	}
	else{
		lockers.delete(el)
		if(!lockers.size) unlock()
	}
}

function unbind(el){
	lockers.delete(el)
	if(!lockers.size) unlock()
}

export default {
	install(Vue){
		Vue.directive('scroll-lock', {
			inserted,
			componentUpdated,
			unbind,
		})
	}
}