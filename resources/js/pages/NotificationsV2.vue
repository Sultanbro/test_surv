<template>
	<div class="NotificationsV2">
		<div class="NotificationsV2-header">
			<b-button
				variant="success"
				@click="openEditSidebar()"
			>
				Создать уведомление
			</b-button>
			<b-form-input
				v-model="search"
				placeholder="Поиск"
			/>
		</div>

		<div class="table-container">
			<b-table-simple striped>
				<b-thead>
					<b-tr>
						<b-th>№</b-th>
						<b-th>Кого уведомляем</b-th>
						<b-th class="text-left">
							Текст уведомления
						</b-th>
						<b-th>Куда отправлять</b-th>
						<b-th>Периодичность отправки</b-th>
						<b-th>Дата создания</b-th>
						<b-th>Создатель</b-th>
						<b-th />
					</b-tr>
				</b-thead>
				<b-tbody>
					<b-tr
						v-for="notification, key in filteredNotifications"
						:key="key"
						@click="openEditSidebar(notification)"
					>
						<b-td>
							{{ key + 1 }}
						</b-td>
						<b-td>
							<template v-for="recipient in notification.recipients">
								{{ recipient.name }},
							</template>
						</b-td>
						<b-td class="NotificationsV2-text">
							{{ notification.title }}
						</b-td>
						<b-td>
							{{ notification.type_of_mailing.join(', ') }}
						</b-td>
						<b-td>
							{{ periodNames[notification.date.frequency] }} {{ notification.date.days.join(', ') }}
						</b-td>
						<b-td>
							{{ $moment(notification.created_at).format('YYYY-MM-DD') }}
						</b-td>
						<b-td>
							{{ notification.created_by.name }} {{ notification.created_by.last_name }}
						</b-td>
						<b-td>
							<b-button
								class="btn btn-danger btn-icon"
								@click.stop="remove(notification)"
							>
								<i class="fa fa-trash" />
							</b-button>
						</b-td>
					</b-tr>
				</b-tbody>
			</b-table-simple>
		</div>
		<SideBar
			v-if="selectedNotification"
			:title="selectedNotification.name || 'Новое уведомление'"
			width="50%"
			:open="!!selectedNotification"
			@close="selectedNotification = null"
		>
			<NotificationsEditForm
				:notification="selectedNotification"
				@save="onSave"
			/>
		</SideBar>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import {
	fetchNotificationVariants,
	createNotification,
} from '@/stores/api/notifications'
import NotificationsEditForm from '@/components/pages/Notifications/NotificationsEditForm'
import SideBar from '@ui/Sidebar'

export default {
	name: 'NotificationsV2',
	components: {
		SideBar,
		NotificationsEditForm,
	},
	data(){
		return {
			notifications: null,
			periodNames: {
				monthly: 'по дням месяца',
				weekly: 'по дням недели'
			},
			selectedNotification: null,
			search: ''
		}
	},
	computed: {
		...mapGetters([
			'user',
			'users',
			'positions',
			'profileGroups',
		]),
		groups(){
			return this.profileGroups
		},
		filteredNotifications(){
			if(!this.search) return this.notifications
			const lowerSearch = this.search.toLowerCase()
			return this.notifications.filter(notification => {
				// in name
				if(~notification.name.toLowerCase().indexOf(lowerSearch)) return true

				// in text
				if(~notification.title.toLowerCase().indexOf(lowerSearch)) return true

				// in date
				if(~notification.created_at.toLowerCase().substring(0, 10).indexOf(lowerSearch)) return true

				// in creator
				const creator = notification.created_by
				if(~(`${creator.name} ${creator.last_name}`.toLowerCase()).indexOf(lowerSearch)) return true

				// in services
				if(~notification.type_of_mailing.join(', ').toLowerCase().indexOf(lowerSearch)) return true

				// in targets
				if(notification.recipients.filter(rec => ~rec.name.toLowerCase().indexOf(lowerSearch)).length) return true

				return false
			})
		}
	},
	watch: {
		users(){
			this.fetchNotifications()
		}
	},
	created(){
		if(!this.users.length) this.loadCompany()
	},
	mounted(){
		if(this.users.length) this.fetchNotifications()
	},
	methods: {
		...mapActions(['loadCompany']),
		async fetchNotifications(){
			const notifications = await fetchNotificationVariants()
			this.addRecipientNames(notifications)
			this.notifications = notifications
		},
		addRecipientNames(notifications){
			notifications.forEach(notification => {
				notification.recipients.forEach(recipient => {
					this.addRecipientName(recipient)
				})
			})
		},
		addRecipientName(recipient){
			this[(['', 'addUserName', 'addGroupName', 'addPositionName'][recipient.type])](recipient)
		},
		addUserName(recipient){
			const user = this.users.find(user => user.id === recipient.id)
			if(user) recipient.name = `${user.name} ${user.last_name}`
		},
		addGroupName(recipient){
			const group = this.groups.find(group => group.id === recipient.id)
			if(group) recipient.name = group.name
		},
		addPositionName(recipient){
			const position = this.positions.find(position => position.id === recipient.id)
			if(position) recipient.name = position.position
		},
		remove(/* notification */){
			// this.removeNotificationVariants
		},
		openEditSidebar(notification){
			if(!notification) this.selectedNotification = this.getBlankNotification()
			else this.selectedNotification = JSON.parse(JSON.stringify(notification))
		},
		getBlankNotification(){
			return {
				id: 0,
				name: '',
				title: '',
				recipients: [],
				date: {
					days: [],
					frequency: 'weekly'
				},
				time: '10:00',
				type_of_mailing: ['jobtron'],
			}
		},
		onSave(notification){
			if(notification.id) this.updateNotification(notification)
			else this.createNotification(notification)
			this.selectedNotification = null
		},
		async createNotification(notification){
			await createNotification(notification)
			const now = new Date().toISOString()
			this.notifications.push({
				...notification,
				created_at: now,
				updated_at: now,
				created_by: JSON.parse(JSON.stringify(this.user))
			})
		},
		updateNotification(notification){
			// call api
			const index = this.notifications.findIndex(n => n.id === notification.id)
			if(!~index) return
			this.$set(this.notifications, index, notification)
		},
	}
}
</script>

<style lang="scss">
.NotificationsV2{
	&-header{
		display: flex;
		flex-flow: row nowrap;
		align-items: center;
		gap: 20px;

		margin-bottom: 10px;
	}
	&-text{
		max-width: 300px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.ui-sidebar.is-open{
		.ui-sidebar__body{
			right: 60px;
		}
	}

}
</style>
