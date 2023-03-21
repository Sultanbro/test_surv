import Vue from 'vue'
import VueRouter from 'vue-router'
import HomeView from '../views/HomeView'
import ContactsView from '../views/ContactsView'
import PaymentsView from '../views/PaymentsView'
import ContractOffer from '../views/ContractOffer'
import SiteUseAgreement from '../views/SiteUseAgreement'
import PersonalData from '../views/PersonalData'
import PrivacyPolicy from '../views/PrivacyPolicy'

const router = new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/',
			name: 'HomeView',
			component: HomeView,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/contacts',
			name: 'ContactsView',
			component: ContactsView,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/contract-offer',
			name: 'ContractOffer',
			component: ContractOffer,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/site-use-agreement',
			name: 'SiteUseAgreement',
			component: SiteUseAgreement,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/personal-data',
			name: 'PersonalData',
			component: PersonalData,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/privacy-policy',
			name: 'PrivacyPolicy',
			component: PrivacyPolicy,
			meta: {
				title: 'Jobtron',
			},
		},
		{
			path: '/payments',
			name: 'PaymentsView',
			component: PaymentsView,
			meta: {
				title: 'Jobtron',
			},
		},
	]
});

const DEFAULT_TITLE = 'Jobtron';
router.afterEach(to => {
	// Use next tick to handle router history correctly
	// see: https://github.com/vuejs/vue-router/issues/914#issuecomment-384477609
	Vue.nextTick(() => {
		document.title = to.meta.title || DEFAULT_TITLE
	})
});

export default router
