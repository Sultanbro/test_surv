<template>
	<div class="NotificationsV2">
		<b-button
			variant="success"
			class="mb-2"
			@click="onCreate"
		>
			Создать уведомление
		</b-button>

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
						v-for="notification, key in notifications"
						:key="key"
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
								@click="remove(notification)"
							>
								<i class="fa fa-trash" />
							</b-button>
						</b-td>
					</b-tr>
				</b-tbody>
			</b-table-simple>
		</div>
	</div>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useCompanyStore } from '@/stores/Company'
import { fetchNotificationVariants } from '@/stores/api/notifications'

export default {
	name: 'NotificationsV2',
	components: {},
	data(){
		return {
			notifications: null,
			periodNames: {
				monthly: 'по дням месяца',
				weekly: 'по дням недели'
			}
		}
	},
	computed: {
		...mapState(useCompanyStore, ['dictionaries', 'isReady']),
		...mapState(useCompanyStore, {isDictionariesReady: 'isReady'}),
		users(){
			return this.dictionaries.users
		},
		groups(){
			return this.dictionaries.profile_groups
		},
		positions(){
			return this.dictionaries.positions
		}
	},
	watch: {
		isDictionariesReady(){
			this.fetchNotifications()
		}
	},
	created(){
		this.fetchDictionaries()
	},
	mounted(){
		if(this.isDictionariesReady) this.fetchNotifications()
	},
	methods: {
		...mapActions(useCompanyStore, ['fetchDictionaries']),
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
			if(position) recipient.name = position.name
		},
		remove(/* notification */){
			// this.removeNotificationVariants
		}
	}
}
</script>

<style lang="scss">
.NotificationsV2{
	&-text{
		max-width: 300px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
}
</style>
