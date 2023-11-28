<template>
	<div
		class="header__profile _anim _anim-no-hide custom-scroll-y mt-3"
		:class="{
			'v-loading': loading,
			hidden: hide,
			'_active': inViewport
		}"
	>
		<div class="profile__content">
			<div class="profile__col">
				<StartDayBtn
					v-if="showButton"
					:status="buttonStatus"
					:workday-status="status"
					@clickStart="startDay"
				/>
				<div class="profile__balance">
					Текущий баланс
					<p
						v-if="!balance.loading"
						class="profile__balance-value"
					>
						{{ separateNumber(totalBalance) }} <span class="profile__balance-currecy">{{ balance.currency }}</span>
					</p>
				</div>
			</div>

			<div class="profile__col">
				<ProfileInfo :data="userInfo" />

				<!-- <RefWidget v-if="isBP" /> -->
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import { useProfileStatusStore } from '@/stores/ProfileStatus'
import { useProfileSalaryStore } from '@/stores/ProfileSalary'

import ProfileInfo from '@/pages/Widgets/ProfileInfo'
import StartDayBtn from '@/pages/Widgets/StartDayBtn'
// import RefWidget from '@/components/pages/Profile/RefWidget.vue'

export default {
	name: 'MobileProfileSidebar',
	components: {
		ProfileInfo,
		StartDayBtn,
		// RefWidget,
	},
	props: {},
	data: function () {
		return {
			loading: false,
			hide: false,
			inViewport: false,
			isBP: ['bp', 'test'].includes(location.hostname.split('.')[0]),
		};
	},
	computed: {
		...mapState(usePersonalInfoStore, ['user', 'position', 'groups', 'salary', 'workingDay', 'schedule', 'workingTime']),
		...mapState(useProfileStatusStore, ['status', 'balance', 'message', 'buttonStatus']),
		...mapState(useProfileSalaryStore, ['user_earnings']),
		...mapState(useProfileSalaryStore, {isSalaryReady: 'isReady'}),
		totalBalance(){
			if(!this.user_earnings) return 0
			return (this.user_earnings.sumSalary || 0) + (this.user_earnings.sumKpi || 0) + (this.user_earnings.sumBonuses || 0)
		},
		userInfo(){
			return {
				user: this.user,
				position: this.position,
				groups: this.groups,
				salary: this.salary,
				workingDay: this.workingDay,
				schedule: this.schedule,
				workingTime: this.workingTime,
			}
		},
		showButton(){
			if(this.$can('ucalls_view') && !this.$laravel.is_admin) return false
			return this.status === 'started' || (this.user && this.user.user_type === 'remote')
		},
		isProfileVisible(){
			return this.$viewportSize.width < 1360
		},
	},
	mounted(){
		const scrollObserver = new IntersectionObserver(() => {
			this.inViewport = true
		})
		scrollObserver.observe(this.$el)
	},
	created(){},
	methods: {
		...mapActions(useProfileStatusStore, ['updateStatus']),
		getParams() {
			const now = this.$moment().format('HH:mm:ss')
			if(this.status === 'started') return {stop: now}
			return {start: now}
		},
		/**
		 * Начать или завершить день
		 */
		async startDay() {
			if(this.buttonStatus === 'loading') return
			const profileStatusStore = useProfileStatusStore()
			profileStatusStore.buttonStatus = 'loading'
			try{
				await this.updateStatus(this.getParams())
				if(this.status === 'workdone') this.$toast.info(this.message)
				if(this.status === 'started') this.$toast.info('День начат')
				if(this.status === 'stopped' || this.status === '') this.$toast.info('День завершен')
				profileStatusStore.buttonStatus = 'init'
			}
			catch(error){
				profileStatusStore.buttonStatus = 'error'
				console.error('startDay', error)
			}
		},
		separateNumber(x){
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		},
	}
};
</script>

<style lang="scss">
</style>
