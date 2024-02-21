<template>
	<div
		v-if="isVisible"
		id="page-profile"
	>
		<router-link
			v-if="profileUnfilled"
			to="/cabinet"
			class="ProfilePage-fillProfile"
		>
			Поздравляем вы приняты на работу, заполните свой профиль
		</router-link>
		<div class="intro">
			<IntroTop
				:courses="intro['courses']"
				:profit="intro['profit']"
				:estimation="intro['estimation']"
				:indicators="intro['indicators']"
				:class="{ _active: anim.intro }"
			/>
			<Courses
				v-if="coursesBeforeIntro"
				ref="courses"
				:class="{ _active: anim.courses }"
				@init="intro['courses'] = true"
			/>
			<IntroStats
				v-else
				ref="intro"
				:class="{ _active: anim.intro }"
				@pop="pop"
			/>
			<!-- <new-intro-smart-table/> -->
		</div>
		<MobileProfileSidebar
			v-show="isMobileVisible"
			ref="profileSidebar"
			:class="{ _active: anim.profileSidebar }"
		/>
		<IntroStats
			v-if="coursesBeforeIntro"
			ref="intro"
			:class="{ _active: anim.intro }"
			@pop="pop"
		/>
		<Courses
			v-else
			ref="courses"
			:class="{ _active: anim.courses }"
			@init="intro['courses'] = true"
		/>
		<RefWidget
			v-if="isBP && isMobileVisible"
		/>
		<Profit
			ref="profit"
			:class="{ _active: anim.profit }"
			@init="intro['profit'] = true"
		/>
		<TraineeEstimation
			ref="estimation"
			:class="{ _active: anim.estimation }"
			@init="intro['estimation'] = true"
		/>
		<CompareIndicators
			ref="indicators"
			:class="{ _active: anim.indicators }"
			@init="intro['indicators'] = true"
		/>

		<RefStat
			v-if="isBP"
			ref="referals"
			:class="{ _active: anim.referals }"
			@init="intro['referals'] = true"
		/>

		<Popup
			v-if="popBalance"
			title="Баланс оклада"
			desc="Заработанная сумма именно от окладной части"
			:open="popBalance"
			:width="popupWidth"
			@close="popBalance=false"
		>
			<Balance />
		</Popup>

		<Popup
			v-if="popKpi"
			title="Kpi"
			desc="Выполняя дополнительные активности, заработайте больше денег"
			:open="popKpi"
			:width="popupWidth"
			@close="popKpi=false"
		>
			<Kpi />
		</Popup>

		<Popup
			v-if="popBonuses"
			title="Бонусы"
			desc="Зарабатывайте бонусы, выполняя дополнительные активности"
			:open="popBonuses"
			:width="popupWidth"
			@close="popBonuses=false"
		>
			<Bonuses />
		</Popup>

		<Popup
			v-if="popQuartalPremiums"
			title="Квартальные премии"
			:desc="popQPSubTitle"
			:open="popQuartalPremiums"
			:width="popupWidth"
			@close="popQuartalPremiums=false"
		>
			<PopupQuartal
				class="ProfilePage-qp"
				@title="popQPSubTitle = $event"
			/>
		</Popup>

		<Popup
			v-if="popNominations"
			title="Награды"
			:desc="descNominations"
			:open="popNominations"
			:width="popupWidth"
			:add-button="true"
			:add-button-route="'/timetracking/settings?tab=9#nav-awards'"
			:add-button-popover-text="'Добавить новую награду'"
			@close="popNominations=false"
		>
			<Nominations @get-desc="getDesc" />
		</Popup>
	</div>
</template>

<script>
import IntroTop from '@/pages/Profile/IntroTop.vue'
import IntroStats from '@/pages/Profile/IntroStats.vue'
// import IntroSmartTable from '@/pages/Profile/IntroSmartTable.vue'
import MobileProfileSidebar from '@/pages/Layouts/MobileProfileSidebar.vue'
import Courses from '@/pages/Profile/Courses.vue'
import Profit from '@/pages/Profile/Profit.vue'
import TraineeEstimation from '@/pages/Profile/TraineeEstimation.vue'
import CompareIndicators from '@/pages/Profile/CompareIndicators.vue'
import RefStat from '@/pages/Profile/RefStat.vue'
import Popup from '@/pages/Layouts/Popup.vue'
import Balance from '@/pages/Profile/Popups/Balance.vue'
import Kpi from '@/pages/Profile/Popups/Kpi.vue'
import Bonuses from '@/pages/Profile/Popups/Bonuses.vue'
import PopupQuartal from '@/pages/Profile/Popups/PopupQuartal.vue'
import Nominations from '@/pages/Profile/Popups/Nominations.vue'
import RefWidget from '@/components/pages/Profile/RefWidget.vue'


import { mapGetters } from 'vuex'
import { mapState, mapActions } from 'pinia'
import { useSettingsStore } from '@/stores/Settings'
import { useProfileStatusStore } from '@/stores/ProfileStatus'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'
import { useProfileCoursesStore } from '@/stores/ProfileCourses'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import { usePaymentTermsStore } from '@/stores/PaymentTerms'
import { useReferralStore } from '@/stores/Referral'

export default {
	name: 'ProfilePage',
	components: {
		IntroTop,
		IntroStats,
		// IntroSmartTable,
		MobileProfileSidebar,
		Courses,
		Profit,
		TraineeEstimation,
		CompareIndicators,
		RefStat,
		Popup,
		Balance,
		Kpi,
		Bonuses,
		PopupQuartal,
		Nominations,
		RefWidget,
	},
	props: {},
	data: function () {
		return {
			descNominations: 'Подождите, идет загрузка...',
			fields: [],
			popBalance: false,
			popKpi: false,
			popBonuses: false,
			popQuartalPremiums: false,
			popNominations: false,
			popQPSubTitle: '',
			intro: {
				courses: false,
				profit: false,
				estimation: false,
				indicators: false,
				referals: false,
			},
			anim: {
				intro: false,
				profileSidebar: false,
				courses: false,
				profit: false,
				estimation: false,
				indicators: false,
				referals: false,
			},
			intersectionObserver: null,
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0]),
		};
	},
	computed: {
		...mapGetters(['user']),
		...mapState(useSettingsStore, {settingsReady: 'isReady'}),
		...mapState(useProfileStatusStore, {statusReady: 'isReady'}),
		...mapState(useProfileSalaryStore, {salaryReady: 'isReady'}),
		...mapState(useProfileCoursesStore, {coursesReady: 'isReady'}),
		...mapState(useProfileCoursesStore, ['courses']),
		...mapState(usePersonalInfoStore, {infoReady: 'isReady'}),
		...mapState(usePaymentTermsStore, {termsReady: 'isReady'}),
		...mapState(useReferralStore, {refReady: 'isReady'}),
		isTrainee(){
			if(!this.user) return false
			return !this.user.applied_at
		},
		coursesFinished(){
			const completed = this.courses.filter(course => course.all_stages && (course.all_stages === course.completed_stages))
			return completed.length
		},
		coursesBeforeIntro(){
			return this.isTrainee && (this.courses.length && !this.coursesFinished)
		},
		popupWidth(){
			const w = this.$viewportSize.width
			if(w < 651) return '100%'
			if(w < 1360) return '75%'
			return w - (19 * this.$viewportSize.rem) + 'px'
		},
		isMobileVisible(){
			return this.$viewportSize.width < 1360
		},
		isReady(){
			return this.settingsReady
				&& this.statusReady
				&& this.salaryReady
				&& this.coursesReady
				&& this.infoReady
				&& this.termsReady
				&& (this.refReady || !this.isBP)
		},
		isVisible(){
			return this.isReady || this.$viewportSize.width <= 900
		},
		profileUnfilled(){
			return !this.user.phone
				|| !this.user.name
				|| !this.user.last_name
				|| !this.user.email
				|| !this.user.birthday
				|| !this.user.working_country
		}
	},
	watch: {
		isReady(value){
			if(value) this.initAnimOnScroll()
		}
	},
	mounted(){
		if(this.isReady) this.initAnimOnScroll()
		if(this.isBP) this.fetchUserStats()
	},
	beforeDestroy(){
		this.intersectionObserver.disconnect()
		this.intersectionObserver = null
	},
	methods: {
		...mapActions(useReferralStore, ['fetchUserStats']),
		pop(window) {
			if(window == 'balance') this.popBalance = true;
			if(window == 'kpi') this.popKpi = true;
			if(window == 'bonus') this.popBonuses = true;
			if(window == 'qp') this.popQuartalPremiums = true;
			if(window == 'nominations') this.popNominations = true;
		},
		getDesc(text){
			this.descNominations = text;
		},
		initAnimOnScroll(){
			if(this.intersectionObserver) return
			this.$nextTick(() => {
				const w = this.$viewportSize.width
				if(w > 900){
					this.intersectionObserver = new IntersectionObserver(this.animOnScroll, {
						threshold: 0.1
					})
					this.$refs.intro.$el instanceof Element && this.intersectionObserver.observe(this.$refs.intro.$el)
					this.$refs.profileSidebar.$el instanceof Element && this.intersectionObserver.observe(this.$refs.profileSidebar.$el)
					this.$refs.courses.$el instanceof Element && this.intersectionObserver.observe(this.$refs.courses.$el)
					this.$refs.profit.$el instanceof Element && this.intersectionObserver.observe(this.$refs.profit.$el)
					this.$refs.estimation.$el instanceof Element && this.intersectionObserver.observe(this.$refs.estimation.$el)
					this.$refs.indicators.$el instanceof Element && this.intersectionObserver.observe(this.$refs.indicators.$el)
					this.$refs.referals.$el instanceof Element && this.intersectionObserver.observe(this.$refs.referals.$el)
					return
				}
				Object.keys(this.anim).forEach(key => {
					this.anim[key] = true
				})
			})
		},
		animOnScroll(entries){
			entries.forEach(entry => {
				if(entry.isIntersecting){
					Object.keys(this.anim).forEach(key => {
						if(this.$refs[key].$el === entry.target){
							this.anim[key] = true
						}
					})
				}
			})
		}
	}
};
</script>

<style lang="scss">
#page-profile{
	padding-bottom: 2rem;
	padding-right: 2rem;
	.RefWidget{
		opacity: 1;
		transform: translateY(0);
		visibility: visible;
	}
}
@media(max-width:1910px){
	#page-profile{
		padding-right: 0;
	}
}

.ProfilePage{
	&-qp{
		.popup__header-content{
			display: flex;
			align-items: flex-end;
			gap: 10px;
		}
		.popup__subtitle{
			font-size: 18px;
		}
	}
	&-fillProfile{
		display: block;
		margin-top: 20px;
		padding: 20px;

		font-size: 20px;
		color: #fff;
		text-decoration: none;
		text-align: center;

		background-color: #e84f71;
		border-radius: 16px;
		transition: 0.3s;
		&:hover{
			color: #fff;
			transform: translateY(-2px);
		}
	}
}
</style>
