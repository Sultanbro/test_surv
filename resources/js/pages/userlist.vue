<template>
	<div
		v-if="positions"
		class="UserList"
	>
		<div class="row mb-2">
			<div class="col-3 text-left">
				<UserListFilter
					v-model="filter"
					:search="search"
					:groups="groupOptions"
					:positions="positionOptions"
					:user-types="userOptions"
					:segments="activeSegments"
					class="mb-2"
					@search="search = $event"
				/>
			</div>
			<div class="col-2">
				<b-button
					class="btn-primary rounded ml-1"
					title="Показывать поля"
					@click="showModal = !showModal"
				>
					<i
						class="fa fa-cog"
						aria-hidden="true"
					/>
				</b-button>
			</div>
			<div class="col-2  d-flex align-items-center" />
			<div class="col-2  d-flex align-items-center" />
			<div class="col-3 justify-content-end d-flex align-items-start">
				<a
					href="/timetracking/create-person"
					class="btn btn-success rounded"
				>Пригласить</a>
			</div>
			<div class="col-12">
				<p class="mt-2 mr-2 fz14 mb-0">
					<b> Все:</b> {{ items.length }}  <b>Рес:</b> {{ staff_res }}
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
				:items="items"
				:fields="fields2"
				:sort-by.sync="sortBy"
				:sort-desc.sync="sortDesc"
				sort-direction="desc"
				empty-text="Не найдено ни одной записи"
			>
				<template #cell(index)="row">
					{{ ((currentPage - 1) * perPage) + (row.index + 1) }}
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
						<div v-if="segments.find(segment => segment.id === data.value)">
							{{ data.value === 0 || data.value === '0' ? 'Принят через Jobtron' : segments.find(segment => segment.id === data.value).name }}
						</div>
						<div v-else>
							{{ data.value === 0 || data.value === '0' ? 'Принят через Jobtron' : data.value }}
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
				<template #cell(position)="data">
					{{ data.value }}
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
					<div v-if="filter.userType != 'active'">
						{{ data.value }}
					</div>
				</template>
				<template #cell(restored_at)="data">
					{{ data.value ? $moment(data.value).format('DD.MM.YYYY') : '' }}
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
						v-model="showFields.position"
						:value="true"
						:unchecked-value="false"
					>
						Должность
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
				</div>

				<div class="col-md-4 mb-2">
					<b-form-checkbox
						v-model="showFields.fire_cause"
						:value="true"
						:unchecked-value="false"
					>
						Причина увольнения
					</b-form-checkbox>
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
	</div>
</template>

<script>
/* eslint-disable camelcase */

import UserListFilter from '@/components/pages/UserList/UserListFilter.vue'

export default {
	name: 'PageUserlist',
	components: {
		UserListFilter,
	},
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
			auth_token: '',
			showModal: false, /// Какие поля показывать
			showFields: {},
			segments: [],
			tableFilters: {
				'all': 'Все',
				'active': 'Работающие',
				'deactivated': 'Уволенные',
				'reactivated': 'Восстановленные',
				'trainees': 'Стажеры',
				'nonfilled': 'Незаполненные',
			},
			items: [],
			groups: {
				0: 'Выберите отдел'
			},
			currentPage: 1,
			perPage: 20,
			pageOptions: [5, 10, 15],
			sortBy: 'created_at',
			sortDesc: true,
			totalRows: 0,
			currentUser: null,
			search: '',
			seatchTimeout: null,
			filter: {
				group: 0,
				position: 0,
				userType: 'active',
				register: ['', ''],
				restore: ['', ''],
				fire: ['', ''],
				applied: ['', ''],
				segment: [],
				type: '',
				fullpart: '',
				notrainees: false,
			},
			isRestored: false,
			loading: false,
		}
	},
	computed: {
		searchText(){
			return this.search.toLowerCase().trim()
		},
		staff_res(){
			let res = 0
			this.items.forEach(user => {
				if(user.full_time == 1) {
					res += 1
				}
				else {
					res += 0.5
				}
			})
			return res
		},
		isBP(){
			return ['test', 'bp'].includes(location.hostname.split('.')[0])
		},
		fields2(){
			const fields = []
			if(this.showFields.number) fields.push({
				key: 'index',
				label: '№'
			})
			if(this.showFields.id) fields.push({
				key: 'id',
				label: 'id'
			})
			if(this.showFields.email) fields.push({
				key: 'email',
				label: 'Email'
			})
			if(this.showFields.last_name) fields.push({
				key: 'last_name',
				label: 'Фамилия',
				sortable: true
			})
			if(this.showFields.name) fields.push({
				key: 'name',
				label: 'Имя',
				sortable: true
			})
			if(this.showFields.groups) fields.push({
				key: 'groups',
				label: 'Отделы',
				sortable: true
			})
			if(this.showFields.position) fields.push({
				key: 'position',
				label: 'Должность',
				sortable: true
			})
			if(this.showFields.register_date) fields.push({
				key: 'created_at',
				label: 'Дата регистрации (сделка)',
				sortable: true,
			})
			if(this.showFields.fire_date) fields.push({
				key: 'deleted_at',
				label: 'Дата увольнения',
				sortable: true,
			})
			if(this.showFields.fire_cause) fields.push({
				key: 'fire_cause',
				label: 'Причина увольнения',
				sortable: true
			})
			if(this.showFields.user_type) fields.push({
				key: 'user_type',
				label: 'Тип',
				sortable: true
			})
			if(this.showFields.segment) fields.push({
				key: 'segment',
				label: 'Сегмент',
				sortable: true
			})
			if(this.showFields.segment) fields.push({
				key: 'segment',
				label: 'Сегмент',
				sortable: true
			})
			if(this.showFields.applied) fields.push({
				key: 'applied',
				label: 'Дата принятия',
				sortable: true,
			})
			if(this.showFields.full_time) fields.push({
				key: 'full_time',
				label: 'Full/Part',
				sortable: true
			})
			if(this.isRestored) fields.push({
				key: 'restored_at',
				label: 'Восстановлен'
			})

			return fields
		},
		activeSegments(){
			return this.segments.slice().filter(segment => segment.active)
		},
		groupOptions(){
			return Object.keys(this.groups).map(key => ({value: key, title: this.groups[key]}))
		},
		userOptions(){
			return Object.keys(this.tableFilters).map(key => ({value: key, title: this.tableFilters[key]}))
		},
		positionsMap(){
			const map = {}
			const positions = this.positions || []
			positions.forEach(pos => {
				map[pos.id] = pos
			})
			return map
		},
		positionOptions(){
			const positions = this.positions || []
			return [
				{ title: 'Должность', value: 0 },
				...positions.map(pos => ({
					value: pos.id,
					title: pos.position
				}))
			]
		},
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
		},
		filter(){
			this.getUsers()
		},
		currentPage(){
			this.getUsers()
		},
		sortBy(){
			this.currentPage = 1
			this.getUsers()
		},
		sortDesc(){
			this.currentPage = 1
			this.getUsers()
		},
		searchText(){
			clearTimeout(this.seatchTimeout)
			this.seatchTimeout = setTimeout(() => {
				this.currentPage = 1
				this.getUsers()
			}, 500);
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
					position: true,
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

		getUsers() {
			if(this.loading) return
			this.items = []
			this.loading = true
			const loader = this.$loading.show();
			//if(this.filter.start_date.length > 10 || this.filter.end_date.length > 10) return ;
			//if(this.filter.start_date[0] == '0' || this.filter.end_date[0] == '0' || this.filter.end_date[0] == '1')  return ;
			const filter = {
				filter: this.filter.userType,
				segment: this.filter.segment.map(segment => segment.id),
				job: this.filter.position,
				notrainees: this.filter.notrainees,
				type: this.filter.type,
				part: this.filter.fullpart,
				search: this.searchText,
				page: this.currentPage,
				perPage: this.perPage,
				sortBy: this.sortBy,
				sortDirection: this.sortDesc ? 'desc' : 'asc',
			}

			if(this.filter.register[0] && this.filter.register[1]) {
				filter.start_date = this.DMY2YMD(this.filter.register[0])
				filter.end_date = this.DMY2YMD(this.filter.register[1])
			}

			if(this.filter.fire[0] && this.filter.fire[1]/*  && this.filter.userType != 'active' */) {
				filter.start_date_deactivate = this.DMY2YMD(this.filter.fire[0])
				filter.end_date_deactivate = this.DMY2YMD(this.filter.fire[1])
			}

			if(this.filter.applied[0] && this.filter.applied[1] && this.filter.userType != 'trainees') {
				filter.start_date_applied = this.DMY2YMD(this.filter.applied[0])
				filter.end_date_applied = this.DMY2YMD(this.filter.applied[1])
			}

			if(this.filter.restore[0]  && this.filter.restore[1] && this.filter.userType != 'trainees') {
				filter.start_date_reapplied = this.DMY2YMD(this.filter.restore[0])
				filter.end_date_reapplied = this.DMY2YMD(this.filter.restore[1])
			}

			this.isRestored = this.filter.userType === 'reactivated'

			this.axios.post('/timetracking/get-persons', filter).then(({data}) => {
				const users = []
				data.users.data.slice().reverse().forEach(user => {
					const exists = users.find(u => u.id === user.id)
					if(!exists) {
						users.push(user)
					}
				})
				this.totalRows = data.users.total
				this.items = users.reverse().map(user => {
					const pos = this.positionsMap[user.position_id]
					return { ...user, position: pos ? pos.position : '' }
				})
				this.groups = data.groups
				this.segments = data.segments

				this.auth_token = data.auth_token
				this.currentUser = data.currentUser
				// if(this.filter.start_date == null) {
				// 	this.filter.start_date = data.start_date
				// 	this.filter.end_date = data.end_date
				// 	this.filter.start_date_deactivate = data.start_date
				// 	this.filter.end_date_deactivate = data.end_date
				// 	this.filter.start_date_applied = data.start_date
				// 	this.filter.end_date_applied = data.end_date
				// 	this.filter.start_date_reapplied = data.start_date
				// 	this.filter.end_date_reapplied = data.end_date
				// }
				loader.hide()
				this.loading = false
			}).catch(() => {
				loader.hide()
				this.loading = false
			})
		},

		exportData() {
			var link = '/timetracking/get-persons'
			link += '?filter=' + this.filter.userType

			if(this.filter.register[0] && this.filter.register[1]) {
				link += '&start_date=' + this.DMY2YMD(this.filter.register[0])
				link += '&end_date=' + this.DMY2YMD(this.filter.register[1])
			}

			if(this.filter.fire[0] && this.filter.fire[1]/*  && this.filter.userType != 'active' */) {
				link += '&start_date_deactivate=' + this.DMY2YMD(this.filter.fire[0])
				link += '&end_date_deactivate=' + this.DMY2YMD(this.filter.fire[1])
			}

			if(this.filter.applied[0] && this.filter.applied[1] && this.filter.userType != 'trainees') {
				link += '&start_date_applied=' + this.DMY2YMD(this.filter.applied[0])
				link += '&end_date_applied=' + this.DMY2YMD(this.filter.applied[1])
			}

			if(this.filter.restore[0]  && this.filter.restore[1] && this.filter.userType != 'trainees') {
				link += '&start_date_reapplied=' + this.DMY2YMD(this.filter.restore[0])
				link += '&end_date_reapplied=' + this.DMY2YMD(this.filter.restore[1])
			}

			if(this.filter.type) link += '&type=' + this.filter.type
			if(this.filter.part) link += '&part=' + this.filter.part

			link += '&segment=' + this.filter.segment
			link += '&excel=1'
			window.location.href = link
		},

		idLink(user){
			if(!(this.is_admin && this.subdomain == 'bp')) return `/timetracking/edit-person?id=${user.id}`
			const testhost = location.host.split('.')
			testhost[0] = 'test'
			return `${location.protocol}//${testhost.join('.')}/login-as-employee/${user.id}?auth=${this.auth_token}`
		},

		DMY2YMD(dmy){
			return this.$moment(dmy, 'DD.MM.YYYY').format('YYYY-MM-DD')
		},
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
.UserList{
	.fa-cog{
		color: #fff;
	}
}
</style>
