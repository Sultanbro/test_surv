<template>
	<div
		v-if="groups"
		class="quality quality-page"
	>
		<div class="row">
			<div
				class="col-3"
				v-if="individual_request"
			>
				<select
					class="form-control"
					v-model="currentGroup"
					@change="fetchData('selected_group')"
				>
					<option
						v-for="group in groups"
						:value="group.id"
						:key="group.id"
					>
						{{ group.name }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					class="form-control"
					v-model="monthInfo.currentMonth"
					@change="fetchData"
				>
					<option
						v-for="month in $moment.months()"
						:value="month"
						:key="month"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					class="form-control"
					v-model="currentYear"
					@change="fetchData"
				>
					<option
						v-for="year in years"
						:value="year"
						:key="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-3 d-flex align-items-start">
				<JobtronButton
					small
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</JobtronButton>
			</div>
			<div
				v-if="hasSettingsPermisstion"
				class="col-2 text-right"
			>
				<JobtronButton
					small
					class="ml-a"
					@click="showSettings = true"
				>
					<i class="fa fa-cogs mr-2" />
					Настройки
				</JobtronButton>
			</div>
		</div>



		<!--    <h4 class="d-flex align-items-center">-->
		<!--      <div class="mr-2 mt-2">{{ groupName }}</div>-->
		<!--    </h4>-->
		<div v-if="this.hasPermission">
			<b-tabs
				type="card"
				class="mt-4"
				:default-active-key="3"
			>
				<b-tab
					title="Оценка диалогов"
					:key="1"
					card
				>
					<b-tabs
						type="card"
						v-if="dataLoaded"
						class="mt-4 overflow-hidden"
					>
						<b-tab
							title="Неделя"
							:key="1"
							card
						>
							<div class="table-responsive table-container mt-4">
								<table class="table table-bordered custom-table-quality">
									<thead>
										<tr>
											<th class="b-table-sticky-column text-left t-name wd">
												<div>Сотрудник</div>
											</th>
											<th
												v-for="(field, key) in fields"
												:key="key"
												:class="field.klass"
											>
												<div>{{ field.name }}</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in items"
											:key="index"
										>
											<td class="b-table-sticky-column text-left t-name wd">
												<div>
													{{ item.name }}
													<template v-if="item.groupName">
														<b-badge
															v-if="item.groupName == 'Просрочники'"
															variant="success"
														>
															{{ item.groupName }}
														</b-badge>
														<b-badge
															variant="primary"
															v-else
														>
															{{ item.groupName }}
														</b-badge>
													</template>
												</div>
											</td>
											<td
												:class="field.klass"
												v-for="(field, key) in fields"
												:key="key"
											>
												<input
													v-if="field.type == 'day' && can_add_records"
													type="number"
													:title="field.key + ' :' + item.name"
													@change="updateWeekValue(item, field.key)"
													v-model="item.weeks[field.key]"
												>
												<div v-else>
													<div v-if="item.weeks[field.key] != 0">
														{{ item.weeks[field.key] }}
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</b-tab>
						<b-tab
							title="Месяц"
							:key="2"
							card
						>
							<div class="table-responsive table-container mt-4">
								<table class="table table-bordered custom-table-quality">
									<thead>
										<tr>
											<th class="b-table-sticky-column text-left t-name wd">
												<div>Сотрудник</div>
											</th>
											<th
												v-for="(field, key) in monthFields"
												:key="key"
												:class="field.klass"
											>
												<div>{{ field.name }}</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(item, index) in items"
											:key="index"
										>
											<td class="b-table-sticky-column text-left t-name wd">
												<div>
													{{ item.name }}
													<template v-if="item.groupName">
														<b-badge
															variant="success"
															v-if="item.groupName == 'Просрочники'"
														>
															{{ item.groupName }}
														</b-badge>
														<b-badge
															variant="primary"
															v-else
														>
															{{ item.groupName }}
														</b-badge>
													</template>
												</div>
											</td>

											<td
												v-for="(field, key) in monthFields"
												:key="key"
												:class="field.klass"
											>
												<div>{{ item.months[field.key] }}</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</b-tab>
						<b-tab
							v-if="can_add_records"
							title="Оценка переговоров"
							key="3"
							card
						>
							<div class="row mt-4">
								<div class="col-6 col-md-3">
									<select
										class="form-control"
										v-model="filters.currentEmployee"
										@change="filterRecords"
									>
										<option :value="0">
											Выберите сотрудника
										</option>
										<option
											v-for="item in items"
											:value="item.id"
											:key="item.id"
										>
											{{ item.name }}
										</option>
									</select>
								</div>
								<div class="col-2 col-md-1 d-flex align-items-center">
									<select
										class="form-control"
										v-model="currentDay"
										@change="fetchData"
									>
										<option value="0">
											Все дни
										</option>
										<option
											v-for="day in this.monthInfo.daysInMonth"
											:value="day"
											:key="day"
										>
											{{ day }}
										</option>
									</select>
								</div>
								<div class="col-4 col-md-8 d-flex align-items-center">
									<b-button
										variant="primary"
										@click="addRecord"
										class="mr-1"
									>
										<i class="fa fa-plus" /> Добавить запись
									</b-button>
									<b-button
										variant="success"
										@click="exportData()"
										class="mr-1"
									>
										<i class="far fa-file-excel" /> 20
									</b-button>
									<b-button
										variant="success"
										@click="exportAll()"
									>
										<i class="far fa-file-excel" /> Экспорт
									</b-button>
								</div>
								<div class="col-12 col-md-12 d-flex mt-2 mb-2">
									<p class="mb-0">
										Найдено записей: <b class="bluish">{{ records.total }}</b>
									</p>
									<p
										class="ml-3 mb-0"
										v-if="records_unique != 0"
									>
										Кол-во сотрудников:
										<b class="bluish">{{ records_unique }}</b>
									</p>
									<p
										class="ml-3 mb-0"
										v-if="avgMonth != 0"
									>
										Среднее за месяц: <b class="bluish">{{ avgMonth }}</b>
									</p>
									<p
										class="ml-3 mb-0"
										v-if="avgDay != 0"
									>
										Среднее за день: <b class="bluish">{{ avgDay }}</b>
									</p>
								</div>
							</div>

							<div class="table-responsive">
								<table class="table b-table table-sm table-bordered records-table">
									<tr>
										<th class="b-table-sticky-column text-left t-name wd">
											<div>Сотрудник</div>
										</th>
										<th
											v-for="(field, key) in recordFields"
											:key="key"
											:class="field.klass"
										>
											<div>{{ field.name }}</div>
										</th>
										<th class="actions" />
										<th class="actions" />
									</tr>

									<!-- RECORDS -->
									<tr
										v-for="(record, index) in records.data"
										:key="index"
										:class="{
											selected: record.editable,
											changed: record.changed,
										}"
									>
										<td class="b-table-sticky-column text-left t-name wd">
											<div @click="editMode(record)">
												{{ record["name"] }}
											</div>
										</td>

										<template v-if="currentGroup == 42">
											<td
												class="text-left segment-width"
												v-if="record.editable"
											>
												<div>
													<select
														v-model="record.segment_id"
														class="form-control text-center sg"
														@change="statusChanged(record)"
													>
														<option
															:value="index"
															v-for="(segm, index) in segment"
															:key="index"
														>
															{{ segm }}
														</option>
													</select>
												</div>
											</td>
											<td
												v-else
												class="text-center segment-width"
												@click="editMode(record)"
											>
												<div>
													{{ segment[record.segment_id] }}
												</div>
											</td>
										</template>

										<td
											v-if="record.editable"
											class="text-center phoner"
										>
											<div>
												<input
													type="text"
													v-model="record.phone"
													class="form-control text-center"
													@focus="$event.target.select()"
													@change="statusChanged(record)"
												>
											</div>
										</td>
										<td
											v-else
											class="text-center phoner"
											@click="editMode(record)"
										>
											<div>
												{{ record.phone }}
											</div>
										</td>

										<template v-if="currentGroup == 42">
											<td
												class="text-center"
												v-if="record.editable"
											>
												<div>
													<input
														type="text"
														v-model="record.dayOfDelay"
														class="form-control text-center"
														@focus="$event.target.select()"
														@change="statusChanged(record)"
													>
												</div>
											</td>
											<td
												v-else
												class="text-center"
												@click="editMode(record)"
											>
												<div>{{ record.dayOfDelay }}</div>
											</td>
										</template>

										<td
											class="text-center"
											v-if="record.editable"
										>
											<div>
												<input
													type="text"
													v-model="record.interlocutor"
													class="form-control text-center"
													@focus="$event.target.select()"
													@change="statusChanged(record)"
												>
											</div>
										</td>
										<td
											class="text-center"
											v-else
											@click="editMode(record)"
										>
											<div>
												{{ record.interlocutor }}
											</div>
										</td>

										<td
											class="text-center"
											v-if="record.editable"
										>
											<div>
												<input
													type="date"
													v-model="record.date"
													class="form-control text-center"
													placeholder="dd-mm-yyyy"
													min="1997-01-01"
													max="2030-12-31"
													@change="statusChanged(record)"
												>
											</div>
										</td>
										<td
											class="text-center"
											v-else
											@click="editMode(record)"
										>
											<div>{{ record.date }}</div>
										</td>

										<template v-for="(param, pk) in params">
											<td
												:key="pk"
												class="text-center params"
												v-if="record.editable"
											>
												<div>
													<input
														type="number"
														v-model="record['param' + pk]"
														class="form-control text-center"
														@change="changeStat(record)"
														@focus="$event.target.select()"
													>
												</div>
											</td>
											<td
												:key="pk + 'a'"
												class="text-center params"
												v-else
												@click="editMode(record)"
											>
												<div>{{ record["param" + pk] }}</div>
											</td>
										</template>

										<td class="text-center">
											<div>{{ record.total }}</div>
										</td>

										<td
											class="text-left"
											v-if="record.editable"
										>
											<div>
												<textarea
													type="text"
													v-model="record.comments"
													class="form-control"
													@focus="$event.target.select()"
													@change="statusChanged(record)"
												/>
											</div>
										</td>
										<td
											class="text-left"
											v-else
											@click="editMode(record)"
										>
											<div class="pl2">
												{{ record.comments }}
											</div>
										</td>

										<td
											class="actions"
											@click="editMode(record)"
										>
											<div>
												<b-button
													v-if="record.editable"
													variant="success"
													size="sm"
													@click="saveRecord(record)"
												>
													<i class="fa fa-save" />
												</b-button>
											</div>
										</td>
										<td
											class="actions"
											@click="editMode(record)"
										>
											<div>
												<b-button
													variant="danger"
													size="sm"
													@click="deleteRecordModal(record, index)"
												>
													<i class="fa fa-trash" />
												</b-button>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<div>
								<!-- <pagination
									:data="records"
									@pagination-change-page="getResults"
									:limit="3"
								></pagination> -->

								<b-pagination
									@change="getResults"
									:limit="3"
									:total-rows="records.total"
								/>
							</div>
						</b-tab>
					</b-tabs>
				</b-tab>
				<b-tab
					title="Прогресс по курсам"
					:key="2"
					card
				>
					<CourseResults
						:month-info="monthInfo"
						:current-group="currentGroup"
					/>
				</b-tab>

				<!-- Статус: скрыто. Компонент: components/tables/TableQuality.vue. Дата скрытия: 27.02.2023 14:46 -->
				<b-tab
					title="Чек Лист"
					:key="3"
					type="card"
					card
					:active="check == 3"
					v-if="false"
				>
					<b-tabs
						type="card"
						class="mt-4"
					>
						<b-tab
							title="Неделя"
							:key="1"
						>
							<div class="table-container table-responsive">
								<table class="table table-bordered whitespace-no-wrap mt-4">
									<thead>
										<tr>
											<th class="b-table-sticky-column text-left t-name wd">
												<div>Сотрудник</div>
											</th>
											<th
												v-for="(field, key) in checklist_fields"
												:key="key"
											>
												<div>{{ field.name }}</div>
											</th>
										</tr>
									</thead>
									<tbody>
										<template v-for="( check_r,index ) in check_result">
											<tr :key="index">
												<th class="b-table-sticky-column text-left t-name wd">
													{{ check_r.last_name }} {{ check_r.name }}
												</th>
												<template v-for="(field, key) in fields">
													<td
														:class="field.klass"
														:key="key"
													>
														<template v-if="currentGroup == check_r.gr_id">
															<div v-if="field.name == 'Итог' ">
																{{ check_r.total_day }}
															</div>

															<template v-for="(checked_day,index) in check_r.day">
																<template v-if="index == field.name">
																	<div
																		:key="index"
																		@click="showSidebar(check_r.user_id, index)"
																	>
																		{{ checked_day }}
																	</div>
																</template>
															</template>

															<template v-if="field.name === 'Ср. 1'">
																{{ check_r.average[1] }}
															</template>

															<template v-if="field.name === 'Ср. 2'">
																{{ check_r.average[2] }}
															</template>

															<template v-if="field.name === 'Ср. 3'">
																{{ check_r.average[3] }}
															</template>

															<template v-if="field.name === 'Ср. 4'">
																{{ check_r.average[4] }}
															</template>

															<template v-if="field.name === 'Ср. 5'">
																{{ check_r.average[5] }}
															</template>
														</template>
													</td>
												</template>
											</tr>
										</template>
									</tbody>
								</table>
							</div>
						</b-tab>
						<b-tab
							title="Месяц"
							:key="2"
						>
							<div class="table-container table-responsive">
								<table class="table table-bordered whitespace-no-wrap mt-4">
									<thead>
										<tr>
											<th class="b-table-sticky-column text-left t-name wd">
												<div>Сотрудник</div>
											</th>
											<template v-for="(field, key) in monthFields">
												<th
													:key="key"
													:class="field.klass"
												>
													<div>{{ field.name }}</div>
												</th>
											</template>
										</tr>
									</thead>
									<tbody>
										<template v-for="( check_r,index ) in check_result">
											<tr :key="index">
												<th class="b-table-sticky-column text-left t-name wd">
													{{ check_r.name }}
												</th>
												<template v-for="(field, key) in monthFields">
													<td
														:class="field.klass"
														:key="key"
													>
														<template v-if="currentGroup == check_r.gr_id">
															<div v-if="field.name == 'Итог' ">
																{{ check_r.total_month }}
															</div>
															<template v-for="(checked_m,index) in check_r.month">
																<template v-if="index == field.key">
																	{{ checked_m }}
																</template>
															</template>
														</template>
													</td>
												</template>
											</tr>
										</template>
									</tbody>
								</table>
							</div>
						</b-tab>
					</b-tabs>
				</b-tab>
			</b-tabs>
		</div>
		<div v-else>
			<p>У вас нет доступа к этой группе</p>
		</div>

		<b-modal
			id="delete-modal"
			hide-footer
		>
			<template #modal-title>
				Подтвердите удаление
			</template>
			<div class="">
				<div class="row">
					<div class="col-md-12">
						<div>Вы собираетесь удалить следующую запись</div>
						<div>{{ newRecord }}</div>
					</div>
				</div>
				<div class="d-flex">
					<b-button
						class="mt-3 mr-1"
						variant="danger"
						block
						@click="deleteRecord"
					>
						Удалить
					</b-button>
					<b-button
						variant="primary"
						class="mt-3 ml-1"
						block
						@click="$bvModal.hide('delete-modal')"
					>
						Отмена
					</b-button>
				</div>
			</div>
		</b-modal>



		<b-modal
			v-model="showSettings"
			title="Настройки"
			:width="400"
			hide-footer
		>
			<div class="row">
				<div class="col-12 d-flex mb-3">
					<div class="fl">
						Источник оценок
						<i
							class="fa fa-info-circle ml-2"
							v-b-popover.hover.right.html="'Заполнять оценки диалогов и критерии на странице <b>Контроль качества</b>, либо подтягивать их по крону с cp.callibro.org'"
							title="Оценки контроля качества"
						/>
					</div>
					<div class="fl d-flex ml-3">
						<b-form-radio
							v-model="can_add_records"
							name="some-radios"
							:value="false"
							class="mr-3"
						>
							C U-calls
						</b-form-radio>
						<b-form-radio
							v-model="can_add_records"
							name="some-radios"
							:value="true"
						>
							Ручная оценка
						</b-form-radio>
					</div>
				</div>

				<div
					class="col-12"
					v-if="!can_add_records"
				>
					<div class="bg mb-2">
						<div class="fl">
							ID диалера
							<i
								class="fa fa-info-circle ml-2"
								v-b-popover.hover.right.html="'Нужен, чтобы <b>подтягивать часы</b> или <b>оценки диалогов</b> для контроля качества.<br>С сервиса cp.callibro.org'"
								title="Диалер в U-Calls"
							/>
						</div>
						<div class="fl d-flex mt-1 gap-3">
							<input
								type="text"
								v-model="dialer_id"
								placeholder="ID"
								class="form-control form-control-sm"
							>
							<input
								type="number"
								v-model="script_id"
								placeholder="ID скрипта"
								class="form-control form-control-sm"
							>
						</div>
					</div>
				</div>

				<div
					class="col-12"
					v-if="can_add_records"
				>
					<div class="row">
						<div
							class="col-12 d-flex mb-1"
							v-for="crit in params"
							:key="crit.name"
						>
							<b-form-checkbox
								v-model="crit.active"
								:value="1"
								:unchecked-value="0"
							/>
							<input
								type="text"
								v-model="crit.name"
								class="form-control form-control-sm"
							>
						</div>

						<div class="col-12">
							<button
								class="btn btn-sm btn-default rounded"
								style="font-size:12px;"
								@click="addParam()"
							>
								Добавить критерий
							</button>
						</div>
					</div>
				</div>

				<div class="col-12 mt-3">
					<button
						class="btn btn-sm btn-primary rounded"
						@click="saveSettings"
					>
						Сохранить
					</button>
				</div>
			</div>
		</b-modal>
		<Sidebar
			title="Индивидуальный чек лист"
			:open="showChecklist"
			@close="toggle()"
			width="70%"
		>
			<div
				class="col-10 p-0 mt-2"
				v-for="(val,ind) in checklists"
				:key="ind"
			>
				<div class="mr-5">
					<b-form-checkbox
						v-model="val.checked"
						size="sm"
					>
						<span style="cursor: pointer">{{ val.task.task }}</span>
					</b-form-checkbox>
				</div>

				<div style="position: absolute;right: 0px;top: 0px">
					<a
						v-if="val.url"
						:href="val.url"
						target="_blank"
					>{{ val.url }}</a>
					<p v-else>
						нет ссылки
					</p>
				</div>
			</div>

			<div class="col-md-12 mt-3">
				<div class="col-md-6 p-0">
					<button
						@click.prevent="saveChecklist"
						title="Сохранить"
						class="btn btn-primary"
					>
						Сохранить
					</button>
				</div>
			</div>
		</Sidebar>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import CourseResults from '@/pages/CourseResults' // результаты по курсам
import { useYearOptions } from '../../composables/yearOptions'
import JobtronButton from '@ui/Button'
// import Template from "../../../../public/static/partner/templates/template.html";
export default {
	name: 'TableQuality',
	components: {
		Sidebar,
		CourseResults,
		JobtronButton,
	},
	props: {
		groups: Array,
		individual_type:{
			default:null
		},
		individual_type_id:{
			default:null
		},
		active_group: String,
		check: String,
		user: {
			type: Object,
			default: () => ({})
		}
	},
	data() {
		const now = new Date()
		return {
			auth_user: this.user,
			currentGroup: this.active_group,
			showChecklist: false,
			checklists:{},
			fields: [],
			checklist_fields: [],
			monthFields: [],
			recordFields: [],
			filters: {
				currentEmployee: 0,
				fromDate: this.$moment().format('YYYY-MM-DD'),
				toDate: this.$moment().format('YYYY-MM-DD'),
			},
			can_add_records: false, // like kaspi
			script_id: null,
			dialer_id: null,
			fieldsNumber: 15,
			pageNumber: 1,
			currentDay: now.getDate(),
			avgDay: 0,
			avgMonth: 0,
			showCritWindow: false,
			showSettings: false,
			newRecord: {
				id: 0,
				employee_id: 0,
				name: '',
				segment: '1-5',
				segment_id: 1,
				interlocutor: 'Клиент',
				phone: '',
				dayOfDelay: this.$moment().format('YYYY-MM-DD'),
				date: this.$moment().format('YYYY-MM-DD'),
				param1: 0,
				param2: 0,
				param3: 100,
				param4: 0,
				param5: 0,
				comments: '',
				changed: true,
			},
			records_unique: 0,
			records: {
				data: [],
			},
			deletingElementIndex: 0,
			groupName: 'Контроль качества',
			monthInfo: {},
			user_ids: {},
			currentYear: now.getFullYear(),
			hasPermission: false,
			dataLoaded: true,
			segment: {
				1: '1-5',
				2: 'Нап',
				3: '3160',
				4: '6190',
				5: 'ОВД',
				6: '1-5 RED',
				7: 'Нап RED',
				10: 'ОВД RED',
				11: '6_30 RED',
				12: '6_30',
			},
			loader: null,
			fill:{ gradient: ['#1890ff', '#28a745'] },
			items: [],
			params: [],
			pagination: {
				current_page: 1,
				first_page_url: '',
				from: 1,
				last_page: 1,
				last_page_url: '',
				next_page_url: '',
				per_page: 100,
				prev_page_url: null,
				to: 100,
				total: 4866,
			},
			individual_request: true,

			viewStaticButton:{
				weekCheck: true,
				montheCheck: false
			},
			active: 1,
			selected_active: 1,
			flagGroup: 'index',
			checklist_tab: false,
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		hasSettingsPermisstion(){
			return this.auth_user.is_admin === 1;
		}
	},
	watch: {
		groups(){
			this.init()
		}
	},
	created() {
		if(this.groups){
			this.init()
		}
	},
	methods: {
		init(){
			this.$nextTick(() => {
				this.currentGroup = this.active_group
				this.auth_user = this.user
				this.fetchData()
			})
		},
		saveChecklist(){
			this.axios.post('/checklist/save-checklist',{
				checklists: this.checklists
			}).then(() => {
				this.toggle();
				this.$toast.success('Сохранено');
			})
		},
		showSidebar(user_id, day){
			this.toggle();
			var date = this.currentYear + '-' + this.monthInfo.month.padStart(2, '0') + '-' + day.padStart(2, '0');

			this.axios.post('/checklist/get-checklist-by-user',{
				user_id:user_id,
				created_date: date
			}).then(response => {
				this.checklists = response.data;
			})
		},
		toggle(){
			this.showChecklist = !this.showChecklist;
		},
		viewStaticCheck(type){
			if (type == 'w'){
				this.viewStaticButton.weekCheck = true
				this.viewStaticButton.montheCheck = false
			}
			else if(type == 'm'){
				this.viewStaticButton.weekCheck = false
				this.viewStaticButton.montheCheck = true
			}
		},

		watchChanges(values, oldValues) {
			const index = values.findIndex(function (v, i) {
				return v !== oldValues[i]
			});
			// console.log(this.records.data[index]);
			this.records.data[index].changed = true
		},

		getResults(page = 1) {
			this.fetchItems('/timetracking/quality-control/records?page=' + page)
		},

		fetchData(flag = null) {
			if (flag == 'selected_group'){
				this.flagGroup = 'selected_group'
			}

			let loader = this.$loading.show();
			this.setDates();
			this.fetchItems();
			loader.hide();
		},

		normalizeItems() {
			if (this.items.length > 0) {
				this.newRecord.employee_id = this.items[0].id;
				this.newRecord.name = this.items[0].name;
			}

			this.records.data.forEach(record => {
				record.segment = this.segment[record.segment_id];
				record.changed = false;

				this.params.forEach((param, key) => {
					record['param' + key] = 0;
				});

				record.param_values.forEach(item => {
					this.params.forEach((param, key) => {
						if (item.param_id == param.id) {
							record['param' + key] = item.value;
						}
					});
				});
			});
		},

		addParam() {
			this.params.push({
				name: 'Новый критерий',
				id: -1,
				active: 0,
			});
		},

		saveSettings() {
			let loader = this.$loading.show();

			// if (this.individual_type != null  &&  this.individual_type_id != null) {
			//
			// }

			this.axios
				.post('/timetracking/quality-control/crits/save', {
					crits: this.params,
					can_add_records: this.can_add_records,
					script_id: this.script_id,
					dialer_id: this.dialer_id,
					group_id: this.currentGroup,
				})
				.then(() => {
					this.$toast.success('Сохранено!!');
					this.showSettings = false;
					this.fetchData();
					loader.hide();
				})
				.catch(function (e) {
					loader.hide();
					alert(e);
				});
		},

		fetchItems($url = '/timetracking/quality-control/records') {
			let loader = this.$loading.show();

			// console.log(this.individual_type_id,'this.individual_type_id')
			// console.log(this.individual_type,'this.individual_type')
			// console.log(this.flagGroup,'this.flagGroup')
			// console.log(this.currentGroup,'this.currentGroup')

			if (this.individual_type_id != null){
				if (this.flagGroup == 'index'){
					if (this.individual_type == 2 || this.individual_type == 3){
						this.currentGroup = this.active_group;
					}
					// this.currentGroup = this.individual_type_id
				}
			}

			this.axios
				.post($url, {
					day: this.currentDay,
					month: this.monthInfo.month,
					year: this.currentYear,
					employee_id: this.filters.currentEmployee,
					group_id: this.currentGroup,
					individual_type:this.individual_type,
					individual_type_id:this.individual_type_id,
				})
				.then((response) => {
					this.currentGroup = response.data['individual_current']
					if (response.data.error && response.data.error == 'access') {
						this.hasPermission = false;
						loader.hide();
						return;
					}

					this.check_result = response.data.check_users;

					this.hasPermission = true;
					this.items = response.data.items;
					this.records = response.data.records;
					this.records_unique = response.data.records_unique;
					this.avgDay = response.data.avg_day;
					this.avgMonth = response.data.avg_month;
					this.records = response.data.records;
					this.can_add_records = response.data.can_add_records;
					this.params = response.data.params;
					this.script_id = response.data.script_id;
					this.dialer_id = response.data.dialer_id;

					this.$toast.success('Записи загружены');
					this.normalizeItems();
					this.createUserIdList();
					this.setChecklistWeekTable();
					this.setWeeksTable();
					this.setMonthsTable();

					this.setRecordsTable();
					this.calcTotalWeekField();
					loader.hide();
				});
		},

		chooseEmployee(record) {
			var name = this.items.filter((item) => {
				return record.employee_id == item.id;
			});
			record['name'] = name[0]['name'];
		},

		setDates() {
			this.setYear();
			this.setMonth();
		},

		filterRecords() {
			this.fetchItems();
		},
		setChecklistWeekTable() {
			this.setChecklistWeeksTableFields();
		},
		setWeeksTable() {
			this.setWeeksTableFields();
		},
		setMonthsTable() {
			this.setMonthsTableFields();
		},
		statusChanged(record) {
			record.changed = true;
		},

		createUserIdList() {
			this.items.forEach(item => {
				this.user_ids[item.id] = item.name;
			});
		},

		editRecordModal(record) {
			this.newRecord.id = record.id;
			this.newRecord.name = record.name;
			this.newRecord.interlocutor = record.interlocutor;
			this.newRecord.employee_id = record.employee_id;
			this.newRecord.phone = record.phone;
			this.newRecord.dayOfDelay = record.dayOfDelay;
			this.newRecord.date = record.date;
			this.newRecord.param1 = record.param1;
			this.newRecord.param2 = record.param2;
			this.newRecord.param3 = record.param3;
			this.newRecord.param4 = record.param4;
			this.newRecord.param5 = record.param5;
			this.newRecord.total = record.total;
			this.newRecord.comments = record.comments;
			this.$bvModal.show('bv-modal');
		},

		addRecord() {
			if (this.filters.currentEmployee == 0)
				return this.$toast.info('Выберите сотрудника!');

			if (this.records.data.length != 0) this.records.data[0].editable = false;

			let obj = {
				id: 0,
				employee_id: this.filters.currentEmployee,
				name: this.user_ids[this.filters.currentEmployee],
				segment_id: 1,
				phone: '',
				interlocutor: 'Клиент',
				dayOfDelay: 0,
				date: this.$moment().format('YYYY-MM-DD'),
			};

			let param_values = [];
			this.params.forEach((param, key) => {
				param_values.push({
					param_id: param.id,
					value: 0,
					record_id: 0,
				});
				obj['param' + key] = 0;
			});

			obj['param_values'] = param_values;
			obj['comments'] = '';
			obj['changed'] = true;
			obj['editable'] = true;
			this.records.data.unshift(obj);
		},

		saveRecord(record) {
			let loader = this.$loading.show();

			if (record.phone.length == 0) {
				this.$toast.error('Укажите телефон!!!');
				loader.hide();
				return;
			}

			let obj = {
				id: record.id,
				employee_id: record.employee_id,
				segment_id: record.segment_id,
				phone: record.phone,
				interlocutor: record.interlocutor,
				dayOfDelay: record.dayOfDelay,
				date: record.date,
				param_values: record.param_values,
			};

			// this.params.forEach((param, key) => {
			//     obj['param' + key] = 0;
			// });

			obj['comments'] = record.comments;
			obj['group_id'] = this.currentGroup;

			this.axios
				.post('/timetracking/quality-control/save', obj)
				.then((response) => {

					if (response.data.method == 'save') {
						record.id = response.data.id;
						record.total = response.data.total;
						record.segment = this.segment[record.segment_id];
						record.name = this.user_ids[record.employee_id];
						// this.records.data.shift()
						// this.records.data.unshift(record)
						this.$toast.success('Сохранено');
					}
					if (response.data.method == 'update') {
						this.$toast.success('Изменено');
					}
					record.changed = false;
					this.$bvModal.hide('bv-modal');
					loader.hide();
				})
				.catch(function (e) {
					loader.hide();
					alert(e);
				});
		},

		deleteRecordModal(record, index) {
			this.deletingElementIndex = index;
			this.newRecord.id = record.id;
			this.newRecord.name = record.name;
			this.newRecord.interlocutor = record.interlocutor;
			this.newRecord.employee_id = record.employee_id;
			this.newRecord.phone = record.phone;
			this.newRecord.dayOfDelay = record.dayOfDelay;
			this.newRecord.date = record.date;
			this.newRecord.param1 = record.param1;
			this.newRecord.param2 = record.param2;
			this.newRecord.param3 = record.param3;
			this.newRecord.param4 = record.param4;
			this.newRecord.param5 = record.param5;
			this.newRecord.total = record.total;
			this.newRecord.comments = record.comments;
			this.$bvModal.show('delete-modal');
		},

		deleteRecord() {
			let loader = this.$loading.show();

			this.axios
				.post('/timetracking/quality-control/delete', {
					id: this.newRecord.id,
				})
				.then(() => {
					this.$toast.info('Запись #' + this.newRecord.id + ' удалена');
					this.$bvModal.hide('delete-modal');

					// ES6 Func
					let index = this.records.data.findIndex(
						(x) => x.id === this.newRecord.id
					);
					this.records.data.splice(index, 1);

					this.newRecord.id = 0;
					loader.hide();
				});
		},

		setRecordsTable() {
			this.setRecordsTableFields();
			if (this.records.data.length > 0) this.records.data[0].editable = true;
		},

		editMode(item) {
			this.records.data.forEach(record => {
				record.editable = false;
			});
			item.editable = true;
		},

		setRecordsTableFields() {
			let fieldsArray = [];
			let order = 1;

			if (this.currentGroup == 42) {
				fieldsArray.push({
					key: 'segment',
					name: 'Сегмент',
					type: 'select',
					order: order++,
					klass: ' text-center px-1 segment-width',
				});
			}

			fieldsArray.push({
				key: 'phone',
				name: 'Номер',
				typ: 'text',
				order: order++,
				klass: ' text-center px-1 phoner',
			});

			if (this.currentGroup == 42) {
				fieldsArray.push({
					key: 'dayOfDelay',
					name: 'День просрочки',
					type: 'date',
					order: order++,
					klass: ' text-center px-1 ',
				});
			}

			fieldsArray.push({
				key: 'interlocutor',
				name: 'Собеседник',
				type: 'text',
				order: order++,
				klass: ' text-center px-1 ',
			});

			fieldsArray.push({
				key: 'date',
				name: 'Дата прослушки',
				type: 'date',
				order: order++,
				klass: ' text-center px-1 ',
			});

			this.params.forEach((param, k) => {
				fieldsArray.push({
					key: 'param' + k,
					name: param.name,
					type: 'number',
					order: order++,
					klass: 'text-center px-1 arg number',
				});
			});

			fieldsArray.push({
				key: 'total',
				name: 'Сумма оценки',
				type: 'auto',
				order: order++,
				klass: ' text-center px-1 number',
			});

			fieldsArray.push({
				key: 'comments',
				name: 'Совет',
				type: 'text',
				order: order++,
				klass: ' text-center px-1 comments',
			});

			this.recordFields = fieldsArray;
		},

		setMonthsTableFields() {
			let fieldsArray = [];
			let order = 1;

			fieldsArray.push({
				key: 'total',
				name: 'Итог',
				order: order++,
				klass: ' text-center px-1 t-total',
			});

			fieldsArray.push({
				key: 'quantity',
				name: 'N',
				order: order++,
				klass: ' text-center px-1 t-quantity',
			});

			for (let i = 1; i <= 12; i++) {
				if (i.length == 1) i = '0' + i;

				fieldsArray.push({
					key: i,
					name: this.$moment(this.currentYear + '-' + i + '-01').format('MMMM'),
					order: order++,
					klass: 'text-center px-1 month',
				});
			}

			this.monthFields = fieldsArray;
		},

		calcTotalWeekField() {
			let weekly_totals = [];

			this.fields.forEach(field => {
				let total = 0;
				let count = 0;
				let key = field.key;
				this.items.forEach(item => {
					if (item.weeks[key] !== undefined && Number(item.weeks[key]) > 0) {
						total += Number(item.weeks[key]);
						count++;
					}
				});

				weekly_totals[key] = count > 0 ? Number(total / count).toFixed(0) : 0;
			});

			this.items.unshift({
				id: 0,
				name: '',
				months: {},
				weeks: weekly_totals,
			});
		},

		setChecklistWeeksTableFields(){
			let fieldsArray = [];
			let weekNumber = 1;
			let order = 1;
			// eslint-disable-next-line no-unused-vars
			let day = 1;

			fieldsArray.push({
				key: 'total',
				name: 'Итог',
				order: order++,
				klass: ' text-center px-1 t-total',
			});

			for (let i = 1; i <= this.monthInfo.daysInMonth; i++) {
				let m = this.monthInfo.month.toString();
				let d = i;
				if (d.toString().length == 1) d = '0' + d;
				if (m.length == 1) m = '0' + m;
				//console.log(this.currentYear + '-' + m + '-' + d)

				let date = this.$moment(this.currentYear + '-' + m + '-' + d);
				let dow = date.day();

				fieldsArray.push({
					key: i,
					name: i,
					order: order++,
					klass: 'text-center px-1',
					type: 'day',
				});

				if (dow == 0) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg',
					});
					weekNumber++;
					// eslint-disable-next-line no-unused-vars
					day = 0;
				}

				if (dow != 0 && i == this.monthInfo.daysInMonth) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg',
					});
				}
				// eslint-disable-next-line no-unused-vars
				day++;
			}

			this.checklist_fields = fieldsArray;
		},
		setWeeksTableFields() {
			let fieldsArray = [];
			let weekNumber = 1;
			let order = 1;

			fieldsArray.push({
				key: 'total',
				name: 'Итог',
				order: order++,
				klass: ' text-center px-1 t-total',
			});

			for (let i = 1; i <= this.monthInfo.daysInMonth; i++) {
				let m = this.monthInfo.month.toString();
				let d = i;
				if (d.toString().length == 1) d = '0' + d;
				if (m.length == 1) m = '0' + m;
				//console.log(this.currentYear + '-' + m + '-' + d)

				let date = this.$moment(this.currentYear + '-' + m + '-' + d);
				let dow = date.day();

				fieldsArray.push({
					key: i,
					name: i,
					order: order++,
					klass: 'text-center px-1',
					type: 'day',
				});

				if (dow == 0) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg',
					});
					weekNumber++;
				}

				if (dow != 0 && i == this.monthInfo.daysInMonth) {
					fieldsArray.push({
						key: 'avg' + weekNumber,
						name: 'Ср. ' + weekNumber,
						order: order++,
						klass: 'text-center px-1 averages',
						type: 'avg',
					});
				}
			}

			this.fields = fieldsArray;
		},

		updateWeekValue(item, key) {


			let loader = this.$loading.show();

			this.axios
				.post('/timetracking/quality-control/saveweekly', {
					day: key,
					month: this.monthInfo.month,
					year: this.currentYear,
					total: item.weeks[key],
					user_id: item.id,
					group_id: this.currentGroup,
				})
				.then(() => {

					this.$toast.success('Сохранено');
					loader.hide();
				})
				.catch(function (e) {
					loader.hide();
					alert(e);
				});
		},

		changeStat(record) {
			this.params.forEach((param, k) => {
				if (record['param' + k] < 0) record['param' + k] = 0;
				if (record['param' + k] > 100) record['param' + k] = 100;

				if (record.param_values[k] !== undefined) {
					record.param_values[k].value = Number(record['param' + k]);
				} else {
					record.param_values[k] = {
						id: 0,
						param_id: param.id,
						record_id: record.id,
						value: Number(record['param' + k]),
					};
				}

				// r//ecord['param' + k] = Number(record.param_values[k].value);
				total += Number(record['param' + k]);
			});

			record.changed = true;

			let total = 0;

			this.params.forEach((param, k) => {
				record.param_values[k].value = Number(record['param' + k]);
				total += Number(record['param' + k]);
			});

			if (Number(total) > 100) total = 100;
			record.total = Number(total);
			//if(this.params.length > 0) record.total = Number(Number(total / this.params.length).toFixed(0));
			//record.total = Number(record.param1) + Number(record.param2) + Number(record.param3) + Number(record.param4) + Number(record.param5)
		},

		setYear() {
			this.currentYear = this.currentYear
				? this.currentYear
				: this.$moment().format('YYYY');
		},

		setMonth() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth
				? this.monthInfo.currentMonth
				: this.$moment().format('MMMM');
			this.monthInfo.month = this.$moment(
				this.monthInfo.currentMonth,
				'MMMM'
			).format('M');

			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM');
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(
				currentMonth.startOf('month').toString(),
				currentMonth.endOf('month').toString(),
				[6]
			); //Колличество выходных
			this.monthInfo.daysInMonth = new Date(
				this.currentYear,
				this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				0
			).getDate(); //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays; //Колличество рабочих дней
		},

		toFloat(number) {
			return Number(number).toFixed(0);
		},

		// ucalls or local grades
		change_type() {
			let e = null;

			if (this.can_add_records) {
				e = confirm('Перевести в автоматическую оценку с U-calls?');
			} else {
				e = confirm('Перевести в ручную оценку?');
			}

			if (e) {
				let loader = this.$loading.show();
				this.axios
					.post('/timetracking/quality-control/change-type', {
						type: this.can_add_records ? 'ucalls' : 'local',
						group_id: this.currentGroup,
					})
					.then(() => {
						this.$toast.success('Сохранено!');
						this.fetchData();
						loader.hide();
					})
					.catch(function (e) {
						loader.hide();
						alert(e);
					});
			}
		},

		exportData() {
			var link = '/timetracking/quality-control/export';
			link += '?group_id=' + this.currentGroup;
			link += '&day=' + this.currentDay;
			link += '&month=' + this.monthInfo.month;
			link += '&year=' + this.currentYear;
			window.location.href = link;
		},

		exportAll() {
			var link = '/timetracking/quality-control/exportall';
			link += '?month=' + this.monthInfo.month;
			link += '&group_id=' + this.currentGroup;
			link += '&year=' + this.currentYear;
			window.location.href = link;
		},
	},
};
</script>

<style lang="scss">
	.records-table{
		th, td{
			padding: 5px 10px!important;
			input, textarea{
				padding: 0 !important;
				text-align: center!important;
				border: none!important;
				background-color: transparent !important;
				outline: none!important;
				box-shadow: none !important;
				max-height: 100% !important;
				line-height: 1.1;
			}
			textarea{
				text-align: left !important;
			}
		}
	}
.check_list_mon{
	color: #999999;
	border: 1px solid #e8e8e8
}
.isActiveCheck{
	background-color: white;
	color: rgb(24 144 255);
	border: 1px solid #e8e8e8
}
.quality-page.quality-page .table-container .custom-table-quality th,
.quality-page.quality-page .table-container .custom-table-quality td{
	&.averages{
		background-color: #28a745;
		color: #fff;
	}
	min-width: 35px;
	padding: 0 10px !important;
	vertical-align: middle;
	input{
		min-width: 35px;
		padding: 0 10px !important;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance: textfield;
	}
}
</style>
