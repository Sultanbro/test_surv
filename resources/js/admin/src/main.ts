/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import '@/styles/styles.scss'
import '@core/scss/index.scss'
import { createPinia } from 'pinia'
import { createApp } from 'vue'
import flatpickr from 'flatpickr'
import { Russian } from 'flatpickr/dist/l10n/ru.js'
import 'flatpickr/dist/flatpickr.css'
import userInfo from '@/plugins/userInfo'
import 'cropperjs/dist/cropper.css'

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

loadFonts()

flatpickr.localize(Russian)

const app = createApp(App)

app.use(vuetify)
app.use(createPinia())
app.use(userInfo)
app.use(router)
app.use(Toast/* , options */)

app.mount('#app')


// /admins (GET)- список кто может входить на admin.jobtron.org (они не должны видеть права)
// /admins/add  (POST) -добавить
// /admins/delete/{id}  (DELETE) - удалить
