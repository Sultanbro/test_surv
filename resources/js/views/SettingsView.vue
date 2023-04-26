<script>
import DefaultLayout from '@/layouts/DefaultLayout'
import { useAsyncPageData } from '@/composables/asyncPageData'
const Userlist = () => import(/* webpackChunkName: "UserlistPage" */ '@/pages/userlist.vue')
const Company = () => import(/* webpackChunkName: "UserlistPage" */ '@/pages/Company.vue')
const Fines = () => import(/* webpackChunkName: "FinesPage" */ '@/pages/Fines.vue')
const Notifications = () => import(/* webpackChunkName: "NotificationsPage" */ '@/pages/Notifications.vue')
const NotificationsV2 = () => import(/* webpackChunkName: "NotificationsPageV2" */ '@/pages/NotificationsV2.vue')
const Permissions = () => import(/* webpackChunkName: "PermissionsPage" */ '@/pages/Permissions.vue')
// const CheckList = () => import(/* webpackChunkName: "checkListPage" */ '@/pages/checkList.vue')
const Awards = () => import(/* webpackChunkName: "AwardsSettingsPage" */ '@/pages/Awards/Awards.vue')
const IntegrationsPage = () => import(/* webpackChunkName: "IntegrationsPage" */ '@/pages/Integrations.vue')

export default {
	name: 'SettingsView',
	components: {
		DefaultLayout,
		Userlist,
		Company,
		Fines,
		Notifications,
		NotificationsV2,
		Permissions,
		Awards,
		IntegrationsPage,
	},
	data(){
		return {
			tabs: [
				{
					id: '1',
					htmlId: 'nav-person',
					path: '/timetracking/settings?tab=1#nav-person',
					title: 'Сотрудники',
					access: ['users_view', 'settings_view']
				},
				{
					id: '2',
					htmlId: 'nav-company',
					path: '/timetracking/settings?tab=2#nav-home',
					title: 'Компания',
					access: ['positions_view', 'groups_view', 'settings_view']
				},
				{
					id: '4',
					htmlId: 'nav-fines',
					path: '/timetracking/settings?tab=4#nav-fines',
					title: 'Депремирования',
					access: ['fines_view', 'settings_view']
				},
				{
					id: '5',
					htmlId: 'nav-notifications',
					path: '/timetracking/settings?tab=5',
					title: 'Уведомления',
					access: ['notifications_view', 'settings_view'],
					domain: 'bp'
				},
				{
					id: '10',
					htmlId: 'nav-notifications-v2',
					path: '/timetracking/settings?tab=10',
					title: 'Уведомления',
					access: ['notifications_view', 'settings_view'],
					beta: true
				},
				{
					id: '6',
					htmlId: 'nav-permissions',
					path: '/timetracking/settings?tab=6#nav-permissions',
					title: 'Доступы',
					access: ['permissions_view', 'settings_view']
				},
				// {
				// 	id: '7',
				// 	htmlId: 'nav-checkList',
				// 	path: '/timetracking/settings?tab=7#nav-checkList',
				// 	title: 'Чек-листы',
				// 	access: ['checklists_view', 'settings_view'],
				// 	domain: 'bp'
				// },
				{
					id: '8',
					htmlId: 'nav-integrations',
					path: '/timetracking/settings?tab=8#nav-integrations',
					title: 'Интеграции',
					access: 'is_admin'
				},
				{
					id: '9',
					htmlId: 'nav-awards',
					path: '/timetracking/settings?tab=9#nav-awards',
					title: 'Награды',
					access: ['awards_view', 'settings_view']
				},
			],
			pageData: {},
			domain: window.location.hostname.split('.')[0]
		}
	},
	computed: {
		activeTab(){
			return this.$route.query.tab || '1'
		},
		activeTabItem(){
			return this.tabs.find(item => item.id === this.activeTab)
		}
	},
	watch: {
		activeTab(){
			this.updatePageData()
		}
	},
	mounted(){
		this.updatePageData()
		console.log(this.$can());
	},
	methods:{
		can(access){
			if(access === 'is_admin') return this.$laravel.is_admin
			if(typeof access === 'string') return this.$can(access)
			return access.some(item => this.$can(item))
		},
		ckeckDomain(domain){
			if(!domain) return true
			return domain === this.domain
		},
		updatePageData(){
			this.pageData = {}
			useAsyncPageData(`/timetracking/settings?tab=${this.activeTab}#${this.activeTabItem.htmlId}`).then(data => {
				this.pageData = data
			}).catch(error => {
				console.error('useAsyncPageData', error)
			})
		}
	},
}
</script>

<template>
	<DefaultLayout>
		<div class="old__content">
			<div class="">
				<div class="">
					<div class="c">
						<div id="app">
							<div class="default-tab">
								<nav class="normal mt-4">
									<ul
										id="nav-tab"
										class="nav nav-tabs set-tabs"
										role="tablist"
									>
										<template v-for="tab in tabs">
											<li
												v-if="can(tab.access) && ckeckDomain(tab.domain)"
												:key="tab.htmlId"
												:id="`${tab.htmlId}-tab`"
												class="nav-item"
											>
												<router-link
													:to="tab.path"
													:aria-controls="tab.htmlId"
													:aria-selected="tab.id === activeTab ? 'true' : 'false'"
													class="nav-link"
													:class="{active: tab.id === activeTab}"
												>
													{{ tab.title }}
													<b-badge
														v-if="tab.beta"
														variant="success"
													>
														beta
													</b-badge>
												</router-link>
											</li>
										</template>
									</ul>
								</nav>
								<div
									id="nav-tabContent"
									class="tab-content"
								>
									<div
										v-if="activeTab === '1' && can(['users_view', 'settings_view'])"
										id="nav-person"
										class="tab-pane fade show active py-3"
										role="tabpanel"
										aria-labelledby="nav-person-tab"
									>
										<Userlist
											v-show="pageData.subdomain"
											:is_admin="$laravel.is_admin"
											:subdomain="pageData.subdomain"
											:positions="pageData.positions"
										/>
									</div>
									<div
										v-if="activeTab === '2' && can(['positions_view', 'groups_view', 'settings_view'])"
										class="tab-pane fade show active py-3"
										id="nav-company"
										role="tabpanel"
										aria-labelledby="nav-home-tab"
									>
										<Company />
									</div>
									<div
										v-if="activeTab === '4' && can(['fines_view', 'settings_view'])"
										id="nav-fines"
										class="tab-pane fade show active py-3"
										role="tabpanel"
										aria-labelledby="nav-fines-tab"
									>
										<Fines />
									</div>
									<div
										v-if="activeTab === '5' && can(['notification_view', 'settings_view'])"
										class="tab-pane fade show active py-3"
										id="nav-notifications"
										role="tabpanel"
										aria-labelledby="nav-notifications-tab"
									>
										<Notifications
											:groups_with_id="pageData.groups_with_id"
											:users="pageData.users"
											:positions="pageData.positions"
										/>
									</div>
									<div
										v-if="activeTab === '10' && can(['notification_view', 'settings_view'])"
										class="tab-pane fade show active py-3"
										id="nav-notifications-v2"
										role="tabpanel"
										aria-labelledby="nav-notifications-v2-tab"
									>
										<NotificationsV2 />
									</div>
									<div
										v-if="activeTab === '6' && can(['permissions_view', 'settings_view'])"
										class="tab-pane fade show active py-3"
										id="nav-bookgroups"
										role="tabpanel"
										aria-labelledby="nav-bookgroups-tab"
									>
										<Permissions />
									</div>
									<!--									<div-->
									<!--										v-if="activeTab === '7' && can(['checklists_view', 'settings_view'])"-->
									<!--										class="tab-pane fade show active py-3"-->
									<!--										id="checkList"-->
									<!--										role="tabpanel"-->
									<!--										aria-labelledby="nav-checkList-tab"-->
									<!--									>-->
									<!--										<CheckList />-->
									<!--									</div>-->
									<div
										v-if="activeTab === '8' && can('is_admin')"
										class="tab-pane fade show active py-3"
										id="integrations"
										role="tabpanel"
										aria-labelledby="nav-integrations-tab"
									>
										<IntegrationsPage />
									</div>
									<div
										v-if="activeTab === '9' && can(['awards_view', 'settings_view'])"
										class="tab-pane fade show active py-3"
										id="awards"
										role="tabpanel"
										aria-labelledby="nav-awards-tab"
									>
										<Awards />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</DefaultLayout>
</template>

<style scoped>

.header__profile {
    display:none !important;
}
@media (min-width: 1360px) {
    .container.container-left-padding {
        padding-left: 9rem !important;
    }
}
</style>
