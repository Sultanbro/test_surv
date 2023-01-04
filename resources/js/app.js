/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap').default;
require('./newprofile').default; // new design for profile. There is jquery

window.Vue = require('vue').default;
window.collect = require('collect.js')// globally

import BootstrapVue from 'bootstrap-vue'
import JQuery from 'jquery'
window.VJQuery = JQuery


import moment from 'moment'
import Notifications from 'vue-notification'
import VueMask from 'v-mask'
import draggable from 'vuedraggable'
import Multiselect from 'vue-multiselect'
import Vue from 'vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import VueCircle from 'vue2-circle-progress'
import axios from 'axios'
import VueAxios from 'vue-axios'

import VueRouter from 'vue-router'
import router from '@/router'
Vue.use(VueRouter)

import Croppa from 'vue-croppa';
import 'vue-croppa/dist/vue-croppa.css';
Vue.use(Croppa);
Vue.use(VueAxios, axios)

// Toast
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
Vue.use(Toast, {timeout: 1500, pauseOnHover: false, rtl: true, position: "top-left",});

import store from "./components/Chat/Store/index.js";
Vue.prototype.$store = store

// Require dependencies
var VueCookie = require('vue-cookie');
// Tell Vue to use the plugin
Vue.use(VueCookie);


import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
//import VueCoreVideoPlayer from 'vue-core-video-player';


// Vue Konva for canvas
import VueKonva from 'vue-konva';
Vue.use(VueKonva);

import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

import CKEditor from '@ckeditor/ckeditor5-vue2';
import VueObserveVisibility from 'vue-observe-visibility';

Vue.use( CKEditor );
Vue.use( VueObserveVisibility );

Vue.use(DatePicker)



moment.locale('ru')
require('moment-weekday-calc')
require('select2')


Date.prototype.addHours = function (h) {
  this.setTime(this.getTime() + (h * 60 * 60 * 1000));
  return this;
}

Vue.prototype.$moment = moment
Vue.prototype.$laravel = Laravel;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(BootstrapVue)

Vue.use(Loading)
Vue.use(VueMask);

Vue.use(Notifications)

// Vue.use(VueCoreVideoPlayer)

// Directive to lock scroll on html and body
import VScrollLock from './plugins/VScrollLock'
Vue.use(VScrollLock)

// reactive viewport sizes vm.$viewportSize.width && vm.$viewportSize.height
import ViewportSize from './plugins/ViewportSize'
Vue.use(ViewportSize)


Vue.component('timetracking', require('./components/timetracking.vue').default); // шапка начать день
Vue.component('draggable', draggable); // драг
Vue.component('multiselect', Multiselect); // select c множественным выбором
Vue.component('v-select', vSelect) // select с поиском
Vue.component('vue-circle', VueCircle) // circle indicator
Vue.component('pagination', require('laravel-vue-pagination').default); // только в ОКК
Vue.component('u-modal', require('./components/ui/UModal.vue').default); // модалка НАДО УБРАТЬ
Vue.component('sidebar', require('./components/ui/Sidebar.vue').default); // сайдбар table
Vue.component('group-premission', require('./components/modals/GroupPremission.vue').default); // доступы к группе
Vue.component('progress-bar', require('./components/ProgressBar.vue').default) // в ответах quiz
Vue.component('rating', require('./components/ui/Rating.vue').default); // звездочки
Vue.component('profile-groups', require('./components/profile/ProfileGroups.vue').default); // настройки user
Vue.component('base-knowledge', require('./components/profile/ProfileBaseKnowledge.vue').default); // настройки user База знание
Vue.component('profile-books', require('./components/profile/ProfileBooks.vue').default); // настройки user
Vue.component('profile-quarter-button', require('./components/ProfileQuarterButton.vue').default); // кнопка Индивид Quarter в настройках User
Vue.component('auth-check-list', require('./components/auth_check_list.vue').default); // кнопка у кого есть Чек Лист список Чек Листов (Fixed)
Vue.component('selected-modal-checkList', require('./components/selectedModalCheckList.vue').default); // чек лист selected modal
Vue.component('ref-linker', require('./components/RefLinker.vue').default); // рефералки

// Chat components
Vue.component('chat-app', require('./components/Chat/ChatApp.vue').default); //
Vue.component('chat-sidepanel', require('./components/Chat/ChatSidePanel/ChatSidePanel.vue').default);
Vue.component('chat-search-button', require('./components/Chat/ChatSearchButton/ChatSearchButton.vue').default);

/**
 * Components
 */
Vue.component('t-kpi-indicator', require('./components/tables/TableKpiIndicator.vue').default); // ряд активности в таблице KPI
Vue.component('selectgroup', require('./components/selectgroup.vue').default); // booklist
Vue.component('selectgroupbook', require('./components/selectgroupbook.vue').default); // booklist
Vue.component('activity-excel-import', require('./components/imports/ActivityExcelImport.vue').default); // импорт в активности
Vue.component('trainee-report', require('./components/TraineeReport.vue').default);
Vue.component('profile', require('./components/profile/Profile.vue').default); // шапка
Vue.component('user-earnings', require('./components/profile/UserEarnings/UserEarnings.vue').default); // Блок начислений в профиле

/**
 * Tables
 */
Vue.component('t-quarter', require('./components/tables/TableQuarter.vue').default); // Quarter
Vue.component('t-user-analytics', require('./components/tables/TableUserAnalytics.vue').default); // Ваши показатели
Vue.component('t-summary-recruting', require('./components/tables/TableSummaryRecruting.vue').default); // Сводная рекрутинг
Vue.component('t-recruting-user', require('./components/tables/TableRecrutingUser.vue').default); // Таблица рекрутера

Vue.component('t-activity', require('./components/tables/TableActivity.vue').default); // Старая активность
Vue.component('t-activity-new', require('./components/tables/TableActivityNew.vue').default); // Активность
Vue.component('t-activity-collection', require('./components/tables/TableActivityCollection.vue').default); // Сборы
Vue.component('t-quality-weekly', require('./components/tables/TableQualityWeekly.vue').default); // Недельные оценки качества
Vue.component('t-usersalary', require('./components/tables/TableUserSalary.vue').default); // таблица начислений
Vue.component('questions', require('./pages/Questions.vue').default); // вопросы тестов

Vue.component('permission-item', require('./components/PermissionItem.vue').default); //






Vue.component('superselect', require('./components/SuperSelect.vue').default); // with User ProfileGroup and Position


/**
 * Pages
 */
// Vue.component('page-playlist-read', require('./pages/PlaylistRead.vue').default); // чтение плейлиста

// Vue.component('page-kb', require('./pages/KBPage.vue').default); // база знаний раздел

// Учет времени
Vue.component('exam', require('./pages/exam.vue').default); // повышение квалификации
// Vue.component('cabinet', require('./pages/Cabinet.vue').default); // курсы мои


// temp
Vue.component('profile-salary-info', require('./pages/ProfileSalaryInfo.vue').default);

Vue.component('award-user-sidebar', require('./components/sidebars/AwardUserSidebar.vue').default); // сайдбар для награждения пользователя

/**
 * Layout of new design
 */
Vue.component('left-sidebar', require('./pages/Layouts/LeftSidebar.vue').default);
Vue.component('right-sidebar', require('./pages/Layouts/RightSidebar.vue').default);
Vue.component('profile-sidebar', require('./pages/Layouts/ProfileSidebar.vue').default);
Vue.component('profile-info', require('./pages/Widgets/ProfileInfo.vue').default);
Vue.component('start-day-btn', require('./pages/Widgets/StartDayBtn.vue').default);
Vue.component('sidebars', require('./pages/Layouts/Sidebars.vue').default);

Vue.component('popup-checklist', require('./pages/Layouts/CheckListPopup.vue').default);
Vue.component('popup-notifications', require('./pages/Layouts/NotificationsPopup.vue').default);
Vue.component('popup-faq', require('./pages/Layouts/FaqPopup.vue').default);
Vue.component('popup-search', require('./pages/Layouts/SearchPopup.vue').default);
Vue.component('popup-mail', require('./pages/Layouts/MailPopup.vue').default);



/**
 * click outside event
 */
Vue.directive("click-outside", {
  bind(el, binding, vnode) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        vnode.context[binding.expression](event);
      }
    };
    document.body.addEventListener("click", el.clickOutsideEvent);
  },
  unbind(el) {
    document.body.removeEventListener("click", el.clickOutsideEvent);
  },

//   stopProp(event) { event.stopPropagation() }
});

/**
 * permissions of auth user
 */

Vue.prototype.$can = function (permission, authorId = false) {
  if (Laravel.is_admin) {
      return true;
  }
  if (Laravel.permissions.indexOf(permission) !== -1) {
      return true;
  }
}

import App from '@/App.vue'

const app = new Vue({
  // el: '.right-panel-app'
  router,
  render: h => h(App)
}).$mount('.right-panel-app')