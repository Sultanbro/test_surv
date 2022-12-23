import Vue from 'vue'
import App from './home/App.vue'
import lang from './home/plugins/lang'
import clickOutside from './home/plugins/clickOutside'
import ViewportSize from './plugins/ViewportSize'

Vue.config.productionTip = false
Vue.use(lang)
Vue.use(clickOutside)
Vue.use(ViewportSize)

new Vue({
  data: {
    lang: 'ru',
    setLang (lang) {
      this.lang = lang
    }
  },
  render: h => h(App),
}).$mount('#app')
