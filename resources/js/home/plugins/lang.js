import ru from './lang/ru.js'
import en from './lang/en.js'
import kz from './lang/kz.js'
import Vue from 'vue'

const l = {
	ru,
	en,
	kz,
}

export default {
	install () {
		Vue.prototype.$lang = function(lang, text){
			if(!l[lang]) throw new Error('no lang ' + lang)
			return l[lang][text]
		}
	}
}