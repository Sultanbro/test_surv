<template>
	<div
		class="mb-3 index__content custom-scroll"
		:class="{'v-loading': loading}"
	>
		<div
			v-if="show_headers"
			class="d-flex align-items-center mb-2"
		>
			<h4 class="mr-2">
				{{ activity.name }} <i
					class="fa fa-cog show"
					@click="editActivity()"
				/>
			</h4>

			<div class="my-2 d-flex ml-auto mr-3">
				<div class="d-flex">
					<div class="mr-2">
						<b-form-radio
							v-model="user_types"
							name="some-radios"
							value="0"
						>
							Действующие
						</b-form-radio>
					</div>
					<div class="mr-2">
						<b-form-radio
							v-model="user_types"
							name="some-radios"
							value="1"
						>
							Уволенные
						</b-form-radio>
					</div>
					<div class="mr-2">
						<b-form-radio
							v-model="user_types"
							name="some-radios"
							value="2"
						>
							Стажеры
						</b-form-radio>
					</div>
				</div>

				<b-form-checkbox
					v-model="filter.fulltime"
					:value="1"
					:unchecked-value="0"
					class="mr-2"
				>
					Full-Time
				</b-form-checkbox>
				<b-form-checkbox
					v-model="filter.parttime"
					:value="1"
					:unchecked-value="0"
					class="mr-2"
				>
					Part-Time
				</b-form-checkbox>
			</div>


			<div>
				<a
					v-if="group_id == 42 || group_id == 88 || (group_id == 71 && activity.id == 149) || (group_id == 71 && activity.id == 151)"
					class="btn btn-success rounded mr-2 text-white"
					@click="showExcelImport = !showExcelImport"
				>
					<i class="fa fa-upload" />
					Импорт</a>
			</div>


			<div>
				<a
					href="javascript:"
					class="btn btn-success rounded"
					@click="exportData()"
				>
					<i class="far fa-file-excel" />
					Экспорт</a>
			</div>
		</div>

		<table
			class="indicators-table-fixed"
			:class="{'inverted' : color_invert}"
		>
			<tr>
				<th
					class="indicators-table-fixed-name text-left max-content pl-4"
					:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
				>
					<div class="max-content">
						Сотрудник
					</div>
					<i
						v-if="show_headers"
						class="fa fa-sort ml-2"
						@click="sort('fullname')"
					/>
				</th>

				<template v-if="activity.plan_unit == 'minutes'">
					<th
						class="indicators-table-fixed-avg text-center"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>Ср.</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('avg')"
						/>
					</th>
					<th
						class="indicators-table-fixed-month text-center"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>План</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('month')"
						/>
					</th>
					<th
						class="indicators-table-fixed-plan text-center plan"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>Вып.</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('plan')"
						/>
					</th>
					<th
						class="indicators-table-fixed-percent text-center"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>%</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('_percent')"
						/>
					</th>
				</template>

				<template v-else>
					<th
						class="indicators-table-fixed-hmonth text-center"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>План</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('month')"
						/>
					</th>
					<th
						class="indicators-table-fixed-hplan text-center"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>Вып.</div>
						<i
							v-if="show_headers"
							class="fa fa-sort ml-2"
							@click="sort('plan')"
						/>
					</th>
				</template>

				<template v-for="day in month.daysInMonth">
					<th
						:key="day"
						class="text-center px-1"
					>
						<div>{{ day }}</div>
					</th>
				</template>
			</tr>

			<tr
				v-for="(item, index) in filtered"
				:key="index"
			>
				<td
					v-if="item.name == 'SPECIAL_BTN'"
					class="indicators-table-fixed-name"
					:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
				>
					<button
						class="btn btn-light rounded"
						@click="switchAction"
					>
						Сумма\Среднее
					</button>
				</td>

				<td
					v-else
					class="indicators-table-fixed-name text-left max-content"
					:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					:title="item.id + ' ' + item.email"
				>
					<div class="d-flex max-content">
						{{ item.lastname }} {{ item.name }}
						<img
							v-if="item.show_cup == 1"
							src="images/dist/first-place.png"
							alt="icon"
						>
						<img
							v-if="item.show_cup == 2"
							src="images/dist/second-place.png"
							alt="icon"
						>
						<img
							v-if="item.show_cup == 3"
							src="images/dist/third-place.png"
							alt="icon"
						>
					</div>
				</td>

				<template v-if="activity.plan_unit == 'minutes'">
					<td
						class="indicators-table-fixed-avg class blue"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>{{ item.avg }}</div>
					</td>
					<td
						class="indicators-table-fixed-month class blue"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div :title="activity.daily_plan + ' * ' + item.applied_from">
							{{ item.month }}
						</div>
					</td>
					<td
						class="indicators-table-fixed-plan class blue plan"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>{{ item.plan }}</div>
					</td>
					<td
						class="indicators-table-fixed-percent class blue"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>{{ item.percent }}</div>
					</td>
				</template>

				<template v-else>
					<td
						class="indicators-table-fixed-hmonth class blue"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>{{ item.month }}</div>
					</td>
					<td
						class="indicators-table-fixed-hplan class blue"
						:class="[ isDesktop ? 'sticky-left' : 'relative' ]"
					>
						<div>{{ item.plan }}</div>
					</td>
				</template>


				<template v-for="day in month.daysInMonth">
					<td
						v-if="item.editable && editable"
						:key="day"
						:class="'text-center ' + item._cellVariants[day]"
					>
						<div>
							<input
								v-model="item[day]"
								type="number"
								class="form-control cell-input"
								@change="updateSettings($event, item, index, day)"
							>
						</div>
					</td>
					<td
						v-else-if="holidays.includes(day) && item[day] > 0"
						:key="day + 'a'"
						:class="'text-center ' + item._cellVariants[day]"
						@click="editMode(item)"
					>
						<div v-if="item[day]">
							{{ item[day] }}{{ activity.unit }}
						</div>
						<div v-else />
					</td>
					<td
						v-else-if="holidays.includes(day)"
						:key="day + 'b'"
						:class="'text-center mywarning'"
						@click="editMode(item)"
					>
						<div v-if="item[day]">
							{{ item[day] }}{{ activity.unit }}
						</div>
						<div v-else />
					</td>
					<td
						v-else
						:key="day + 'c'"
						:class="[item[day] > 0 || holidays.includes(day) ? 'text-center ' + item._cellVariants[day] : 'text-center']"
						@click="editMode(item)"
					>
						<div v-if="item[day]">
							{{ item[day] }}{{ activity.unit }}
						</div>
						<div v-else />
					</td>
				</template>
			</tr>
		</table>

		<sidebar
			v-if="showExcelImport"
			title="Импорт EXCEL"
			:open="showExcelImport"
			width="75%"
			@close="showExcelImport=false"
		>
			<activity-excel-import
				:group_id="group_id"
				table="minutes"
				:activity_id="activity.id"
				@close="showExcelImport=false"
			/>
		</sidebar>

		<!-- Modal edit -->
		<b-modal
			v-model="showEditModal"
			title="Настройки активности"
			size="lg"
			class="modalle"
			@ok="saveActivity()"
		>
			<div class="row">
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

			<div class="row">
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

			<div class="row">
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

			<div class="row">
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

			<div class="row">
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

			<div class="row">
				<div class="col-5 d-flex align-items-center">
					<p class="mb-0">
						Редактируемый
					</p>
					<input
						v-model="local_activity.editable"
						type="checkbox"
						class="form-control form-control-sm"
					>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */
/* eslint-disable vue/prop-name-casing */

export default {
	name: 'TableDefault',
	props: {
		month: {
			type: Object,
			default: null
		},
		activity: {
			type: Object,
			default: null
		},
		group_id: {
			type: Number,
			default: 0
		},
		color_invert: {
			type: Boolean,
			default: false
		},
		work_days: {
			type: Number,
			default: 5,
		}, // 5 или 6 дней в неделю
		editable: {
			type: Boolean,
			default: true
		},
		show_headers: {
			type: Boolean,
			default: true
		},
	},
	data() {
		return {
			holidays: [],
			items: [],
			sorts: {},
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
			loading: false
		};
	},
	computed: {
		isDesktop(){
			return this.$viewportSize.width >= 1300
		},
	},
	watch: {
		activity: function() { // watch it
			this.fetchData();
		},
		filter: {
			handler () {
				this.filterTable()
			},
			deep: true
		},
		user_types() {
			this.fetchData()
		},
	},
	created() {
		this.getWeekends();
		this.fetchData();
		this.local_activity = this.activity
	},
	methods: {
		/**
         * get weekends
         */
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

		/**
         * setFirstRowAsTotals
         */
		setFirstRowAsTotals() {

			this.totalRowName = 'Итого'

			this.records.unshift({
				is_date: false,
				name: this.totalRowName,
			});
		},

		/**
         * addCellVariantsArrayToRecords
         */
		addCellVariantsArrayToRecords(){
			this.itemsArray.forEach((element, key) => {

				this.itemsArray[key]['_cellVariants'] = [];
			});
		},

		/**
         * updateAvgValuesOfRecords
         */
		updateAvgValuesOfRecords() {
			this.itemsArray.forEach((account, index) => {
				this.itemsArray[index]['plan'] = account.plan;

				if(this.activity.plan_unit == 'minutes') {
					this.itemsArray[index]['avg'] = account.avg;
					this.itemsArray[index]['month'] = account.month;
				}

			});
		},

		/**
         * setLeaders
         */
		setLeaders() {

			let arr = this.itemsArray;

			arr.sort((a, b) => Number(a.plan) < Number(b.plan)  ?
				1 : Number(a.plan) > Number(b.plan) ? -1 : 0);

			if(arr[0]) arr[0].show_cup = 1
			if(arr[1]) arr[1].show_cup = 2
			if(arr[2]) arr[2].show_cup = 3
			this.$forceUpdate()
		},

		/**
         * fetch data
         */
		fetchData() {
			this.loading = true

			this.records = this.activity.records;
			this.accountsNumber = this.activity.records.length

			if(this.show_headers) this.setFirstRowAsTotals()
			this.calculateRecordsValues()
			if(this.show_headers) this.calculateTotalsRow()
			if(!this.show_headers) this.setLeaders();
			this.items = this.itemsArray;
			this.filtered = this.itemsArray;
			this.addCellVariantsArrayToRecords();
			this.setCellVariants();

			if(this.show_headers) this.addButtonToFirstItem();

			this.loading = false
		},

		/**
         * switch from avg to sum
         */
		switchAction() {
			if(this.items.length == 0) return;

			if(this.currentAction == 'avg') {
				this.currentAction = 'sum'

				Object.keys(this.sum).forEach((key) => {
					this.items[0][key] = this.sum[key];
				});

			} else if(this.currentAction == 'sum') {

				this.currentAction = 'avg'

				Object.keys(this.sum).forEach((key) => {
					this.items[0][key] = this.percentage[key] > 0
						? Number(this.sum[key] / this.percentage[key]).toFixed(2)
						: 0;
				});
			}

			this.filterTable();
		},

		/**
         * addButtonToFirstItem
         */
		addButtonToFirstItem() {
			if(this.itemsArray.length == 0) return;

			this.itemsArray[0].name = 'SPECIAL_BTN';
		},

		/**
         * action
         */
		updateTable(items) {
			this.loading = true

			this.records = items;
			this.calculateRecordsValues();
			if(this.show_headers)  this.calculateTotalsRow();
			this.updateAvgValuesOfRecords();


			this.items = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			this.loading = false
		},

		/**
         * action
         */
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

		/**
         * action
         */
		calculateTotalsRow() {


			// вот здесь я считаю итоговые суммы минут по всем сотрудникам, и мне их видимо придется сохранить в бд

			let total = 0
			// eslint-disable-next-line no-unused-vars
			let quantity = 0

			for (let key in this.sum) {
				if (this.sum.hasOwnProperty(key)) {
					let sum = isNaN(parseFloat(this.sum[key])) ? 0 : parseFloat(this.sum[key]);
					let percentage = isNaN(parseFloat(this.percentage[key])) ? 0 : parseFloat(this.percentage[key]);
					if(this.activity.plan_unit == 'minutes') {
						this.itemsArray[0][key] = parseFloat(sum).toFixed(0);
						if(sum != 0)  {
							total += sum;
							// eslint-disable-next-line no-unused-vars
							quantity++;
						}
					} else {
						this.itemsArray[0][key] = parseFloat(sum / percentage).toFixed(1);
						if(percentage != 0 && sum != 0) {
							total += parseFloat(sum / percentage);
							// eslint-disable-next-line no-unused-vars
							quantity++;
						}
					}
				} else {
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

		/**
         * action
         */
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
											this.filtered[index]._cellVariants[key] = 'green';
										} else {
											this.filtered[index]._cellVariants[key] = 'red';
										}
									}
								} else {
									if (key >= 1 && key <= 31 && account[key] !== undefined && account[key] !== null) {
										if (account[key] > this.activity.daily_plan) {
											this.filtered[index]._cellVariants[key] = 'danger';
										} else {
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

		/**
         * action
         */
		editMode(item) {
			this.filtered.forEach(account => {
				account.editable = false
			})

			item.editable = item.name == 'Итого' ? false : true;
		},

		/**
         * action
         */
		updateSettings(e, data, index, key) {

			data.editable = false
			var clearedValue = e.target.value.replace(',', '.');
			var value = null;
			if(this.activity.plan_unit == 'minutes') value = parseFloat(clearedValue);
			if(this.activity.plan_unit == 'less_sum') value = parseFloat(clearedValue);
			if(this.activity.plan_unit == 'percent') value = parseFloat(clearedValue).toFixed(1);
			if(this.activity.plan_unit == 'less_avg') value = parseFloat(clearedValue).toFixed(1);
			if(value < 0) {
				this.filtered[index][key] = 0;
			}

			if(value > 999) {
				this.filtered[index][key] = 999;
			}

			this.filtered[index][key] = Number(this.filtered[index][key])
			let employee_id = data.id;

			let filtered = this.filtered;

			let loader = this.$loading.show();

			this.updateTable(filtered);



			this.axios
				.post('/timetracking/analytics/update-stat', {
					month: this.month.month,
					year: this.month.currentYear,
					group_id: this.activity.group_id,
					employee_id: employee_id,
					id: this.activity.id,
					day: key,
					value: '' + (value || 0)
				})
				.then(() => {
					loader.hide();
				});

		},

		/**
         * action
         */
		exportData() {
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

		/**
         * action
         */
		calculateRecordsValues() {
			this.sum = {};
			if(this.show_headers) {
				this.itemsArray = [{
					'plan': '',
					'avg': '',
				}];
			} else {
				this.itemsArray = [];
			}

			this.totalCountDays = 0;
			this.avgOfAverage = 0;
			this.percentage = []

			// eslint-disable-next-line no-unused-vars
			let row0_avg = 0;
			// eslint-disable-next-line no-unused-vars
			let row0_avg_items = 0;

			let avg_of_column = 0;
			let quan_of_column = 0;

			this.records.forEach(account => {
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

						cellValues['percent'] =
							this.toFloat(
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
				} else {
					this.itemsArray[0]['plan'] = Number(avg).toFixed(2);
				}
			}

			this.records.forEach(account => {
				if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {

					row0_avg += parseFloat(account['plan']);
					row0_avg_items++;
				}
			})




		},

		/**
         * action
         */
		toFloat(number) {
			return Number(number).toFixed(2);
		},

		/**
         * action
         */
		editActivity() {
			this.showEditModal = true;
		},

		/**
         * action
         */
		saveActivity() {
			let loader = this.$loading.show();
			this.axios.post('/timetracking/analytics/edit-activity', {
				month: this.month.month,
				year: this.month.currentYear,
				activity: this.local_activity,
			}).then(() => {
				this.$toast.success('Обновите, чтобы посмотреть новую таблицу!')
				this.showEditModal = false
				loader.hide()
			}).catch(error => {
				loader.hide()
				this.$toast.error('Ошибка!')
				alert(error)
			});
		},

		/**
         * action
         */
		sort(field) {

			if(this.sorts[field] === undefined) {
				this.sorts[field] = 'asc';
			}

			let item = this.items[0];

			this.items.shift();
			if(this.sorts[field] === 'desc') {
				if(field == 'name') {
					this.items.sort((a, b) => (a[field] > b[field]) ? 1 : -1);
				} else {
					this.items.sort((a, b) => (Number(a[field]) > Number(b[field])) ? 1 : -1);
				}

				this.sorts[field] = 'asc';
			} else {
				if(field == 'name') {
					this.items.sort((a, b) => (a[field] < b[field]) ? 1 : -1);
				} else {
					this.items.sort((a, b) => (Number(a[field]) < Number(b[field])) ? 1 : -1);
				}
				this.sorts[field] = 'desc';
			}

			this.items.unshift(item);
		},
	},
};
</script>

<style lang="scss">

</style>
