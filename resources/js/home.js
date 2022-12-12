import Vue from 'vue'
import App from './home/App.vue'
import lang from './home/plugins/lang'

Vue.config.productionTip = false
Vue.use(lang)

new Vue({
  data: {
    lang: 'ru',
    setLang (lang) {
      this.lang = lang
    }
  },
  render: h => h(App),
}).$mount('#app')
