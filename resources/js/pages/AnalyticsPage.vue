<template>
	<div
		v-if="groups"
		class="AnalyticsPage"
	>
		<div class="AnalyticsPage-filters row mb-4">
			<div class="col-3">
				<select
					class="form-control"
					v-model="currentGroup"
					@change="fetchData"
				>
					<option
						v-for="group in ggroups"
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
			<div class="col-1">
				<div
					class="btn btn-primary rounded"
					@click="fetchData()"
				>
					<i class="fa fa-redo-alt" />
				</div>
			</div>
			<div
				class="col-2"
				v-if="$laravel.is_admin"
			>
				<button
					v-if="!firstEnter && !dataLoaded"
					class="btn btn-info rounded add-s"
					@click="add_analytics()"
					title="Создать аналитику"
				>
					<i class="fa fa-plus-square" />
				</button>

				<button
					v-if="!noan"
					class="btn btn-info rounded add-s"
					@click="archive()"
					title="Архивировать"
				>
					<i class="fa fa-trash" />
				</button>

				<button
					class="btn btn-info rounded add-s ml-2"
					@click="showArchive = true"
					title="Восстановить из архива"
				>
					<i class="fa fa-archive" />
				</button>
			</div>
			<div
				class="col-1"
				v-else
			/>
		</div>
		<template v-if="!firstEnter">
			<template v-if="hasPremission">
				<template v-if="dataLoaded">
					<div class="AnalyticsPage-header wrap mb-4">
						<TopGauges
							:utility_items="data.utility"
							:editable="false"
							wrapper_class="d-flex"
							:key="123"
							page="analytics"
							class="AnalyticsPage-gauges"
						/>

						<div class="p-4">
							<p class="ap-text">
								Процент текучки кадров за прошлый месяц: <span>{{ data.fired_percent_prev }}%</span>
							</p>
							<p class="ap-text">
								Процент текучки кадров за текущий месяц: <span>{{ data.fired_percent }}%</span>
							</p>
							<p class="ap-text">
								В прошлом месяце было уволено: <span>{{ data.fired_number_prev }}</span>
							</p>
							<p class="ap-text">
								В текущем месяце было уволено: <span>{{ data.fired_number }}</span>
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
								<AnalyticStat
									:table="data.table"
									:fields="data.columns"
									:activeuserid="activeuserid"
									:is-admin="isAdmin"
									:month-info="monthInfo"
									:group_id="currentGroup"
									:activities="activity_select"
								/>
							</div>

							<CallBase
								v-if="currentGroup == 53"
								:data="call_bases"
								:month-info="monthInfo"
							/>

							<TableDecomposition
								:month="monthInfo"
								:data="data.decomposition"
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
							<div class="kakieto-knopki">
								<button
									class="btn btn-success rounded btn-sm"
									@click="add_activity()"
								>
									<i
										class="fa fa-plus-square"
										style="font-size:14px"
									/>
								</button>
								<button
									class="btn btn-primary rounded btn-sm"
									@click="showOrder = true"
								>
									<i class="fas fa-sort-amount-down" />
								</button>
							</div>
							<b-tabs
								type="card"
								class="mt-4"
								@change="showSubTab"
								:default-active-key="active_sub_tab"
							>
								<template v-for="(activity, index) in data.activities">
									<b-tab
										:title="activity.name"
										:key="index"
										@change="showcubTab(index)"
									>
										<!-- Switch month and year of Activity in detailed -->
										<button
											class="btn btn-default rounded mt-4"
											@click="switchToMonthInActivity(index)"
										>
											Месяц
										</button>
										<button
											class="btn btn-default rounded mt-4"
											@click="switchToYearInActivity(index)"
										>
											Год
										</button>

										<!-- tabs -->
										<div
											v-if="activityStates[index] !== undefined"
											class="mt-2"
										>
											<!-- Month tab of activity in detailed -->
											<div
												:class="{
													'hidden' : activityStates[index] == 'year'
												}"
											>
												<TableActivityNew
													v-if="activity.type == 'default'"
													:key="activity.id"
													:month="monthInfo"
													:activity="activity"
													:group_id="currentGroup"
													:work_days="monthInfo.workDays"
													:editable="activity.editable == 1 ? true : false"
												/>

												<TableActivityCollection
													v-if="activity.type == 'collection'"
													:key="activity.id"
													:month="monthInfo"
													:activity="activity"
													:is_admin="true"
													:price="activity.price"
												/>

												<TableQualityWeekly
													v-if="activity.type == 'quality'"
													:key="activity.id"
													:month-info="monthInfo"
													:items="activity.records"
													:editable="activity.editable == 1 ? true : false"
												/>
											</div>

											<!-- Year tab of activity in detailed -->
											<div
												:class="{
													'hidden' : activityStates[index] == 'month'
												}"
											>
												<h4 class="mb-2">
													{{ activity.name }}
												</h4>

												<!-- Year table -->
												<div class="table-container table-responsive">
													<table class="table table-bordered">
														<thead>
															<tr>
																<th
																	v-for="(field, key) in yearActivityTableFields"
																	:key="key"
																	:class="field.classes"
																>
																	<div>{{ field.name }}</div>
																</th>
															</tr>
														</thead>
														<tbody>
															<tr
																v-for="( row, index ) in yearActivityTable"
																:key="index"
															>
																<td
																	v-for="(field, key) in yearActivityTableFields"
																	:key="key"
																	:class="field.classes"
																	:style="field.key === 'name' || !row[field.key] ? '' : `background: ${getCellColor(row[field.key])};`"
																	:data-key="field.key"
																>
																	<div>{{ row[field.key] }}</div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</b-tab>
								</template>
							</b-tabs>
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
			@ok="restore_analytics()"
			size="lg"
			class="modalle"
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
							:value="archived_group.id"
							v-for="(archived_group, key) in archived_groups"
							:key="key"
						>
							{{ archived_group.name }}
						</option>
					</select>
				</div>
			</div>
		</b-modal>

		<!-- Modal Create activity -->
		<b-modal
			v-model="showOrder"
			title="Порядок активностей"
			@ok="save_order()"
			size="md"
		>
			<div :key="askey">
				<Draggable
					:list="activity_select"
					@end="onEndSortcat('test')"
				>
					<div
						v-for="act in activity_select"
						:key="act.id"
						class="drag_item"
					>
						<span>{{ act.name }}</span>
						<i
							@click="delete_activity(act)"
							class="fa fa-trash pointer"
						/>
					</div>
				</Draggable>
			</div>
		</b-modal>


		<!-- Modal Create activity -->
		<b-modal
			v-model="showActivityModal"
			title="Добавить активность"
			@ok="create_activity()"
			size="lg"
			class="modalle"
		>
			<div class="row">
				<div class="col-5">
					<p class="">
						Название активности
					</p>
				</div>
				<div class="col-7">
					<input
						type="text"
						class="form-control form-control-sm"
						v-model="activity.name"
					>
				</div>
			</div>

			<div class="row">
				<div class="col-5">
					<p class="">
						Метод
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="activity.plan_unit"
						class="form-control form-control-sm"
					>
						<option
							:value="key"
							v-for="(value, key) in plan_units"
							:key="key"
						>
							{{ value }}
						</option>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="col-5">
					<p class="">
						План (Если сумма, на день)
					</p>
				</div>
				<div class="col-7">
					<input
						type="number"
						class="form-control form-control-sm"
						v-model="activity.daily_plan"
					>
				</div>
			</div>

			<div class="row">
				<div class="col-5">
					<p class="">
						Кол-во рабочих дней в неделе
					</p>
				</div>
				<div class="col-7">
					<input
						type="number"
						class="form-control form-control-sm"
						v-model="activity.weekdays"
						min="1"
						max="7"
					>
				</div>
			</div>

			<div class="row">
				<div class="col-5">
					<p class="">
						Ед. измерения (Символ в конце показателя)
					</p>
				</div>
				<div class="col-7">
					<input
						type="text"
						class="form-control form-control-sm"
						v-model="activity.unit"
					>
				</div>
			</div>

			<div class="row">
				<div class="col-5 d-flex align-items-center">
					<p class="mb-0">
						Редактируемый
					</p>
					<input
						type="checkbox"
						class="form-control form-control-sm"
						v-model="activity.editable"
					>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
import Draggable from 'vuedraggable'
import AnalyticStat from '@/components/AnalyticStat'
import CallBase from '@/components/CallBase'
import TableDecomposition from '@/components/tables/TableDecomposition'
const TableActivityNew = () => import(/* webpackChunkName: "TableActivityNew" */ '@/components/tables/TableActivityNew')
import TableActivityCollection from '@/components/tables/TableActivityCollection'
import TableQualityWeekly from '@/components/tables/TableQualityWeekly'
const TopGauges = () => import(/* webpackChunkName: "TopGauges" */ '@/components/TopGauges')  // TOП спидометры, есть и в аналитике
import { useYearOptions } from '../composables/yearOptions'
import { mapState } from 'pinia'
import { usePortalStore } from '@/stores/Portal'
import {
	fetchAnalyticsMonthlyStats,
	fetchAnalytics,
	createAnalyticsActivity,
	deleteAnalyticsActivity,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,
	updateAnalyticsOrder,
} from '@/stores/api.mock'

const API = {
	fetchAnalyticsMonthlyStats,
	fetchAnalytics,
	createAnalyticsActivity,
	deleteAnalyticsActivity,
	createAnalyticsGroup,
	archiveAnalyticsGroup,
	restoreAnalyticsGroup,
	updateAnalyticsOrder,
}

function percentMinMax(value, min, max){
	return (value - min) / (max - min)
}

export default {
	name: 'AnalyticsPage',
	components: {
		Draggable,
		AnalyticStat,
		CallBase,
		TableDecomposition,
		TableActivityNew,
		TableActivityCollection,
		TableQualityWeekly,
		TopGauges,
	},
	props: ['groups', 'activeuserid', 'isAdmin'],
	data() {
		return {
			data: [],
			ggroups: [],
			active: '1',
			hasPremission: false, // доступ
			yearActivityTableFields: [],
			yearActivityTable: [],
			yearMin: 0,
			yearMax: 0,
			activityStates: {},
			currentYear: new Date().getFullYear(),
			monthInfo: {},
			currentGroup: null,
			loader: null,
			showOrder: false,
			firstEnter: true,
			showArchive: false,
			askey: 1,
			activity_select: [],
			archived_groups: [],
			call_bases: [], // euras call base unique table
			restore_group: null,
			noan: false, // нет аналитики
			showActivityModal:false, // activity
			dataLoaded: false,
			active_sub_tab: 0,
			activity: {// activity
				name: null,
				daily_plan: null,
				plan_unit: null,
				unit: null,
				editable: 1,
				weekdays: 6,
			},
			plan_units: {// activity
				minutes: 'Сумма показателей',
				percent: 'Среднее значение',
				less_sum: 'Не более, сумма',
				less_avg: 'Не более, сред. зн.',
			},
			list: [
				{ name: 'John', id: 0 },
				{ name: 'Joao', id: 1 },
				{ name: 'Jean', id: 2 }
			],
			users: [], // year table of activity
			statistics: [] // year table of activity
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
			const urlParams = new URLSearchParams(window.location.search);
			let group = urlParams.get('group');
			let active = urlParams.get('active');
			let load = urlParams.get('load');

			this.ggroups = this.groups
			this.currentGroup = (group == null) ? this.groups[0].id : parseFloat(group)

			this.active = (active == null) ? '1' : active

			this.setMonth()
			this.setYear()
			this.setActivityYearTableFields()

			if(load != null) {
				this.fetchData()
			}
		},
		/**
		 * ACTIVITY YEAR
		 */
		switchToMonthInActivity(index) {
			this.activityStates[index] = 'month'
		},

		/**
		 * ACTIVITY YEAR
		 */
		switchToYearInActivity(index) {
			this.activityStates[index] = 'year'

			this.fetchYearTableOfActivity(this.data.activities[index].id);
		},

		/**
		 * ACTIVITY YEAR
		 * full name
		 */
		fullNameOfUser(user) {
			return user.last_name !== '' || user.last_name !== null
				? user.last_name + ' ' + user.name
				: user.last_name
		},

		/**
		 * ACTIVITY YEAR
		 * server returns total key
		 * and if there is no result not returns total key
		 */
		normalizeStat(obj) {
			let res = {}

			Object.keys(obj).forEach((key) => {
				res[key] = obj[key] == 0
					? 0
					: Number(obj[key].total).toFixed(2);
			});

			return res
		},

		/**
		 * ACTIVITY YEAR
		 */
		formYearActivityTable(stats) {
			let res = [];

			this.users.forEach((user) => {

				if(stats[user.id] !== undefined) {
					res.push({
						name: this.fullNameOfUser(user),
						...this.normalizeStat(stats[user.id]),
					});
				}
			});

			this.yearActivityTable = res;
			this.yearCalcMinMax()
		},

		yearCalcMinMax(){
			let min = 9999999999
			let max = 0
			this.yearActivityTable.forEach(row => {
				Object.keys(row).forEach(key => {
					if(key === 'name') return
					const value = parseFloat(row[key])
					if(value < min) min = value
					if(value > max) max = value
				})
			})
			this.yearMin = min
			this.yearMax = max
		},
		getCellColor(value) {
			const perc = percentMinMax(value, this.yearMin, this.yearMax) * 100
			let r, g, b = 0;
			if(perc < 50) {
				r = 235;
				g = Math.round(5.1 * perc);
				b = Math.round(113 - 1.13 * perc);
			}
			else {
				g = 225;
				r = Math.round(510 - 5.1 * perc);
			}
			const h = r * 0x10000 + g * 0x100 + b * 0x1;
			return '#' + ('000000' + h.toString(16)).slice(-6);
		},

		/**
		 * ACTIVITY YEAR
		 */
		fetchYearTableOfActivity(activity_id) {
			let loader = this.$loading.show();

			API.fetchAnalyticsMonthlyStats({
				group_id: this.currentGroup,
				date: {
					year: this.currentYear,
					month: this.monthInfo.month
				},
				activity_id: activity_id
			}).then(({data}) => {
				this.users = data.users
				this.formYearActivityTable(data.statistics)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		/**
		 * ACTIVITY YEAR
		 */
		setActivityYearTableFields() {
			let fieldsArray = [];
			let order = 1;

			fieldsArray.push({
				key: 'name',
				name: 'Сотрудник',
				order: order++,
				classes: ' b-table-sticky-column text-left t-name wd',
			});

			for (let i = 1; i <= 12; i++) {
				if (i.length == 1) i = '0' + i;

				fieldsArray.push({
					key: i,
					name: this.$moment(this.currentYear + '-' + i + '-01').format('MMMM'),
					order: order++,
					classes: 'text-center px-1 month',
				});
			}

			this.yearActivityTableFields = fieldsArray;


		},

		onTabClick() {
			//
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
			this.monthInfo.workDays = this.monthInfo.daysInMonth - this.monthInfo.weekDays //Колличество рабочих дней
			this.monthInfo.workDays5 = this.monthInfo.daysInMonth - this.monthInfo.weekDays5 //Колличество рабочих дней

		},
		//Установка выбранного года
		setYear() {
			this.currentYear = this.currentYear ? this.currentYear : this.$moment().format('YYYY')
			this.monthInfo.currentYear = this.currentYear;
		},

		onTabChange(active) {
			this.active = active;
			window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroup + '&active=' + this.active);
		},

		fetchData() {
			let loader = this.$loading.show();


			API.fetchAnalytics({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				group_id: this.currentGroup
			}).then(data => {
				if (data.error && data.error == 'access') {
					this.hasPremission = false
					loader.hide();
					return;
				}
				this.hasPremission = true

				this.setMonth()
				this.setYear()

				let urlParamss = new URLSearchParams(window.location.search);

				this.firstEnter = false


				let active = urlParamss.get('active');
				this.active = (active == null) ? '1' : active

				if(data.error !== undefined) {
					this.dataLoaded = false
					this.noan = true;
					this.archived_groups = data.archived_groups
					this.ggroups = data.groups
				} else {
					this.dataLoaded = true
					this.data = data
					this.noan = false;

					this.activity_select = [];

					let activityStatesObj = {};
					this.data.activities.forEach((a, index) => {
						this.activity_select.push({
							'name':a.name,
							'id':a.id,
						});

						activityStatesObj[index] = 'month';
					})

					this.activityStates = activityStatesObj;

					this.call_bases = data.call_bases;
					this.archived_groups = data.archived_groups;
					this.ggroups = data.groups;
				}

				this.askey++;
				window.history.replaceState({ id: '100' }, 'Аналитика групп', '/timetracking/an?group=' + this.currentGroup + '&active=' + this.active);
				this.monthInfo.workDays = this.work_days = this.getBusinessDateCount(this.monthInfo.month,this.monthInfo.currentYear, data.workdays)
				loader.hide()
			}).catch(error => {
				loader.hide()
				alert(error)
			});
		},

		getBusinessDateCount(month, year, workdays) {

			month = month - 1;
			let next_month = (month + 1) == 12 ? 0 : month + 1;
			let next_year = (month + 1) == 12 ? year + 1 : year;

			var start = new Date(year, month, 1);
			var end = new Date(next_year, next_month, 1);

			let days = (end - start) / 86400000;

			let business_days = 0,
				weekends = workdays == 5 ? [0,6] : [0];

			for(let i = 1; i <= days; i++) {
				let d = new Date(year, month, i).getDay();
				if(!weekends.includes(d)) business_days++;
			}

			return business_days;
		},

		add_activity() {
			this.showActivityModal = true;
		},

		create_activity() {
			let loader = this.$loading.show();
			API.createAnalyticsActivity({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				activity: this.activity,
				group_id: this.currentGroup
			}).then(data => {
				this.$toast.success('Активность для группы добавлена!')
				this.fetchData();

				this.activity = {
					name: null,
					daily_plan: null,
					plan_unit: null,
					unit: null,
					editable: 1,
					weekdays: 6,
				};

				this.data.activities = data;
				this.showActivityModal = false
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Активность для группы не добавлена!')
				alert(error)
			});
		},

		add_analytics() {
			let loader = this.$loading.show();
			API.createAnalyticsGroup({
				month: this.$moment(this.monthInfo.currentMonth, 'MMMM').format('M'),
				year: this.currentYear,
				group_id: this.currentGroup
			}).then(() => {
				this.$toast.success('Аналитика для группы добавлена!')
				this.fetchData()
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Аналитика для группы не добавлена!')
				alert(error)
			});
		},

		onEndSortcat(/* test */) {

		},

		save_order() {
			let loader = this.$loading.show();
			API.updateAnalyticsOrder({
				activities: this.activity_select
			}).then(() => {
				this.$toast.success('Порядок сохранен!');
				this.showOrder = false;
				this.fetchData();
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},

		delete_activity(act) {

			if (!confirm('Вы уверены что хотите удалить активность \'' + act.name + '\' ?')) {
				return '';
			}

			let loader = this.$loading.show();
			API.deleteAnalyticsActivity({
				id: act.id
			}).then(() => {
				this.$toast.success('Удален!');
				this.fetchData();
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},

		restore_analytics() {

			if (!confirm('Вы уверены что хотите восстановить аналитику группы?')) {
				return '';
			}

			let loader = this.$loading.show();
			API.restoreAnalyticsGroup({
				id: this.restore_group
			}).then(data => {
				this.$toast.success('Восстановлен!');
				this.currentGroup = this.restore_group
				this.ggroups =data.groups
				this.fetchData();
				this.restore_group = null
				this.showArchive = false
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});

		},

		archive() {
			if (!confirm('Вы уверены что хотите архивировать аналитику группы ?')) {
				return '';
			}

			let loader = this.$loading.show();
			API.archiveAnalyticsGroup({
				id: this.currentGroup
			}).then(() => {
				this.$toast.success('Архивирован!');
				this.currentGroup = this.ggroups[0].id
				this.fetchData();
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!');
				alert(error)
			});
		},

		showSubTab(tab) {
			this.active_sub_tab = tab
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
		.TopGauges-gauge{
			flex: 0 0 content;
			&:last-of-type{
				margin-left: auto;
			}
		}
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
	.kakieto-knopki{
		position: absolute;
		top: 0;
		right: 0;
	}
</style>
