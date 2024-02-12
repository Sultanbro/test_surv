<template>
	<div class="default-tab PageCompany">
		<nav class="normal mt-4">
			<ul
				id="nav-tab"
				class="nav nav-tabs set-tabs"
				role="tablist"
			>
				<template v-for="tab in tabs">
					<li
						:id="`${tab.htmlId}-tab`"
						:key="tab.htmlId"
						class="nav-item"
					>
						<span
							class="nav-link"
							:class="{active: tab.id === activeTab}"
							@click="activeTab = tab.id"
						>
							{{ tab.title }}
						</span>
					</li>
				</template>
			</ul>
		</nav>
		<div
			id="nav-tabContent"
			class="tab-content"
		>
			<div
				v-if="activeTab === 2 && can(['positions_view', 'settings_view'])"
				id="nav-home"
				class="tab-pane fade show active py-3"
				role="tabpanel"
				aria-labelledby="nav-home-tab"
			>
				<Professions :positions="pageData.positions" />
			</div>
			<div
				v-if="activeTab === 3 && can(['groups_view', 'settings_view'])"
				id="nav-profile"
				class="tab-pane fade show active py-3"
				role="tabpanel"
				aria-labelledby="nav-profile-tab"
			>
				<Groups
					:statuseses="pageData.statuseses"
					:archived_groupss="pageData.archived_groupss"
					:activeuserid="pageData.activeuserid"
					:book_groups="pageData.book_groups"
					:corpbooks="pageData.corpbooks"
				/>
			</div>
			<div
				v-if="activeTab === 4 && can(['settings_view'])"
				id="nav-shift"
				class="tab-pane fade show active py-3"
				role="tabpanel"
				aria-labelledby="nav-profile-tab"
			>
				<Shifts />
			</div>
			<div
				v-if="activeTab === 5 && can(['settings_view'])"
				id="nav-taxes"
				class="tab-pane fade show active py-3"
				role="tabpanel"
				aria-labelledby="nav-profile-tab"
			>
				<CompanyTaxes />
			</div>
		</div>
	</div>
</template>

<script>
import Professions from '@/pages/professions.vue';
import Groups from '@/pages/groups.vue';
import Shifts from '@/pages/shifts.vue';
import CompanyTaxes from '@/pages/Company/CompanyTaxes.vue';
import {useAsyncPageData} from '@/composables/asyncPageData'

export default {
	name: 'PageCompany',
	components: {
		Professions,
		Groups,
		Shifts,
		CompanyTaxes,
	},
	data() {
		return {
			tabs: [
				{
					id: 2,
					htmlId: 'nav-home',
					path: '/timetracking/settings?tab=2#nav-home',
					title: 'Должности',
					access: ['positions_view', 'settings_view']
				},
				{
					id: 3,
					htmlId: 'nav-profile',
					path: '/timetracking/settings?tab=3#nav-profile',
					title: 'Отделы',
					access: ['groups_view', 'settings_view']
				},
				{
					id: 4,
					htmlId: 'nav-shift',
					path: '???',
					title: 'Смены',
					access: ['settings_view']
				},
				{
					id: 5,
					htmlId: 'nav-tax',
					path: '????',
					title: 'Налоги',
					access: ['settings_view'],
				},
			],
			activeTab: 2,
			pageData: {}
		}
	},
	computed: {
		activeTabItem() {
			return this.tabs.find(item => item.id === this.activeTab)
		}
	},
	watch: {
		activeTab() {
			this.updatePageData()
		}
	},
	mounted() {
		this.updatePageData();
		this.activeTab = Number(this.$route.query.tabswitch ? this.$route.query.tabswitch : 2);
	},
	methods: {
		can(access) {
			if (access === 'is_admin') return this.$laravel.is_admin
			if (typeof access === 'string') return this.$can(access)
			return access.some(item => this.$can(item))
		},
		updatePageData() {
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

<style lang="scss">
.PageCompany{
	.img-info{
		vertical-align: middle;
	}
}
</style>
<style lang="scss" scoped>
	.nav-item{
		position: relative;
	}
	.beta{
		position: absolute;
		top: -13px;
		right: -15px;
		padding: 2px 5px;
		border-radius: 2px;
		background-color: #45b44d;
		color: #fff;
		font-weight: 700;
		font-size: 12px;
	}
</style>
