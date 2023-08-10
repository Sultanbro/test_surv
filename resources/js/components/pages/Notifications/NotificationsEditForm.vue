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
					v-b-popover.hover="'Название уведомления'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
				>
			</div>
			<b-form-input
				v-model="value.name"
				class="NotificationsEditForm-control"
				type="text"
				placeholder="Название"
				required
			/>
		</label>

		<!-- Получатели -->
		<div
			class="NotificationsEditForm-row"
			@click="onClickRecipments"
		>
			<div class="NotificationsEditForm-label">
				Кого уведомляем
				<img
					v-b-popover.hover="'Сотрудники, отделы или должности кому будет приходить уведомление'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
				>
			</div>
			<div class="NotificationsEditForm-control NotificationsEditForm-badges form-control">
				<b-badge
					v-for="recipient, index in value.recipients"
					:key="index"
				>
					{{ recipient.name }}
				</b-badge>
				&nbsp;
			</div>
		</div>

		<!-- Текст -->
		<label class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Текст уведомления
				<img
					v-b-popover.hover="'Текст уведомления'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
				>
			</div>
			<b-form-textarea
				v-model="value.title"
				class="NotificationsEditForm-control"
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
					v-b-popover.hover="'Портал, почта или интеграция'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
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
					track-by="value"
				/>
			</div>
		</div>

		<!-- Периодичность -->
		<div class="NotificationsEditForm-row">
			<div class="NotificationsEditForm-label">
				Периодичность отправки
				<img
					v-b-popover.hover="'В какаие дни и в какое время будут отправляться уведомления'"
					src="/images/dist/profit-info.svg"
					class="img-info"
					alt="info icon"
				>
			</div>
			<div class="NotificationsEditForm-control">
				<!-- <b-form-timepicker
					v-model="value.time"
					:hour12="false"
					class="mb-4"
				/> -->
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
				:value="value.recipients"
				:tabs="['Сотрудники', 'Отделы', 'Должности']"
				:access-dictionaries="accessDictionaries"
				search-position="beforeTabs"
				:submit-button="'Применить'"
				class="NotificationsEditForm-accessSelect"
				@submit="onSubmitAccess"
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
import {
	services,
	periods,
} from './helper'


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
		return {
			value: JSON.parse(JSON.stringify(this.notification)),
			isRecipientsOpen: false,
			selectedServices: this.notification.type_of_mailing.map(value => services.find(service => service.value === value)),
			services,
			periods,
		}
	},
	computed: {
		...mapGetters([
			'users',
			'positions',
			'profileGroups',
			'accessDictionaries',
		]),
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
			this.value.type_of_mailing = this.selectedServices.map(service => service.value)
			this.$emit('save', this.value)
		},
		onClickRecipments(){
			this.isRecipientsOpen = true
		},
		onSubmitAccess(value){
			this.value.recipients = value
			this.isRecipientsOpen = false
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
		.img-info{
			width: 20px;
			vertical-align: middle;
		}
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
	.multiselect-surv .multiselect__tags{
		padding: 0.9rem 4rem 0.9rem 2rem;
		border: 1px solid #e8e8e8;
	}
	.multiselect-surv .multiselect__select{
		top: 0.25rem;
	}
}
</style>
