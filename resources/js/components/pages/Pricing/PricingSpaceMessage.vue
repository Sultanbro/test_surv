<template>
	<button
		class="price-space-button"
		@click="initSupportChat"
	>
		<MessageIcon />
	</button>
</template>

<script>
import MessageIcon from './assets/MessageIcon.vue';
import {fetchSettings} from '../../../stores/api/settings';
import {mapActions, mapState} from 'pinia';
import {useWorkChartStore} from '../../../stores/WorkChart';
import {useNotificationsStore} from '../../../stores/Notifications';
const NotificationsLastCheck = 'NotificationsLastCheck'

export default {
	name: 'PricingSpaceMessage',
	components: {MessageIcon},
	computed: {
		...mapState(useWorkChartStore, ['workChartList']),
		...mapState(useWorkChartStore, {isWorkChartLoading: 'isLoading'}),
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

		this.initSupportChat()
	},
	beforeDestroy(){
		clearInterval(this.notificationsInterval)
		this.destroySupportChat()
	},
	methods: {
		...mapActions(useNotificationsStore, ['fetchUnreadCount']),

		initSupportChat(){
			if(!window.jChatWidget) {
				window.addEventListener('onBitrixLiveChat', this.onInitChatWidget)
				const url = 'https://cdn-ru.bitrix24.kz/b1734679/crm/site_button/loader_14_qetlt8.js';
				const s = document.createElement('script');
				s.async = true;
				s.src = url + '?' + (Date.now() / 60000 | 0);
				const h = document.getElementsByTagName('script')[0];
				h.parentNode.insertBefore(s,h);
			}
		},
		destroySupportChat(){
			window.removeEventListener('onBitrixLiveChat', this.onInitChatWidget)
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
	}
}
</script>

<style scoped>
.price-space-button {
	background-color: #ededed;
	width: 40px;
	height: 40px;
	border-radius: 8px;
	padding: 8px;
}
</style>
