/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
window.collect = require('collect.js')// globally

import BootstrapVue from 'bootstrap-vue'
import mDatePicker from './vue-multi-date-picker/src/lib'
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

import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import playerJs from './plugins/playerjs/playerjs'

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
Vue.use(mDatePicker)
Vue.use(Loading)
Vue.use(VGauge);
Vue.use(VueMask);
Vue.use(Antd)
Vue.use(Notifications)
Vue.use(PlayerJs)

Vue.component('timetracking', require('./components/timetracking.vue')); // шапка начать день
Vue.component('draggable', draggable); // драг
Vue.component('multiselect', Multiselect); // select c множественным выбором
Vue.component('v-select', vSelect) // select с поиском
Vue.component('editor', require('@tinymce/tinymce-vue').default); // booklist
Vue.component('pagination', require('laravel-vue-pagination')); // только в ОКК
Vue.component('u-modal', require('./components/ui/UModal.vue')); // модалка НАДО УБРАТЬ
Vue.component('sidebar', require('./components/ui/Sidebar.vue')); // сайдбар table
Vue.component('group-premission', require('./components/modals/GroupPremission.vue')); // доступы к группе
Vue.component('progress-bar', require('./components/ProgressBar.vue')) // в ответах quiz
Vue.component('rating', require('./components/ui/Rating.vue')); // звездочки
Vue.component('profile-kpi-button', require('./components/ProfileKpiButton.vue')); // кнопка Индивид KPI в настройках User
Vue.component('profile-groups', require('./components/profile/ProfileGroups.vue')); // настройки user
Vue.component('profile-books', require('./components/profile/ProfileBooks.vue')); // настройки user

/** 
 * Components
 */
Vue.component('upload-files', require('./components/UploadFiles.vue')); // загрузка файлов
Vue.component('t-kpi-indicator', require('./components/tables/TableKpiIndicator.vue')); // ряд активности в таблице KPI
Vue.component('selectgroup', require('./components/selectgroup.vue')); // booklist
Vue.component('selectgroupbook', require('./components/selectgroupbook.vue')); // booklist
Vue.component('group-excel-import', require('./components/imports/GroupExcelImport.vue')); // импорт в табели
Vue.component('activity-excel-import', require('./components/imports/ActivityExcelImport.vue')); // импорт в активности
Vue.component('nps', require('./components/tables/NPS.vue')); // Оценка руководителей
Vue.component('call-bases', require('./components/CallBase.vue')); // для Евраз 
Vue.component('trainee-report', require('./components/TraineeReport.vue'));
Vue.component('profile', require('./components/profile/Profile.vue')); // шапка
Vue.component('t-recruiter-stats', require('./components/analytics/TableRecruiterStats.vue')); // Почасовая таблица рекрутинга
Vue.component('user-earnings', require('./components/profile/UserEarnings.vue')); // Блок начислений в профиле
Vue.component('g-recruting', require('./components/analytics/Recruting.vue')); // сводная информация рекрутинг
Vue.component('top-gauges', require('./components/TopGauges.vue')); // TOП спидометры, есть и в аналитике

/**
 * Tables
 */
Vue.component('t-kpi', require('./components/tables/TableKPI.vue')); // KPI
Vue.component('t-decomposition', require('./components/tables/TableDecomposition.vue')); // Декомпозиция
Vue.component('t-user-analytics', require('./components/tables/TableUserAnalytics.vue')); // Ваши показатели
Vue.component('t-funnel', require('./components/tables/TableFunnel.vue')); // Воронка
Vue.component('t-skypes', require('./components/tables/TableSkype.vue')); // Стажеры
Vue.component('t-summary-recruting', require('./components/tables/TableSummaryRecruting.vue')); // Сводная рекрутинг
Vue.component('t-recruting-user', require('./components/tables/TableRecrutingUser.vue')); // Таблица рекрутера
Vue.component('t-summary-kaspi', require('./components/tables/TableSummaryKaspi.vue')); // Сводная Каспи старая
Vue.component('t-rentability', require('./components/tables/TableRentability.vue')); // ТОП рентабельность
Vue.component('analytic-stat', require('./components/AnalyticStat.vue')); // Сводная аналитики
Vue.component('t-activity', require('./components/tables/TableActivity.vue')); // Старая активность
Vue.component('t-activity-new', require('./components/tables/TableActivityNew.vue')); // Активность
Vue.component('t-activity-collection', require('./components/tables/TableActivityCollection.vue')); // Сборы
Vue.component('t-quality', require('./components/tables/TableQuality.vue')); // ОКК 
Vue.component('t-quality-weekly', require('./components/tables/TableQualityWeekly.vue')); // Недельные оценки качества
Vue.component('t-usersalary', require('./components/tables/TableUserSalary.vue')); // таблица начислений
Vue.component('questions', require('./pages/Questions.vue')); // вопросы тестов

/**
 * Pages
 */
Vue.component('page-courses', require('./pages/Courses.vue')); // курсы
Vue.component('course', require('./pages/Course.vue')); // курс

Vue.component('page-upbooks', require('./pages/Upbooks.vue')); // книги
Vue.component('page-upbooks-read', require('./pages/UpbooksRead.vue')); // книга чтение
Vue.component('page-upbooks-edit', require('./pages/UpbooksEdit.vue')); // книги редактирование

Vue.component('page-playlist-edit', require('./pages/PlaylistEdit.vue')); // редактирование плейлиста

Vue.component('booklist', require('./pages/booklist.vue')); // база знаний раздел
Vue.component('page-kb', require('./pages/KBPage.vue')); // база знаний раздел

// Учет времени 
Vue.component('page-top', require('./pages/Top.vue')); // четверг
Vue.component('exam', require('./pages/exam.vue')); // повышение квалификации
Vue.component('t-report', require('./pages/TableReport.vue'));  // табель
Vue.component('t-coming', require('./pages/TableComing.vue')); // время прихода 
Vue.component('t-accrual', require('./pages/TableAccrual.vue')); // начисления
Vue.component('analytics', require('./pages/Analytics.vue')); // hr 
Vue.component('analytics-page', require('./pages/AnalyticsPage.vue')); // аналитика

// Настройки
Vue.component('userlist', require('./pages/userlist.vue')); // Сотрудники
Vue.component('professions', require('./pages/professions.vue')); // должности
Vue.component('groups', require('./pages/groups.vue')); // Группы
Vue.component('fines', require('./pages/Fines.vue')); // штрафы table
Vue.component('s-notifications', require('./pages/Notifications.vue')); // Уведомления
Vue.component('bookgroups', require('./pages/bookgroups.vue')); // обучение книги

const app = new Vue({
  el: '.right-panel-app'
}); 