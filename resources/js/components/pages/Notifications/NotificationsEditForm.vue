<template>
	<div
		v-if="notification"
		class="NotificationsEditForm"
	>
		<!-- Название -->
		<label class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Название уведомления
				<img
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					v-b-popover.hover="'Название уведомления'"
				>
			</div>
			<b-form-input
				class="NotificationsEditForm-control"
				v-model="value.name"
				type="text"
				placeholder="Название"
				required
			/>
		</label>

		<!-- Получатели -->
		<div
			class="NotificationsEditForm-row"
			@click="isRecipientsOpen = true"
		>
			<div class="NotificationsEditForm-label">
				Кого уведомляем
				<img
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					v-b-popover.hover="'Сотрудники, отделы или должности кому будет приходить уведомление'"
				>
			</div>
			<div class="NotificationsEditForm-control NotificationsEditForm-badges form-control">
				<b-badge
					v-for="recipient, index in value.recipients"
					:key="index"
				>
					{{ recipient.name }}
				</b-badge>
			</div>
		</div>

		<!-- Текст -->
		<label class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Текст уведомления
				<img
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					v-b-popover.hover="'Текст уведомления'"
				>
			</div>
			<b-form-textarea
				class="NotificationsEditForm-control"
				v-model="value.title"
				placeholder="Текст уведомления"
				rows="3"
				max-rows="6"
				required
			/>
		</label>

		<!-- Куда отправлять -->
		<div class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Куда отправлять
				<img
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					v-b-popover.hover="'Портал, почта или интеграция'"
				>
			</div>
			<div class="NotificationsEditForm-control">
				<Multiselect
					v-model="selectedServices"
					:options="services"
					:multiple="true"
					label="title"
					:close-on-select="false"
					:clear-on-select="true"
					:preserve-search="true"
					placeholder="Выберите"
					:taggable="true"
					class="multiselect-surv"
				/>
			</div>
		</div>

		<!-- Периодичность -->
		<div class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Периодичность отправки
				<img
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
					v-b-popover.hover="'В какаие дни и в какое время будут отправляться уведомления'"
				>
			</div>
			<div class="NotificationsEditForm-control">
				<b-form-timepicker
					v-model="value.time"
					:hour12="false"
					class="mb-4"
				/>
				<b-form-select
					v-model="value.date.frequency"
					:options="periods"
					class="mb-4"
				/>
				<DaysCheck
					v-if="value.date.frequency === 'monthly'"
					v-model="value.date.days"
				/>
				<WeekdaysCheck
					v-if="value.date.frequency === 'weekly'"
					v-model="value.date.days"
				/>
			</div>
		</div>

		<hr class="mb-4">
		<b-button
			variant="primary"
			@click="onSave"
		>
			Сохранить
		</b-button>

		<JobtronOverlay
			v-if="isRecipientsOpen"
			@close="isRecipientsOpen = false"
		>
			<AccessSelect
				v-model="value.recipients"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				:submit-button="''"
				class="NotificationsEditForm-accessSelect"
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
import JobtronOverlay from '@ui/Overlay'
import AccessSelect from '@ui/AccessSelect/AccessSelect'
import Multiselect from 'vue-multiselect'
import DaysCheck from '@ui/Checkers/DaysCheck'
import WeekdaysCheck from '@ui/Checkers/WeekdaysCheck'


export default {
	name: 'NotificationsEditForm',
	components: {
		JobtronOverlay,
		AccessSelect,
		Multiselect,
		DaysCheck,
		WeekdaysCheck,
	},
	props: {
		notification: {
			type: Object,
			required: true
		}
	},
	data(){
		const services = [
			{
				value: 'jobtron',
				title: 'Jobtron'
			},
			{
				value: 'mail',
				title: 'Почта'
			},
			{
				value: 'sms',
				title: 'SMS'
			},
			{
				value: 'telegram',
				title: 'Telegram'
			},
			{
				value: 'bitrix',
				title: 'bitrix'
			},
		]
		return {
			value: JSON.parse(JSON.stringify(this.notification)),
			isRecipientsOpen: false,
			selectedServices: this.notification.type_of_mailing.map(value => services.find(service => service.value === value)),
			services,
			periods: [
				{
					value: 'weekly',
					text: 'По дням недели'
				},
				{
					value: 'monthly',
					text: 'По числам месяца'
				}
			]
		}
	},
	computed: {
		...mapGetters([
			'users',
			'positions',
			'profileGroups',
		]),
		groups(){
			return this.profileGroups
		},
		positionMap(){
			return this.positions.reduce((result, pos) => {
				result[pos.id] = pos.position
				return result
			}, {})
		},
		accessDictionaries(){
			return {
				users: this.users.reduce((users, user) => {
					if(user.deleted_at) return users
					users.push({
						id: user.id,
						name: `${user.name} ${user.last_name}`,
						avatar: `/users_img/${user.img_url}`,
						position: this.positionMap[user.position_id]
					})
					return users
				}, []),
				profile_groups: this.groups.filter(group => group.active),
				positions: this.positions.filter(pos => !pos.deleted_at).map(pos => ({
					id: pos.id,
					name: pos.position
				})),
			}
		},
	},
	watch: {
		notification(){
			this.value = JSON.parse(JSON.stringify(this.notification))
			this.selectedServices = this.value.type_of_mailing.map(value => this.services.find(service => service.value === value))
		},
		'value.date.frequency'(){
			this.value.date.days = []
		}
	},
	methods: {
		onSave(){
			this.$emit('save', this.value)
		}
	}
}
</script>

<style lang="scss">
.NotificationsEditForm{
	&-row{
		display: flex;
		align-items: flex-start;
		gap: 10px;

		margin-bottom: 20px;
	}

	&-label{
		flex: 0 0 30%;

		margin-top: 5px;

		font-size: 1.4rem;
    line-height: 2rem;
	}

	&-control{
		flex: 1;
	}

	&-badges{
		display: flex;
		flex-flow: row wrap;
		align-items: center;
		justify-content: flex-start;
		gap: 5px;

    padding: 10px;
    border: 1px solid #e8e8e8;
		border-radius: 6px;

		font-size: 14px;
		line-height: 1.3;

		background-color: #F7FAFC;
	}

	&-accessSelect{
		width: 420px;
		height: 720px;
		max-height: 80vh;
		padding: 20px;
		border-radius: 15px;

		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);

		background-color: #fff;
	}

	.b-form-timepicker{
		> .btn{
			margin-left: -20px;
		}
	}
}
</style>
