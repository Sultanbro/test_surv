<template>
	<div class="NotificationsV2">
		<!-- Шапка -->
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
			<b-button
				class="btn btn-icon"
				@click="isSettings = true"
			>
				<i class="icon-nd-settings" />
			</b-button>
		</div>

		<!-- Таблица -->
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
							<div class="NotificationsV2-shortText">
								{{ notification.title }}
							</div>
							<div class="NotificationsV2-fullText">
								{{ notification.title }}
							</div>
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

		<!-- Редактирование уведомлений -->
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

		<!-- Настройки -->
		<SideBar
			v-if="isSettings"
			title="Настройки"
			width="50%"
			:open="isSettings"
			@close="isSettings = false"
		>
			<b-container>
				<b-row>
					<b-col cols="4">
						<span class="NotificationsV2-label">Количество напоминаний в день</span>
						<img
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
							v-b-popover.hover="'Указаное кол-во раз, в течение рабочего дня будет показан попап с уведомлениями'"
						>
					</b-col>
					<b-col cols="8">
						<b-form-input
							v-model="settings.showCount"
							type="number"
						/>
					</b-col>
				</b-row>
			</b-container>
			<hr class="mb-4">
			<b-button
				variant="primary"
				@click="onSaveSettings"
			>
				Сохранить
			</b-button>
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
				weekly: 'по дням недели',
				daily: 'каждый день',
			},
			selectedNotification: null,
			search: '',
			isSettings: '',
			settings: {
				showCount: 0
			}
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
		onSaveSettings(){
			// call api
			this.isSettings = false
		}
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
		position: relative;
		&:hover{
			.NotificationsV2-fullText{
				z-index: 11;
				top: 40px;

				visibility: visible;
				opacity: 1;
			}
		}
	}
	&-shortText{
		max-width: 300px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	&-fullText{
    max-width: 400px;
    padding: 10px 20px;
    border: 1px solid #999;

		position: absolute;
    top: 20px;
    left: 10px;

    visibility: hidden;
    opacity: 0;

    font-size: 14px;
    line-height: 1.3;
    text-align: left;

    box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
    transition: 0.2s all ease;
    border-radius: 10px;
    background-color: #fff;
	}
	&-label{
		font-size: 1.4rem;
    line-height: 2rem;
	}

	.ui-sidebar{
		&.is-open{
			.ui-sidebar__body{
				right: 60px;
			}
		}
		.ui-sidebar__header {
			padding: 20px 25px;
			background: #fff;
			border-bottom: 1px solid #ddd;

			&-text{
				font-size: 14px;
				color: #333;
				font-weight: 700;
			}
		}
		.ui-sidebar__body {
			border-radius: 20px 0 0 20px;
			// overflow: hidden !important;
		}
	}
	.container{
		padding: 0 15px;
	}
}
</style>
