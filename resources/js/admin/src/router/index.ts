/* global userInfo */
import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHashHistory } from 'vue-router'
import routes from '~pages'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    ...setupLayouts(routes),
  ],
  scrollBehavior() {
    return { top: 0 }
  },
})
router.beforeEach((to, from) => {
  if (to.meta && to.meta.auth) {
    if (!('id' in userInfo)){
      window.location.href = '/login'
      return 
    }
  }
})

export default router
