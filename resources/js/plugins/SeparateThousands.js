function install(Vue){
	Vue.prototype.$separateThousands = (number) => number.toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, '$1' + ' ')
}

export default {
	install
}
