<template>
	<div v-if="positions">
		<div class="row mb-2">
			<div class="col-3 text-left">
				<b-input-group size="sm">
					<b-form-input
						id="filterInput"
						v-model="filter.email"
						type="search"
						placeholder="Поиск"
					/>
					<b-input-group-append>
					<!-- <b-button :disabled="!filter.email" @click="filter.email = ''">Очистить</b-button> -->
					</b-input-group-append>
				</b-input-group>
			</div>
			<div class="col-2">
				<b-form-select
					v-model="filter.group"
					:options="groups"
					size="sm"
				/>
			</div>
			<div class="col-2  d-flex align-items-center">
				<b-form-select
					v-model="tableFilter"
					:options="tableFilters"
					size="sm"
					@change="getUsers()"
				/>
			</div>
			<div class="col-2  d-flex align-items-center">
				<b-form-select
					v-model="position"
					:options="jobFilters"
					size="sm"
					@change="getUsers()"
				/>
			</div>
			<div class="col-3 justify-content-end d-flex align-items-start">
				<a
					href="/timetracking/create-person"
					class="btn btn-success rounded"
				>Пригласить</a>
				<b-button
					class="btn-primary rounded ml-1"
					title="Показывать поля"
					@click="showModal = !showModal"
				>
					<i
						class="fa fa-eye"
						aria-hidden="true"
					/>
				</b-button>
				<b-button
					class="btn-primary rounded ml-1"
					title="Дополнительные фильтры таблицы"
					@click="showFilterModal = !showFilterModal"
				>
					<i
						class="fa fa-filter"
						aria-hidden="true"
					/>
				</b-button>
			</div>
			<div class="col-12">
				<p class="mt-2 mr-2 fz14 mb-0">
					<b> Все:</b> {{ filtered.length }}  <b>Рес:</b> {{ staff_res }}
				</p>
			</div>
		</div>

		<div style="clear: both;" />

		<div class="table-responsive ul table-container">
			<b-table
				id="table"
				ref="table"
				bordered
				show-empty
				stacked="md"
				:items="filtered"
				:fields="fields2"
				:current-page="currentPage"
				:per-page="perPage"
				:filter="filter"
				:filter-included-fields="filterOn"
				:sort-by.sync="sortBy"
				:sort-desc.sync="sortDesc"
				:sort-direction="sortDirection"
				empty-filtered-text="Еще не найдено ни одной записи"
				empty-text="Не найдено ни одной записи"
				:class="{
					'hide-1': !showFields.number,
					'hide-2': !showFields.id,
					'hide-3': !showFields.email,
					'hide-4': !showFields.name,
					'hide-5': !showFields.last_name,
					'hide-6': !showFields.groups,
					'hide-7': !showFields.register_date,
					'hide-8': !showFields.fire_date,
					'hide-9': !showFields.fire_cause,
					'hide-10': !showFields.user_type,
					'hide-11': !showFields.segment,
					'hide-12': !showFields.applied,
					'hide-13': !showFields.full_time,
				}"
				@filtered="onFiltered"
			>
				<template #cell(index)="row">
					{{ row.index + 1 }}
				</template>
				<template #cell(id)="data">
					<a
						:href="idLink(data.item)"
						target="_blank"
					>
						{{ data.value }}
					</a>
				</template>
				<template #cell(email)="data">
					<a
						:href="'/timetracking/edit-person?id='+data.item.id"
						target="_blank"
					>{{ data.value }}</a>
				</template>

				<template #cell(segment)="data">
					<div>
						<div v-if="segments.hasOwnProperty(data.value)">
							{{ segments[data.value] }}
						</div>
						<div v-else>
							{{ data.value }}
						</div>
					</div>
				</template>

				<template #cell(groups)="data">
					<b-badge
						v-for="group_id in data.value"
						:key="group_id"
						variant="primary"
						class="mr-1"
					>
						{{ groups[group_id] }}
					</b-badge>
				</template>
				<template #cell(created_at)="row">
					{{ row.value ? $moment(row.value).format('DD.MM.YYYY') : '' }}
				</template>
				<template #cell(deleted_at)="row">
					{{ row.value ? $moment(row.value).format('DD.MM.YYYY') : '' }}
				</template>
				<template #cell(applied)="row">
					{{ row.value ? $moment(row.value).format('DD.MM.YYYY') : '' }}
				</template>
				<template #cell(full_time)="data">
					<div v-if="data.value == 1">
						Full
					</div>
					<div v-else>
						Part
					</div>
				</template>
				<template #cell(fire_cause)="data">
					<div v-if="tableFilter != 'active'">
						{{ data.value }}
					</div>
				</template>
				<template #cell(restored_at)="data">
					{{ data.value }}
				</template>
			</b-table>
		</div>

		<div class="my-2 d-flex align-items-center justify-content-end">
			<a
				v-if="currentUser === 18 && isBP"
				href="javasctipt:void(0)"
				class="btn btn-success btn-sm mr-a rounded d-block"
				@click.prevent="exportData()"
			>
				<i class="far fa-file-excel" /> Экспорт
			</a>
			<div>
				<b-pagination
					v-model="currentPage"
					:total-rows="totalRows"
					:per-page="perPage"
					align="fill"
					size="sm"
					class="my-0 p-0"
				/>
			</div>
		</div>

		<b-modal
			v-model="showModal"
			title="Настройка списка «Сотрудники»"
			ok-text="Закрыть"
			size="lg"
			@ok="showModal = !showModal"
		>
			<template v-for="error in errors">
				<b-alert
					:key="error"
					show
					variant="danger"
				>
					{{ error }}
				</b-alert>
			</template>

			<div class="row">
				<div class="col-md-4 mb-2">
					<b-form-checkbox
						v-model="showFields.number"
						:value="true"
						:unchecked-value="false"
					>
						Номер
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.id"
						:value="true"
						:unchecked-value="false"
					>
						id
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.email"
						:value="true"
						:unchecked-value="false"
					>
						Email
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.name"
						:value="true"
						:unchecked-value="false"
					>
						Имя
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.full_time"
						:value="true"
						:unchecked-value="false"
					>
						Full / Part-time
					</b-form-checkbox>
				</div>
				<div class="col-md-4 mb-2">
					<b-form-checkbox
						v-model="showFields.last_name"
						:value="true"
						:unchecked-value="false"
					>
						Фамилия
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.groups"
						:value="true"
						:unchecked-value="false"
					>
						Отделы
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.register_date"
						:value="true"
						:unchecked-value="false"
					>
						Дата регистрации
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.fire_date"
						:value="true"
						:unchecked-value="false"
					>
						Дата увольнения
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.fire_cause"
						:value="true"
						:unchecked-value="false"
					>
						Причина увольнения
					</b-form-checkbox>
				</div>

				<div class="col-md-4 mb-2">
					<b-form-checkbox
						v-model="showFields.user_type"
						:value="true"
						:unchecked-value="false"
					>
						Remote/Office
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.segment"
						:value="true"
						:unchecked-value="false"
					>
						Сегмент
					</b-form-checkbox>
					<b-form-checkbox
						v-model="showFields.applied"
						:value="true"
						:unchecked-value="false"
					>
						Дата принятия
					</b-form-checkbox>
				</div>
			</div>
		</b-modal>

		<b-modal
			v-model="showFilterModal"
			title="Фильтр «Сотрудники»"
			ok-text="Применить"
			size="md"
			@ok="applyFilter"
		>
			<div class="row">
				<div class="col-md-6 mb-2">
					<div class="d-flex align-items-center">
						<input
							v-model="active.date"
							type="checkbox"
							class="mr-3"
						>
						<p
							class="mb-0 pointer"
							@click="toggleActive('date')"
						>
							Дата регистрации
						</p>
					</div>
				</div>

				<div class="col-6">
					<div
						class="relative ooooo"
						:class="{'active': active.date}"
					>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>От</label> <input
								v-model="filter.start_date"
								class="form-control mb-1 form-control-sm"
								type="date"
							>
						</div>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>До</label> <input
								v-model="filter.end_date"
								class="form-control form-control-sm"
								type="date"
							>
						</div>
					</div>
				</div>
			</div>

			<div class="row mt-2">
				<div class="col-md-6 mb-2">
					<div class="d-flex align-items-center">
						<input
							v-model="active.date_deactivate"
							type="checkbox"
							class="mr-3"
						>
						<p
							class="mb-0 pointer"
							@click="toggleActive('date_deactivate')"
						>
							Дата увольнения
						</p>
					</div>
				</div>

				<div class="col-6">
					<div
						class="relative ooooo"
						:class="{'active': active.date_deactivate}"
					>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>От</label> <input
								v-model="filter.start_date_deactivate"
								class="form-control mb-1 form-control-sm"
								type="date"
							>
						</div>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>До</label> <input
								v-model="filter.end_date_deactivate"
								class="form-control form-control-sm"
								type="date"
							>
						</div>
					</div>
				</div>
			</div>

			<div
				v-if="tableFilter != 'trainees'"
				class="row mt-2"
			>
				<div class="col-md-6 mb-2">
					<div class="d-flex align-items-center">
						<input
							v-model="active.date_applied"
							type="checkbox"
							class="mr-3"
						>
						<p
							class="mb-0 pointer"
							@click="toggleActive('date_applied')"
						>
							Дата принятия
						</p>
					</div>
				</div>

				<div class="col-6">
					<div
						class="relative ooooo"
						:class="{'active': active.date_applied}"
					>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>От</label> <input
								v-model="filter.start_date_applied"
								class="form-control mb-1 form-control-sm"
								type="date"
							>
						</div>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>До</label> <input
								v-model="filter.end_date_applied"
								class="form-control form-control-sm"
								type="date"
							>
						</div>
					</div>
				</div>
			</div>

			<div
				v-if="tableFilter != 'trainees'"
				class="row mt-2"
			>
				<div class="col-md-6 mb-2">
					<div class="d-flex align-items-center">
						<input
							v-model="active.date_reapplied"
							type="checkbox"
							class="mr-3"
						>
						<p
							class="mb-0 pointer"
							@click="toggleActive('date_reapplied')"
						>
							Дата восстановления
						</p>
					</div>
				</div>

				<div class="col-6">
					<div
						class="relative ooooo"
						:class="{'active': active.date_reapplied}"
					>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>От</label> <input
								v-model="filter.start_date_reapplied"
								class="form-control mb-1 form-control-sm"
								type="date"
							>
						</div>
						<div class="d-flex align-items-center">
							<label
								for=""
								class=" mr-2 mb-0"
							>До</label> <input
								v-model="filter.end_date_reapplied"
								class="form-control form-control-sm"
								type="date"
							>
						</div>
					</div>
				</div>
			</div>

			<div class="row mt-2">
				<div class="col-md-6 mb-2">
					<p>Сегмент</p>
				</div>

				<div class="col-6">
					<div class="d-flex align-items-center">
						<select
							v-model="filter.segment"
							class="form-control mb-1 form-control-sm"
						>
							<option value="0">
								Все сегменты
							</option>
							<option value="1">
								Кандидаты на вакансию (таргет)
							</option>
							<option value="2">
								Кандидаты на вакансию (hh, nur, job)
							</option>
							<option value="3">
								Кандидаты на вакансию (promo акции)
							</option>
							<option value="4">
								Кандидаты на вакансию (месенджеры)
							</option>
							<option value="5">
								Кандидаты на вакансию (Гарантия трудоустройства)
							</option>
							<option value="6">
								Кандидаты на вакансию (Участники семинаров, форумов, встреч)
							</option>
						</select>
					</div>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */

export default {
	name: 'PageUserlist',
	props: {
		// eslint-disable-next-line vue/prop-name-casing
		is_admin: {
			type: Boolean,
			default: false,
		},
		subdomain: {
			type: String,
			default: 'nosub'
		},
		positions: {
			type: Array,
			default: () => []
		},
	},
	data() {
		return {
			myPositions: [],
			position: 0,
			jobFilters: [{ text: 'Должность', value: 0 }],
			sel: false,
			newtime: '',
			auth_token: '',
			showFilterModal: false, /// Фильтры
			showModal: false, /// Какие поля показывать
			showFields: {},
			errors: [],
			useractive: {
				name: '',
				day: '',
				time: '',
			},
			tableFilter: 'active',
			segments: {
				0: 'Нет сегмента',
				1: 'Таргет',
				2: 'HH',
				3: 'promo',
				4: 'messenger',
				5: 'Гарантия труд',
				6: 'HH',
				7: 'Муса',
				8: 'Алина',
				9: 'Салтанат',
				10: 'Акжол',
				11: 'Дархан',
				12: 'Салтанат',
				13: 'Сайт BP',
				14: 'Вход. обр.',
			},
			tableFilters: {
				'all': 'Все',
				'active': 'Работающие',
				'deactivated': 'Уволенные',
				'reactivated': 'Восстановленные',
				'trainees': 'Стажеры',
				'nonfilled': 'Незаполненные',
			},
			mount: 'Январь',
			items: [],
			fields: [
				{
					key: 'index',
					label: '№'
				},
				{
					key: 'id',
					label: 'id'
				},
				{
					key: 'email',
					label: 'Email'
				},
				{
					key: 'last_name',
					label: 'Фамилия',
					sortable: true
				},
				{
					key: 'name',
					label: 'Имя',
					sortable: true
				},
				{
					key: 'groups',
					label: 'Отделы',
					sortable: true
				},
				{
					key: 'created_at',
					label: 'Дата регистрации (сделка)',
					sortable: true,
				},
				{
					key: 'deleted_at',
					label: 'Дата увольнения',
					sortable: true,
				},
				{
					key: 'fire_cause',
					label: 'Причина увольнения',
					sortable: true
				},
				{
					key: 'user_type',
					label: 'Тип',
					sortable: true
				},
				{
					key: 'segment',
					label: 'Сегмент',
					sortable: true
				},
				{
					key: 'applied',
					label: 'Дата принятия',
					sortable: true,
				},
				{
					key: 'full_time',
					label: 'Full/Part',
					sortable: true
				},
			],
			groups: {
				0: 'Выберите отдел'
			},
			currentGroup: 0,
			currentPage: 1,
			perPage: 20,
			pageOptions: [5, 10, 15],
			sortBy: 'created_at',
			sortDesc: true,
			sortDirection: 'desc',
			can_login_users: [],
			currentUser: null,
			filter: {
				email: '',
				group: 0,
				start_date: null,
				end_date: '2030-01-01',
				start_date_deactivate: '2015-01-01',
				end_date_deactivate: '2030-01-01',
				start_date_applied: '2015-01-01',
				end_date_applied: '2030-01-01',
				start_date_reapplied: '2015-01-01',
				end_date_reapplied: '2030-01-01',
				segment: 0
			},
			active: {
				date: false,
				date_deactivate: false,
				date_applied: false,
				date_reapplied: false,
			},
			filterOn: [],
			value: [],
			options: [],
			infoModal: {
				id: 'info-modal',
				title: '',
				content: ''
			},
			isRestored: false,
		}
	},
	computed: {
		sortOptions() {
			return this.fields
				.filter(f => f.sortable)
				.map(f => {
					return {
						text: f.label,
						value: f.key
					}
				})
		},
		searchText(){
			return this.filter.email.toLowerCase()
		},

		filtered() {
			if(!this.items) return []
			return this.items.filter(el => {
				if (el.FULLNAME == null)  el.FULLNAME = ''
				if (el.FULLNAME2 == null)  el.FULLNAME2 = ''
				if (el.fullname == null)  el.fullname = ''
				if (el.fullname2 == null)  el.fullname2 = ''
				if (el.last_name == null)  el.last_name = ''
				if (el.name == null)  el.name = ''

				if(Number(this.filter.group) !== 0) {
					return el.groups.includes(Number(this.filter.group))
						&& (el.email.toLowerCase().indexOf(this.searchText) > -1
						|| el.FULLNAME.toLowerCase().indexOf(this.searchText) > -1
						|| el.FULLNAME2.toLowerCase().indexOf(this.searchText) > -1
						|| el.fullname.toLowerCase().indexOf(this.searchText) > -1
						|| el.fullname2.toLowerCase().indexOf(this.searchText) > -1
						|| el.last_name.toLowerCase().indexOf(this.searchText) > -1
						|| el.name.toLowerCase().indexOf(this.searchText) > -1
						|| el.id.toString().indexOf(this.searchText) > -1)
				}
				else {
					return el.email.toLowerCase().indexOf(this.searchText) > -1
						|| el.FULLNAME.toLowerCase().indexOf(this.searchText) > -1
						|| el.FULLNAME2.toLowerCase().indexOf(this.searchText) > -1
						|| el.fullname.toLowerCase().indexOf(this.searchText) > -1
						|| el.fullname2.toLowerCase().indexOf(this.searchText) > -1
						|| el.last_name.toLowerCase().indexOf(this.searchText) > -1
						|| el.name.toLowerCase().indexOf(this.searchText) > -1
						|| el.id.toString().indexOf(this.searchText) > -1
				}
			})
		},
		staff_res(){
			let res = 0
			this.filtered.forEach(user => {
				if(user.full_time == 1) {
					res += 1
				}
				else {
					res += 0.5
				}
			})
			return res
		},
		totalRows(){
			return this.filtered.length || 0
		},
		isBP(){
			return ['test', 'bp'].includes(location.hostname.split('.')[0])
		},
		fields2(){
			return this.isRestored ? [
				...this.fields,
				{
					key: 'restored_at',
					label: 'Восстановлен'
				}
			] : this.fields
		}
	},
	watch: {
		showFields: {
			handler: function (val) {
				localStorage.showFields = JSON.stringify(val)
			},
			deep: true
		},
		positions(){
			this.init()
		}
	},
	created() {
		if(this.positions){
			this.init()
		}
	},
	mounted() {},
	methods: {
		init(){
			this.myPositions = this.positions
			this.myPositions.forEach(value => {
				this.jobFilters.push({ text: value.position, value: value.id })
			})
			this.getUsers()
			this.setDefaultShowFields()
		},

		setDefaultShowFields() {
			if(localStorage.showFields) {
				this.showFields = JSON.parse(localStorage.getItem('showFields'))
			}
			else {
				this.showFields = { // Какие поля показывать
					number: true,
					id: true,
					email: true,
					name: true,
					last_name: true,
					groups: true,
					register_date: true,
					fire_date: true,
					fire_cause: false,
					user_type: false,
					segment: false,
					applied: false,
					full_time: false,
				}
			}
		},

		resetInfoModal() {
			this.infoModal.title = ''
			this.infoModal.content = ''
		},

		applyFilter() {
			this.showFilterModal = !this.showFilterModal
			//this.$refs.table.refresh()
			this.getUsers()
		},

		getUsers() {
			//if(this.filter.start_date.length > 10 || this.filter.end_date.length > 10) return ;
			//if(this.filter.start_date[0] == '0' || this.filter.end_date[0] == '0' || this.filter.end_date[0] == '1')  return ;
			let filter = {
				filter: this.tableFilter,
				segment: this.filter.segment,
				job: this.position,
			}

			if(this.active.date) {
				filter.start_date = this.filter.start_date
				filter.end_date = this.filter.end_date
			}

			if(this.active.date_deactivate && this.tableFilter != 'active') {
				filter.start_date_deactivate = this.filter.start_date_deactivate
				filter.end_date_deactivate = this.filter.end_date_deactivate
			}

			if(this.active.date_applied && this.tableFilter != 'trainees') {
				filter.start_date_applied = this.filter.start_date_applied
				filter.end_date_applied = this.filter.end_date_applied
			}

			if(this.active.date_reapplied && this.tableFilter != 'trainees') {
				filter.start_date_reapplied = this.filter.start_date_reapplied
				filter.end_date_reapplied = this.filter.end_date_reapplied
			}

			this.isRestored = this.active.date_reapplied && this.tableFilter != 'trainees'

			this.axios.post('/timetracking/get-persons', filter).then(response => {
				const users = []
				response.data.users.slice().reverse().forEach(user => {
					const exists = users.find(u => u.id === user.id)
					if(!exists) {
						users.push(user)
					}
				})
				this.items = users.reverse()
				this.groups = response.data.groups
				this.segments = response.data.segmentsё

				this.can_login_users = response.data.can_login_users
				this.auth_token = response.data.auth_token
				this.currentUser = response.data.currentUser
				if(this.filter.start_date == null) {
					this.filter.start_date = response.data.start_date
					this.filter.end_date = response.data.end_date
					this.filter.start_date_deactivate = response.data.start_date
					this.filter.end_date_deactivate = response.data.end_date
					this.filter.start_date_applied = response.data.start_date
					this.filter.end_date_applied = response.data.end_date
					this.filter.start_date_reapplied = response.data.start_date
					this.filter.end_date_reapplied = response.data.end_date
				}
			})
		},

		exportData() {
			var link = '/timetracking/get-persons'
			link += '?filter=' + this.tableFilter

			if(this.active.date) {
				link += '&start_date=' + this.filter.start_date
				link += '&end_date=' + this.filter.end_date
			}

			if(this.active.date_deactivate && this.tableFilter != 'active') {
				link += '&start_date_deactivate=' + this.filter.start_date_deactivate
				link += '&end_date_deactivate=' + this.filter.end_date_deactivate
			}

			if(this.active.date_applied && this.tableFilter != 'trainees') {
				link += '&start_date_applied=' + this.filter.start_date_applied
				link += '&end_date_applied=' + this.filter.end_date_applied
			}

			if(this.active.date_reapplied && this.tableFilter != 'trainees') {
				link += '&start_date_reapplied=' + this.filter.start_date_reapplied
				link += '&end_date_reapplied=' + this.filter.end_date_reapplied
			}

			link += '&segment=' + this.filter.segment
			link += '&excel=1'
			window.location.href = link
		},

		toggleActive(item) {
			this.active[item] = !this.active[item]
		},

		onFiltered() {
			this.currentPage = 1
		},

		idLink(user){
			if(!(this.is_admin && this.subdomain == 'bp')) return `/timetracking/edit-person?id=${user.id}`
			const testhost = location.host.split('.')
			testhost[0] = 'test'
			return `${location.protocol}//${testhost.join('.')}/login-as-employee/${user.id}?auth=${this.auth_token}`
		}
	}
}
</script>

<style lang="scss">
.ul table {
	border-radius: 3px;
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-bottom: 1px solid #dee2e6;
}

.ant-btn.ant-btn-primary {
	display: inline;
}
.ant-btn {
	display: none;
}

@for $i from 1 through 13 {
 table.hide-#{$i} {
	td:nth-child(#{$i}),
	th:nth-child(#{$i}) {
		display: none;
	}
 }
}
.fz14 {
	font-size: 14px;
}
.relative.ooooo {
	position: relative;
	transition: 0.5s ease all;
	height: 0;
		overflow: hidden;
	&.active {
		height: auto;
		overflow: initial;
	}
}
</style>
