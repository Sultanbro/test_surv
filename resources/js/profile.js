/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import { BootstrapVue } from 'bootstrap-vue'
Vue.use(BootstrapVue)

import mDatePicker from './vue-multi-date-picker/src/lib'
Vue.use(mDatePicker)

import moment from 'moment'
moment.locale('ru')
require('moment-weekday-calc')

import Notifications from 'vue-notification'

/*
or for SSR:
import Notifications from 'vue-notification/dist/ssr.js'
*/

Vue.use(Notifications)


// globally
window.collect = require('collect.js')
Vue.prototype.$moment = moment

// import Antd from 'ant-design-vue'
// Vue.use(Antd)


// Import loading component
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
Vue.use(Loading)

// select with search
import Vue from 'vue'

Date.prototype.addHours = function(h) {
	this.setTime(this.getTime() + (h*60*60*1000));
	return this;
}

/**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
Vue.component('profile', require('./components/profile/Profile.vue'));
Vue.component('timetracking', require('./components/timetracking.vue'));
Vue.component('user-earnings', require('./components/profile/UserEarnings/UserEarnings.vue'));
Vue.component('trainee-report', require('./components/TraineeReport.vue'));

Vue.component('t-usersalary', require('./components/tables/TableUserSalary.vue')); // b table


Vue.component('t-activity', require('./components/tables/TableActivity.vue')); // checkbox radio
Vue.component('t-activity-new', require('./components/tables/TableActivityNew.vue'));  // checkbox radio
Vue.component('t-activity-collection', require('./components/tables/TableActivityCollection.vue')); // b-badge
Vue.component('t-quality-weekly', require('./components/tables/TableQualityWeekly.vue'));
Vue.component('t-recruiter-stats', require('./components/analytics/TableRecruiterStats.vue')); // b table b modal
Vue.component('g-recruting', require('./components/analytics/Recruting.vue')); // popover
Vue.component('t-recruting-user', require('./components/tables/TableRecrutingUser.vue')); // b table
Vue.component('t-user-analytics', require('./components/tables/TableUserAnalytics.vue')); // a tabs
Vue.component('u-modal', require('./components/ui/UModal.vue')); // b-modal
Vue.component('sidebar', require('./components/ui/Sidebar.vue')); // сайдбар table
Vue.component('t-kpi', require('./components/tables/TableKPI.vue')); // b input b modal
Vue.component('t-kpi-indicator', require('./components/tables/TableKpiIndicator.vue')); // b input

Vue.component('progress-bar', {
	template: `
    <div class="progress-bar mb-1">
      <div class="background-bar">
        <div class="info">
          <label>{{label}}</label>
          <label class="percentage">{{percentage}}%   </label>
        </div>
      </div>
      <transition appear @before-appear="beforeEnter" @after-appear="enter">
        <div class="tracker-bar"></div>
      </transition>
   </div>
  </div>`,
	props: {
		percentage: Number,
		label: String,
	},
	methods: {
		beforeEnter (el) {
			el.style.width = 0
		},
		enter (el) {
			el.style.width = `${this.percentage}%`
			el.style.transition = 'width 1s linear'
		}
	}
});

import { store } from './store';

const app = new Vue({
	store,
	el: '.right-panel-app'
});
