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
				class="NotificationsV2-settings"
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
							{{ notification.creator.name }} {{ notification.creator.last_name }}
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
				<b-row class="mb-4">
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
				<b-row class="mb-4">
					<b-col>
						<JobtronButton
							fade
							@click="isTemplate = true"
						>
							<ChatIconPlus
								class="ChatIcon-active"
								width="11"
								height="11"
							/>
							Шаблонное уведомление
						</JobtronButton>
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

		<!-- Шаблонные уведомления -->
		<NotificationsTemplates
			v-if="isTemplate"
			@close="isTemplate = false"
		/>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import {
	fetchNotificationVariants,
	createNotification,
	deleteNotification,
	updateNotification,
} from '@/stores/api/notifications'
import NotificationsEditForm from '@/components/pages/Notifications/NotificationsEditForm'
import NotificationsTemplates from '@/components/pages/Notifications/NotificationsTemplates'
import SideBar from '@ui/Sidebar'
import JobtronButton from '@ui/Button'
import {
	ChatIconPlus,
} from '@icons'

const getNamesMethods = {
	'App\\User': 'getUserName',
	'App\\ProfileGroup': 'getGroupName',
	'App\\Position': 'getPositionName',
}
const typeToNumber = {
	'App\\User': 1,
	'App\\ProfileGroup': 2,
	'App\\Position': 3,
}

export default {
	name: 'NotificationsV2',
	components: {
		SideBar,
		NotificationsEditForm,
		NotificationsTemplates,
		JobtronButton,
		ChatIconPlus,
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
			},

			isTemplate: false,
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
				if(notification.creator){
					if(~(`${notification.creator.name} ${notification.creator.last_name}`.toLowerCase()).indexOf(lowerSearch)) return true
				}

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
			this.notifications = this.parseNotifications(notifications)
		},
		parseNotifications(notifications){
			return notifications.map(notification => {
				return {
					...notification,
					type_of_mailing: JSON.parse(notification.type_of_mailing),
					recipients: notification.recipients ? notification.recipients.map(recipient => {
						return {
							...recipient,
							id: recipient.notificationable_id,
							type: typeToNumber[recipient.notificationable_type],
							name: this[getNamesMethods[recipient.notificationable_type]](recipient)
						}
					}) : [],
					date: {
						frequency: notification.frequency,
						days: notification.recipients ? JSON.parse((notification.recipients.find(() => true) || {days: '[]'}).days) : []
					},
					creator: this.getCreator(notification)
				}
			})
		},
		getCreator(notification){
			return this.users.find(user => user.id === notification.created_by)
		},
		getUserName(recipient){
			const user = this.users.find(user => user.id === recipient.notificationable_id)
			if(user) return `${user.name} ${user.last_name}`
			return ''
		},
		getGroupName(recipient){
			const group = this.groups.find(group => group.id === recipient.notificationable_id)
			if(group) return group.name
			return ''
		},
		getPositionName(recipient){
			const position = this.positions.find(position => position.id === recipient.notificationable_id)
			if(position) return position.position
			return ''
		},
		async remove(notification){
			const {data} = await deleteNotification(notification.id)
			if(data) {
				this.$toast.success('Уведомление удалено')
				const index = this.notifications.findIndex(noti => noti.id === notification.id)
				this.notifications.splice(index, 1)
			}
			else{
				this.$toast.error('Ошибка при удалении уведомления, попробуйте позже')
			}
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
			const errors = this.validate(notification)
			if(errors.length){
				this.$toast.error(errors.join('\n'))
				return
			}
			if(notification.id) this.updateNotification(notification)
			else this.createNotification(notification)
			this.selectedNotification = null
		},
		validate(notification){
			const errors = []
			if(!notification.name) errors.push({field: 'name', error: 'Название уведомления должно быть заполнено'})
			if(!notification.title) errors.push({field: 'title', error: 'Текст уведомления должен быть заполнен'})
			if(!notification.recipients.length) errors.push({field: 'recipients', error: 'Укажите минимум одного получателя'})
			if(notification.date.frequency !== 'daily' && !notification.date.frequency.days.length) errors.push({field: 'days', error: 'Укажите минимум один день отправки'})
			return errors
		},
		async createNotification(notification){
			const {message} = await createNotification(notification)
			if(message === 'Success created'){
				this.$toast.success('Уведомление успешно создано')
			}
			else{
				this.$toast.error(message)
			}
			this.fetchNotifications()
		},
		async updateNotification(notification){
			const {message} = await updateNotification(notification)
			if(message === 'Success created'){
				this.$toast.success('Уведомление успешно создано')
			}
			else{
				this.$toast.error(message)
			}
			const index = this.notifications.findIndex(n => n.id === notification.id)
			if(!~index) return
			this.$set(this.notifications, index, notification)
		},
		onSaveSettings(){
			// call api
			this.$toast.warning('В разработке')
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
	&-settings{
		&.btn-secondary{
			background: #F7FAFC;
			border-color: #F7FAFC;
			color: #868e96;
		}
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

	.form-control {
		min-height: 35px;
		background-color: #F7FAFC;
		padding: 0 20px;
		font-size: 14px;
		border-radius: 6px;
		border: 1px solid #e8e8e8;

		&::placeholder {
			color: #BDCADF;
		}

		&:active,
		&:focus-within,
		&:focus {
			border: 1px solid #ddd;
			background-color: #fff;
			box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
		}

		&:disabled {
			background-color: #eff2f4;
			color: #BDCADF;
			box-shadow: none;

			&:active,
			&:focus-within,
			&:focus {
				border: 1px solid #eff2f4;
				background-color: #eff2f4;
				box-shadow: none;
			}
		}
	}
}
</style>
