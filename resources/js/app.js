/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap').default;
window.Vue = require('vue').default;
window.collect = require('collect.js')// globally

import BootstrapVue from 'bootstrap-vue'

import moment from 'moment'
import Notifications from 'vue-notification'
import Antd from 'ant-design-vue'
import VueMask from 'v-mask' 
import VGauge from 'vgauge';
import draggable from 'vuedraggable' 
import Multiselect from 'vue-multiselect'
import Vue from 'vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import VueCircle from 'vue2-circle-progress'

import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
//import VueCoreVideoPlayer from 'vue-core-video-player';


import VueVideoPlayer from 'vue-video-player'
 
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
 

Vue.use(DatePicker)

Vue.use(VueVideoPlayer, /* {
  options: global default options,
  events: global videojs events
} */)


moment.locale('ru')
require('moment-weekday-calc')
require('select2')


Date.prototype.addHours = function (h) {
  this.setTime(this.getTime() + (h * 60 * 60 * 1000));
  return this;
}

Vue.prototype.$moment = moment

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */ 
Vue.use(BootstrapVue)

Vue.use(Loading)
Vue.use(VGauge);
Vue.use(VueMask);
Vue.use(Antd)
Vue.use(Notifications)

// Vue.use(VueCoreVideoPlayer)

 
 
Vue.component('svod-table', require('./components/SvodTable.vue').default);//сводная таблица для аналитики

Vue.component('timetracking', require('./components/timetracking.vue').default); // шапка начать день
Vue.component('draggable', draggable); // драг
Vue.component('multiselect', Multiselect); // select c множественным выбором
Vue.component('v-select', vSelect) // select с поиском
Vue.component('vue-circle', VueCircle) // circle indicator
Vue.component('editor', require('@tinymce/tinymce-vue').default); // booklist
Vue.component('pagination', require('laravel-vue-pagination').default); // только в ОКК
Vue.component('u-modal', require('./components/ui/UModal.vue').default); // модалка НАДО УБРАТЬ
Vue.component('sidebar', require('./components/ui/Sidebar.vue').default); // сайдбар table
Vue.component('group-premission', require('./components/modals/GroupPremission.vue').default); // доступы к группе
Vue.component('progress-bar', require('./components/ProgressBar.vue').default) // в ответах quiz
Vue.component('rating', require('./components/ui/Rating.vue').default); // звездочки
Vue.component('profile-kpi-button', require('./components/ProfileKpiButton.vue').default); // кнопка Индивид KPI в настройках User
Vue.component('profile-groups', require('./components/profile/ProfileGroups.vue').default); // настройки user
Vue.component('profile-books', require('./components/profile/ProfileBooks.vue').default); // настройки user

Vue.component('profile-quarter-button', require('./components/ProfileQuarterButton.vue').default); // кнопка Индивид Quarter в настройках User
Vue.component('auth-check-list', require('./components/auth_check_list.vue').default); // кнопка у кого есть Чек Лист список Чек Листов (Fixed)
Vue.component('selected-modal-checkList', require('./components/selectedModalCheckList.vue').default); // чек лист selected modal

/** 
 * Components
 */
Vue.component('upload-files', require('./components/UploadFiles.vue').default); // загрузка файлов
Vue.component('t-kpi-indicator', require('./components/tables/TableKpiIndicator.vue').default); // ряд активности в таблице KPI
Vue.component('selectgroup', require('./components/selectgroup.vue').default); // booklist
Vue.component('selectgroupbook', require('./components/selectgroupbook.vue').default); // booklist
Vue.component('group-excel-import', require('./components/imports/GroupExcelImport.vue').default); // импорт в табели
Vue.component('activity-excel-import', require('./components/imports/ActivityExcelImport.vue').default); // импорт в активности
Vue.component('nps', require('./components/tables/NPS.vue').default); // Оценка руководителей
Vue.component('call-bases', require('./components/CallBase.vue').default); // для Евраз 
Vue.component('trainee-report', require('./components/TraineeReport.vue').default);
Vue.component('profile', require('./components/profile/Profile.vue').default); // шапка
Vue.component('t-recruiter-stats', require('./components/analytics/TableRecruiterStats.vue').default); // Почасовая таблица рекрутинга
Vue.component('user-earnings', require('./components/profile/UserEarnings.vue').default); // Блок начислений в профиле
Vue.component('g-recruting', require('./components/analytics/Recruting.vue').default); // сводная информация рекрутинг
Vue.component('top-gauges', require('./components/TopGauges.vue').default); // TOП спидометры, есть и в аналитике

/**
 * Tables
 */
Vue.component('t-kpi', require('./components/tables/TableKPI.vue').default); // KPI
Vue.component('t-quarter', require('./components/tables/TableQuarter.vue').default); // Quarter
Vue.component('t-decomposition', require('./components/tables/TableDecomposition.vue').default); // Декомпозиция
Vue.component('t-user-analytics', require('./components/tables/TableUserAnalytics.vue').default); // Ваши показатели
Vue.component('t-funnel', require('./components/tables/TableFunnel.vue').default); // Воронка
Vue.component('t-skypes', require('./components/tables/TableSkype.vue').default); // Стажеры
Vue.component('t-summary-recruting', require('./components/tables/TableSummaryRecruting.vue').default); // Сводная рекрутинг
Vue.component('t-recruting-user', require('./components/tables/TableRecrutingUser.vue').default); // Таблица рекрутера

Vue.component('t-rentability', require('./components/tables/TableRentability.vue').default); // ТОП рентабельность
Vue.component('analytic-stat', require('./components/AnalyticStat.vue').default); // Сводная аналитики
Vue.component('t-activity', require('./components/tables/TableActivity.vue').default); // Старая активность
Vue.component('t-activity-new', require('./components/tables/TableActivityNew.vue').default); // Активность
Vue.component('t-activity-collection', require('./components/tables/TableActivityCollection.vue').default); // Сборы
Vue.component('t-quality', require('./components/tables/TableQuality.vue').default); // ОКК 
Vue.component('t-quality-weekly', require('./components/tables/TableQualityWeekly.vue').default); // Недельные оценки качества
Vue.component('t-usersalary', require('./components/tables/TableUserSalary.vue').default); // таблица начислений
Vue.component('questions', require('./pages/Questions.vue').default); // вопросы тестов
Vue.component('v-player', require('./components/VideoPlayerItem.vue').default); // плеер

Vue.component('permission-item', require('./components/PermissionItem.vue').default); // 

Vue.component('video-accordion', require('./components/VideoAccordion.vue').default); //

Vue.component('video-list', require('./components/VideoList.vue').default); //

Vue.component('superselect', require('./components/SuperSelect.vue').default); // 

Vue.component('glossary', require('./components/Glossary.vue').default); // 


/**
 * Pages
 */
Vue.component('page-courses', require('./pages/Courses.vue').default); // курсы
Vue.component('course', require('./pages/Course.vue').default); // курс

Vue.component('page-upbooks-read', require('./pages/UpbooksRead.vue').default); // книга чтение
Vue.component('page-upbooks', require('./pages/Upbooks.vue').default); // книги редактирование

Vue.component('page-playlist-edit', require('./pages/PlaylistEdit.vue').default); // редактирование плейлиста
Vue.component('page-playlist-read', require('./pages/PlaylistRead.vue').default); // чтение плейлиста
Vue.component('page-playlists', require('./pages/Playlists.vue').default); // редактирование плейлиста

Vue.component('booklist', require('./pages/booklist.vue').default); // база знаний раздел
Vue.component('page-kb', require('./pages/KBPage.vue').default); // база знаний раздел

// Учет времени 
Vue.component('page-top', require('./pages/Top.vue').default); // четверг
Vue.component('exam', require('./pages/exam.vue').default); // повышение квалификации
Vue.component('t-report', require('./pages/TableReport.vue').default);  // табель
Vue.component('t-coming', require('./pages/TableComing.vue').default); // время прихода 
Vue.component('t-accrual', require('./pages/TableAccrual.vue').default); // начисления
Vue.component('analytics', require('./pages/Analytics.vue').default); // hr 
Vue.component('analytics-page', require('./pages/AnalyticsPage.vue').default); // аналитика
Vue.component('course-results', require('./pages/CourseResults.vue').default); // результаты по курсам
Vue.component('my-course', require('./pages/MyCourse.vue').default); // курсы мои
Vue.component('permissions', require('./pages/Permissions.vue').default); // курсы мои
Vue.component('cabinet', require('./pages/Cabinet.vue').default); // курсы мои

 
// Настройки
Vue.component('userlist', require('./pages/userlist.vue').default); // Сотрудники
Vue.component('professions', require('./pages/professions.vue').default); // должности
Vue.component('groups', require('./pages/groups.vue').default); // Группы
Vue.component('fines', require('./pages/Fines.vue').default); // штрафы table
Vue.component('s-notifications', require('./pages/Notifications.vue').default); // Уведомления
Vue.component('bookgroups', require('./pages/bookgroups.vue').default); // обучение книги
Vue.component('check-list', require('./pages/checkList.vue').default); // чек лист


 
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

const app = new Vue({
  el: '.right-panel-app'
}); 