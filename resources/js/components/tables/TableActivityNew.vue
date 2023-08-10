<template>
	<div class="TableActivityNew mb-3">
		<div
			v-if="show_headers"
			class="d-flex align-items-center justify-content-end mb-2"
		>
			<!-- Filtees -->
			<div
				v-click-outside="onClickFiltersOutside"
				class="TableActivityNew-filters ml-3"
			>
				<JobtronButton
					class="ChatIcon-parent"
					fade
					small
					@click="onClickFilters"
				>
					<img
						src="/icon/news/filter/filter.svg"
						alt="filters"
						class="TableActivityNew-filtersIcon"
					>
				</JobtronButton>
				<PopupMenu
					v-if="isFilters"
					class="TableActivityNew-filtersPopup"
				>
					<JobtronSelect
						v-model="user_types"
						:options="userTypeOptions"
						compact
						class="mb-3"
					/>
					<JobtronSelect
						v-model="userShift"
						:options="userShiftOptions"
						compact
						class="mb-3"
					/>
					<JobtronButton
						small
						@click="onClickFilter"
					>
						Найти
					</JobtronButton>
				</PopupMenu>
			</div>

			<!-- Controls -->
			<div
				v-click-outside="onClickControlsOutside"
				class="TableActivityNew-contlors ml-3"
			>
				<JobtronButton
					class="ChatIcon-parent"
					fade
					small
					@click="onClickControls"
				>
					<i
						class="fa fa-bars"
						style="font-size:14px"
					/>
				</JobtronButton>
				<PopupMenu v-if="isControls">
					<div
						class="PopupMenu-item wsnw"
						@click="editActivity()"
					>
						<i class="icon-nd-settings" />
						Настройка таблицы
					</div>
					<div
						class="PopupMenu-item wsnw"
						@click="exportData()"
					>
						<i class="far fa-file-excel" />
						Экспорт
					</div>
					<div
						v-if="isImportable"
						class="PopupMenu-item wsnw"
						@click="onClickImport"
					>
						<i class="fa fa-upload" />
						Импорт
					</div>
				</PopupMenu>
			</div>
		</div>

		<div class="table-container whitespace-no-wrap">
			<table
				class="table b-table table-bordered table-responsive"
				:class="{'inverted' : color_invert}"
			>
				<thead>
					<tr>
						<th class="b-table-sticky-column text-left px-1 t-name bg-white">
							<div class="wd">
								Сотрудник  <i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('fullname')"
								/>
							</div>
						</th>

						<template v-if="activity.plan_unit == 'minutes'">
							<th class="text-center px-1 day-minute">
								Ср.
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('avg')"
								/>
							</th>
							<th class="text-center px-1 day-minute">
								План
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('month')"
								/>
							</th>
							<th class="text-center px-1 day-minute plan">
								Вып.
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('plan')"
								/>
							</th>
							<th class="text-center px-1 day-minute">
								%
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('_percent')"
								/>
							</th>
						</template>

						<template v-else>
							<th class="text-center px-1 day-minute">
								План
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('month')"
								/>
							</th>
							<th class="text-center px-1 day-minute">
								Вып.
								<i
									v-if="show_headers"
									class="fa fa-sort ml-2"
									@click="sort('plan')"
								/>
							</th>
						</template>

						<th
							v-for="day in month.daysInMonth"
							:key="day"
							class="text-center px-1"
						>
							{{ day }}
						</th>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in filtered"
						:key="index"
					>
						<td
							v-if="item.name == 'SPECIAL_BTN'"
							class="b-table-sticky-column text-left"
						>
							<button
								class="btn btn-light rounded btn-sm"
								@click="switchAction"
							>
								Сумма\Среднее
							</button>
						</td>

						<td
							v-else
							class="table-primary b-table-sticky-column text-left px-2 t-name"
							:title="item.id + ' ' + item.email"
						>
							<div class="wd d-flex">
								{{ item.lastname }} {{ item.name }}
								<b-badge
									v-if="item.group"
									:variant="item.group == 'Просрочники' ? 'success' : 'primary'"
								>
									{{ item.group }}
								</b-badge>

								<JobtronCup
									:place="sortDir === 'asc' ? index : filtered.length - index"
									rotate
								/>
							</div>
						</td>

						<template v-if="activity.plan_unit == 'minutes'">
							<td class="px-2 stat da">
								<div>{{ item.avg }}</div>
							</td>
							<td class="px-2 stat da">
								<div :title="activity.daily_plan + ' * ' + item.applied_from">
									{{ item.month }}
								</div>
							</td>
							<td class="px-2 stat da plan">
								<div>{{ item.plan }}</div>
							</td>
							<td class="px-2 stat da">
								<div>{{ item.percent }}</div>
							</td>
						</template>

						<template v-else>
							<td class="px-2 stat day-minute ">
								<div>{{ item.month }}</div>
							</td>
							<td class="px-2 stat day-minute">
								<div>{{ item.plan }}</div>
							</td>
						</template>

						<template v-for="day in month.daysInMonth">
							<td
								v-if="item.editable && editable"
								:key="day"
								class="TableActivityNew-data px-0 day-minute text-center Fri"
								:class="'table-' + item._cellVariants[day]"
							>
								<div>
									<input
										v-model="item[day]"
										type="number"
										class="form-control cell-input"
										@change="updateSettings($event, item, index, day)"
										@focusout="viewMode(item)"
									>
								</div>
							</td>
							<td
								v-else-if="holidays.includes(day) && item[day] > 0"
								:key="day + 'a'"
								class="TableActivityNew-data px-0 day-minute text-center Fri"
								:class="'table-' + item._cellVariants[day]"
								@click="editMode(item)"
							>
								<div v-if="item[day]">
									{{ item[day] }}{{ activity.unit }}
								</div>
							</td>
							<td
								v-else-if="holidays.includes(day)"
								:key="day + 'b'"
								class="TableActivityNew-data day-minute text-center Fri mywarning"
								@click="editMode(item)"
							>
								<div v-if="item[day]">
									{{ item[day] }}{{ activity.unit }}
								</div>
							</td>
							<td
								v-else
								:key="day + 'c'"
								class="TableActivityNew-data px-0 day-minute text-center Fri"
								:class="[item[day] > 0 || holidays.includes(day) ? ' table-' + item._cellVariants[day] : 'table-text-center']"
								@click="editMode(item)"
							>
								<div v-if="item[day]">
									{{ item[day] }}{{ activity.unit }}
								</div>
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>

		<Sidebar
			v-if="showExcelImport"
			:open="showExcelImport"
			title="Импорт EXCEL"
			width="75%"
			@close="showExcelImport=false"
		>
			<ActivityExcelImport
				:group_id="group_id"
				table="minutes"
				:activity_id="activity.id"
				@close="showExcelImport=false"
			/>
		</Sidebar>

		<!-- Modal edit -->
		<b-modal
			v-model="showEditModal"
			title="Настройки активности"
			size="lg"
			class="modalle"
			no-enforce-focus
			@ok="saveActivity()"
		>
			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						Название активности
					</p>
				</div>
				<div class="col-7">
					<input
						v-model="local_activity.name"
						type="text"
						class="form-control form-control-sm"
					>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						Метод
					</p>
				</div>
				<div class="col-7">
					<select
						v-model="local_activity.plan_unit"
						class="form-control form-control-sm"
					>
						<option
							v-for="(value, key) in plan_units"
							:key="key"
							:value="key"
						>
							{{ value }}
						</option>
					</select>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						План (Если сумма, на день)
					</p>
				</div>
				<div class="col-7">
					<input
						v-model="local_activity.daily_plan"
						type="number"
						class="form-control form-control-sm"
					>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						Кол-во рабочих дней в неделе
					</p>
				</div>
				<div class="col-7">
					<input
						v-model="local_activity.weekdays"
						type="number"
						class="form-control form-control-sm"
						min="1"
						max="7"
					>
				</div>
			</div>

			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						Ед. измерения (Символ в конце показателя)
					</p>
				</div>
				<div class="col-7">
					<input
						v-model="local_activity.unit"
						type="text"
						class="form-control form-control-sm"
					>
				</div>
			</div>

			<div
				class="row mb-3"
				@click="showHideUsersOverlay = true"
			>
				<div class="col-5">
					<p class="">
						Кого не&nbsp;показывать в&nbsp;таблице
					</p>
				</div>
				<div class="col-7">
					<div class="TableActivityNew-toHide form-control">
						<b-badge
							v-for="user, index in usersToHide"
							:key="index"
						>
							{{ user.name }}
						</b-badge>
						&nbsp;
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-7 offset-5 d-flex align-items-center">
					<div class="custom-control custom-checkbox">
						<input
							id="checkbox-edit"
							v-model="local_activity.editable"
							type="checkbox"
							class="custom-control-input"
						>
						<label
							for="checkbox-edit"
							class="custom-control-label"
						>
							Редактируемый
						</label>
					</div>
				</div>
			</div>
		</b-modal>
		<JobtronOverlay
			v-if="showHideUsersOverlay"
			:z="10000"
			@close="showHideUsersOverlay = false"
		>
			<AccessSelect
				:value="activityUsersToShowForm"
				:tabs="['Сотрудники']"
				search-position="beforeTabs"
				:submit-button="'Сохранить'"
				:access-dictionaries="accessDictionaries"
				absolute
				class="TableActivityNew-accessSelect"
				@submit="onSubmitHideUsers"
			/>
		</JobtronOverlay>
	</div>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar'
import PopupMenu from '@ui/PopupMenu'
import JobtronSelect from '@ui/Select'
import JobtronButton from '@ui/Button'
import JobtronCup from '@ui/Cup'
import JobtronOverlay from '@ui/Overlay'
import AccessSelect from '@ui/AccessSelect/AccessSelect.vue'
import ActivityExcelImport from '@/components/imports/ActivityExcelImport' // импорт в активности

import {
	hideAnalyticsActivityUsers,
} from '@/stores/api'

export default {
	name: 'TableActivityNew',
	components: {
		Sidebar,
		PopupMenu,
		JobtronSelect,
		JobtronButton,
		JobtronCup,
		AccessSelect,
		JobtronOverlay,
		ActivityExcelImport,
	},
	props: {
		month: Object,
		activity: Object,
		group_id: Number,
		color_invert: {
			type: Boolean,
			default: false
		},
		work_days: Number, // 5 или 6 дней в неделю
		editable: {
			type: Boolean,
			default: true
		},
		show_headers: {
			type: Boolean,
			default: true
		},
		hiddenUsers: {
			type: Array,
			default: () => {},
		}
	},
	data() {
		return {
			holidays: [],
			items: [],
			sorts: {},
			sortField: '',
			sortDir: 'asc',
			filtered: [],
			local_activity: {},
			fields: [],
			itemsArray: [],
			avgOfAverage: 0,
			totalCountDays: 0,
			currentAction: 'avg',
			sum: {},
			avg: {},
			counts: {}, // elements for avg
			percentage: [],
			records: [],
			totalRowName: '',
			accountsNumber: 0,
			user_types: 0,
			userTypeOptions: [
				{value: 0, title: 'Действующие'},
				{value: 1, title: 'Уволенные'},
				{value: 2, title: 'Стажеры'},
			],
			userShift: 0,
			userShiftOptions: [
				{value: 0, title: 'Все'},
				{value: 1, title: 'Full-Time'},
				{value: 2, title: 'Part-Time'},
			],
			filter: {
				group1: 0,
				group2: 0,
				fulltime: 0,
				parttime: 0,
			},
			plan_units: {// activity
				minutes: 'Сумма показателей',
				percent: 'Среднее значение',
				less_sum: 'Не более, сумма',
				less_avg: 'Не более, сред. зн.',
			},
			showEditModal: false,
			showExcelImport: false,
			tenant: location.hostname.split('.')[0],
			isFilters: false,
			isControls: false,

			showHideUsersOverlay: false,
			activityUsersToShowForm: [],
		};
	},
	computed: {
		isImportable(){
			if(this.tenant !== 'bp') return false
			return this.group_id == 42
				|| this.group_id == 88
				|| (this.group_id == 71 && this.activity.id == 149)
				|| (this.group_id == 71 && this.activity.id == 151)
		},
		accessDictionaries(){
			return {
				users: this.activity.records.reduce((result, user) => {
					if(!user.email) return result
					result.push({
						id: user.id,
						name: user.fullname,
						position: ''
					})
					return result
				}, []),
				profile_groups: [],
				positions: [],
			}
		},
		usersToHide(){
			return this.accessDictionaries.users.filter(user => {
				const isShowed = this.activityUsersToShowForm.find(u => u.id === user.id)
				return !isShowed
			})
		}
	},
	watch: {
		activity: function() {
			this.fetchData();
		},
		filter: {
			handler () {
				this.filterTable()
			},
			deep: true
		},
		// user_types() {
		// 	this.fetchData()
		// },
	},
	created() {
		this.getWeekends();
		this.fetchData();
		this.local_activity = this.activity
	},
	methods: {
		getWeekends(){
			var d = new Date(this.month.currentYear +'-'+ this.month.month +'-01');

			for(var i = 1;i <= this.month.daysInMonth; i++){
				var newDate = new Date(d.getFullYear(),d.getMonth(),i)
				if(newDate.getDay()==0){   //if Sunday
					this.holidays.push(i);
				}
				if(newDate.getDay()==6){   //if Saturday
					this.holidays.push(i);
				}
			}
		},
		setFirstRowAsTotals() {
			this.totalRowName = 'Итого'

			this.records.unshift({
				is_date: false,
				name: this.totalRowName,
			});
		},
		addCellVariantsArrayToRecords(){
			this.itemsArray.forEach((element, key) => {

				this.itemsArray[key]['_cellVariants'] = [];
			});
		},

		updateAvgValuesOfRecords() {
			this.itemsArray.forEach((account, index) => {
				this.itemsArray[index]['plan'] = account.plan;

				if(this.activity.plan_unit == 'minutes') {
					this.itemsArray[index]['avg'] = account.avg;
					this.itemsArray[index]['month'] = account.month;
				}
			});
		},

		setLeaders() {
			const arr = this.filtered;

			if(arr > 4) {
				arr[1].show_cup = 1;
				arr[2].show_cup = 2;
				arr[3].show_cup = 3;
			}
		},

		async fetchData() {
			let loader = this.$loading.show();

			this.activityUsersToShowForm = this.accessDictionaries.users.reduce((result, user) => {
				const isHidden = this.hiddenUsers.find(id =>  id === user.id)
				if(!isHidden) result.push({
					type: 1,
					...user,
				})
				return result
			}, [])

			this.records = this.activity.records;
			this.accountsNumber = this.activity.records.length

			if(this.show_headers) this.setFirstRowAsTotals()
			this.calculateRecordsValues()
			if(this.show_headers) this.calculateTotalsRow()
			this.setLeaders();
			this.items = this.itemsArray;
			this.filtered = this.itemsArray;
			this.addCellVariantsArrayToRecords();
			this.setCellVariants();

			this.addButtonToFirstItem();

			loader.hide();
		},

		switchAction() {
			if(this.items.length == 0) return;

			if(this.currentAction == 'avg') {
				this.currentAction = 'sum'

				Object.keys(this.sum).forEach((key) => {
					this.items[0][key] = parseFloat(this.sum[key]) === parseInt(this.sum[key]) ? parseInt(this.sum[key]) : parseFloat(this.sum[key]).toFixed(2);
				});
			}
			else if(this.currentAction == 'sum') {
				this.currentAction = 'avg'

				Object.keys(this.sum).forEach((key) => {
					this.items[0][key] = this.percentage[key] > 0
						? Number(this.sum[key] / this.percentage[key]).toFixed(2)
						: 0;
				});
			}

			this.filterTable();
		},

		addButtonToFirstItem() {
			if(this.itemsArray.length == 0) return;

			this.itemsArray[0].name = 'SPECIAL_BTN';
		},

		updateTable(items) {
			let loader = this.$loading.show();

			this.records = items;
			this.calculateRecordsValues();
			if(this.show_headers)  this.calculateTotalsRow();
			this.updateAvgValuesOfRecords();

			this.items = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			loader.hide();
		},
		setAvgCell() {},

		filterTable() {
			this.filtered = this.items.filter(el => {
				let a = true
				let b = false
				let pass_b = true
				let c = false
				let pass_c = true

				if(this.filter.group1 == 1) {
					b = b || el.group == 'Напоминание'
					pass_b = false
				}

				if(this.filter.group2 == 1) {
					b = b || el.group == 'Просрочники'
					pass_b = false
				}

				if(!pass_b) a = a && b

				if(this.filter.fulltime == 1) {
					c = c || el.full_time == 1
					pass_c = false
				}

				if(this.filter.parttime == 1) {
					c = c || el.full_time == 0
					pass_c = false
				}

				if(!pass_c) a = a && c

				return a
			})
		},

		calculateTotalsRow() {
			// вот здесь я считаю итоговые суммы минут по всем сотрудникам, и мне их видимо придется сохранить в бд

			let total = 0
			// let quantity = 0;

			for (let key in this.sum) {
				if (this.sum.hasOwnProperty(key)) {
					let sum = isNaN(parseFloat(this.sum[key])) ? 0 : parseFloat(this.sum[key]);
					let percentage = isNaN(parseFloat(this.percentage[key])) ? 0 : parseFloat(this.percentage[key]);
					if(this.activity.plan_unit == 'minutes') {
						this.itemsArray[0][key] = parseFloat(sum).toFixed(0);
						if(sum != 0)  {
							total += sum;
							// quantity++;
						}
					}
					else {
						this.itemsArray[0][key] = parseFloat(sum / percentage).toFixed(1);
						if(percentage != 0 && sum != 0) {
							total += parseFloat(sum / percentage);
							// quantity++;
						}
					}
				}
				else {
					this.itemsArray[0][key] = 0;
				}
			}

			if(this.activity.plan_unit == 'minutes') {
				this.itemsArray[0]['plan'] = Number(total).toFixed(0);
			}

			if(this.activity.plan_unit == 'less_sum') {
				this.itemsArray[0]['plan'] = Number(total).toFixed(0);
			}
		},

		setCellVariants() {
			if (typeof this.activity === 'object') {
				let minutes = this.filtered;

				if(this.activity.plan_unit != 'less_sum') {
					minutes.forEach((account, index) => {
						if (index > 0 || !this.show_headers) {
							for (let key in account) {
								if(this.activity.plan_unit != 'less_avg') {
									if (key >= 1 && key <= 31 && account[key] !== undefined && account[key] !== null) {
										if (account[key] >= this.activity.daily_plan) {
											this.filtered[index]._cellVariants[key] = 'success';
										}
										else {
											this.filtered[index]._cellVariants[key] = 'danger';
										}
									}
								}
								else {
									if (key >= 1 && key <= 31 && account[key] !== undefined && account[key] !== null) {
										if (account[key] > this.activity.daily_plan) {
											this.filtered[index]._cellVariants[key] = 'danger';
										}
										else {
											this.filtered[index]._cellVariants[key] = 'success';
										}
									}
								}
							}
						}
					});
				}
			}
		},

		editMode(item) {
			this.filtered.forEach(account => {
				account.editable = false
			})

			item.editable = item.name == 'Итого' ? false : true;
		},
		viewMode(item){
			item.editable = false
		},

		updateSettings(e, data, index, key) {
			data.editable = false
			var clearedValue = e.target.value.replace(',', '.');
			var value = null;
			if(this.activity.plan_unit == 'minutes') value = parseFloat(clearedValue);
			if(this.activity.plan_unit == 'less_sum') value = parseFloat(clearedValue);
			if(this.activity.plan_unit == 'percent') value = parseFloat(clearedValue).toFixed(1);
			if(this.activity.plan_unit == 'less_avg') value = parseFloat(clearedValue).toFixed(1);
			if(value < 0) this.filtered[index][key] = 0;

			if(value > 999) this.filtered[index][key] = 999;

			this.filtered[index][key] = Number(this.filtered[index][key])
			let employee_id = data.id;

			let filtered = this.filtered;

			let loader = this.$loading.show();
			// let yesar = new Date().getFullYear();

			this.updateTable(filtered);

			this.axios.post('/timetracking/analytics/update-stat', {
				month: this.month.month,
				year: this.month.currentYear,
				group_id: this.activity.group_id,
				employee_id: employee_id,
				id: this.activity.id,
				day: key,
				value: '' + (value || 0)
			}).then(() => {
				loader.hide();
			});
		},

		exportData() {
			this.isControls = false
			var link = '/timetracking/analytics/activity/exportxx';
			link += '?month=' + this.$moment(
				`${this.month.currentMonth}`,
				'MMMM YYYY'
			).format('MM');
			link += '&year=' + new Date().getFullYear();
			link += '&group_id=' + this.activity.group_id;

			if(this.filter.group1 == 1 && this.filter.group2 == 0) link += '&only_nap=1';
			if(this.filter.group1 == 0 && this.filter.group2 == 1) link += '&only_pros=1';
			if(this.filter.fulltime == 1 && this.filter.parttime == 0) link += '&only_full=1';
			if(this.filter.fulltime == 0 && this.filter.parttime == 1) link += '&only_part=1';

			window.location.href = link;
		},

		calculateRecordsValues() {
			this.sum = {};
			if(this.show_headers) {
				this.itemsArray = [{
					'plan': '',
					'avg': '',
				}];
			}
			else {
				this.itemsArray = [];
			}

			this.totalCountDays = 0;
			this.avgOfAverage = 0;
			this.percentage = []

			// let row0_avg = 0;
			// let row0_avg_items = 0;

			let avg_of_column = 0;
			let quan_of_column = 0;

			this.records.forEach(account => {
				if(this.hiddenUsers.includes(account.id)) return
				let countWorkedDays = 0;
				let cellValues = [];

				if (account.name != this.totalRowName) {
					let sumForOne = 0;
					for (let key in account) {
						let value = account[key];

						if (key >= 1 && key <= 31) {
							cellValues[key] = Number(value);

							if (isNaN(this.sum[key])) this.sum[key] = 0;

							if (isNaN(this.percentage[key])) this.percentage[key] = 0;

							this.sum[key] = this.sum[key] + Number(account[key]); // vertical sum

							if(Number(account[key]) > 0) {
								this.percentage[key] = this.percentage[key] + 1;

								sumForOne += Number(account[key]); // horizontal sum
								countWorkedDays++;
								this.totalCountDays++;
							}
						}
					}

					cellValues['plan_unit'] = this.activity.plan_unit;

					let daily_plan = Number(this.activity.daily_plan);

					if(this.activity.plan_unit == 'minutes') {
						if(account.full_time == 0)  daily_plan = Number(daily_plan / 2);

						cellValues['plan'] = sumForOne;

						let average = (sumForOne / countWorkedDays).toFixed(2);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['avg'] = finishAverage;

						if(finishAverage != 0) {
							quan_of_column++;
							avg_of_column += Number(finishAverage);
						}

						let wd = Number(this.activity.workdays);
						cellValues['month'] = account.applied_from != 0 ? Number(account.applied_from) * daily_plan : Number(wd) * daily_plan;

						cellValues['percent'] = this.toFloat(
							Number(sumForOne) / (Number(cellValues['month']) / 100)
						) + '%';

						cellValues['_percent'] = Number(
							Number(sumForOne) / (Number(cellValues['month']) / 100)
						);

						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);

						cellValues['plan'] = Number(sumForOne).toFixed(2);
					}

					if(this.activity.plan_unit == 'percent') {
						let average = (sumForOne / countWorkedDays).toFixed(2);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['month'] = daily_plan;
						cellValues['plan'] = finishAverage;
						cellValues['avg'] = finishAverage;

						if(finishAverage != 0) {
							quan_of_column++;
							avg_of_column += Number(finishAverage);
						}

						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);
					}

					if(this.activity.plan_unit == 'less_avg') {
						let average = (sumForOne / countWorkedDays).toFixed(2);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['month'] = daily_plan;
						cellValues['plan'] = finishAverage;
						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);

						if(finishAverage != 0) {
							quan_of_column++;
							avg_of_column += Number(finishAverage);
						}
					}

					if(this.activity.plan_unit == 'less_sum') {
						cellValues['month'] = daily_plan;
						cellValues['plan'] =  Number(sumForOne).toFixed(0);

						this.avgOfAverage = parseFloat(this.avgOfAverage) + Number(sumForOne);
					}
				}

				if((this.user_types == 1 && account.fired == 1 && account.is_trainee == false) || (this.user_types == 0 && account.fired == 0 && account.is_trainee == false) || (this.user_types == 2 && account.is_trainee)) {
					this.itemsArray.push({
						name: account.name,
						lastname: account.lastname,
						fullname: account.fullname,
						id: account.id,
						editable: false,
						group: account.group,
						fired: account.fired,
						show_cup: 0,
						applied_from: account.applied_from,
						full_time: account.full_time,
						email: account.email,
						...cellValues,
					});
				}
			});

			let avg = quan_of_column > 0 ? avg_of_column / quan_of_column : '';

			if(this.show_headers)  {
				if(this.activity.plan_unit == 'minutes') {
					this.itemsArray[0]['avg'] = Number(avg).toFixed(0);
				}
				else {
					this.itemsArray[0]['plan'] = Number(avg).toFixed(2);
				}
			}
			// this.records.forEach(account => {
			// 	if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {

			// 		row0_avg += parseFloat(account['plan']);
			// 		row0_avg_items++;
			// 	}
			// })
		},

		toFloat(number) {
			return Number(number).toFixed(2);
		},

		editActivity() {
			this.isControls = false
			this.showEditModal = true;
		},

		async saveActivity() {
			const loader = this.$loading.show();

			try {
				await this.axios.post('/timetracking/analytics/edit-activity', {
					month: this.month.month,
					year: this.month.currentYear,
					activity: this.local_activity,
				})

				await hideAnalyticsActivityUsers({
					group_id: this.group_id,
					groups: {
						[this.activity.id]: this.usersToHide.map(user => user.id)
					}
				})

				this.$toast.success('Обновите, чтобы посмотреть новую таблицу!')
			}
			catch (error) {
				this.$toast.error('Ошибка!')
				alert(error)
			}
			loader.hide()
		},

		sort(field) {
			if(this.sorts[field] === undefined) {
				this.sorts[field] = 'asc';
			}

			this.sortField = field
			this.sortDir = this.sorts[field]

			let item = this.items[0];

			this.items.shift();
			if(this.sorts[field] === 'desc') {
				if(field == 'name') {
					this.items.sort((a, b) => (a[field] > b[field]) ? 1 : -1);
				}
				else {
					this.items.sort((a, b) => (Number(a[field]) > Number(b[field])) ? 1 : -1);
				}

				this.sorts[field] = 'asc';
			}
			else {
				if(field == 'name') {
					this.items.sort((a, b) => (a[field] < b[field]) ? 1 : -1);
				}
				else {
					this.items.sort((a, b) => (Number(a[field]) < Number(b[field])) ? 1 : -1);
				}
				this.sorts[field] = 'desc';
			}

			this.items.unshift(item);
		},

		onClickImport(){
			this.isControls = false
			this.showExcelImport = !this.showExcelImport
		},

		onClickFilters(){
			this.isFilters = true
			this.isControls = false
		},
		onClickFiltersOutside(){
			this.isFilters = false
		},
		onClickControls(){
			this.isControls = true
			this.isFilters = false
		},
		onClickControlsOutside(){
			this.isControls = false
		},
		onClickFilter(){
			this.isFilters = false
			this.fetchData()
			switch(+this.userShift){
			case 0:
				this.filter.fulltime = 0
				this.filter.parttime = 0
				break
			case 1:
				this.filter.fulltime = 1
				this.filter.parttime = 0
				break
			case 2:
				this.filter.fulltime = 0
				this.filter.parttime = 1
				break
			}
			this.filterTable()
		},
		async onSubmitHideUsers(users){
			this.activityUsersToShowForm = users
			this.showHideUsersOverlay = false
		}
	},
};
</script>

<style lang="scss">
.my-table.m2 tr .badge {
	opacity: 1;
}

.day-minute {
	padding: 0 10px!important;
	text-align: center;
	vertical-align: middle;

	div {
		font-size: 0.8rem;
	}
	&.table-success {
		background-color: #01c601 !important;
	}

	&.table-danger {
		background-color: #ff7669  !important;
	}
}

.inverted {
	.day-minute {
		&.table-success {
			background-color: #ff7669  !important;
		}

		&.table-danger {
			background-color: #01c601 !important;
		}
	}
}
.table td,
.table th,
.table thead th{
	vertical-align: middle;
	min-width: 42px;
	text-align: center;
}
.table.b-table.table-sm>thead>tr>[aria-sort]:not(.b-table-sort-icon-left),
.table.b-table.table-sm>tfoot>tr>[aria-sort]:not(.b-table-sort-icon-left) {
	background-image: none !important;
	min-width: 32px;
}
.table .stat {
	background-color: #d9edff;
	border: 1px solid #bbd3e9!important;
}
.table {
	position: relative;
}
.b-table-sticky-column{
	position: sticky;
	left: 0;
	z-index: 2;
}
.wd {
	font-size: 0.75rem;
	width: max-content;
	font-weight: 500;
}
.table .stat.plan{
	background-color: #779bbb;
	color: #fff;
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
.text-white {
	color:#fff;
}
.table-primary, .table-primary>td, .table-primary>th {
	background-color: #dddfe5;
}
.mywarning{
	background-color: #f7f2a6;
}

.TableActivityNew{
	min-height: 300px;
	&-contlors{
		position: relative;
	}
	&-filters{
		position: relative;
	}
	&-filtersPopup{
		width: 250px;
		padding: 10px;
	}
	&-filtersIcon{
		width: 14px;
		filter: invert(27%) sepia(73%) saturate(2928%) hue-rotate(209deg) brightness(96%) contrast(89%);
	}
	// --- костылище
	&-data{
		width: 42px !important;
		max-width: 42px !important;
		padding: 0 !important;
		.cell-input{
			width: 42px !important;
			border: none !important;
			background-color: transparent !important;
			&:focus{
				border: none !important;
				background-color: transparent !important;
				box-shadow: none !important;
			}
		}
	}
	// --- костылище
	&-toHide{
		display: flex;
		flex-flow: row wrap;
		align-items: center;
		justify-content: flex-start;
		gap: 5px;

		min-height: 35px;
		padding: 0 20px;

		border: 1px solid #e8e8e8;
		border-radius: 6px;
		font-size: 14px;
		line-height: 1.3;
		background-color: #F7FAFC;
	}
}
</style>
