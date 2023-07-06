<template>
	<div class="header__right">
		<div class="header__right-nav">
			<a
				v-if="isBp && user.is_admin === 1"
				href="javascript:void(0)"
				class="header__right-icon"
				@click="$emit('pop', 'faq')"
			>
				<img
					src="/images/dist/header-right-1.svg"
					alt="nav icon"
					class="header__icon-img"
				>
			</a>
			<a
				v-if="isBp && user.is_admin !== 1"
				href="javascript:void(0)"
				class="header__right-icon"
				v-b-popover.hover.left.html="'Вопросы и ответы - Этот функционал в разработке'"
			>
				<img
					src="/images/dist/header-right-1.svg"
					alt="nav icon"
					class="header__icon-img"
				>
			</a>
			<a
				href="javascript:void(0)"
				class="header__right-icon bell red"
				@click="$emit('pop', 'notifications')"
			>
				<PulseCard
					v-if="unreadQuantity"
					color="#ed2353"
					:size="2"
					class="RightSidebar-pulseIcon"
				>
					<img
						:src="`/images/dist/header-right-2-active.svg`"
						alt="nav icon"
						class="header__icon-img"
					>
				</PulseCard>
				<img
					v-else
					:src="`/images/dist/header-right-2.svg`"
					alt="nav icon"
					class="header__icon-img"
				>
			</a>

			<a
				href="javascript:void(0)"
				class="header__right-icon"
			>
				<img
					src="/images/dist/header-right-5.svg"
					alt="nav icon"
					class="header__icon-img"
					@click.once="openChat"
				>
			</a>

			<!-- Статус: скрыто. Компонент: RightSidebar. Дата скрытия: 27.01.2023 14:15 -->
			<a
				v-if="false"
				href="javascript:void(0)"
				class="header__right-icon check"
				@click="$emit('pop', 'checklist')"
			>
				<img
					src="/images/dist/header-right-6.svg"
					alt="nav icon"
					class="header__icon-img"
				>
			</a>

			<chat-search-button />
		</div>

		<chat-sidepanel />
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useNotificationsStore } from '@/stores/Notifications'
import { useWorkChartStore } from '@/stores/WorkChart.js'
import { usePersonalInfoStore } from '@/stores/PersonalInfo'
import { fetchSettings } from '@/stores/api.js'
import PulseCard from '@ui/PulseCard.vue'

const NotificationsLastCheck = 'NotificationsLastCheck'

export default {
	name: 'RightSidebar',
	components: {
		PulseCard,
	},
	props: {},
	data: function () {
		return {
			isBp: window.location.hostname.split('.')[0] === 'bp',
			notificationsInterval: null,
			prevNotificationsCheck: +(localStorage.getItem(NotificationsLastCheck) || Date.now()),
			showCount: 0,
		};
	},
	computed: {
		...mapState(useNotificationsStore, ['unreadQuantity']),
		...mapState(useWorkChartStore, ['workChartList']),
		...mapState(useWorkChartStore, {isWorkChartLoading: 'isLoading'}),
		...mapState(usePersonalInfoStore, ['user']),
		workChart(){
			if(!this.workChartList) return null
			if(!this.user) return null
			return this.workChartList.find(wc => this.user.work_chart_id === wc.id)
		},
		workTime(){
			if(this.workChart) return [this.workChart.start_time, this.workChart.end_time]
			if(this.user) return this.getOldWorkTime(this.user)
			return null
		},
		workTimeTS(){
			if(!this.workTime) return null
			return this.workTime.map(time => {
				return this.$moment(time, 'HH:mm').valueOf()
			})
		}
	},
	async mounted(){
		this.fetchUnreadCount()
		const {settings} = await fetchSettings('notifications_remind_count')
		if(settings.custom_notifications_remind_count){
			this.showCount = parseInt(settings.custom_notifications_remind_count) || 0
		}
		if(!this.workChartList && !this.isWorkChartLoading) this.fetchWorkChartList()
		this.notificationsInterval = setInterval(() => {
			this.hourlyNotifications()
		}, 60000)
		this.hourlyNotifications()
		if(localStorage.getItem(NotificationsLastCheck) === null){
			localStorage.setItem(NotificationsLastCheck, Date.now())
		}
	},
	beforeUnmount(){
		clearInterval(this.notificationsInterval)
	},
	methods: {
		...mapActions(useNotificationsStore, ['fetchUnreadCount']),
		...mapActions(useWorkChartStore, ['fetchWorkChartList']),
		openChat(){
			if(!this.isBp){
				const url = 'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_12_koodzo.js';
				const s = document.createElement('script');
				s.async = true;
				s.src = url + '?' + (Date.now() / 60000 | 0);
				const h = document.getElementsByTagName('script')[0];
				h.parentNode.insertBefore(s,h);
			}
		},
		hourlyNotifications(){
			if(!this.unreadQuantity) return
			if(!this.workTimeTS) return
			if(!this.showCount) return
			const now = Date.now()
			const inRange = this.workTimeTS[0] <= now && now <= this.workTimeTS[1]
			if(!inRange) return
			const timeBetween = parseInt((this.workTimeTS[1] - this.workTimeTS[0]) / (this.showCount + 1))

			if(now - this.prevNotificationsCheck > timeBetween){
				this.$emit('pop', 'notifications')
				this.prevNotificationsCheck = now
				localStorage.setItem(NotificationsLastCheck, now)
			}
		},
		getOldWorkTime(user){
			if(user.work_start && user.work_end) return [
				user.work_start.substring(0, 5),
				user.work_end.substring(0, 5),
			]
			return null
		}
	}
};
</script>

<style lang="scss">
.RightSidebar{
	&-pulseIcon{
		border-radius: 12px;
	}
}
</style>
