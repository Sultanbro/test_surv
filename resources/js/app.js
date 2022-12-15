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
import VGauge from 'vgauge';
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

// pagination
import JwPagination from 'jw-vue-pagination';
Vue.component('jw-pagination', JwPagination);

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
import VueVideoPlayer from 'vue-video-player'

import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

import CKEditor from '@ckeditor/ckeditor5-vue2';
import VueObserveVisibility from 'vue-observe-visibility';

Vue.use( CKEditor );
Vue.use( VueObserveVisibility );

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
Vue.prototype.$laravel = Laravel;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(BootstrapVue)

Vue.use(Loading)
Vue.use(VGauge);
Vue.use(VueMask);

Vue.use(Notifications)

// Vue.use(VueCoreVideoPlayer)

// Directive to lock scroll on html and body
import VScrollLock from './plugins/VScrollLock'
Vue.use(VScrollLock)

// reactive viewport sizes vm.$viewportSize.width && vm.$viewportSize.height
import ViewportSize from './plugins/ViewportSize'
Vue.use(ViewportSize)

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
Vue.component('user-earnings', require('./components/profile/UserEarnings/UserEarnings.vue').default); // Блок начислений в профиле
Vue.component('g-recruting', require('./components/analytics/Recruting.vue').default); // сводная информация рекрутинг
Vue.component('top-gauges', require('./components/TopGauges.vue').default); // TOП спидометры, есть и в аналитике
Vue.component('book-segment', require('./components/BookSegment.vue').default); //

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

Vue.component('video-uploader', require('./components/VideoUploader.vue').default); //


Vue.component('superselect', require('./components/SuperSelect.vue').default); // with User ProfileGroup and Position
Vue.component('superselect-alt', require('./components/SuperSelectAlt.vue').default); // with Book Video and Knowbase

Vue.component('glossary', require('./components/Glossary.vue').default); //


/**
 * Pages
 */
Vue.component('page-courses', require('./pages/Courses.vue').default); // курсы
Vue.component('course', require('./pages/Course.vue').default); // курс

Vue.component('page-upbooks-read', require('./pages/UpbooksRead.vue').default); // книга чтение
Vue.component('page-upbooks', require('./pages/Upbooks.vue').default); // книги редактирование

Vue.component('page-playlist-edit', require('./pages/PlaylistEdit.vue').default); // редактирование плейлиста

// Vue.component('page-playlist-read', require('./pages/PlaylistRead.vue').default); // чтение плейлиста

Vue.component('page-playlists', require('./pages/Playlists.vue').default); // редактирование плейлиста

Vue.component('booklist', require('./pages/booklist.vue').default); // база знаний раздел
// Vue.component('page-kb', require('./pages/KBPage.vue').default); // база знаний раздел

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
Vue.component('check-list', require('./pages/checkList.vue').default); // чек
Vue.component('awards', require('./pages/Awards/Awards.vue').default); // награды


// KPI

Vue.component('super-filter', require('./pages/kpi/SuperFilter.vue').default); // filter like bitrix

Vue.component('kpi-pages', require('./pages/kpi/KpiPages.vue').default); // kpi
Vue.component('kpi', require('./pages/kpi/Kpi.vue').default); // kpi
Vue.component('kpi-items', require('./pages/kpi/KpiItems.vue').default); // kpi
Vue.component('indicators', require('./pages/kpi/Indicators.vue').default); // kpi
Vue.component('stats', require('./pages/kpi/Stats.vue').default); // kpi
Vue.component('t-stats', require('./pages/kpi/StatsTable.vue').default); // kpi
Vue.component('t-stats-bonus', require('./pages/kpi/StatsTableBonus.vue').default); // kpi
Vue.component('t-stats-quartal', require('./pages/kpi/StatsTableQuartal.vue').default); // kpi
Vue.component('bonuses', require('./pages/kpi/Bonuses.vue').default); // kpi
Vue.component('quartal-premium', require('./pages/kpi/QuartalPremium.vue').default); // kpi


// NEWS

// Vue.component('news-pages', require('./pages/News/NewsPages').default); // новостная лента раздел
// Vue.component('news-feed', require('./pages/News/NewsFeed').default); // новостная лента раздел
// Vue.component('news-create', require('./pages/News/NewsCreate').default);
// Vue.component('drop-zone', require('./pages/News/DropZone').default);
// Vue.component('post-component', require('./pages/News/PostComponent').default);
// Vue.component('comments-component', require('./pages/News/CommentsComponent').default);
// Vue.component('filter-component', require('./pages/News/FilterComponent').default);
// Vue.component('reactions', require('./pages/News/ReactionComponent').default);

// Vue.component('birthday-feed', require('./pages/News/BirthdayFeed').default);
// Vue.component('birthday-user', require('./pages/News/BirthdayUser').default);



// temp
Vue.component('profile-salary-info', require('./pages/ProfileSalaryInfo.vue').default);

Vue.component('award-user-sidebar', require('./components/sidebars/AwardUserSidebar.vue').default); // сайдбар для награждения пользователя


/**
 * new design Profile page
 */
// Vue.component('page-profile', require('./pages/Profile/ProfilePage.vue').default);

// Vue.component('new-intro-stats', require('./pages/Profile/IntroStats.vue').default);
// Vue.component('new-intro-smart-table', require('./pages/Profile/IntroSmartTable.vue').default);
// Vue.component('new-intro-top', require('./pages/Profile/IntroTop.vue').default);
// Vue.component('new-profit', require('./pages/Profile/Profit.vue').default);
// Vue.component('new-courses', require('./pages/Profile/Courses.vue').default);
// Vue.component('new-trainee-estimation', require('./pages/Profile/TraineeEstimation.vue').default);
// Vue.component('new-compare-indicators', require('./pages/Profile/CompareIndicators.vue').default);

// Vue.component('popup-quartal', require('./pages/Profile/Popups/PopupQuartal.vue').default);
// Vue.component('popup-kpi', require('./pages/Profile/Popups/Kpi.vue').default);
// Vue.component('popup-balance', require('./pages/Profile/Popups/Balance.vue').default);
// Vue.component('popup-bonuses', require('./pages/Profile/Popups/Bonuses.vue').default);
// Vue.component('popup-nominations', require('./pages/Profile/Popups/Nominations.vue').default);


/**
 * Layout of new design
 */
Vue.component('left-sidebar', require('./pages/Layouts/LeftSidebar.vue').default);
Vue.component('right-sidebar', require('./pages/Layouts/RightSidebar.vue').default);
Vue.component('profile-sidebar', require('./pages/Layouts/ProfileSidebar.vue').default);
Vue.component('profile-info', require('./pages/Widgets/ProfileInfo.vue').default);
Vue.component('start-day-btn', require('./pages/Widgets/StartDayBtn.vue').default);
Vue.component('popup', require('./pages/Layouts/Popup.vue').default);
Vue.component('sidebars', require('./pages/Layouts/Sidebars.vue').default);

Vue.component('popup-checklist', require('./pages/Layouts/CheckListPopup.vue').default);
Vue.component('popup-notifications', require('./pages/Layouts/NotificationsPopup.vue').default);
Vue.component('popup-faq', require('./pages/Layouts/FaqPopup.vue').default);
Vue.component('popup-search', require('./pages/Layouts/SearchPopup.vue').default);
Vue.component('popup-mail', require('./pages/Layouts/MailPopup.vue').default);

/**
 * Tables of activity
 */
Vue.component('t-collection', require('./pages/Tables/Collection.vue').default);
Vue.component('t-default', require('./pages/Tables/Default.vue').default);
Vue.component('t-quality-new', require('./pages/Tables/Quality.vue').default);


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