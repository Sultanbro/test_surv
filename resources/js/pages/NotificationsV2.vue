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
						<b-th>
							Название
						</b-th>
						<b-th>
							Текст уведомления
						</b-th>
						<b-th class="text-center">
							Кого уведомляем
						</b-th>
						<b-th class="text-center">
							Куда отправлять
						</b-th>
						<b-th class="text-center">
							Периодичность отправки
						</b-th>
						<b-th class="text-center">
							Дата создания
						</b-th>
						<b-th class="text-center">
							Создатель
						</b-th>
						<b-th />
					</b-tr>
				</b-thead>
				<b-tbody>
					<b-tr
						v-for="notification, key in filteredNotifications"
						:key="key"
					>
						<b-td @click="openEditSidebar(notification)">
							{{ key + 1 }}
						</b-td>
						<b-td
							class=""
							@click="openEditSidebar(notification)"
						>
							{{ notification.name }}
						</b-td>
						<b-td
							class="NotificationsV2-text"
							@click="openEditSidebar(notification)"
						>
							<TextClip>
								{{ notification.title }}
							</TextClip>
						</b-td>
						<b-td
							class="NotificationsV2-text text-center"
							@click="openEditSidebar(notification)"
						>
							<TextClip>
								<template v-if="recipientsNames[notification.date.frequency]">
									{{ recipientsNames[notification.date.frequency] }}
								</template>
								<template v-else>
									{{ notification.recipients.filter(recipient => !!recipient.name).map(recipient => recipient.name).join(', ') }}
								</template>
							</TextClip>
						</b-td>
						<b-td
							class="NotificationsV2-text text-center"
							@click="openEditSidebar(notification)"
						>
							{{ notification.type_of_mailing.map(type => services.find(service => service.value === type).short).join(', ') }}
						</b-td>
						<b-td
							class="text-center"
							@click="openEditSidebar(notification)"
						>
							{{ periodNames[notification.date.frequency] }} {{
								notification.date.frequency === 'weekly' ? weekdayNames(notification.date.days).join(', ') : notification.date.days.join(', ')
							}}
						</b-td>
						<b-td
							class="text-center wsnw"
							@click="openEditSidebar(notification)"
						>
							{{ $moment(notification.created_at).format('DD.MM.YYYY') }}
						</b-td>
						<b-td
							class="text-center wsnw"
							@click="openEditSidebar(notification)"
						>
							{{ notification.creator.name }} {{ notification.creator.last_name }}
						</b-td>
						<b-td class="text-center">
							<div class="d-flex gap-3">
								<JobtronSwitch
									:value="!!notification.status"
									@input="onChangeStatus(notification)"
								/>
								<b-button
									class="btn btn-danger btn-icon"
									@click.stop="remove(notification)"
								>
									<i class="fa fa-trash" />
								</b-button>
							</div>
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
			<b-container class="NotificationsV2-settingsDialog">
				<b-row class="mb-4">
					<b-col cols="4">
						<span class="NotificationsV2-label">Количество напоминаний в день</span>
						<img
							v-b-popover.hover="'Указаное кол-во раз, в течение рабочего дня будет показан попап с уведомлениями'"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
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
							class="relative"
						>
							<ChatIconPlus
								class="ChatIcon-active"
								width="11"
								height="11"
							/>
							Шаблонное уведомление
							<select
								v-model="template"
								class="NotificationsV2-hiddenSelect custom-select"
							>
								<option
									v-for="tpl in templates"
									:key="tpl.value"
									:class="tpl.class"
									:value="tpl.value"
								>
									{{ tpl.text }}
								</option>
							</select>
						</JobtronButton>
					</b-col>
				</b-row>
			</b-container>
			<hr class="mb-4">
			<JobtronButton
				small
				@click="onSaveSettings"
			>
				Сохранить
			</JobtronButton>
		</SideBar>

		<!-- Шаблонные уведомления -->
		<NotificationsTemplates
			v-if="selectedTemplate"
			:edit="selectedTemplate"
			@close="onCloseTemplate"
			@save="onSave"
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
	fetchSettings,
	updateSettings,
} from '@/stores/api.js'
import NotificationsEditForm from '@/components/pages/Notifications/NotificationsEditForm'
import NotificationsTemplates from '@/components/pages/Notifications/NotificationsTemplates'
import {
	templateFrequency,
	services,
	templates,
	weekdays,
} from '@/components/pages/Notifications/helper'
import SideBar from '@ui/Sidebar'
import JobtronButton from '@ui/Button'
import JobtronSwitch from '@ui/Switch'
import TextClip from '@ui/TextClip'
import {
	ChatIconPlus,
} from '@icons'

const getNamesMethods = {
	'App\\User': 'getUserName',
	'App\\ProfileGroup': 'getGroupName',
	'App\\Position': 'getPositionName',
	'All': 'getAllName',
}
const typeToNumber = {
	'App\\User': 1,
	'App\\ProfileGroup': 2,
	'App\\Position': 3,
	'All': 4,
}

export default {
	name: 'NotificationsV2',
	components: {
		SideBar,
		NotificationsEditForm,
		NotificationsTemplates,
		JobtronButton,
		JobtronSwitch,
		ChatIconPlus,
		TextClip,
	},
	data(){
		return {
			notifications: null,
			/* eslint-disable camelcase */
			periodNames: {
				monthly: 'по дням месяца',
				weekly: 'по дням недели',
				daily: 'каждый день',
				apply_employee: 'Уведомлять в момент принятия (триггер)',
				fired_employee: 'Через день после отметки об увольнении (триггер)',
				absent_internship: 'В момент отметки в табеле об отсутствии (триггер)',
				manager_assessment: 'За 2 дня до окончания календарного месяца (триггер)',
				coach_assessment: 'В период 17:00 - 19:00 в первый день обучения стажера, если он не отмечен, как отсутствовал (триггер)',
			},
			recipientsNames: {
				fired_employee: 'Статус сотрудника "Уволенный"',
				manager_assessment: 'Все сотрудники отделов',
				coach_assessment: 'Стажеры первого дня',
			},
			/* eslint-enable camelcase */
			selectedNotification: null,
			search: '',
			services,
			templates,
			template: '',

			isSettings: '',
			settings: {
				showCount: 0
			},

			selectedTemplate: null,
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
		},
		template(){
			if(this.template) this.onNewTemplate(this.template)
		}
	},
	created(){
		if(!this.users.length) this.loadCompany()
		this.fetchSettings()
	},
	mounted(){
		if(this.users.length) this.fetchNotifications()
	},
	methods: {
		...mapActions(['loadCompany']),
		async fetchNotifications(){
			const notifications = await fetchNotificationVariants()
			this.notifications = this.parseNotifications(notifications)
			this.removeInactiveTargets()
		},
		parseNotifications(notifications){
			return notifications.map(notification => {
				return {
					...notification,
					/* eslint-disable-next-line camelcase */
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
						days: JSON.parse(notification.days || '[]') || []
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
		getAllName(){
			return 'Все'
		},
		async remove(notification){
			if(!confirm('Удалить уведомление?')) return
			const {data} = await deleteNotification(notification.id)
			if(data) {
				const index = this.notifications.findIndex(noti => noti.id === notification.id)
				this.notifications.splice(index, 1)
				this.$toast.success('Уведомление удалено')
			}
			else{
				this.$toast.error('Ошибка при удалении уведомления, попробуйте позже')
			}
		},
		openEditSidebar(notification){
			if(!notification) {
				this.selectedNotification = this.getBlankNotification()
				return
			}
			if(!notification.is_template){
				this.selectedNotification = JSON.parse(JSON.stringify(notification))
				return
			}
			const template = templateFrequency.includes(notification.date.frequency) ? notification.date.frequency : 'apply_employee'
			this.selectedTemplate = {
				template,
				...notification
			}
		},
		getBlankNotification(){
			/* eslint-disable camelcase */
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
				type_of_mailing: [],
				is_template: false,
			}
			/* eslint-enable camelcase */
		},
		onSave(notification){
			const errors = this.validate(notification)
			if(errors.length){
				this.$toast.error(errors.map(err => err.error).join('\n'))
				return
			}
			if(notification.id) this.updateNotification(notification)
			else this.createNotification(notification)
		},
		validate(notification){
			const errors = []
			if(!notification.name) errors.push({field: 'name', error: 'Название уведомления должно быть заполнено'})
			if(!notification.title) errors.push({field: 'title', error: 'Текст уведомления должен быть заполнен'})
			if(!notification.is_template && !notification.recipients.length) errors.push({field: 'recipients', error: 'Укажите минимум одного получателя'})
			if(!notification.type_of_mailing && !notification.type_of_mailing.length) errors.push({field: 'type_of_mailing', error: 'Укажите минимум однин способ отправки'})
			if((notification.date.frequency === 'weekly' || notification.date.frequency === 'monthly') && !notification.date.days.length) errors.push({field: 'days', error: 'Укажите минимум один день отправки'})
			return errors
		},
		async createNotification(notification){
			const {message} = await createNotification(notification)
			if(message === 'Success created'){
				this.$toast.success('Уведомление успешно создано')
				this.template = ''
				this.selectedTemplate = null
				this.selectedNotification = null
			}
			else{
				this.$toast.error((message || '').raplace('title', 'Текст уведомления'))
			}
			this.fetchNotifications()
		},
		async updateNotification(notification, silent){
			const {message} = await updateNotification(notification)
			if(message === 'Success'){
				if(!silent) this.$toast.success('Уведомление успешно сохранено')
				this.template = ''
				this.selectedTemplate = null
				this.selectedNotification = null
				const index = this.notifications.findIndex(n => n.id === notification.id)
				if(!~index) return
				this.$set(this.notifications, index, {
					...this.notifications[index],
					...notification,
				})
			}
			else{
				if(!silent) this.$toast.error((message || '').raplace('title', 'Текст уведомления'))
			}
		},
		async fetchSettings(){
			const {settings} = await fetchSettings('notifications_remind_count')
			if(settings.custom_notifications_remind_count){
				this.settings.showCount = parseInt(settings.custom_notifications_remind_count) || 0
			}
		},
		async onSaveSettings(){
			await updateSettings({
				type: 'notifications_remind_count',
				/* eslint-disable-next-line camelcase */
				custom_notifications_remind_count: this.settings.showCount
			})
			this.$toast.success('Настройки сохранены')
			this.isSettings = false
		},
		onNewTemplate(template = ''){
			/* eslint-disable camelcase */
			this.selectedTemplate = {
				template,
				id: 0,
				name: '',
				title: '',
				recipients: [],
				date: {
					days: [],
					frequency: 'weekly'
				},
				time: '10:00',
				type_of_mailing: [],
				is_template: true,
			}
			/* eslint-enable camelcase */
		},
		onCloseTemplate(){
			this.template = ''
			this.selectedTemplate = null
		},
		async onChangeStatus(notification){
			notification.status = notification.status ? 0 : 1
			const request = JSON.parse(JSON.stringify(notification))
			if(!request.recipients?.length){
				delete request.recipients
			}
			const {message} = await updateNotification(request)
			if(message === 'Success'){
				this.$toast.success(`Уведомление ${notification.status ? 'активировано' : 'деактивировано'}`)
				const index = this.notifications.findIndex(n => n.id === notification.id)
				if(!~index) return
				this.$set(this.notifications, index, notification)
			}
			else{
				this.$toast.error(message)
			}
		},
		removeInactiveTargets(){
			this.notifications.forEach(item => {
				const hasEmpty = item.recipients.find(rec => !rec.name)
				if(!hasEmpty) return
				this.updateNotification({
					...item,
					recipients: item.recipients.filter(rec => rec.name)
				}, true)
			})
		},
		weekdayNames(days){
			return days.map(day => weekdays[day])
		},
	}
}
</script>

<style lang="scss">
.NotificationsV2{
	&-hiddenSelect{
		opacity: 0;
		position: absolute;
		z-index: 1;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}
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
		max-width: 300px;
	}

	&-label{
		font-size: 1.4rem;
    line-height: 2rem;
	}
	&-settingsDialog{
		.img-info{
			width: 20px;
			vertical-align: middle;
		}
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
