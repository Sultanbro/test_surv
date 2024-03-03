<template>
	<div
		v-if="activeuserid"
		class="groups CompanyGroups"
	>
		<b-alert
			v-if="message!=null"
			variant="info"
		>
			{{ message }}
		</b-alert>
		<b-row class="align-items-center">
			<b-col
				cols="12"
				lg="4"
				md="5"
				class="d-flex align-items-start"
			>
				<b-form-group class="w-100">
					<multiselect
						ref="groupsMultiselect"
						v-model="activebtn"
						:options="statuses"
						placeholder="Выберите отдел из списка"
						track-by="group"
						label="group"
						:show-no-options="false"
						@select="selectGroup"
					>
						<template #afterList>
							<li class="multiselect-add-li">
								<span
									class="multiselect-add-btn"
									@click="addGroup"
								>Добавить новый отдел</span>
							</li>
						</template>
					</multiselect>
				</b-form-group>
				<button
					class="btn btn-info rounded add-s ml-4"
					title="Восстановить из архива"
					@click="showArchiveModal = true"
				>
					<i class="fa fa-archive" />
				</button>
			</b-col>
		</b-row>

		<hr
			v-if="activebtn != null || addNewGroup"
			class="my-4"
		>
		<h4
			v-if="addNewGroup"
			class="groups-title-new mb-5"
		>
			Создание новой группы
		</h4>
		<div
			v-if="activebtn != null || addNewGroup"
			class="row"
		>
			<div class="col-lg-6 mb-3">
				<b-form-group
					label="Название отдела"
					label-cols="6"
					class="mb-4"
				>
					<b-form-input
						v-model="new_status"
						type="text"
						class="form-control"
					/>
				</b-form-group>
				<div
					v-if="workChart"
					class="dialerlist"
				>
					<div class="fl">
						График работы
						<img
							v-b-popover.hover.right="'Начало и окончание рабочего дня всего отдела, индивидуальное время выставляется в профиле'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</div>
					<div class="fl">
						<b-form-select v-model="workChartId">
							<b-form-select-option
								disabled
								value="null"
							>
								Выберите график работы
							</b-form-select-option>
							<template v-for="chart in workChart">
								<b-form-select-option
									:key="chart.id"
									:value="chart.id"
								>
									{{ getShiftDays(chart) }} (с {{ chart.start_time }} по {{ chart.end_time }}) - {{ chart.text_name }}
								</b-form-select-option>
							</template>
						</b-form-select>
					</div>
				</div>

				<div
					v-if="isBp"
					class="dialerlist"
				>
					<div class="fl">
						Подтягивать время
						<i
							class="icon-nd-settings ml-2"
							@click="editTimeAddress()"
						/>
					</div>
					<div class="fl">
						<input
							v-model="time_address_text"
							type="text"
							class="form-control"
							style="background: #fff"
							disabled
						>
					</div>
				</div>

				<!-- Статус: скрыто. Компонент: groups. Дата скрытия: 03.02.2023 12:13 -->
				<div
					v-if="false"
					class="dialerlist"
				>
					<div class="fl">
						Кол-во рабочих дней
						<img
							id="info1"
							src="/images/dist/profit-info.svg"
							class="img-info"
							alt="info icon"
						>
						<b-popover
							target="info1"
							triggers="hover"
							placement="right"
						>
							<p style="font-size: 15px">
								Тут указывается количество рабочих дней в неделю конкретно для этого отдела.
								<br>
								Для будующего функционала.
							</p>
						</b-popover>
					</div>
					<div class="fl">
						<input
							v-model="workdays"
							type="number"
							class="form-control"
							min="1"
							max="7"
						>
					</div>
				</div>

				<div class="dialerlist">
					<b-form-checkbox
						v-model="editable_time"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Табель редактируется
					</b-form-checkbox>
				</div>

				<div class="dialerlist">
					<b-form-checkbox
						v-model="paid_internship"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Оплачиваемая стажировка
						<img
							v-b-popover.hover.right="'Стажировочные дни будут оплачиваться в размере 50% от дневного оклада'"
							src="/images/dist/profit-info.svg"
							class="img-info"
						>
					</b-form-checkbox>
				</div>

				<div class="mt-4">
					<b-form-checkbox
						v-model="show_payment_terms"
						:value="1"
						:unchecked-value="0"
						switch
					>
						Показывать в профиле
					</b-form-checkbox>
				</div>

				<div
					v-if="show_payment_terms"
					class="card groups-card mt-4"
				>
					<div class="card-header">
						Информация в профиле
					</div>
					<div class="card-body">
						<b-form-group label="Условия оплаты труда">
							<b-textarea
								v-model="payment_terms"
								style="min-height: 150px;"
							/>
						</b-form-group>
					</div>
				</div>

				<div
					v-if="!addNewGroup"
					class="card groups-card mt-4"
				>
					<div class="CompanyGroups-label card-header">
						Документы
					</div>
					<div class="card-body">
						<div
							v-if="documents.length"
							class="CompanyGroups-docs"
						>
							<div
								v-for="doc, index in documents"
								:key="index"
								class="CompanyGroups-doc mb-2"
							>
								<div class="CompanyGroups-docIcon">
									<img
										src="/icon/doc-pdf.png"
										alt="pdf"
										width="24"
									>
								</div>
								<a
									:href="`/signature/view?group=${group_id}&doc=${doc.id}`"
									target="_blank"
									class="CompanyGroups-docName"
								>
									{{ doc.name }}
								</a>
								<div class="CompanyGroups-docControls">
									<JobtronButton
										small
										@click="onEditDoc(doc)"
									>
										<i class="far fa-edit" />
									</JobtronButton>
									<JobtronButton
										small
										error
										@click="onDeleteDoc(doc)"
									>
										<i class="fas fa-trash" />
									</JobtronButton>
								</div>
							</div>
						</div>
						<div
							v-else
							class="CompanyGroups-docsEmpty"
						>
							Нет документов
						</div>
						<div class="CompanyGroups-add mt-4">
							<JobtronButton
								small
								@click="onAddDoc()"
							>
								<i class="fas fa-plus" /> Добавить документ
							</JobtronButton>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 mb-3 sssz">
				<div
					v-if="value.length"
					class="card groups-card staff-list"
				>
					<div class="card-header">
						<b-form-input
							v-model="searchUsers"
							placeholder="Поиск по сотрудникам"
						/>
						<span>Сотрудников: {{ value.length }}</span>
					</div>
					<div class="card-body">
						<div
							v-for="(employee, index) in filteredUsers"
							:key="employee.id"
							class="employee"
						>
							<span>{{ employee.email }}</span> <i
								class="fa fa-trash btn btn-icon btn-danger"
								@click="removeValue(index)"
							/>
						</div>
					</div>
					<div class="card-footer">
						<b-button
							variant="danger"
							@click="showAlert"
						>
							Удалить всех сотрудников
						</b-button>
					</div>
				</div>
				<div v-else>
					Пока нет ни одного сотрудника в группе
				</div>
			</div>
			<div class="col-lg-12 mb-3">
				<button
					class="btn btn-success mr-2 rounded"
					@click="saveusers"
				>
					Сохранить
				</button>
				<button
					v-if="!addNewGroup"
					class="btn btn-danger mr-2 rounded"
					@click.stop="deleted"
				>
					<i class="fa fa-trash" /> Удалить группу
				</button>
			</div>
		</div>

		<!-- Modal  -->
		<b-modal
			v-model="showEditTimeAddress"
			title="Подтягивать часы"
			size="lg"
			class="modalle"
			@ok="saveTimeAddress()"
		>
			<div class="row">
				<div class="col-5 mt-1">
					<p class="">
						Источник часов
						<i
							v-b-popover.hover.right.html="'При смене источника, новые данные в табеле будут только со дня смены источника'"
							class="fa fa-info-circle"
						/>
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="time_address"
						class="form-control form-control-sm"
					>
						<option
							v-for="(time, key) in time_variants"
							:key="key"
							:value="key"
						>
							{{ time }}
						</option>
					</select>
				</div>
			</div>

			<div
				v-if="time_address == -1"
				class="row"
			>
				<div class="col-5 mt-1">
					<div class="fl">
						ID диалера
						<i
							v-b-popover.hover.right.html="'Нужен, чтобы <b>подтягивать часы</b> или <b>оценки диалогов</b> для контроля качества.<br>С сервиса cp.callibro.org'"
							class="fa fa-info-circle ml-2"
							title="Диалер в U-Calls"
						/>
					</div>
				</div>
				<div class="col-7 mt-1">
					<div class="fl d-flex">
						<input
							v-model="dialer_id"
							type="text"
							placeholder="ID"
							class="form-control scscsc"
						>
						<input
							v-model="script_id"
							type="number"
							placeholder="ID скрипта"
							class="form-control scscsc"
						>
					</div>
				</div>
			</div>

			<div
				v-if="time_address == -1"
				class="row"
			>
				<div class="col-5 mt-1">
					<div class="fl">
						Сколько минут считать, за полный рабочий день
						<i
							v-b-popover.hover.right.html="'Запишите сколько минут разговора с сервиса cp.callibro.org считать, за полный рабочий день. <br>Пример: 250 минут считается как 8 часов'"
							class="fa fa-info-circle ml-2"
							title="Ставить полный рабочий день"
						/>
					</div>
				</div>
				<div class="col-7 mt-1">
					<div class="fl d-flex">
						<input
							v-model="talk_minutes"
							type="text"
							placeholder="ID"
							class="form-control scscsc"
						>
						<input
							v-model="talk_hours"
							type="number"
							placeholder="ID скрипта"
							class="form-control scscsc"
						>
					</div>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-12">
					<p class="">
						Исключения

						<i
							v-b-popover.hover.right.html="'Часы выбранных сотрудников, не будут копироваться из аналитики в табель'"
							class="fa fa-info-circle"
						/>
					</p>
				</div>
				<div class="col-12 mt-1">
					<multiselect
						v-model="time_exceptions"
						:options="time_exceptions_options"
						:multiple="true"
						:close-on-select="false"
						:clear-on-select="false"
						:preserve-search="true"
						placeholder="Выберите, кого не связывать"
						label="email"
						track-by="email"
						:taggable="true"
						:show-no-options="false"
						@tag="addExceptionTag"
					/>
				</div>
			</div>
		</b-modal>

		<!-- Modal restore archived group -->
		<b-modal
			v-model="showArchiveModal"
			size="md"
			title="Восстановить из архива"
			class="modalle"
			@ok="restoreGroup()"
		>
			<div>
				<b-form-group>
					<select
						v-model="restore_group"
						class="form-control"
					>
						<option
							v-for="(archived_group, key) in archived_groups"
							:key="key"
							:value="archived_group"
						>
							{{ archived_group.name }}
						</option>
					</select>
				</b-form-group>
			</div>
		</b-modal>

		<b-modal
			v-model="docEditDialog"
			size="md"
			:title="documentForm.id > 0 ? 'Редактирование документа' : 'Создание  документа'"
			body-class="CompanyGroups-modal"
			:no-close-on-backdrop="uploadProgress > 0"
			:no-close-on-esc="uploadProgress > 0"
			:hide-header="uploadProgress > 0"
		>
			<b-row class="mb-4">
				<b-col
					cols="3"
					class="pt-3"
				>
					Название
				</b-col>
				<b-col cols="9">
					<b-form-input
						v-model="documentForm.name"
					/>
				</b-col>
			</b-row>

			<b-row
				v-if="documentForm.id <= 0"
				class="mb-4"
			>
				<b-col
					cols="3"
					class="pt-3"
				>
					Файл pdf
				</b-col>
				<b-col cols="9">
					<InputFile
						accept=".pdf"
						@change="uploadDoc"
					>
						<div class="form-control">
							<div class="mt-2">
								{{ documentForm.upload ? documentForm.upload.name : 'Нет документа' }}
							</div>
						</div>
					</InputFile>
				</b-col>
			</b-row>

			<b-progress
				v-if="uploadProgress"
				:value="uploadProgress"
				:max="100"
				show-progress
				animated
			/>
			<template #modal-footer>
				<b-btn
					variant="primary"
					:disabled="uploadProgress > 0"
					@click="onSaveDoc"
				>
					OK
				</b-btn>
			</template>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */
import JobtronButton from '../components/ui/Button.vue'
import InputFile from '../components/ui/InputFile.vue'

import { getShiftDays } from '@/composables/shifts'

export default {
	name: 'CompanyGroups',
	components: {
		JobtronButton,
		InputFile,
	},
	props: {
		statuseses: {
			type: Array,
			default: () => []
		},
		archived_groupss: {
			type: Array,
			default: () => []
		},
		activeuserid: {
			type: Number,
			default: 0
		},
	},
	data() {
		return {
			isBp: window.location.hostname.split('.')[0] === 'bp',
			addNewGroup: false,
			message: null,
			activebtn: null,
			statuses: [],
			activities: [],
			restore_group: null,
			new_status: '',
			value: [], // selected users
			options: [], // users options
			workChart: null,
			workChartId: null,
			archived_groups: [],
			payment_terms: '', // Условия оплаты труда в группе
			timeon: '09:00',
			timeoff: '18:00',
			dialer_id: null,
			script_id: null,
			talk_minutes: null,
			talk_hours: null,
			// time edit
			time_address: 0,
			editable_time: 0,
			time_address_text: 'Не выбран',
			time_variants: [],
			workdays: 5,
			quality: 'local',
			time_exceptions: [],
			time_exceptions_options: [],
			showEditTimeAddress: false,
			paid_internship: 0,
			show_payment_terms: 0,
			zarplata: '0',
			showKPI: false,
			showDeleteButton: false,
			showArchiveModal: false,
			showAfterEdit: false,
			group_id: 0,
			zoom_link: 'empty', // Ссылка zoom для обучения стажеров
			bp_link: 'empty',
			searchUsers: '',
			units: [
				{
					title: 'За единицу',
					value: 'one',
				},
				{
					title: 'За все, первому',
					value: 'first',
				},
				{
					title: 'За все, каждому',
					value: 'all',
				},
			],
			dayparts: [
				{
					title: 'Весь день',
					value: 0,
				},
				{
					title: '09:00 - 13:00',
					value: 1,
				},
				{
					title: '14:00 - 19:00',
					value: 2,
				},
				{
					title: '12:00 - 16:00',
					value: 3,
				},
				{
					title: '17:00 - 21:00',
					value: 4,
				},
			],
			documents: [],
			documentForm: {
				id: 0,
				name: '',
				file: '',
				upload: null,
			},
			docId: 0,
			docEditDialog: false,
			uploadProgress: 0,
		};
	},
	computed: {
		filteredUsers() {
			return this.searchUsers ? this.value.filter(v => v.email.toLowerCase().includes(this.searchUsers.toLowerCase())) : this.value;
		}
	},
	watch: {
		activeuserid() {
			this.init()
		}
	},
	created() {
		this.axios.get('/work-chart').then(res => {
			this.workChart = res.data.data;
		});
		if (this.activeuserid) {
			this.init()
		}
	},
	methods: {
		resetState() {
			this.message = null;
			this.activebtn = null;
			this.activities = [];
			this.restore_group = null;
			this.new_status = '';
			this.value = [];
			this.options = [];
			this.payment_terms = '';
			this.timeon = '09:00';
			this.timeoff = '18:00';
			this.dialer_id = null;
			this.script_id = null;
			this.talk_minutes = null;
			this.talk_hours = null;
			this.time_address = 0;
			this.editable_time = 0;
			this.time_address_text = 'Не выбран';
			this.time_variants = [];
			this.workdays = 5;
			this.quality = 'local';
			this.time_exceptions = [];
			this.time_exceptions_options = [];
			this.showEditTimeAddress = false;
			this.paid_internship = 0;
			this.show_payment_terms = 0;
			this.zarplata = '0';
			this.showKPI = false;
			this.showDeleteButton = false;
			this.showArchiveModal = false;
			this.showAfterEdit = false;
			this.group_id = 0;
		},
		removeValue(index) {
			this.value.splice(index, 1);
			this.searchUsers = '';
		},
		init() {
			Object.keys(this.statuseses).forEach(item => {
				this.statuses.push({
					id: item,
					group: this.statuseses[item]
				})
			});
			this.archived_groups = this.archived_groupss;
		},

		addTag(newTag) {
			const tag = {
				email: newTag,
				ID: newTag,
			};
			this.options.push(tag);
			this.value.push(tag);
		},

		addExceptionTag(newTag) {
			const tag = {
				email: newTag,
				ID: newTag,
			};
			this.time_exceptions_options.push(tag);
			this.time_exceptions.push(tag);
		},

		messageoff() {
			setTimeout(() => {
				this.message = null;
			}, 3000);
		},
		async selectGroup(value) {
			let loader = this.$loading.show();
			try {
				this.documents = []
				const response = await this.axios.post('/timetracking/users-new', {
					id: value.id,
				})
				const data = response.data.data

				this.workChartId = data.work_chart_id;
				this.new_status = data.name;
				this.value = data.users;
				this.options = data.users;
				this.timeon = data.timeon;
				this.timeoff = data.timeoff;
				this.group_id = data.group_id;
				this.zoom_link = data.zoom_link;
				this.bp_link = data.bp_link;
				this.dialer_id = data.dialer_id;
				this.talk_minutes = data.talk_minutes;
				this.talk_hours = data.talk_hours;
				this.script_id = data.script_id;
				this.quality = data.quality;
				this.activities = data.activities;
				this.payment_terms = data.payment_terms;
				this.time_address = data.time_address;
				this.workdays = data.workdays;
				this.paid_internship = data.paid_internship;
				this.show_payment_terms = data.show_payment_terms;

				this.editable_time = data.editable_time;
				if (this.time_address != -1 || this.time_address != 0)
					this.time_address_text = 'Из аналитики';
				if (this.time_address == -1) this.time_address_text = 'Из U-calls';
				if (this.time_address == 0) this.time_address_text = 'Не выбран';
				this.addNewGroup = false


				// loadDocuments
				this.fetchDocs(value.id)

				loader.hide()
			}
			catch (error) {
				this.$toast.error(error)
				console.error(error)
			}
		},
		addGroup() {
			if (this.$refs.groupsMultiselect) {
				this.$refs.groupsMultiselect.toggle();
			}
			this.addNewGroup = true;
			this.resetState();
		},
		async saveusers() {
			if (!this.new_status.length) return this.$toast.error('Введите название группы');
			if (!this.workChartId) return this.$toast.error('Выберите график работы');
			// save group data
			let loader = this.$loading.show()

			if (this.addNewGroup) {
				try {
					const {data} = await this.axios.post('/timetracking/group/save-new', {
						name: this.new_status,
					})
					if (data.status == 200) {
						const dataPush = {
							id: data.data.id,
							group: data.data.name
						};
						this.statuses.push(dataPush);
						this.activebtn = dataPush;
					}
					else {
						this.$toast.error(`Название "${this.new_status}" не свободно, выберите другое имя для группы`);
					}
				}
				catch (error) {
					this.$onError({error})
				}
			}

			try {
				await this.axios.post('/work-chart/group/add', {
					group_id: this.activebtn.id,
					work_chart_id: this.workChartId
				})
			}
			catch (error) {
				this.$onError({error})
			}

			try {
				await this.axios.post('/timetracking/users/group/save-new', {
					group_id: this.activebtn.id,
					users: this.value,
					group_info: {
						work_start: this.timeon,
						work_end: this.timeoff,
						name: this.new_status,
						zoom_link: this.zoom_link,
						workdays: this.workdays,
						payment_terms: this.payment_terms || null,
						editable_time: this.editable_time,
						paid_internship: this.paid_internship,
						quality: this.quality,
						show_payment_terms: this.show_payment_terms,
						bp_link: this.bp_link,
					},
					script_id: this.script_id,
					dialer_id: this.dialer_id,
					talk_hours: this.talk_hours,
					talk_minutes: this.talk_minutes,
				})
				this.$toast.info('Успешно сохранено');
				this.messageoff()
			}
			catch (error) {
				this.$onError({error})
			}

			loader.hide()
		},

		deleted() {
			if (confirm('Вы уверены что хотите удалить группу?')) {
				this.axios
					.post('/timetracking/group/delete-new', {
						group: this.activebtn.id,
					})
					.then(() => {
						this.$toast.info('Удалена');
					});

				const ind = this.statuses.findIndex(item => item.id === this.activebtn.id);
				this.statuses.splice(ind, 1);
				this.activebtn = null;
			}
		},
		showAlert() {
			if (confirm('Вы уверены что хотите удалить всех пользователей?')) {
				this.value = [];
			}
		},

		editTimeAddress() {
			this.showEditTimeAddress = true;

			this.axios
				.post('/timetracking/settings/get_time_addresses', {
					group_id: this.activebtn.id,
				})
				.then((response) => {
					this.time_variants = response.data.time_variants;
					this.time_exceptions_options = response.data.time_exceptions_options;
					this.time_exceptions = response.data.time_exceptions;
				})
				.catch((error) => {
					alert(error);
				});
		},

		restoreGroup() {
			if (!confirm('Вы уверены что хотите восстановить группу?')) {
				return '';
			}

			let loader = this.$loading.show();
			this.axios
				.post('/timetracking/groups/restore-new', {
					id: this.restore_group.id,
				})
				.then(() => {
					this.$toast.success('Восстановлен!');
					const valueSelect = {
						id: this.restore_group.id,
						group: this.restore_group.name
					};
					this.selectGroup(valueSelect);
					this.activebtn = valueSelect;
					this.restore_group = null;
					this.showArchiveModal = false;
					loader.hide();
				})
				.catch((error) => {
					loader.hide();
					this.$toast.error('Ошибка!');
					alert(error);
				});
		},

		saveTimeAddress() {
			this.axios.post('/timetracking/settings/save_time_addresses', {
				group_id: this.activebtn,
				time_address: this.time_address,
				time_exceptions: this.time_exceptions,
			}).then(() => {
				if (this.time_address != -1 || this.time_address != 0) this.time_address_text = 'Из аналитики';
				if (this.time_address == -1) this.time_address_text = 'Из U-calls';
				if (this.time_address == 0) this.time_address_text = 'Не выбран';
				this.time_address_text = this.time_variants !== undefined ? this.time_variants[this.time_address] : 'Не выбран';
			}).catch(error => alert(error));
			this.showEditTimeAddress = false;
		},
		getShiftDays,

		async fetchDocs(groupId){
			try {
				const {data} = await this.axios.get(`/signature/groups/${groupId}/files`)
				const docs = data.data || []
				this.documents = docs.map(doc => ({
					id: doc.id,
					name: doc.original_name || 'Без названия',
					file: doc.url,
				}))
			}
			catch (error) {
				window.onerror && window.onerror(error)
				alert(error)
			}
		},
		uploadDoc(files){
			const file = files ? files[0] : null
			if(!file) return

			this.documentForm.upload = file
		},
		onEditDoc(doc){
			this.documentForm = {
				...JSON.parse(JSON.stringify(doc)),
				upload: null,
			}
			this.uploadProgress = 0
			this.docEditDialog = true
		},
		onSaveDoc(doc){
			this.docEditDialog = true
			if(doc.id > 0){
				this.updateDoc()
			}
			else{
				this.createDoc()
			}
		},
		onAddDoc(){
			this.documentForm = {
				id: --this.docId,
				name: '',
				file: '',
				upload: null,
			}
			this.uploadProgress = 0
			this.docEditDialog = true
		},
		async createDoc(){
			try {
				const onUploadProgress = event => {
					this.uploadProgress = Math.round((event.loaded * 100) / event.total);
				}
				const formData = new FormData()
				formData.append('file', this.documentForm.upload)
				formData.append('original_name', this.documentForm.name)
				await this.axios.post(`/signature/groups/${this.activebtn.id}/files`, formData, {
					headers: {
						'Content-Type': 'multipart/form-data'
					},
					onUploadProgress,
				})
				this.fetchDocs(this.activebtn.id)
				this.docEditDialog = false
			}
			catch (error) {
				console.error(error)
			}
		},
		async updateDoc(){
			try {
				await this.axios.put(`/signature/files/${this.documentForm.id}`, {
					original_name: this.documentForm.name
				})
				this.fetchDocs()
				this.docEditDialog = false
			}
			catch (error) {
				this.$toast.error(error)
				console.error(error)
			}
		},
		async onDeleteDoc(doc){
			if(!confirm('Вы действительно хотите удалить документ?')) return

			const index = this.documents.findIndex(d => d.id === doc.id)
			if(~index) this.documents.splice(index, 1)
			if(doc.id <= 0) return
			try {
				await this.axios.delete(`/signature/files/${doc.id}`)
			}
			catch (error) {
				console.error(error)
			}
		},
	},
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
	.groups-title-new {
		color: rgb(0 128 0);
		display: inline-block;
		padding: 5px 20px;
		border-radius: 6px;
		background-color: rgba(0, 128, 0, 0.2)
	}

	.groups-card {
		border: 1px solid #ddd;

		.card-header, .card-footer {
			padding: 5px 20px;
		}

		.card-body {
			padding: 10px 20px;
		}

		&.staff-list {
			.card-header {
				padding: 10px 20px;
				background-color: #fff;
				border-bottom: 1px solid #ddd;
				display: flex;
				align-items: center;

				span {
					white-space: nowrap;
					margin-left: 20px;
				}
			}

			.card-body {
				padding: 10px 0;
				max-height: 500px;
				overflow: auto;

				.employee {
					display: flex;
					align-items: center;
					justify-content: space-between;
					padding: 3px 20px;
					line-height: 1.2;

					&:hover {
						background-color: #f2f2f2;
					}

					span {
						font-size: 16px;
						margin-right: 20px;
					}

					.btn-icon {
						width: 25px;
						height: 25px;
						min-width: 25px;
						font-size: 12px;
					}
				}
			}
		}

		textarea.form-control {
			padding: 5px 20px !important;
			min-height: 165px !important;
		}
	}

	.ant-tabs {
		overflow: visible;
	}

	.listprof {
		display: flex;
		flex-wrap: wrap;
		margin-top: 20px;
	}

	.profitem {
		margin-right: 10px;
		margin-bottom: 5px;
	}

	.add-grade {
		display: flex;
		max-width: 500px;
	}

	.dialerlist {
		display: flex;
		align-items: center;
		margin: 0 0 20px 0;

		.fl {
			flex: 1;
			display: flex;
			align-items: center;
		}
	}

	.group-select {
		border-radius: 0;
		max-width: 100%;
	}

	p.choose {
		line-height: 31px;
		margin-right: 15px;
	}

	span.before {
		padding: 0 10px;
	}

	.multiselect__tags {
		border-radius: 0 !important;
	}

	.multiselect__tag {
		display: block !important;
		max-width: max-content !important;
	}

	.blu .multiselect__tag {
		background: #017cff !important;
	}

	@media (min-width: 1000px) {
		.groups .multiselect__tags-wrap {
			flex-wrap: wrap;
			display: flex !important;
		}
		.groups .multiselect__tag {
			flex: 0 0 49%;
			/* margin-left: 1% !important; */
			margin-right: 1% !important;
			max-width: 49% !important;
		}
	}

	@media (min-width: 1300px) {
		.groups .multiselect__tag {
			flex: 0 0 32%;
			/* margin-left: 1% !important; */
			margin-right: 1% !important;
			max-width: 32% !important;
		}
	}

	@media (min-width: 1700px) {
		.groups .multiselect__tag {
			flex: 0 0 24%;
			/* margin-left: 1% !important; */
			margin-right: 1% !important;
			max-width: 24% !important;
		}
	}

	.custom-table-permissions {
		.groups .multiselect__tag {
			flex: 0 0 auto !important;
			max-width: 100% !important;
			margin-right: 5px !important
		}
	}

	.scscsc {
		margin-left: 15px;
	}

	.sssz button {
		margin-top: 1px;
	}

	.add-grade input {
		border-radius: 0;
	}
</style>
<style lang="scss">
.CompanyGroups{
	// &-label{}
	&-doc{
		display: flex;
		align-items: center;
		gap: 10px;
		&:hover{
			background-color: #eef;
		}
	}
	&-docIcon{
		flex: 0 0 32px;
		font-size: 24px;
	}
	&-docName{
		flex: 1;
	}
	&-docControl{
		display: flex;
		align-items: center;
		gap: 10px;
	}

	&-modal{
		.form-control{
			height: 35px !important;
			padding: 0 20px !important;
			border: 1px solid #e8e8e8;

			font-size: 14px;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;

			background-color: #F7FAFC !important;
			border-radius: 6px !important;
		}
	}

	.progress-bar-striped{
		background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
		background-size: 1rem 1rem;
	}
	.progress-bar-animated{
		animation: progress-bar-stripes 1s linear infinite;
	}
}
</style>
