<template>
	<div
		v-if="groups"
		class="AnalyticsPage"
	>
		<div class="AnalyticsPage-filters row mb-4">
			<div class="col-3">
				<select
					v-model="currentGroupId"
					class="form-control"
					@change="fetchData"
				>
					<option
						v-for="group in ggroups"
						:key="group.id"
						:value="group.id"
					>
						{{ group.name }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					v-model="monthInfo.currentMonth"
					class="form-control"
					@change="fetchData"
				>
					<option
						v-for="month in $moment.months()"
						:key="month"
						:value="month"
					>
						{{ month }}
					</option>
				</select>
			</div>
			<div class="col-2">
				<select
					v-model="currentYear"
					class="form-control"
					@change="fetchData"
				>
					<option
						v-for="year in years"
						:key="year"
						:value="year"
					>
						{{ year }}
					</option>
				</select>
			</div>
			<div class="col-1">
				<div
					class="btn btn-primary rounded"
					title="Обновить данные"
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div
				v-if="$laravel.is_admin"
				class="col-2"
			>
				<button
					v-if="noAnGroups.length"
					class="btn btn-info rounded add-s"
					title="Создать аналитику"
					@click="showNoAn = true"
				>
					<i class="fa fa-plus-square" />
				</button>

				<button
					v-if="table"
					class="btn btn-info rounded add-s"
					title="Архивировать"
					@click="archive()"
				>
					<i class="fa fa-trash" />
				</button>

				<button
					class="btn btn-info rounded add-s ml-2"
					title="Восстановить из архива"
					@click="showArchive = true"
				>
					<i class="fa fa-archive" />
				</button>
			</div>
			<div
				v-else
				class="col-1"
			/>
		</div>
		<template v-if="!firstEnter">
			<template v-if="hasPremission">
				<template v-if="true">
					<div
						v-if="isMain"
						class="AnalyticsPage-header wrap mb-4"
					>
						<TopGauges
							v-if="ready.performances"
							:key="123"
							:utility_items="gauges"
							:editable="false"
							wrapper_class="d-flex"
							page="analytics"
							class="AnalyticsPage-gauges"
						/>
						<template v-else>
							<div class="AnalyticsPage-skeletonImg b-skeleton b-skeleton-img b-skeleton-animate-wave ml-4 mb-4" />
							<div class="AnalyticsPage-skeletonImg b-skeleton b-skeleton-img b-skeleton-animate-wave ml-4 mb-4" />
							<div class="AnalyticsPage-skeletonImg b-skeleton b-skeleton-img b-skeleton-animate-wave ml-a mb-4 mr-4" />
						</template>

						<div
							v-if="ready.fired && firedInfo"
							class="p-4"
						>
							<p class="ap-text">
								Процент текучки кадров за прошлый месяц: <span>{{ firedInfo.fired_percent_prev }}%</span>
							</p>
							<p class="ap-text">
								Процент текучки кадров за текущий месяц: <span>{{ firedInfo.fired_percent }}%</span>
							</p>
							<p class="ap-text">
								В прошлом месяце было уволено: <span>{{ firedInfo.fired_number_prev }}</span>
							</p>
							<p class="ap-text">
								В текущем месяце было уволено: <span>{{ firedInfo.fired_number }}</span>
							</p>
						</div>
					</div>

					<b-tabs
						type="card"
						:default-active-key="active"
						@change="onTabChange"
					>
						<b-tab
							key="1"
							card
						>
							<template #title>
								<span v-b-popover.hover.top="'таблица продаж'">Сводная</span>
							</template>
							<div class="mb-5 mt-4">
								<template v-if="ready.analytics && ready.activities">
									<AnalyticStat
										v-if="table"
										:table="table"
										:fields="columns"
										:activeuserid="activeuserid"
										:is-admin="isAdmin"
										:month-info="monthInfo"
										:group_id="currentGroupId"
										:activities="activitiesOptions"
										:current-group="currentGroup"
										:report-cards="reportCards"
									/>
								</template>
								<b-skeleton-table
									v-else
									:rows="6"
									:columns="5"
									:table-props="{ bordered: true, striped: true }"
								/>
							</div>

							<CallBase
								v-if="currentGroupId == 53"
								:data="call_bases"
								:month-info="monthInfo"
							/>

							<TableDecomposition
								v-if="ready.decompositions"
								:month="monthInfo"
								:decompositions="decompositions"
								:group-id="currentGroupId"
								class="pt-4"
							/>
							<b-skeleton-table
								v-else
								:rows="6"
								:columns="5"
								:table-props="{ bordered: true, striped: true }"
							/>
						</b-tab>

						<b-tab
							key="2"
							card
							class="position-relative"
						>
							<template #title>
								<span v-b-popover.hover.top="'данные по показателям'">Подробная</span>
							</template>
							<AnalyticsDetailes
								v-if="ready.activities"
								:activities="activities"
								:weeks="weeks"
								:current-group="currentGroupId"
								:month-info="monthInfo"
								:activity-select="activitiesOptions"
								@updateActivity="onUpdateActivity"
								@deleteActivity="onDeleteActivity"
								@orderActivity="onOrderActivity"
							/>
							<b-skeleton-table
								v-else
								:rows="6"
								:columns="5"
								:table-props="{ bordered: true, striped: true }"
							/>
						</b-tab>
					</b-tabs>
				</template>

				<template v-else>
					<p class="no-info">
						Аналитика для группы еще не создана
					</p>
				</template>
			</template>

			<template v-else>
				<p class="no-info">
					У вас нет доступа к этой группе
				</p>
			</template>
		</template>
		<div class="empty-space" />

		<!-- Modal restore archived group -->
		<b-modal
			v-model="showArchive"
			title="Восстановить из архива"
			size="lg"
			class="modalle"
			@ok="restore_analytics()"
		>
			<div class="row">
				<div class="col-5">
					<p class="">
						Отдел
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="restore_group"
						class="form-control form-control-sm"
					>
						<option
							v-for="(archived_group, key) in archived_groups"
							:key="key"
							:value="archived_group.id"
						>
							{{ archived_group.name }}
						</option>
					</select>
				</div>
			</div>
		</b-modal>

		<b-modal
			v-model="showNoAn"
			title="Создать аналитику"
			size="lg"
			class="modalle"
			@ok="add_analytics()"
		>
			<div class="row">
				<div class="col-5">
					<p class="">
						Отдел
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="groupForCreate"
						class="form-control form-control-sm"
					>
						<option
							v-for="(group, key) in noAnGroups"
							:key="key"
							:value="group.id"
						>
							{{ group.name }}
						</option>
					</select>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */

import AnalyticStat from '@/components/AnalyticStat'
import CallBase from '@/components/CallBase'
const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import TableDecomposition from '@/components/tables/TableDecomposition'
import AnalyticsDetailes from '@/components/pages/AnalyticsPage/AnalyticsDetailes'
import { useYearOptions } from '../composables/yearOptions'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import {
	fetchAnalytics,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,

	fetchActivitiesV2,
	fetchDecompositionsV2,
	fetchPerformancesV2,
	fetchFiredInfoV2,
	fetchAnalyticsGroupsV2,
	fetchAnalyticsV2,
} from '@/stores/api'

const API = {
	fetchAnalytics,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,

	fetchActivitiesV2,
	fetchDecompositionsV2,
	fetchPerformancesV2,
	fetchFiredInfoV2,
	fetchAnalyticsGroupsV2,
	fetchAnalyticsV2,
}

export default {
	name: 'AnalyticsPage',
	components: {
		AnalyticStat,
		CallBase,
		TableDecomposition,
		TopGauges,
		AnalyticsDetailes,
	},
	props: {
		groups: {
			type: Array,
			default: () => []
		},
		activeuserid: {
			type: Number,
			default: 0
		},
		isAdmin: {
			type: Boolean,
		},
	},
	data() {
		return {
			data: [],
			ggroups: [],
			active: '1',
			hasPremission: true, // доступ
			yearActivityTableFields: [],
			yearMin: 0,
			yearMax: 0,
			currentYear: new Date().getFullYear(),
			monthInfo: {},
			currentGroupId: null,
			loader: null,
			firstEnter: true,
			showArchive: false,
			askey: 1,
			call_bases: [], // euras call base unique table
			restore_group: null,
			noan: false, // нет аналитики
			dataLoaded: false,
			list: [
				{ name: 'John', id: 0 },
				{ name: 'Joao', id: 1 },
				{ name: 'Jean', id: 2 }
			],

			activities: [],
			weeks: [],
			decompositions: [],
			performances: {
				utility: [],
				rentability: null,
			},
			firedInfo: null,
			archived_groups: [],
			columns: [],
			table: null,
			ready: {
				activities: false,
				decompositions: false,
				performances: false,
				fired: false,
				groups: false,
				analytics: false,
			},
			reportCards: [],

			showNoAn: false,
			noAnGroups: [],
			groupForCreate: null,
		}
	},
	computed: {
		...mapState(usePortalStore, ['portal', 'isMain']),
		years(){
			if(!this.portal.created_at) return [new Date().getFullYear()]
			return useYearOptions(new Date(this.portal.created_at).getFullYear())
		},
		activitiesOptions(){
			return this.activities.map(({name, id}) => ({
				id,
				name,
			}))
		},
		gauges(){
			if(!this.performances) return []
			return [{
				gauges: [
					...this.performances.utility,
					{
						...this.performances.rentability,
						name: 'Рентабельность'
					},
				]
			}]
		},
		currentGroup(){
			return this.ggroups.find(group => group.id === this.currentGroupId)
		},
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
			// выбор группы
			// переделать на роуты
			const urlParams = new URLSearchParams(window.location.search)
			const group = parseInt(urlParams.get('group') || 0)
			const active = urlParams.get('active')
			const load = urlParams.get('load')
			const year = parseInt(urlParams.get('year') || 0)

			this.ggroups = this.groups
			this.currentGroupId = group || this.groups[0].id
			this.currentYear = year || new Date().getFullYear()

			this.active = active || '1'

			this.setMonth()
			this.setYear()
			this.setActivityYearTableFields()

			const now = new Date()
			this.fetchGroups({
				month: now.getMonth() + 1,
				year: now.getFullYear(),
				group_id: this.currentGroupId,
			})

			if(load) this.fetchData()
		},

		/**
		 * ACTIVITY YEAR
		 */
		setActivityYearTableFields() {
			const fieldsArray = []
			let order = 1

			fieldsArray.push({
				key: 'name',
				name: 'Сотрудник',
				order: order++,
				classes: ' b-table-sticky-column text-left t-name wd',
			})

			for (let i = 1; i <= 12; i++) {
				const month = (i.length === 1 ? '0': '') + i

				fieldsArray.push({
					key: i,
					name: this.$moment(this.currentYear + '-' + month + '-01').format('MMMM'),
					order: order++,
					classes: 'text-center px-1 month',
				})
			}

			this.yearActivityTableFields = fieldsArray
		},

		setMonth() {
			this.monthInfo.currentMonth = this.monthInfo.currentMonth ? this.monthInfo.currentMonth : this.$moment().format('MMMM')
			this.monthInfo.month = this.monthInfo.currentMonth ? this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M') : this.$moment().format('M')
			let currentMonth = this.$moment(this.monthInfo.currentMonth, 'MMMM')
			//Расчет выходных дней
			this.monthInfo.monthEnd = currentMonth.endOf('month'); //Конец месяца
			this.monthInfo.weekDays = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6]) //Колличество выходных
			this.monthInfo.weekDays5 = currentMonth.weekdayCalc(currentMonth.startOf('month').toString(), currentMonth.endOf('month').toString(), [6,0]) //Колличество выходных
			this.monthInfo.daysInMonth = new Date(this.$moment().format('YYYY'), this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'), 0).getDate() //Колличество дней в месяце
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays // Колличество рабочих дней
			this.monthInfo.workDays5 = this.monthInfo.daysInMonth - this.monthInfo.weekDays5 // Колличество рабочих дней

		},
		//Установка выбранного года
		setYear() {
			this.currentYear = this.currentYear ? this.currentYear : this.$moment().format('YYYY')
			this.monthInfo.currentYear = this.currentYear
		},

		onTabChange(active) {
			this.active = active
			window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroupId + '&active=' + this.active)
		},

		async fetchActivities(request){
			try{
				const {activities, weeks} = await API.fetchActivitiesV2(request)
				this.activities = activities
				this.weeks = weeks
				this.ready.activities = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке показателей')
			}
		},

		async fetchDecompositions(request){
			try{
				this.decompositions = await API.fetchDecompositionsV2(request)
				this.ready.decompositions = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке декомпозиции')
			}
		},

		async fetchPerformances(request){
			try{
				this.performances = await API.fetchPerformancesV2(request)
				this.ready.performances = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке полезности')
			}
		},

		async fetchFiredInfo(request){
			try{
				this.firedInfo = await API.fetchFiredInfoV2(request)
				this.ready.fired = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке информации о уволенных сотрудниках')
			}
		},

		async fetchGroups(request){
			try{
				const {is_active, is_archived } = await API.fetchAnalyticsGroupsV2(request)
				const groups = Array.isArray(is_active) ? is_active : Object.values(is_active)
				this.ggroups = groups.filter(group => group.has_analytics)
				this.noAnGroups = groups.filter(group => !group.has_analytics)
				this.archived_groups = Array.isArray(is_archived) ? is_archived : Object.values(is_archived)
				this.ready.groups = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке информации о отделах')
			}
		},

		async fetchAnalytics(request){
			try{
				const {columns, table, report_cards: reportCards} = await API.fetchAnalyticsV2(request)
				this.columns = Array.isArray(columns) ? columns : Object.values(columns)
				if(this.columns[0]?.key !== 'name') this.columns.splice(0, 1)
				this.table = Array.isArray(table) ? table : Object.values(table)
				this.reportCards = reportCards || {}
				this.ready.analytics = true
			}
			catch(error){
				console.error(error)
				this.$toast('Ошибка при загрузке сводной')
			}
		},

		async fetchData() {
			this.ready = {
				activities: false,
				decompositions: false,
				performances: false,
				fired: false,
				groups: false,
				analytics: false,
			}
			this.firstEnter = false

			const request = {
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				group_id: this.currentGroupId,
			}


			this.setMonth()
			this.setYear()
			await Promise.all([
				this.fetchActivities(request),
				this.fetchDecompositions(request),
				this.fetchPerformances(request),
				this.fetchFiredInfo(request),
				this.fetchAnalytics(request),
			])

			this.dataLoaded = !!this.table.length
			this.noan = false
			this.firstEnter = false
			this.hasPremission = true
			const urlParamss = new URLSearchParams(window.location.search)
			const active = urlParamss.get('active')
			this.active = active ? active : '1'

			this.askey++
			window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroupId + '&active=' + this.active)
			this.monthInfo.workDays = this.getBusinessDateCount(this.monthInfo.month, this.monthInfo.currentYear, this.currentGroup.workdays)
		},

		getBusinessDateCount(month, year, workdays) {
			month = month - 1
			const next_month = (month + 1) == 12 ? 0 : month + 1
			const next_year = (month + 1) == 12 ? year + 1 : year

			const start = new Date(year, month, 1)
			const end = new Date(next_year, next_month, 1)

			const days = (end - start) / 86400000
			const weekends = workdays == 5 ? [0,6] : [0]

			let business_days = 0

			for(let i = 1; i <= days; i++) {
				const d = new Date(year, month, i).getDay()
				if(!weekends.includes(d)) business_days++
			}

			return business_days
		},

		async add_analytics() {
			const loader = this.$loading.show()
			try {
				await API.createAnalyticsGroup({
					month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
					year: this.currentYear,
					group_id: this.groupForCreate,
				})
				const index = this.noAnGroups.findIndex(group => group.id === this.groupForCreate)
				if(~index){
					const [group] = this.noAnGroups.splice(index, 1)
					group.has_analytics = 1
					this.ggroups.push(group)
				}
				this.$toast.success('Аналитика для группы добавлена')
				this.fetchData()
			}
			catch (error) {
				this.$toast.error('Аналитика для группы не добавлена')
				console.error(error)
			}
			loader.hide()
		},

		async restore_analytics() {
			if (!confirm('Вы уверены что хотите восстановить аналитику группы?')) return

			const loader = this.$loading.show()
			try {
				await API.restoreAnalyticsGroup({
					id: this.restore_group
				})
				const index = this.archived_groups.findIndex(group => group.id === this.restore_group)
				if(~index){
					const group = this.archived_groups[index]
					this.ggroups.push(group)
					this.archived_groups.splice(index, 1)
					this.currentGroupId = this.restore_group
				}
				this.$toast.success('Восстановлен')
				this.restore_group = null
				this.showArchive = false
				this.fetchData()
			}
			catch (error) {
				this.$toast.error('Не удалось восстановить аналитику')
				console.error(error)
			}
			loader.hide()
		},

		async archive() {
			if (!confirm('Вы уверены что хотите архивировать аналитику группы ?')) return

			const loader = this.$loading.show()
			try {
				await API.archiveAnalyticsGroup({
					id: this.currentGroupId
				})
				this.$toast.success('Архивирован')
				const index = this.ggroups.findIndex(group => group.id === this.currentGroupId)
				if(~index){
					const group = this.ggroups[index]
					this.archived_groups.push(group)
					this.ggroups.splice(index, 1)
				}
				this.currentGroupId = this.ggroups[0]?.id
				this.fetchData()
			}
			catch (error) {
				this.$toast.error('Не удалось архивировать аналитику')
				console.error(error)
			}
			loader.hide()
		},
		onUpdateActivity(activities){
			this.activities = activities
			this.fetchData()
		},
		onDeleteActivity(){
			this.fetchData()
		},
		onOrderActivity(){
			this.fetchData()
		},
	}
}
</script>

<style lang="scss">
.AnalyticsPage{
	&-gauges{
		flex: 1;
		.TopGauges-group,
		.TopGauges-gauges{
			flex: 1;
			justify-content: flex-start !important;
			align-items: flex-end;
		}
		.TopGauges-gauges{
			gap: 1.5rem;
		}
		.TopGauges-gauge{
			flex: 0 0 content;
			&:last-of-type{
				margin-left: auto;
			}
		}
	}

	&-header{
		~ .tabs{
			overflow: visible;
		}
	}

	&-skeletonImg{
		width: 100px;
		height: 100px;
	}

	.btn {
		padding: .375rem .75rem;
		&.btn-sm {
			padding: 0.15rem 0.5rem;
		}
	}
	.cell-input{
		padding: 0 !important;
	}
}
.mw30 {
	min-width: 30px;
}
.rating {
	display: inline-block;
	unicode-bidi: bidi-override;
	color: #888888;
	font-size: 25px;
	height: 25px;
	width: auto;
	margin: 0;
	position: relative;
	padding: 0;
}

.rating-upper {
	color: #c52b2f;
	padding: 0;
	position: absolute;
	z-index: 1;
	display: flex;
	top: 0;
	left: 0;
	overflow: hidden;
}

.rating-lower {
	padding: 0;
	display: flex;
	z-index: 0;
}
.ap-text {
	margin: 0;
	display: flex;
	font-size: 12px;
	align-items: center;
}
.ap-text span {
	font-size: 16px;
	font-weight: 700;
	margin-left: 5px;
}
.fz12 {
	font-size: 12px;
	margin-bottom: 0;
	line-height: 20px;
	color: #000 !important;
}
.wrap {
	background: #f3f7f9;
	margin-bottom: 15px;
	padding-top: 15px;
	border: 1px solid #dde8ee;
	border-radius: 5px;
}
</style>
