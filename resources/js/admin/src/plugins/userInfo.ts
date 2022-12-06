/* global userInfo */
import { ref } from 'vue'
export default {
  install: (app) => {
    // app.config.globalProperties.$userInfo = ref(userInfo)
    app.provide('userInfo', ref(userInfo))
  }
}