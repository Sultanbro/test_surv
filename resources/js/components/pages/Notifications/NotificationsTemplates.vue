<template>
	<SideBar
		width="50%"
		:open="true"
		@close="$emit('close')"
		class="NotificationsTemplates"
	>
		<template #header>
			<b-form-select
				v-model="template"
				:options="templates"
			/>
		</template>
		<template v-if="template">
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
								<b-badge
									v-for="recipient, index in value.recipients"
									:key="index"
								>
									{{ recipient.name }}
								</b-badge>
								&nbsp;
							</div>
							<JobtronOverlay
								v-if="isRecipientsOpen"
								@close="isRecipientsOpen = false"
							>
								<AccessSelect
									v-model="value.recipients"
									:tabs="value.targets"
									:access-dictionaries="accessDictionaries"
									search-position="beforeTabs"
									:submit-button="''"
									class="NotificationsEditForm-accessSelect"
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
						</div>
						<div class="form-control">
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
							<b-form-timepicker
								v-model="time"
								:hour12="false"
								class="mb-4"
							/>
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
			value: null,
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
			this.template = this.edit.template
			this.$nextTick(() => {
				this.value.recipients = this.edit.recipients
				this.value.title = this.edit.title
				this.selectedServices = this.edit.type_of_mailing.map(value => services.find(service => service.value === value))
				this.frequency = this.edit.date.frequency
				this.value.id = this.edit.id
				if(!templateFrequency.includes(this.edit.date.frequency)){
					this.when = 'period'
				}
			})
		},
		onClickRecipments(){
			if(!this.value.id) this.isRecipientsOpen = true
		},
		onSave(){
			const name = templates.find(template => template.value === this.template).text
			this.$emit('save', {
				id: this.value.id,
				name,
				title: this.value.title,
				recipients: this.value.recipients,
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
}
</style>
