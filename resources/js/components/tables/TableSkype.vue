<template>
	<div class="TableSkype">
		<div class="skypo">
			<div class="row mt-4 align-items-center">
				<div class="col-4 col-md-4 d-flex mb-4">
					<!-- <select class="form-control" v-model="currentDay">
								0">Все дни</option>
								<option v-for="day in this.month.daysInMonth" :value="day" :key="day">{{ day }}</option>
						</select>    -->
					<div class="p-o pl-3">
						<!-- как это вобще работает?????? -->
						<date-picker
							v-model="filter.dates"
							value="test"
							placeholder="Дата подписи"
							:lang="lang"
							range
							multiple
						/>
						<!-- <m-date-picker v-model="filter.dates" lang="ru" :multi="true" :always-display="false" :format="formatDate"></m-date-picker> -->
					</div>
				</div>
				<div class="col-8 col-md-8 d-flex justify-end gap-3">
					<div class="TableSkype-select mb-4">
						<select
							v-model="filter.currentInviteGroup"
							class="form-control form-control-sm mt-2"
						>
							<option
								v-for="(invite_group, index) in invite_groups"
								:key="index"
								:value="index"
							>
								{{ invite_group }}
							</option>
						</select>
					</div>
					<div class="TableSkype-select mb-4">
						<select
							v-model="filter.user_type"
							class="form-control form-control-sm mt-2"
						>
							<option
								v-for="(user_type, index) in user_types"
								:key="index"
								:value="index"
							>
								{{ user_type }}
							</option>
						</select>
					</div>
					<div class="TableSkype-select mb-4">
						<select
							v-model="filter.lang"
							class="form-control form-control-sm mt-2"
						>
							<option
								v-for="(lang, index) in langs"
								:key="index"
								:value="index"
							>
								{{ lang }}
							</option>
						</select>
					</div>
					<div class="TableSkype-select mb-4">
						<select
							v-model="filter.wishtime"
							class="form-control form-control-sm mt-2"
						>
							<option
								v-for="(wishtime, index) in wishtimes"
								:key="index"
								:value="index"
							>
								{{ wishtime }}
							</option>
						</select>
					</div>
				</div>
				<!-- <div class="col-md-2">
                <select class="form-control form-control-sm" v-model="filter.segment">
                    <option v-for="(segment, index) in segments" :key="index" :value="index">{{ segment }}</option>
                </select>
            </div> -->
				<div class="col-md-4 mb-4">
					<b>Кол-во:</b> {{ records.length }}
				</div>
				<div class="col-md-4 mb-4" />
				<div class="col-md-4 mb-4">
					<div class="d-flex justify-end">
						<!-- <div class="d-flex mr-3 align-items-center">
                        <div class="circle bg-green"></div>
                        <div>Приглашенные</div>
                    </div>
                    <div class="d-flex mr-3 align-items-center">
                        <div class="circle bg-green-2"></div>
                        <div>Повторная попытка</div>
                    </div>
                    <div class="d-flex mr-3 align-items-center">
                        <div class="circle bg-green-3"></div>
                        <div>Ссылка добавлена в сделку</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="circle bg-green-4"></div>
                        <div>Не найдена сделка</div>
                    </div> -->
						<JobtronButton
							class="ml-4"
							small
							@click="showModal = !showModal"
						>
							+ Добавить
						</JobtronButton>
						<JobtronButton
							class="ml-4 fz-11"
							title="Показывать поля"
							fade
							small
							@click="showSkypeFieldsModal = !showSkypeFieldsModal"
						>
							<i
								class="icon-nd-settings"
								aria-hidden="true"
							/>
						</JobtronButton>
					</div>
				</div>
			</div>
		</div>

		<div class="TableSkype-table mb-4">
			<JobtronTable
				class="text-nowrap mb-3 skypes-table"
				:small="true"
				:bordered="true"
				:items="records"
				:fields="fields"
				primary-key="a"
				:current-page="currentPage"
				:per-page="perPage"
				:tr-class-fn="detailsClassFn"
				:class="{
					'hide-2': !showSkypeFields.lead_id,
					'hide-3': !showSkypeFields.skyped,
					'hide-4': !showSkypeFields.project,
					'hide-5': !showSkypeFields.name,
					'hide-6': !showSkypeFields.lang,
					'hide-7': !showSkypeFields.net,
					'hide-8': !showSkypeFields.wishtime,
					'hide-9': !showSkypeFields.invited_at,
					'hide-10': !showSkypeFields.invite_group,
					'hide-11': !showSkypeFields.country,
					'hide-12': !showSkypeFields.segment,
					'hide-13': !showSkypeFields.resp,
					'hide-14': !showSkypeFields.phone,
					'hide-15': !showSkypeFields.file,
				}"
			>
				<!-- <template #head(checked)>
                <input type="checkbox" v-model="checker" :value="false">
            </template> -->

				<template #cell(checked)="data">
					<input
						v-model="checkedBoxes"
						type="checkbox"
						:value="data.item.id"
					>
				</template>

				<template #cell(lead_id)="data">
					<div>
						<a
							:href="'/timetracking/analytics/skypes/' + data.value"
							target="_blank"
						>Сделка</a>
					</div>
				</template>

				<template #cell(name)="data">
					<div class="text-left">
						{{ data.value }}
						<span
							v-if="data.item.user_type == 'office'"
							pill
							variant="success"
							class="badge badge-success badge-pill"
						>
							office
						</span>
					</div>
				</template>

				<template #cell(invite_group)="data">
					<div>
						<div>
							{{ data.value }}
						</div>
					</div>
				</template>

				<template #cell(resp)="data">
					<div>
						<div
							class="resp_user"
							v-html="data.value"
						/>
					</div>
				</template>

				<template #cell(country)="data">
					<div>
						<div
							v-if="countries.hasOwnProperty(data.value)"
							class="country"
							:title="data.value"
						>
							{{ countries[data.value] }}
						</div>
						<div v-else>
							{{ data.value }}
						</div>
					</div>
				</template>

				<template #cell(lang)="data">
					<div>
						<div v-if="data.value != '1' && data.value != '2' && data.value != '3'">
							{{ data.value }}
						</div>
						<div v-else>
							{{ langs[data.value] }}
						</div>
					</div>
				</template>

				<template #cell(net)="data">
					<div
						class="TableSkype-maw"
						:title="data.value != '1' && data.value != '2' && data.value != '3' && data.value != '4' && data.value != '5' ? data.value : nets[data.value]"
					>
						<template v-if="data.value != '1' && data.value != '2' && data.value != '3' && data.value != '4' && data.value != '5'">
							{{ data.value }}
						</template>
						<template v-else>
							{{ nets[data.value] }}
						</template>
					</div>
				</template>

				<template #cell(segment)="data">
					<div
						class="TableSkype-maw"
						:title="segments.hasOwnProperty(data.value) ? segments[data.value] : data.value"
					>
						<template v-if="segments.hasOwnProperty(data.value)">
							{{ segments[data.value] }}
						</template>
						<template v-else>
							{{ data.value }}
						</template>
					</div>
				</template>

				<template #cell(wishtime)="data">
					<div>
						<div v-if="data.value != '1' && data.value != '2' && data.value != '3' && data.value != '4' && data.value != '5' && data.value != '6'">
							{{ data.value }}
						</div>
						<div v-else>
							{{ wishtimes[data.value] }}
						</div>
					</div>
				</template>

				<template #cell(file)="data">
					<div
						style="position:relative;"
						:title="data.item.name"
					>
						<a
							:href="data.value"
							target="_blank"
							class="imagy imagy1"
						>
							<i
								class="fa fa-image"
								aria-hidden="true"
							/>
						</a>
					</div>
				</template>
			</JobtronTable>
		</div>

		<div class="mb-2">
			<b-pagination
				v-model="currentPage"
				:total-rows="totalRows"
				:per-page="perPage"
				align="fill"
				size="sm"
				class="my-0"
			/>
		</div>

		<div
			v-if="checkedBoxes.length > 0"
			class="bottomvars"
		>
			<div class="row align-items-center">
				<div class="col-sm-3">
					<select
						v-model="selected.group_id"
						required="required"
						class="form-control form-control-sm"
					>
						<option
							v-for="group in groups"
							:key="group.id"
							:value="group.id"
						>
							{{ group.name }}
						</option>
					</select>
				</div>

				<div class="col-sm-3">
					<b-form-datepicker
						id="example-datepicker"
						v-model="selected.date"
						v-bind="datepickerLabels"
						class="form-control form-control-sm"
						locale="ru"
						:min="new Date()"
						:start-weekday="1"
					/>
				</div>
				<div class="col-sm-1">
					<input
						v-model="selected.time"
						type="time"
						class="form-control form-control-sm timer"
					>
				</div>
				<div class="col-sm-2">
					<button
						class="btn btn-primary rounded py-1"
						@click="inviteUsers()"
					>
						Пригласить на стажировку
					</button>
				</div>
				<div class="col-sm-3 d-flex justify-end">
					<div class="blues">
						<div
							v-if="checkedBoxes.length == records.length"
							@click="unCheckAll"
						>
							Снять все
						</div>
					</div>
					<div class="ml-2">
						Выбрано: <b>{{ checkedBoxes.length }}</b> из <b>{{ records.length }}</b>
					</div>
				</div>
			</div>
		</div>

		<b-modal
			v-model="showModal"
			ok-text="Сохранить"
			cancel-text="Отмена"
			title="Новый лид"
			size="lg"
			class="modalle"
			@ok="saveLead"
		>
			<b-alert
				v-for="error in errors"
				:key="error"
				show
				variant="danger"
			>
				{{ error }}
			</b-alert>
			<b-form-input
				v-model="lead.name"
				placeholder="ФИО"
				:required="true"
				class="form-control form-control-sm mb-2"
			/>
			<b-form-input
				v-model="lead.phone"
				placeholder="Телефон"
				:required="true"
				class="form-control form-control-sm mb-2"
			/>
			<div class="d-flex gap-3">
				<select
					v-model="lead.lang"
					required="required"
					class="form-control form-control-sm"
				>
					<option
						v-for="(lang, index) in langs"
						:key="index"
						:value="index"
					>
						{{ lang }}
					</option>
				</select>
				<select
					v-model="lead.wishtime"
					required="required"
					class="form-control form-control-sm"
				>
					<option
						v-for="(wishtime, index) in wishtimes"
						:key="index"
						:value="index"
					>
						{{ wishtime }}
					</option>
				</select>
			</div>
		</b-modal>

		<b-modal
			v-model="showSkypeFieldsModal"
			title="Настройка списка"
			ok-text="Закрыть"
			size="lg"
			class="modalle"
			@ok="showSkypeFieldsModal = !showSkypeFieldsModal"
		>
			<b-alert
				v-for="error in errors"
				:key="error"
				show
				variant="danger"
			>
				{{ error }}
			</b-alert>

			<div class="row">
				<div
					v-for="(field, key) in showSkypeFields"
					:key="key"
					class="col-md-4 mb-2"
				>
					<b-form-checkbox
						v-if="key !== 'file'"
						v-model="showSkypeFields[key]"
						:unchecked-value="false"
					>
						{{ showSkypeFieldsDesc[key] }}
					</b-form-checkbox>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable vue/no-mutating-props */
import JobtronTable from '@ui/Table'
import JobtronButton from '@ui/Button'

export default {
	name: 'TableSkype', // Раньше был нужен чтобы собирать скайпы, сейчас собираются стажеры для Zoom обучения
	components: {
		JobtronTable,
		JobtronButton,
	},
	props: {
		skypes: Array,
		segments: Object,
		groups: Array,
		month: Object,
		invite_groups: Object,
	},
	data: function () {
		return {
			lang:{
				formatLocale:{
					firstDayOfWeek: 1,
					months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Йюнь', 'Йюль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
					// MMM
					monthsShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Йюн', 'Йюл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
					// dddd
					weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
					// ddd
					weekdaysShort: ['Вос', 'Пон', 'Втр', 'Срд', 'Чтв', 'Пят', 'Суб'],
					// dd
					weekdaysMin: ['Вс', 'По', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
				},
				monthBeforeYear: false,
			},
			mydate: Date.now(),
			showSkypeFields: {},
			showSkypeFieldsDesc: {},
			fields: [], // поля таблицы
			selected: { // отдел для приглашения
				group_id: 0,
				date: null,
				time: '09:30',
			},
			status: 1,
			copied: false,
			checkedBoxes: [],
			checker: false,
			showModal: false,
			showSkypeFieldsModal: false,
			lead: {
				name: '',
				phone: '',
				lang: 1,
				wishtime: 1,
			},
			errors: [],
			records: [],
			disp: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб','', '', 'Сброс', 'Ок'],
			currentDay: 0,
			filter: { // фильтр чекбоксы
				flat: 0,
				kazakh: 0,
				kazrus: 0,
				russian: 0,
				cable: 0,
				lang: 0,
				user_type: 'all',
				wishtime: 0,
				segment: 0,
				dates: [], // выбор нексольких дней
				currentInviteGroup: 0 // select приглашенная Отдел
			},
			user_types: {
				'all' : 'Все типы',
				'office' : 'Офисные',
				'remote' : 'Удаленные',
			},
			projects: {
				0: '',
				1720: 'Каспи',
				1722: 'Детский Мир',
				1724: 'Tailor Suit',
				1726: 'Евраз',
				1728: 'Народный Банк',
				1770: 'Ростелеком',
				1794: 'Альфа/МТС',
				1892: 'Сертификация',
				2080: 'Тинькофф',
				2478: 'OZON 1',
				2480: 'OZON 2',
				2492: 'Хоум Банк',
			},
			langs: {
				0: 'Все языки',
				1: 'Каз',
				2: 'Рус',
				3: 'Каз|Рус',
			},
			countries: {
				'KZ': '🇰🇿',
				'KG': '🇰🇬',
				'UZ': '🇺🇿',
				'RU': '🇷🇺',
				'BY': '🇧🇾',
				'UA': '🇺🇦',
				'UN': '❓',
			},
			wishtimes: {
				0: 'Все графики',
				1: 'с 08:45 - 19:00',
				2: 'с 13:00 - 23:00',
				4: 'c 08:45 - 13:00',
				5: 'c 14:00 - 19:00',
			},
			datepickerLabels: {
				labelPrevDecade: 'Пред 10 лет',
				labelPrevYear: 'Предыдущий год',
				labelPrevMonth: 'Предыдущий месяц',
				labelCurrentMonth: 'Текущий месяц',
				labelNextMonth: 'Следующий месяц',
				labelNextYear: 'Следующий год',
				labelNextDecade: 'След 10 лет',
				labelToday: 'Cегодня',
				labelSelected: 'Выбранная дата',
				labelNoDateSelected: 'Дата не выбрана',
				labelCalendar: 'Календарь',
				labelNav: 'Навигация',
				labelHelp: 'Перемещайтесь по календарю с помощью клавиш со стрелками'
			},
			nets: {
				1 : 'Кабельный интернет',
				2 : 'Кабельный интернет',
				3 : 'Мобильный интернет',
				4 : 'Переносной модем',
				5 : 'Нет интернета',
			},
			filtered: {},
			workDays: 26,
			hasPremission: false,
			totalRows: 1,
			currentPage: 1,
			perPage: 100,
			pageOptions: [5, 10, 15],
		};
	},
	watch: {
		// эта функция запускается при любом изменении данных
		skypes: {
			// the callback will be called immediately after the start of the observation
			deep: true,
			handler () {
				this.filterTable()
			}
		},
		month: {
			// the callback will be called immediately after the start of the observation
			deep: true,
			handler () {
				this.filterTable()
			}
		},
		checker: {
			deep: true,
			handler (val) {
				if(val) {
					this.checkAll();
				}
				else {
					this.unCheckAll();
				}
			}
		},
		filter: {
			handler () {
				this.filterTable()
				this.unCheckAll()
			},
			deep: true
		},
		currentDay: {
			handler () {
				this.filterTable()
			},
		},
		showSkypeFields: {
			handler: function (val) {
				localStorage.showSkypeFields = JSON.stringify(val);
			},
			deep: true
		}
	},

	mounted() {
		this.setDefaultShowFields()
		this.setFields()
		this.setSegments()
		this.filterTable()
	},

	methods: {
		getDates(s, e) {
			for(var a=[],d=new Date(s);d<=new Date(e);d.setDate(d.getDate()+1)){
				a.push(new Date(d));
			}
			return a;
		},
		setSegments() {
			this.segments['0'] = '-'
		},

		setDefaultShowFields() {

			// localStorage.clear();

			if(localStorage.showSkypeFields) {
				this.showSkypeFields = JSON.parse(localStorage.getItem('showSkypeFields'));
			} else {
				this.showSkypeFields = { // Какие поля показывать
					checked: true,
					lead_id: true,
					skyped: true,
					project: true,
					name: true,
					lang: true,
					net: true,
					wishtime: true,
					invited_at: true,
					invite_group: true,
					country: true,
					segment: true,
					resp: true,
					phone: true,
					// file: true,
				};
			}

			this.showSkypeFieldsDesc = {
				checked: 'Номер',
				lead_id: 'Сделка',
				skyped: 'Дата подписи',
				project: 'Проект',
				name: 'ФИО',
				lang: 'Языки',
				net: 'Интернет',
				wishtime: 'График',
				invited_at: 'Приглашен на',
				invite_group: 'Отдел',
				country: 'Cтрана',
				segment: 'Сегмент',
				resp: 'Ответственный',
				phone: 'Телефон',
				// file: 'Файл',
			}

		},

		saveLead() {

			this.axios.post('/timetracking/analytics/recruting/create-lead', {
				name: this.lead.name,
				phone: this.lead.phone,
				lang: this.lead.lang,
				wishtime: this.lead.wishtime,
			})
				.then(() => {


					this.$toast.success('Новый лид сохранен')
					this.skypes.unshift({
						name: this.lead.name,
						phone: this.lead.phone,
						lang: this.lead.lang,
						wishtime: this.lead.wishtime,
						lead_id: 0,
						deal_id: 0,
						checked: false,
						skyped: new Date()
					});

					this.lead = {
						name: '',
						phone: '',
						lang: 1,
						wishtime: 1,
					};

					this.showModal = false

				})
				.catch(() => alert('Ошибка'))
		},

		setFields() {
			let fields = [];

			fields = [
				{
					key: 'checked',
					label: '',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'lead_id',
					label: 'Сделка',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'skyped',
					label: 'Дата подписи',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'project',
					label: 'Проект',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'name',
					label: 'ФИО',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'lang',
					label: 'Языки',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'net',
					label: 'Интернет',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'wishtime',
					label: 'График',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'invited_at',
					label: 'Приглашен на',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'invite_group',
					label: 'Гр.',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'country',
					label: 'Cт',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'segment',
					label: 'Сегмент',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'resp',
					label: 'Отв',
					variant: 'title',
					class: 'text-left t-name'
				},
				{
					key: 'phone',
					label: 'Телефон',
					variant: 'title',
					class: 'text-left t-name'
				},
				// {
				// 	key: 'file',
				// 	label: 'Файл',
				// 	variant: 'title',
				// 	class: 'text-left t-name'
				// },
			];

			this.fields = fields;
		},

		async loadItems() {

			this.workDays = this.month.workDays

			let days = this.month.daysInMonth

			let obj = {};
			obj['headers'] = 'Стажеры'
			for (let i = 1; i <= days; i++) {

				let logins = '';

				this.skypes[i].forEach(el => {
					logins += el.skype + ' ';
				});

				obj[i] = logins
			}
			this.records.push(obj)


		},

		inviteUsers() {

			if(this.selected.date ==  null) {
				this.$toast.info('Выберите дату приглашения')
				return '';
			}

			this.axios.post('/timetracking/analytics/invite-users', {
				users: this.checkedBoxes,
				group_id: this.selected.group_id,
				date: this.selected.date,
				time: this.selected.time,
			})
				.then(response => {
					if(response.data.code == 201) {
						this.$toast.error('Отдел не найден. Обратитесь к разработчику')
					}

					if(response.data.code == 202) {
						this.$toast.error('Не приглашены. В отделе не указана ссылка на Zoom конференцию.')
					}

					if(response.data.code == 200) {
						this.$toast.success('Успешно приглашены')
						this.checkedBoxes = []
					}

				})
				.catch(() => alert('Ошибка'))
		},

		filterTable() {
			this.workDays = this.month.workDays

			this.records = []

			var dates = this.getDates(this.filter.dates[0],this.filter.dates[1]);

			this.filtered = this.skypes.filter(el => {

				let a = true

				let lang = false
				if(this.filter.lang != 0) {
					lang = lang || el.lang == this.filter.lang
					a = a && lang
				}

				let wishtime = false
				if(this.filter.wishtime != 0) {
					wishtime = wishtime || el.wishtime == this.filter.wishtime
					a = a && wishtime
				}

				let segment = false
				if(this.filter.segment != 0) {
					segment = segment || el.segment == this.filter.segment
					a = a && segment
				}

				let group = false;
				if(this.filter.currentInviteGroup != 0) {
					group = group || el.invite_group_id == Number(this.filter.currentInviteGroup)
					a = a && group
				}

				let user_type = false
				if(this.filter.user_type != 'all') {
					user_type = user_type || el.user_type == this.filter.user_type
					a = a && user_type
				}

				let ld = false;
				if(dates.length > 0) {
					dates.forEach(day => {
						ld = ld || day.getDate() == this.$moment(el.skyped_old, 'YYYY-MM-DD HH:mm:ss').date()
					})
				} else {
					ld = true
				}

				return a && ld
			}).sort((a, b) => {
				const aTS = this.$moment(a.skyped, 'DD.MM.YYYY HH:mm')
				const bTS = this.$moment(b.skyped, 'DD.MM.YYYY HH:mm')
				return bTS - aTS
			})

			this.totalRows =  this.filtered.length
			this.records = this.filtered
		},

		clear() {
			this.filter = {
				flat: 0,
				kazakh: 0,
				kazrus: 0,
				russian: 0,
				cable: 0,
				lang: 0,
				user_type: 'all',
				wishtime: 0,
				segment: 0,
				dates: [], // выбор нексольких дней
				currentInviteGroup: 0 // select приглашенная Отдел
			}
			this.currentDay = 0
		},

		checkAll() {    // отметить все при выборе
			this.checkedBoxes = []
			this.records.forEach(el => {
				el.checked = true
				this.checkedBoxes.push(el.id);
			})
		},

		unCheckAll() {
			this.checkedBoxes = []
			this.records.forEach(el => {
				el.checked = false
			})
		},

		copy() {

			let testingCodeToCopy = document.querySelector('#copytext')
			testingCodeToCopy.setAttribute('type', 'text')

			let logins = '';

			this.records.forEach(el => {
				logins += el.skype + ' ';
			});

			testingCodeToCopy.value = logins


			testingCodeToCopy.select()
			document.execCommand('copy');

			testingCodeToCopy.setAttribute('type', 'hidden')
			window.getSelection().removeAllRanges()


			// this.copied = true;
			// setTimeout(() => this.copied = false,3000)
			// this.$root.$emit('bv::enable::tooltip', '#text' + key)

		},

		detailsClassFn(item) {
			// item: the row's item data
			// rowType: a string describing the `<tr>` type


			if (item.invited == 0) return 'bg-red'
			if (item.invited == 1) return 'bg-green'
			if (item.invited == 2) return 'bg-green-2'
			if (item.invited == 3) {
				if(item.user_type == 'office') {
					return 'bg-green-3 office'
				} else {
					return 'bg-green-3'
				}
			}
			if (item.invited == 4) return 'bg-green-4'
		},

		formatDate(date) {
			return date.getDate();
		}
	}
};
</script>

<style lang="scss">

.skypo .fa-cog {
	display: block;
	color: #fff;
}
.resp_user {
	font-size: 0.6rem;
	line-height: 1em;
}
@for $i from 2 through 15 {
 .hide-#{$i} {
  td:nth-child(#{$i}),
  th:nth-child(#{$i}) {
    display: none !important;
  }
 }
}
.skypes-table {
	th:first-child,
	td:first-child {
		width: 15px;
	}
}


.TableSkype{
	&-table{
		overflow-x: auto;
	}

	&-maw{
		max-width: 175px;
		margin: 0 auto;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}

	&-select{
		width: 125px;
	}

	.JobtronTable-th,
	.JobtronTable-td{
		padding: 2px 4px;
		font-size: 14px;
	}
	.JobtronTable-td{
		background-color: transparent;
	}
	.pagination{
		padding: 0;
		.page-item .page-link{
			width: 40px;
			height: 40px;
		}
	}
	.country {
		font-size: 16px;
	}

	.b-calendar-grid-body{
		.row{
			flex-flow: row nowrap;
		}
		.col{
			flex: 0 0 14.285%;
			.btn{
				padding: 0;
			}
		}
	}
}
</style>
<style lang="scss" scoped>
a{
	&:hover{
		color: #0056b3;
	}
}
.skypo .btn {
	padding: 0.375rem .75rem;
	margin: 0;
}
.my-table-max {
	max-height: inherit !important;
	.day {
		padding: 0 !important;
		text-align: center;

		&.table-success {
			background-color: #29dc29 !important;
		}

		&.table-danger {
			background-color: #e4585f !important;
		}

		&.Sat,
		&.Sun {
			background-color: #FEF2CB;
		}
	}
}



.cell-input {
	background: none;
	border: none;
	text-align: center;
	-moz-appearance: textfield;
	font-size: .8rem;
	font-weight: normal;
	padding: 0;
	color: #000;
	border-radius: 0;

	&:focus {
		outline: none;
	}

	&::-webkit-outer-spin-button,
	&::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
}
.my-table td div {
	position: relative !important;
	padding: 0;
	// user-select: none;
	// width: min-content;
	// white-space: pre-line;
}

.my-table-max .day.Sat, .my-table-max .day.Sun {
	background-color: #cedaeb;
	border-color: #dfecfe;
}

input[type="time"]::-webkit-calendar-picker-indicator {
	background: none;
	display:none;
}
.nett {
	opacity: 0;
	transition: 0.3 ease all;
	font-size: 20px;
	font-weight: 600;
}
.nett.copied {
	opacity: 1;
	color:#007bff;
}
.imagy {
	cursor: pointer;
}
.imagy img {
	display:block;
	width: 400px;
	box-shadow: 0 0 15px #ddd;
	position:fixed;
	max-width: 400px;
	top: 20px;
	left: 300px;
	display: none;
	border-radius: 5px;
	z-index: 999;
	pointer-events: none;
}
.imagy2 img{
	transform: rotate(-90deg);
}
.imagy3 img{
	transform: rotate(90deg);
}
.imagy4 img{
	transform: rotate(180deg);
}
.imagy:hover img {
	display:block;
}
.bottomvars {
	position: fixed;
	bottom: 0;
	width: calc(100vw - 127px);
	left: 70px;
	z-index: 999;
	background: #d6dfe5;
	border-top: 1px solid #e9e9e9;
	padding: 7px 30px;
}
.blues div {
	color: #2196f3;
	cursor: pointer;
}
.p-o {
	margin: 0 -10px;
	width: 100%;
}
.circle {
	width: 9px;
	height: 9px;
	border-radius: 10px;
	margin-right: 5px;
}
.my-table tr .badge {
	opacity: 1;
}
</style>
