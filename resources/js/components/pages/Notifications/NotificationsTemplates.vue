<template>
	<SideBar
		width="50%"
		:open="true"
		class="NotificationsTemplates"
		@close="$emit('close')"
	>
		<template #header>
			<div class="ui-sidebar__header-text">
				{{ templates.find(tpl => tpl.value === template).text }}
			</div>
		</template>
		<template v-if="value">
			<b-container>
				<!-- Кого уведомляем -->
				<b-row class="mb-4">
					<b-col>
						<div class="mb-2">
							Кого уведомляем
						</div>
						<div
							v-if="typeof value.targets === 'string'"
							class="form-control"
						>
							{{ value.targets }}
						</div>
						<template v-else>
							<div
								class="form-control NotificationsTemplates-badges"
								@click="onClickRecipments"
							>
								<template v-for="recipient, index in value.recipients">
									<b-badge
										v-if="recipient.name"
										:key="index"
									>
										{{ recipient.name }}
									</b-badge>
								</template>
								&nbsp;
							</div>
							<JobtronOverlay
								v-if="isRecipientsOpen"
								@close="isRecipientsOpen = false"
							>
								<AccessSelect
									:value="value.recipients"
									:tabs="value.targets"
									:access-dictionaries="accessDictionaries"
									search-position="beforeTabs"
									:submit-button="'Применить'"
									class="NotificationsEditForm-accessSelect"
									@submit="onSubmitAccess"
								/>
							</JobtronOverlay>
						</template>
					</b-col>
				</b-row>

				<!-- Сообщение -->
				<b-row class="mb-4">
					<b-col>
						<div class="mb-2">
							Сообщение
							<img
								v-b-popover.hover="'Максимум 1000 символов'"
								src="/images/dist/profit-info.svg"
								class="img-info"
								alt="info icon"
							>
						</div>
						<div
							class="form-control relative"
							:class="{
								'NotificationsTemplates-error': value.title && (value.title.length > 1000)
							}"
						>
							<JobtronTextarea
								v-model="value.title"
								class="NotificationsTemplates-textarea"
							/>
							<div class="NotificationsTemplates-fixed">
								{{ value.titleFixed }}
							</div>
							<div class="NotificationsTemplates-tip">
								{{ value.titleTip }}
							</div>
							<div class="NotificationsEditForm-charCount">
								{{ value.title ? value.title.length : 0 }}
							</div>
						</div>
					</b-col>
				</b-row>

				<!-- Куда отправляем -->
				<b-row class="mb-4">
					<b-col>
						<div class="mb-2">
							Куда отправляем
						</div>
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
					</b-col>
				</b-row>

				<!-- Когда отправляем -->
				<b-row class="mb-4">
					<b-col>
						<div class="mb-2">
							Когда отправляем
						</div>
						<b-form-select
							v-model="when"
							:options="value.when"
							class="mb-4"
						/>
						<template v-if="when === 'period'">
							<!-- <b-form-timepicker
								v-model="time"
								:hour12="false"
								class="mb-4"
							/> -->
							<b-form-select
								v-model="frequency"
								:options="periods"
								class="mb-4"
							/>
							<DaysCheck
								v-if="frequency === 'monthly'"
								v-model="days"
							/>
							<WeekdaysCheck
								v-if="frequency === 'weekly'"
								v-model="days"
							/>
						</template>
					</b-col>
				</b-row>
			</b-container>

			<hr class="mb-4">
			<b-button
				variant="primary"
				@click="onSave"
			>
				Активировать уведомление
			</b-button>
		</template>
	</SideBar>
</template>

<script>
/* eslint-disable camelcase */

import { mapGetters } from 'vuex'
import JobtronOverlay from '@ui/Overlay'
import AccessSelect from '@ui/AccessSelect/AccessSelect'
import SideBar from '@ui/Sidebar'
import JobtronTextarea from '@ui/Textarea'
import Multiselect from 'vue-multiselect'
import DaysCheck from '@ui/Checkers/DaysCheck'
import WeekdaysCheck from '@ui/Checkers/WeekdaysCheck'
import {
	services,
	periods,
	templates,
	templateSettings,
	templateFrequency,
} from './helper'

export default {
	name: 'NotificationsTemplates',
	components: {
		SideBar,
		JobtronOverlay,
		AccessSelect,
		Multiselect,
		DaysCheck,
		WeekdaysCheck,
		JobtronTextarea,
	},
	props: {
		edit: {
			type: Object,
			default: null
		}
	},
	data(){
		return {
			template: '',
			templates,
			templateSettings,
			value: {},
			services,
			periods,
			selectedServices: [],
			when: 'trigger',
			frequency: 'weekly',
			days: [],
			time: '10:00',
			isRecipientsOpen: false,
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
		template(){
			if(!this.template) {
				this.value = null
				return
			}
			this.value = JSON.parse(JSON.stringify(this.templateSettings[this.template]))
			this.when = this.template
		},
		edit(){
			this.loadEdit()
		},
	},
	mounted(){
		this.loadEdit()
	},
	methods: {
		loadEdit(){
			if(!this.edit) return
			if(!this.value) this.value = {
				id: 0,
				title: '',
				recipients: [],
				targets: '',
				titleFixed: '',
				titleTip: '',
				when: [],
			}
			this.template = this.edit.template
			this.$nextTick(() => {
				if(!this.edit.id) return
				this.value.id = this.edit.id
				this.value.recipients = this.edit.recipients
				this.value.title = this.edit?.title || ''
				this.selectedServices = this.edit.type_of_mailing.map(value => services.find(service => service.value === value))
				this.frequency = this.edit.date.frequency
				if(!templateFrequency.includes(this.edit.date.frequency)){
					this.when = 'period'
				}
			})
		},
		onClickRecipments(){
			this.isRecipientsOpen = true
		},
		onSubmitAccess(value){
			this.value.recipients = value
			this.isRecipientsOpen = false
		},
		onSave(){
			const name = templates.find(template => template.value === this.template).text
			this.$emit('save', {
				id: this.value.id,
				name,
				title: this.value.title,
				recipients: Array.isArray(this.value.targets) ? this.value.recipients.map(rec => {
					if(!rec.type){
						rec.type = 4
					}
					return rec
				}) : undefined,
				date: {
					days: this.days,
					frequency: this.when === 'period' ? this.frequency : this.when
				},
				time: this.time,
				type_of_mailing: this.selectedServices.map(service => service.value),
				is_template: true,
			})
		}
	}
}
</script>

<style lang="scss">
.NotificationsTemplates{
	&-textarea{
		width: 100%;
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
	&-fixed{
		font-style: italic;
	}
	&-tip{
		font-style: italic;
		color: #F8254B;
	}
	& .NotificationsTemplates-error.form-control{
		border-color: #f00;
		color: #f00;
		&:focus{
			border-color: #f00;
			color: #f00;
		}
	}
	&-charCount{
		padding: 1px 3px;
		border-top: 1px solid #e8e8e8;
		border-left: 1px solid #e8e8e8;
		border-radius: 6px;

		position: absolute;
		right: 0;
		bottom: 0;

		font-size: 11px;
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
