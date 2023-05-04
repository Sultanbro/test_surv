<template>
	<div
		v-if="groups"
		class="mt-4"
	>
		<div class="mb-0">
			<div class="row mb-4">
				<div class="col-3">
					<select
						class="form-control"
						v-model="currentGroup"
						@change="fetchData()"
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
				<div class="col-3">
					<select
						class="form-control"
						v-model="dateInfo.currentMonth"
						@change="fetchData()"
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
						v-model="dateInfo.currentYear"
						@change="fetchData()"
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
				<div class="col-1">
					<div
						class="btn btn-primary"
						@click="fetchData()"
					>
						<i class="fa fa-redo-alt" />
					</div>
				</div>
				<div class="col-2" />
			</div>

			<div v-if="hasPermission">
				<div class="row my-4">
					<div class="col-6 d-flex align-items-center">
						<b-form-group class="d-flex ddf mb-0">
							<b-form-radio
								v-model="user_types"
								name="some-radios"
								value="0"
							>
								Действующие
							</b-form-radio>
							<b-form-radio
								v-model="user_types"
								name="some-radios"
								value="2"
							>
								Стажеры
							</b-form-radio>
							<b-form-radio
								v-model="user_types"
								name="some-radios"
								value="1"
							>
								Уволенные
							</b-form-radio>
						</b-form-group>
						<button
							class="btn btn-sm rounded btn-primary ml-2"
							v-if="currentGroup != 23 && user_types == 2 && isBp"
							@click="copy()"
							:style="{'padding': '2px 8px'}"
						>
							<i class="fa fa-clone ddpointer" />
							Начать отметку
						</button>
					</div>
					<div class="col-6 d-flex align-items-center justify-content-end">
						<input
							type="text"
							:ref="'mylink' + currentGroup"
							class="hider"
						>
						<button
							v-if="(currentGroup == 42 && canEdit) || (currentGroup == 88 && canEdit)"
							@click="showExcelImport = !showExcelImport"
							class="btn btn-primary mr-2 btn-sm rounded"
							:style="{'padding': '2px 8px'}"
						>
							<i class="fa fa-upload" />
							Импорт EXCEL
						</button>
						<p class="text-right fz-09 text-black mb-0">
							<span>Сотрудники:</span>
							<b> {{ items.length - 1 }} | {{ total_resources }}</b>
						</p>
					</div>
				</div>

				<div class="table-container">
					<b-table
						responsive
						bordered
						:sticky-header="true"
						class="text-nowrap text-right table-custom-report"
						id="tabelTable"
						:small="true"
						:items="items"
						:fields="fields"
						show-empty
						empty-text="Нет данных"
						:current-page="currentPage"
						:per-page="perPage"
					>
						<template #cell(name)="name">
							<div>
								<span v-if="activeuserpos == 46">
									<a
										:href="'/timetracking/edit-person?id=' + name.item.id"
										target="_blank"
										:title="name.item.id"
									>{{ name.value }}</a>
								</span>
								<span v-else>
									{{ name.value }}
								</span>
								<b-badge
									v-if="name.field.key == 'name' && name.value"
									pill
									variant="success"
								>
									{{ name.item.user_type }}
								</b-badge>


								<span
									v-if="name.field.key == 'name' && name.item.is_trainee"
									class="badgy badge-warning badge-pill"
								>
									Стажер
								</span>
							</div>
						</template>
						<template #cell(total)="total">
							<div class="td-div">
								{{ total.value }}
							</div>
						</template>

						<template #cell()="dataItem">
							<div
								@mouseover="dayInfo(dataItem)"
								@click="detectClick(dataItem)"
								class="td-div"
								:class="{
									'updated': dataItem.value.updated,
									'pointer': dataItem.item._cellVariants
								}"
							>
								<template v-if="dataItem.value.hour">
									<input
										class="cell-input"
										type="number"
										@mouseover="$event.preventDefault()"
										:min="0"
										:max="24"
										:step="0.1"
										:value="dataItem.value.hour"
										:readonly="true"
										@dblclick="readOnlyFix"
										@change="openModal"
									>
								</template>

								<template v-else>
									{{ dataItem.value.hour ? dataItem.value.hour : dataItem.value }}
								</template>

								<div
									class="cell-border"
									:id="`cell-border-${dataItem.item.id}-${dataItem.field.key}`"
									v-if="dataItem.value.tooltip"
								/>
								<b-popover
									:target="`cell-border-${dataItem.item.id}-${dataItem.field.key}`"
									triggers="hover"
									placement="top"
									v-if="dataItem.value.tooltip"
								>
									<template #title>
										Время работы
									</template>
									<div v-html="dataItem.value.tooltip" />
								</b-popover>
							</div>
						</template>
					</b-table>
				</div>

				<div class="d-flex align-items-start justify-content-between mt-3">
					<p class="hovered-text">
						{{ dayInfoText }}
					</p>
					<div class="overflow-auto d-flex">
						<b-pagination
							v-model="currentPage"
							:total-rows="totalRows"
							:per-page="perPage"
							align="fill"
							size="sm"
							class="my-0"
						/>
					</div>
				</div>
			</div>

			<div v-else>
				<p>У вас нет доступа к этой группе</p>
			</div>
		</div>


		<Sidebar
			v-if="showExcelImport"
			title="Импорт EXCEL"
			:open="showExcelImport"
			@close="showExcelImport=false"
			width="75%"
		>
			<GroupExcelImport :group_id="currentGroup" />
		</Sidebar>


		<aside
			class="table-report-sidebar"
			v-if="openSidebar"
		>
			<div class="table-report-content">
				<div class="table-report-header">
					<img
						data-v-8e314866=""
						src="/images/dist/popup-close.svg"
						class="table-report-close"
						alt="Close icon"
						@click.self="openSidebar = false"
					>
					<span class="table-report-title">{{ sidebarTitle }}</span>
				</div>
				<div class="table-report-body">
					<b-tabs
						content-class="mt-3"
						justified
					>
						<b-tab
							title="🕒 История"
							active
						>
							<template v-if="sidebarHistory && sidebarHistory.length > 0">
								<div class="history">
									<div
										v-for="(item,index) in sidebarHistory"
										:key="index"
										class="mb-3"
									>
										<p class="fz12">
											<b class="text-black">Дата:</b> {{ (new
												Date(item.created_at)).addHours(-6).toLocaleString('ru-RU') }}
										</p>
										<p class="fz12">
											<b class="text-black">Автор:</b> {{ item.author }} <br>
										</p>
										<p
											class="fz14 mb-0"
											v-html="item.description"
										/><br>
										<hr>
									</div>
								</div>
							</template>
							<template v-else>
								<p>История изменения отсутствует</p>
							</template>
						</b-tab>

						<template v-if="canEdit">
							<b-tab title="📆 Статус">
								<!-- <div v-html="sidebarContent.history"></div>
					<div v-html="sidebarContent.historyTotal"></div> -->
								<template v-if="!sidebarContent.data.item.is_trainee">
									<div class="temari">
										<div
											v-for="dateType in dateTypes"
											:key="dateType.label"
											:class="[dateType.type == 4 ? 'mt-auto' : 'mb-2']"
										>
											<b-button
												block
												@click="openModalDay(dateType)"
												:class="'button-day_' + dateType.type"
											>
												{{ dateType.label }}
											</b-button>
										</div>
										<div class="mt-auto">
											<b-button
												block
												@click="openFiringModal({
													label: 'Уволить без отработки',
													color: '#d35dd3',
													type: 4
												}, 1)"
												:class="'button-day_7'"
											>
												Уволить без отработки
											</b-button>
										</div>
										<div class="mt-2">
											<b-button
												block
												@click="openFiringModal({
													label: 'Уволить с отработкой',
													color: '#c8a2c8',
													type: 4
												}, 2)"
												:class="'button-day_7'"
											>
												Уволить с отработкой
											</b-button>
										</div>
									</div>
								</template>

								<template v-else>
									<div class="temari">
										<button
											class="btn btn-warning btn-block"
											@click="openModalAbsence({type: 2, label: 'Отсутствовал на стажировке'})"
										>
											Отсутствовал на стажировке
										</button>
										<button
											class="btn btn-primary btn-block"
											@click="openModalApply({type: 8, label:'Принят на работу' })"
											v-if="sidebarContent.data.item.requested == null"
										>
											Принять на работу
										</button>
										<button
											class="btn btn-info btn-block"
											@click="setDayWithoutComment(7)"
										>
											Подключился позже
										</button>

										<div
											class="mt-3"
											style="color:green;text-align:center"
										>
											{{ apllyPersonResponse }}
										</div>

										<div
											class="mt-3"
											style="color:green;text-align:center"
											v-if="sidebarContent.data.item.requested !== null"
										>
											Заявка на принятие на работу была подана в {{ sidebarContent.data.item.requested }}
										</div>

										<button
											class="btn btn-danger btn-block mt-auto"
											@click="openFiringModal({
												label: 'Уволить',
												color: '#c8a2c8',
												type: 4
											}, 0)"
										>
											Уволить
										</button>
									</div>
								</template>
							</b-tab>
							<b-tab
								title="⚠️Штрафы"
								v-if="!sidebarContent.data.item.is_trainee"
							>
								<b-form-group
									label="Система депремирования"
									class="fines-modal"
								>
									<b-form-checkbox-group
										v-model="sidebarContent.fines"
										name="flavour-2a"
										stacked
									>
										<b-form-checkbox
											:value="fine.value"
											:key="fine.value"
											v-for="fine in fines"
										>
											<span v-html="fine.text" />
										</b-form-checkbox>
									</b-form-checkbox-group>
								</b-form-group>
								<b-button
									variant="primary"
									@click="openModalFine"
								>
									Сохранить
								</b-button>
							</b-tab>
						</template>
					</b-tabs>
				</div>
			</div>
			<div
				class="table-report-backdrop"
				@click.self="openSidebar = false"
			/>
		</aside>

		<Sidebar
			:title="sidebarTitle"
			:open="openSidebar"
			@close="openSidebar=false"
			v-if="false"
			width="350px"
		>
			<b-tabs
				content-class="mt-3"
				justified
			>
				<b-tab
					title="🕒"
					active
				>
					<template v-if="sidebarHistory && sidebarHistory.length > 0">
						<div class="history">
							<div
								v-for="(item,index) in sidebarHistory"
								:key="index"
								class="mb-3"
							>
								<p class="fz12">
									<b class="text-black">Дата:</b> {{ (new
										Date(item.created_at)).addHours(-6).toLocaleString('ru-RU') }}
								</p>
								<p class="fz12">
									<b class="text-black">Автор:</b> {{ item.author }} <br>
								</p>
								<p
									class="fz14 mb-0"
									v-html="item.description"
								/><br>
								<hr>
							</div>
						</div>
					</template>
					<template v-else>
						<p>История изменения отсутствует</p>
					</template>
				</b-tab>

				<template v-if="canEdit">
					<b-tab title="📆">
						<!-- <div v-html="sidebarContent.history"></div>
            <div v-html="sidebarContent.historyTotal"></div> -->
						<template v-if="!sidebarContent.data.item.is_trainee">
							<div class="temari">
								<div
									v-for="dateType in dateTypes"
									:key="dateType.label"
									:class="[dateType.type == 4 ? 'mt-auto' : 'mb-2']"
								>
									<b-button
										block
										@click="openModalDay(dateType)"
										:class="'table-day-'+dateType.type"
									>
										{{ dateType.label }}
									</b-button>
								</div>
								<div class="mt-auto">
									<b-button
										block
										@click="openFiringModal({
											label: 'Уволить без отработки',
											color: '#d35dd3',
											type: 4
										}, 1)"
										:class="'table-day-4'"
									>
										Уволить без отработки
									</b-button>
								</div>
								<div class="mt-2">
									<b-button
										block
										@click="openFiringModal({
											label: 'Уволить с отработкой',
											color: '#c8a2c8',
											type: 4
										}, 2)"
										:class="'table-day-4'"
									>
										Уволить с отработкой
									</b-button>
								</div>
							</div>
						</template>

						<template v-else>
							<div class="temari">
								<button
									class="btn btn-warning btn-block"
									@click="openModalAbsence({type: 2, label: 'Отсутствовал на стажировке'})"
								>
									Отсутствовал на стажировке
								</button>
								<button
									class="btn btn-primary btn-block"
									@click="openModalApply({type: 8, label:'Принят на работу' })"
									v-if="sidebarContent.data.item.requested == null"
								>
									Принять на работу
								</button>
								<button
									class="btn btn-info btn-block"
									@click="setDayWithoutComment(7)"
								>
									Подключился позже
								</button>

								<div
									class="mt-3"
									style="color:green;text-align:center"
								>
									{{ apllyPersonResponse }}
								</div>

								<div
									class="mt-3"
									style="color:green;text-align:center"
									v-if="sidebarContent.data.item.requested !== null"
								>
									Заявка на принятие на работу была подана в {{ sidebarContent.data.item.requested }}
								</div>

								<button
									class="btn btn-danger btn-block mt-auto"
									@click="openFiringModal({
										label: 'Уволить',
										color: '#c8a2c8',
										type: 4
									}, 0)"
								>
									Уволить
								</button>
							</div>
						</template>
					</b-tab>
					<b-tab
						title="⚠️Штрафы"
						v-if="!sidebarContent.data.item.is_trainee"
					>
						<b-form-group
							label="Система депремирования"
							class="fines-modal"
						>
							<b-form-checkbox-group
								v-model="sidebarContent.fines"
								name="flavour-2a"
								stacked
							>
								<b-form-checkbox
									:value="fine.value"
									:key="fine.value"
									v-for="fine in fines"
								>
									<span v-html="fine.text" />
								</b-form-checkbox>
							</b-form-checkbox-group>
						</b-form-group>
						<b-button
							variant="primary"
							@click="openModalFine"
						>
							Сохранить
						</b-button>
					</b-tab>
				</template>
			</b-tabs>
		</Sidebar>


		<b-modal
			v-model="modalVisibleFines"
			ok-text="Да"
			cancel-text="Нет"
			title="Вы уверены?"
			@ok="saveFines"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>
			<b-form-input
				v-model="commentFines"
				placeholder="Комментарий"
				:required="true"
			/>
		</b-modal>

		<b-modal
			v-model="modalVisibleDay"
			ok-text="Да"
			cancel-text="Нет"
			:title="modalTitle"
			@ok="setDayType"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>
			<b-form-input
				v-model="commentDay"
				placeholder="Комментарий"
				:required="true"
			/>
		</b-modal>

		<b-modal
			v-model="modalVisibleApply"
			ok-text="Да"
			cancel-text="Нет"
			:title="'Принятие на работу'"
			@ok="applyPerson"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>
			<b-form-input
				v-model="applyItems.schedule"
				placeholder="Напишите со скольки и до скольки рабочий день"
				:required="true"
			/>
		</b-modal>


		<b-modal
			v-model="modalVisibleAbsence"
			ok-text="Да"
			cancel-text="Нет"
			title="Отсутствовал на стажировке"
			@ok="setUserAbsent"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>

			<select
				class="form-control"
				v-model="commentAbsent"
			>
				<option
					value=""
					disabled
					selected
				>
					Выберите причину
				</option>
				<option
					v-for="cause in fire_causes"
					:key="cause"
					:value="cause"
				>
					{{ cause }}
				</option>
			</select>
		</b-modal>

		<b-modal
			v-model="modalVisibleFiring"
			ok-text="Да"
			cancel-text="Нет"
			:title="modalTitle"
			@ok="setUserFired"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>


			<select
				class="form-control"
				v-model="commentFiring2"
			>
				<option
					value=""
					disabled
					selected
				>
					Выберите причину
				</option>
				<option
					v-for="cause in fire_causes"
					:key="cause"
					:value="cause"
				>
					{{ cause }}
				</option>
			</select>

			<b-form-input
				v-if="firingItems.type == 0"
				class="mt-3"
				v-model="commentFiring"
				placeholder="Свой вариант"
				:required="true"
			/>

			<b-form-file
				v-if="firingItems.type == 2"
				v-model="firingItems.file"
				:state="Boolean(firingItems.file)"
				placeholder="Выберите или перетащите файл сюда..."
				drop-placeholder="Перетащите файл сюда..."
				class="mt-3"
			/>
		</b-modal>


		<b-modal
			v-model="modalVisible"
			ok-text="Да"
			cancel-text="Нет"
			title="Вы уверены?"
			@ok="updateHour"
			size="md"
		>
			<template v-for="error in errors">
				<b-alert
					show
					variant="danger"
					:key="error"
				>
					{{ error }}
				</b-alert>
			</template>
			<b-form-input
				v-model="comment"
				placeholder="Комментарий"
				:required="true"
			/>
		</b-modal>
	</div>
</template>

<script>
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import GroupExcelImport from '@/components/imports/GroupExcelImport' // импорт в табели
import {useYearOptions} from '../composables/yearOptions'

export default {
	name: 'TableReport',
	components: {
		Sidebar,
		GroupExcelImport,
	},
	props: {
		groups: {
			type: Array,
			default: () => []
		},
		fines: {
			type: Array,
			default: () => []
		},
		activeuserid: {
			type: String,
			default: () => ''
		},
		activeuserpos: {
			type: String,
			default: () => ''
		},
		canEdit: {
			type: Boolean,
			default: () => false
		}
	},
	data() {
		const now = new Date()
		return {
			isBp: window.location.hostname.split('.')[0] === 'bp',
			data: {},
			showExcelImport: false,
			openSidebar: false,
			sidebarTitle: '',
			sidebarContent: {},
			commentAbsent: '',
			sidebarHistory: [],
			items: [],
			fields: [],
			head_ids: [],
			editMode: false,
			dayInfoText: '',
			scrollLeft: 0, // scroller
			maxScrollWidth: 0, // scroller
			defaultScrollValue: 0, // scroller
			totalRows: 1,
			currentPage: 1,
			editable_time: false,
			perPage: 1000,
			pageOptions: [5, 10, 15],
			total_resources: 0,
			apllyPersonResponse: '',
			dayPercentage: (now.getDate() / 31) * 100,
			group_editors: [],
			users: [],
			hasPermission: false,
			dateInfo: {
				currentMonth: null,
				currentYear: now.getFullYear(),
				monthEnd: 0,
				workDays: 0,
				weekDays: 0,
				daysInMonth: 0,
				date: null,
				month: null
			},
			dataLoaded: false,
			currentGroup: null,
			dateTypes: [{
				label: 'Обычный',
				color: '#fff',
				type: 0
			},
			{
				label: 'Выходной',
				color: '#ccc',
				type: 1
			},
			{
				label: 'Прогул',
				color: 'red',
				type: 2
			},
			{
				label: 'Больничный',
				color: 'aqua',
				type: 3
			},
			{
				label: 'Стажер',
				color: 'orange',
				type: 5
			},
			{
				label: 'Переобучение',
				color: 'pink',
				type: 6,
			},
			],
			numClicks: 0,
			currentEditingCell: {},
			comment: '',
			commentDay: '',
			commentFines: '',
			commentFiring: '',
			commentFiring2: '',
			currentDayType: {},
			modalVisible: false,
			modalVisibleDay: false,
			modalVisibleFines: false,
			modalVisibleFiring: false,
			modalVisibleApply: false,
			modalVisibleAbsence: false,
			modalTitle: '',
			currentMinutes: 0,
			errors: [],
			user_types: 0,
			url_page: '',
			firingItems: {
				file: undefined,
				filename: '',
				type: 1 // Без отработки
			},
			applyItems: {
				schedule: '',
			},
			fire_causes: [],
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
	},
	watch: {
		scrollLeft(value) {
			var container = document.querySelector('.table-responsive')
			container.scrollLeft = value
		},
		user_types() {
			this.fetchData()
		},
		groups() {
			this.init()
		}
	},
	created() {
		if (this.groups) {
			this.init()
		}
	},
	methods: {
		init() {
			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')

			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(this.dateInfo.monthEnd, [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней

			//Текущая группа
			this.currentGroup = this.currentGroup ? this.currentGroup : this.groups[0]['id']

			this.fetchData()
		},
		copy() {
			var Url = this.$refs['mylink' + this.currentGroup];
			Url.value = window.location.origin + '/autocheck/' + this.currentGroup;

			Url.select();
			document.execCommand('copy');

			if (confirm(`Если нажмете "ОК", то стажеры должны переходить по ссылке и отмечаться в течении 30 минут.
            \nПосле 30 минут кто не отметился, перейдут в статус "Отсутствует". \nВы уверены?`) == false) {
				return '';
			}

			this.axios.post('/autochecker/' + this.currentGroup, {})
				.then(response => {
					if (response.data.code == 200) {
						this.$toast.success('Ссылка скопирована. Через 30 минут (в ' + response.data.time + ') не отмеченные стажеры перейдут в статус "Отсутствует"')
					} else {
						this.$toast.error('Попробуйте нажать еще раз')
					}
				}).catch(error => {
					alert(error)
				});
		},

		openModalDay(dayType) {
			this.modalTitle = this.sidebarTitle + ' (' + dayType.label + ')'
			this.currentDayType = dayType
			this.modalVisibleDay = true
		},

		openModalApply(dayType) {
			this.currentDayType = dayType
			this.modalVisibleApply = true
		},

		openModalAbsence(dayType) {
			this.currentDayType = dayType
			this.modalVisibleAbsence = true

			this.fire_causes = [
				'Был на основной работе',
				'Бросает трубку',
				'Вышел (-ла) из группы',
				'Забыл (-а), после обеда присутствует',
				'Нашел(-а) другую работу',
				'Не был на обучении / стажировке',
				'Не выходит на связь',
				'Не понравились условия оплаты труда',
				'Не сдал экзамен',
				'Не смог подключиться',
				'Не хочет долго стажироваться',
				'Не хочет работать 6 дней',
				'Отказ от стажировки',
				'Отсутствовал(а) более 3 дней',
				'По техническим причинам',
				'Пропал с обучения',
				'Ребенок заболел, не сможет совмещать',
				'Удалился (-ась), не актуально',
			];
		},

		openFiringModal(dayType, type) {
			this.modalTitle = this.sidebarTitle + ' (' + dayType.label + ')'
			this.currentDayType = dayType
			this.modalVisibleFiring = true
			this.firingItems.type = type

			if (type == 0) { // причины стажеров
				this.fire_causes = [
					'Был на основной работе',
					'Бросает трубку',
					'Вышел (-ла) из группы',
					'Забыл (-а), после обеда присутствует',
					'Нашел(-а) другую работу',
					'Не был на обучении / стажировке',
					'Не выходит на связь',
					'Не понравились условия оплаты труда',
					'Не сдал экзамен',
					'Не смог подключиться',
					'Не хочет долго стажироваться',
					'Не хочет работать 6 дней',
					'Отказ от стажировки',
					'Отсутствовал(а) более 3 дней',
					'По техническим причинам',
					'Пропал с обучения',
					'Ребенок заболел, не сможет совмещать',
					'Удалился (-ась), не актуально',
				];
			} else { // причины действующих
				this.fire_causes = [
					'Взял перерыв, позже возможно будет работать',
					'Дисциплинарные нарушения',
					'Дубликат, 2 учетки',
					'Заказчик снял с проекта',
					'Игнорирование предупреждений',
					'Не справился с обязанностями',
					'Конфликт с коллегами',
					'Нашел(-а) другую работу',
					'Неадекватная личность',
					'Некому за ребенком присматривать',
					'Не выходит на связь более 7 дней',
					'Не успевает по учебе',
					'Не устраивает график',
					'Не устраивает ЗП',
					'Не устраивает пункт в договоре',
					'Оказалось что есть вторая работа',
					'Переезд в другой город',
					'Плохие рабочие показатели/не справился',
					'По семейным обстоятельствам',
					'По состоянию здоровья',
					'По техническим причинам',
					'Проект закрыт. Снят с линии',
					'Решил(-а) работать оффлайн',
					'Слишком большая нагрузка',
				];
			}
		},

		openModalFine() {
			this.modalVisibleFines = true
		},

		openModal(event) {
			const hour = event.target.value
			let clearedValue = hour.replace(',', '.')
			let value = parseFloat(clearedValue) * 60
			this.currentMinutes = value
			this.modalVisible = true

			try {
				this.$toast.info('C ' + this.currentEditingCell.item[this.currentEditingCell.field.key].hour + ' на ' + hour);
			} catch (e) {
				alert(e);
			}
		},

		openDay(data) {
			if (this.editMode) return

			if (data.field.key == 'name') return
			this.openSidebar = true
			this.sidebarTitle = `${data.item.name} - ${data.field.key} ${this.dateInfo.currentMonth} `
			this.sidebarContent = {
				data: data,
				history: `${data.item[data.field.key] ? data.item[data.field.key].tooltip : ''}`,
				historyTotal: `Итого: ${data.value.hour} ч.`.replace('undefined', '0.0'),
				day: data.field.key,
				user_id: data.item.user_id,
				fines: data.item.fines[data.field.key]
			}
			this.sidebarHistory = data.item.history.filter(x => parseInt(x.day) === parseInt(data.field.key))
		},

		setUserFired() {
			if (this.firingItems.type == 2 && this.firingItems.file == undefined) {
				this.errors = ['Заявление об увольнении обязательно!']

			}

			let comment = '';
			if (this.commentFiring.length == 0) {

				if (this.commentFiring2.length == 0) {
					this.errors = ['Комментарий обязателен']
					return null
				} else {
					comment = this.commentFiring2;
				}
			} else {
				comment = this.commentFiring;
			}

			let formData = new FormData();
			formData.append('month', this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'));
			formData.append('day', this.sidebarContent.day);
			formData.append('user_id', this.sidebarContent.user_id);
			formData.append('year', this.dateInfo.currentYear)
			formData.append('type', this.currentDayType.type);
			formData.append('comment', comment);
			formData.append('file', this.firingItems.file);
			formData.append('fire_type', this.firingItems.type);

			this.axios.post('/timetracking/set-day', formData, {
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(response => {

				let v = this.items[this.sidebarContent.data.index]['_cellVariants'];
				[this.sidebarContent.day] = `day-${this.currentDayType.type}`

				this.items[this.sidebarContent.data.index]['_cellVariants'] = v

				this.fetchData()


				this.openSidebar = false

				if (response.data.success == 1) {

					this.sidebarHistory.push(response.data.history)
					this.modalVisibleFiring = false
					this.commentFiring = ''
					this.commentFiring2 = ''
					this.currentDayType = {}
				}
			}).catch(error => {
				alert(error)
			});
		},

		setDayWithoutComment(type) {
			let day = this.sidebarContent.day;
			this.axios.post('/timetracking/set-day', {
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				day: day,
				user_id: this.sidebarContent.user_id,
				enable_comment: this.sidebarContent.data.item.enable_comment,
				type: type,
				group_id: this.currentGroup,
				comment: ' ',
				year: this.dateInfo.currentYear,
			}).then(response => {

				let v = this.items[this.sidebarContent.data.index]['_cellVariants'];
				[day] = `day-${this.currentDayType.type}`

				this.items[this.sidebarContent.data.index]['_cellVariants'] = v

				this.fetchData()


				this.openSidebar = false

				if (response.data.success == 1) {
					this.sidebarHistory.push(response.data.history)
					this.currentDayType = {}
				}
			}).catch(error => {
				alert(error)
			});
		},

		setDayType() {
			if (this.commentDay.length > 0) {
				this.axios.post('/timetracking/set-day', {
					month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
					day: this.sidebarContent.day,
					user_id: this.sidebarContent.user_id,
					type: this.currentDayType.type,
					comment: this.commentDay,
					year: this.dateInfo.currentYear,
				}).then(response => {

					let v = this.items[this.sidebarContent.data.index]['_cellVariants'];
					[this.sidebarContent.day] = `day-${this.currentDayType.type}`

					this.items[this.sidebarContent.data.index]['_cellVariants'] = v

					this.fetchData()


					this.openSidebar = false

					if (response.data.success == 1) {
						this.sidebarHistory.push(response.data.history)
						this.modalVisibleDay = false
						this.commentDay = ''
						this.currentDayType = {}
					}
				}).catch(error => {
					alert(error)
				});
			} else {
				this.errors = ['Комментарий обязателен']
			}
		},

		saveFines() {
			if (this.commentFines.length > 0) {
				this.openSidebar = false
				let loader = this.$loading.show();
				this.axios.post('/timetracking/user-fine', {
					date: this.dateInfo.shortDate + '-' + this.sidebarContent.day,
					user_id: this.sidebarContent.user_id,
					fines: this.sidebarContent.fines,
					comment: this.commentFines
				}).then(() => {
					this.fetchData()
					loader.hide()
					this.commentFines = ''
					this.modalVisibleFines = false
				}).catch(error => {
					loader.hide()
					alert(error)
				});
			} else {
				this.errors = ['Комментарий обязателен']
			}
		},

		dayInfo(data) {
			if (!data.item?._cellVariants) return
			// if (!isNaN(data.field.key))
			this.dayInfoText = `${data.item.name} - ${data.field.key} ${this.dateInfo.currentMonth}`
		},

		//Установка выбранного года
		setYear() {
			this.dateInfo.currentYear = this.dateInfo.currentYear ? this.dateInfo.currentYear : this.$moment().format('YYYY')
		},

		//Установка выбранного месяца
		setMonth() {
			let year = this.dateInfo.currentYear

			this.dateInfo.currentMonth = this.dateInfo.currentMonth ? this.dateInfo.currentMonth : this.$moment().format('MMMM')
			this.dateInfo.date = `${this.dateInfo.currentMonth} ${year}`
			this.dateInfo.shortDate = this.$moment(`${this.dateInfo.currentMonth} ${year}`, 'MMMM YYYY').locale('ru').format('YYYY-MM')
			this.dateInfo.month = this.$moment(`${this.dateInfo.currentMonth} ${year}`, 'MMMM YYYY').locale('ru').format('MM')
			this.dateInfo.year = year
			let currentMonth = this.$moment(this.dateInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.dateInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.dateInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.dateInfo.daysInMonth = currentMonth.daysInMonth() //Колличество дней в месяце
			this.dateInfo.workDays = this.dateInfo.daysInMonth - this.dateInfo.weekDays //Колличество рабочих дней
		},

		//Установка заголовока таблицы
		readOnlyFix(event) {
			if (this.editable_time && this.canEdit) {
				event.target.readOnly = ''
			}
		},

		setFields() {
			let fields = [];

			fields = [
				{
					key: 'name',
					stickyColumn: true,
					label: 'Имя',
					sortable: true,
					class: 'text-left px-3 t-name',
				},
				{
					key: 'total',
					label: '',
					sortable: true,
					class: 'text-center td-lightgreen',
				}
			];

			let days = this.dateInfo.daysInMonth

			for (let i = 1; i <= days; i++) {
				let dayName = this.$moment(`${i} ${this.dateInfo.date}`, 'D MMMM YYYY').locale('en').format('ddd')
				fields.push({
					key: `${i}`,
					label: `${i}`,
					sortable: true,
					class: `day ${dayName}`,
				})
			}
			this.fields = fields
		},

		//Загрузка данных для таблицы
		fetchData(url = null) {

			if (url === null) {
				if (this.url_page === '') {
					url = '/timetracking/reports';
				} else {
					url = this.url_page;
				}
			} else {
				// /timetracking/reports?page=2
				this.url_page = url;
			}

			/*console.log('URL');
				console.log(url);*/

			let loader = this.$loading.show();

			this.axios.post(url, {
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				year: this.dateInfo.currentYear,
				group_id: this.currentGroup,
				user_types: this.user_types,
			}).then(response => {
				if (response.data.error && response.data.error == 'access') {
					this.hasPermission = false
					loader.hide();
					return;
				}
				this.hasPermission = true
				this.data = response.data
				this.head_ids = this.data.head_ids
				this.total_resources = response.data.total_resources
				this.editable_time = response.data.editable_time == 1 ? true : false;

				this.setYear()
				this.setMonth()
				this.setFields()
				this.loadItems()


				this.dataLoaded = true
				setTimeout(() => {
					var container = document.querySelector('.table-responsive')
					this.maxScrollWidth = container.scrollWidth - container.offsetWidth
					if (this.dayPercentage > 50) {
						// this.scrollLeft = (this.maxScrollWidth * this.dayPercentage) / 100
						// this.defaultScrollValue = this.scrollLeft
					}
				}, 1000);
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		//Добавление загруженных данных в таблицу
		loadItems() {

			let items = []

			let daily_totals = {};

			for (let i = 1; i <= 31; i++) {
				daily_totals[i] = 0;
			}

			this.data.users.forEach(item => {

				let dayHours = []
				let startEnd = []

				let total = 0;

				item.timetracking.forEach(tt => {
					if (typeof dayHours[tt.date] === 'undefined') {
						dayHours[tt.date] = {
							hour: 0,
							tooltip: '',
							updated: tt.updated
						}
					}

					let tt_hours = 0;

					if (tt.updated === 1 || tt.updated === 2 || tt.updated === 3) dayHours[tt.date].updated = 1

					let enter = this.$moment.utc(tt.enter, 'YYYY-MM-DD HH:mm:ss').local().format('HH:mm')
					let exit = this.$moment.utc(tt.exit, 'YYYY-MM-DD HH:mm:ss').local().format('HH:mm')
					startEnd[tt.date] += `<tr><td>${enter}</td><td>${exit}</td></td>`

					if (dayHours[tt.date].updated === 1 || dayHours[tt.date].updated === 2 || dayHours[tt.date].updated === 3) {
						if (tt.updated !== 0) {
							dayHours[tt.date].hour = Number(tt.minutes / 60)
							tt_hours = Number(tt.minutes / 60);
						}
					} else {


						if (tt.minutes > 0) {
							dayHours[tt.date].hour += Number(tt.minutes / 60);
							tt_hours += Number(tt.minutes / 60);
						}


						var maxHour = item.working_time_id === 1 ? 8 : 9;

						if (dayHours[tt.date].hour > maxHour && tt.updated === 0) {
							dayHours[tt.date].hour = maxHour;
							tt_hours = maxHour;
						}
					}

					if (Number(tt.date) >= Number(item.applied_at)) {
						total += Number(tt_hours);

						daily_totals[tt.date] += Number(tt_hours);
					}
				})


				//Время, история
				dayHours.forEach((dh, key) => {
					let resultHour = (item.user_type == 'office') ? Number(parseFloat(dh.hour)).toFixed(1) : Number(parseFloat(dh.hour)).toFixed(1)
					let checkHour = (resultHour > 0) ? resultHour : 0
					let fine = []
					if (item.selectedFines[key]) {
						fine = item.selectedFines[key]
					}
					dayHours[key] = {
						hour: Number(checkHour).toFixed(1),
						tooltip: `<table class="table table-sm mb-0 ">${startEnd[key].replace('undefined', '').replace('Invalid date', 'Еще не завершен')}</table>`,
						key: key,
						fine: (fine.length > 0),
						updated: dh.updated === 1 || dh.updated === 2
					}
				})

				let v = [];

				Object.keys(item.dayTypes).forEach(k => {
					if (item.dayTypes) v[k] = `day-${item.dayTypes[k]}`
				});


				Object.keys(item.fines).forEach(k => {
					if (item.fines[k].status == 1) {
						v[parseInt(item.fines[k].date)] += ' table-day-2'
					}

				});


				Object.keys(item.weekdays).forEach(k => {
					if (Number(item.weekdays[k]) == 1) {
						v[Number(k)] += ' table-day-1'
					}
				});

				var variants = {
					_cellVariants: v
				}


				items.push({
					name: `${item.name} ${item.last_name}`,
					total: Number(total).toFixed(1),
					enable_comment: item.enable_comment,
					id: item.id,
					fines: item.selectedFines,
					user_id: item.id,
					user_type: item.user_type,
					is_trainee: item.is_trainee,
					requested: item.requested,
					applied_at: item.applied_at,
					history: item.track_history,
					...variants,
					...dayHours,
				})

			})
			this.items = items

			for (let i = 1; i <= 31; i++) {
				daily_totals[i] = Number(daily_totals[i]).toFixed(1);
			}
			this.items.unshift(daily_totals);
			this.totalRows = this.items.length
		},

		editDay(data) {

			try {
				this.$toast.info('Вы редактируете ' + this.currentEditingCell.field.key + ' число  у ' + this.currentEditingCell.item.name);
			} catch (err) {
				console.error('editDay')
			}

			this.currentEditingCell = data
		},

		updateHour() {
			if (this.isEmpty(this.currentEditingCell)) {
				this.$toast.error('Что-то пошло не так. Выберите поле и попробуйте снова');
				return;
			}

			if (this.comment.length > 0) {
				let loader = this.$loading.show();
				this.axios.post('/timetracking/reports/update/day', {
					year: this.dateInfo.currentYear,
					month: this.dateInfo.month,
					day: this.currentEditingCell.field.key,
					user_id: this.currentEditingCell.item.user_id,
					minutes: this.currentMinutes,
					comment: this.comment
				}).then(response => {
					if (response.data.error && response.data.error == 'access') {
						this.hasPermission = false
						loader.hide();
						return;
					}

					this.currentEditingCell = {}

					this.fetchData()

					loader.hide();
					this.modalVisible = false
					this.comment = ''
					this.errors = []
				}).catch(error => {
					loader.hide()
					alert(error)
				});
			} else {
				this.errors = ['Комментарий обязателен']
			}
		},

		isEmpty(obj) {
			for (var prop in obj) {
				if (Object.prototype.hasOwnProperty.call(obj, prop)) {
					return false;
				}
			}

			return JSON.stringify(obj) === JSON.stringify({});
		},

		applyPerson() {
			if (this.applyItems.schedule.length == 0) {
				return '';
			}

			this.axios.post('/timetracking/apply-person', {
				user_id: this.sidebarContent.user_id,
				schedule: this.applyItems.schedule,
				group_id: this.currentGroup,
			}).then(response => {
				this.apllyPersonResponse = response.data.msg
				this.sidebarContent.data.item.requested = this.$moment().format('DD.MM.Y HH:mm')
				this.modalVisibleApply = false
				setTimeout(() => {
					this.apllyPersonResponse = ''
				}, 2000);
			}).catch(error => {
				alert(error)
			});
		},

		setUserAbsent() {
			let day = this.sidebarContent.day;
			let loader = this.$loading.show();
			this.axios.post('/timetracking/set-day', {
				year: this.dateInfo.currentYear,
				month: this.$moment(this.dateInfo.currentMonth, 'MMMM').format('M'),
				day: day,
				user_id: this.sidebarContent.user_id,
				enable_comment: this.sidebarContent.data.item.enable_comment,
				type: 2,
				group_id: this.currentGroup,
				comment: this.commentAbsent
			}).then(response => {

				let v = this.items[this.sidebarContent.data.index]['_cellVariants'];
				[day] = `day-${this.currentDayType.type}`

				this.items[this.sidebarContent.data.index]['_cellVariants'] = v

				this.fetchData()


				this.openSidebar = false

				if (response.data.success == 1) {
					this.sidebarHistory.push(response.data.history)
					this.currentDayType = {}
				}

				this.modalVisibleAbsence = false
				this.commentAbsent = ''

				loader.hide();
			}).catch(error => {
				alert(error)
			});


		},

		detectClick(data) {
			if (!data.item?._cellVariants) return
			//if([48,53,65,66].includes(this.currentGroup) || this.activeuserid == 5) { // if RECRUITING GROUP ENABLE EDIT HOURS ON DBLCLICK
			if (this.editable_time && this.canEdit) {
				this.numClicks++
				if (this.numClicks === 1) {
					var self = this
					setTimeout(function () {
						if (self.numClicks === 1) {
							self.openDay(data)
						} else {
							self.editDay(data)
						}
						self.numClicks = 0;
					}, 300);
				}

			} else { // ANOTHER GGROUPS JUST OPEN SIDEBAR
				this.openDay(data);
			}

		}
	}
}
</script>

<style lang="scss">


	.table-report-sidebar{
		position: fixed;
		top: 0;
		right: 6rem;
		z-index: 100;
		width: 100%;
		height: 100%;
		.table-report-backdrop{
			position: absolute;
			top: 0;
			left: 0;
			z-index: 10;
			width: 100%;
			height: 100%;
			background-color: #333;
			opacity: 0.5;
		}
		.table-report-content{
			position: absolute;
			top: 0;
			right: 0;
			width: 400px;
			height: 100vh;
			border-radius: 20px 0 0 20px;
			z-index: 15;
			background-color: #fff;
			.table-report-header{
				background: #ECF0F9;
				padding: 3rem;
				display: flex;
				align-items: center;
				.table-report-title{
					font-size: 16px;
					font-weight: 600;
					line-height: 1;
				}
				.table-report-close{
					width: 35px;
					height: 35px;
					cursor: pointer;
					margin-right: 15px;
				}
			}
			.table-report-body{
				.nav-tabs{
					.nav-item{
						.nav-link{
							color: #8D8D8D;
							font-size: 1.7rem;
							font-weight: 600;
							transition: color 0.3s;
							padding-top: 1.5rem;
							cursor: pointer;
							margin-right: 0;
							border-bottom: none;
							&.active{
								border-top: 4px solid #ED2353;
								color: #ED2353;
							}
						}
					}
				}
				.tab-content{
					padding: 0 20px;
				}
			}
		}
	}
	.hovered-text {
		margin-top: 15px;
		color: #62788B;
	}

	.table-custom-report {
		th, td {
			vertical-align: middle;

			.td-div {
				height: 40px;
				min-width: 50px;
				padding: 0 10px;
				position: relative;
				display: inline-flex;
				align-items: center;
				justify-content: center;
			}
		}

		thead {
			th, td {
				text-align: center;
				padding: 10px !important;
				vertical-align: middle;

				&:first-child {
					padding: 0 15px !important;
				}
			}
		}

		tbody {
			th, td {
				padding: 0 !important;

				&:first-child {
					padding: 0 15px !important;
				}
			}
		}

		.td-lightgreen {
			background-color: #B7E100;
		}

		.table-day-2 {
			color: #333;
			background-color: #f58c94;

			input {
				color: #333;
			}
		}

		.table-day-3 {
			color: rgb(0, 0, 0);
			background-color: aqua !important;
		}

		.table-day-4 {
			color: rgb(0, 0, 0);
			background-color: rgb(200, 162, 200) !important;
		}

		.table-day-5 {
			color: rgb(0, 0, 0);
			background-color: #ffd76d !important;
		}

		.table-day-6 {
			color: #fff;
			background-color: pink !important;
		}

		.table-day-7 {
			color: #fff;
			background-color: #ffc107 !important;
		}

		.cell-border {
			position: absolute;
			right: -1px;
			bottom: -5px;
			border-top: 6px solid transparent;
			border-bottom: 6px solid transparent;
			border-left: 6px solid #b8daff;
			-webkit-transform: rotate(45deg);
			transform: rotate(45deg);
		}
	}


	.editmode {
		opacity: 0;
		height: 36px;
	}

	.editmode:active {
		opacity: 1;
	}

	.history {
		height: 100vh;
		overflow-y: auto;

		p {
			font-size: 14px;
			color: #424242;
		}
	}

	.fines-modal {
		overflow-y: auto;
		max-height: calc(100vh - 225px);
		.custom-checkbox{
			margin-bottom: 10px;
		}
	}


	.b-table-sticky-header {
		max-height: calc(100vh - 250px);
	}

	.table-day-1 {
		color: rgb(0, 0, 0);
		background: #fef1cb !important;
	}

	.temari{
		.btn {
		}
		.button-day{
			&_0{
				border: 1px solid #999;
				color: #333;
				background-color: #fff;
				&:hover{
					background-color: #d8d8d8;
				}
			}
			&_1{
				border: 1px solid #958d73;
				background-color: #e5dab6;
				color: #333;
				&:hover{
					background-color: #c7bd9e;
				}
			}
			&_3{
				border: 1px solid #4489c9;
				background-color: #4c9ee5;
				color: #fff;
				&:hover{
					background-color: #4489c9;
				}
			}
			&_5{
				border: 1px solid #e6983f;
				background-color: #faa544;
				color: #fff;
				&:hover{
					background-color: #e6983f;
				}
			}
			&_6{
				border: 1px solid #98116c;
				background-color: #bc1585;
				color: #fff;
				&:hover{
					background-color: #98116c;
				}
			}
			&_7{
				border: 1px solid #bf2216;
				background-color: #df271a;
				color: #fff;
				&:hover{
					background-color: #bf2216;
				}
			}
		}
	}


	.my-table .day.Sat.table-day-2, .my-table .day.Sun.table-day-2 {
		color: #fff;
		background-color: red;
	}


	.updated {
		.cell-border {
			border-left-color: red;
		}
	}


	.badgy {
		font-size: 0.75em;
	}

	.temari {
		height: calc(100vh - 180px);
		display: flex;
		flex-direction: column;
	}

	.ddf div {
		display: flex;
	}

	.ddf .custom-control {
		margin-right: 15px;
	}

	.fz12 {
		line-height: 1.4em;
		font-size: 12px;
		margin-bottom: 0;
	}

	.fz14 {
		font-size: 14px;
		line-height: 1.4em;
		padding: 10px 0;
	}

	hr {
		margin: 2px !important;
	}

	.hider {
		position: absolute;
		left: -10px;
		width: 10px;
		height: 10px;
		opacity: 0;
		display: block;
	}

	.ddpointer {
		margin-top: 2px;
		cursor: pointer;
	}
</style>
