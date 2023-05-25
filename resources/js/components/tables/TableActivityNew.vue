<template>
	<div class="mb-3">
		<div
			class="d-flex align-items-center justify-content-end mb-2"
			style="margin-top: -38px;"
			v-if="show_headers"
		>
			<div class="d-flex mr-3">
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
					class="ml-3"
				>
					Part-Time
				</b-form-checkbox>
			</div>
			<select
				v-model="user_types"
				class="form-control w-200px mr-3"
			>
				<option value="0">
					Действующие
				</option>
				<option value="1">
					Уволенные
				</option>
				<option value="2">
					Стажеры
				</option>
			</select>
			<div>
				<a
					@click="showExcelImport = !showExcelImport"
					v-if="group_id == 42 || group_id == 88 || (group_id == 71 && activity.id == 149) || (group_id == 71 && activity.id == 151)"
					class="btn btn-success text-white rounded"
				>
					<i class="fa fa-upload" />
					Импорт</a>
				<a
					href="#"
					@click="exportData()"
					class="btn btn-success ml-1 rounded"
				>
					<i class="far fa-file-excel" />
					Экспорт</a>
				<button
					class="btn btn-info"
					@click="editActivity()"
				>
					<i class="icon-nd-settings" />
				</button>
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
							class="text-center px-1"
							v-for="day in month.daysInMonth"
							:key="day"
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
							class="table-primary b-table-sticky-column text-left px-2 t-name"
							:title="item.id + ' ' + item.email"
							v-else
						>
							<div class="wd d-flex">
								{{ item.lastname }} {{ item.name }}
								<b-badge
									variant="success"
									v-if="item.group == 'Просрочники'"
								>
									{{ item.group }}
								</b-badge>
								<b-badge
									variant="primary"
									v-else
								>
									{{ item.group }}
								</b-badge>

								<div v-if="item.show_cup == 1">
									<img
										src="/images/goldencup.png"
										class="goldencup ml-2"
										alt=""
									>
								</div>
								<div v-if="item.show_cup == 2">
									<img
										src="/images/silvercup.png"
										class="goldencup ml-2"
										alt=""
									>
								</div>
								<div v-if="item.show_cup == 3">
									<img
										src="/images/bronzecup.png"
										class="goldencup ml-2"
										alt=""
									>
								</div>
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
								:class="'px-0 day-minute text-center Fri table-' + item._cellVariants[day]"
							>
								<div>
									<input
										type="number"
										v-model="item[day]"
										@change="updateSettings($event, item, index, day)"
										@focusout="viewMode(item)"
										class="form-control cell-input"
									>
								</div>
							</td>
							<td
								v-else-if="holidays.includes(day) && item[day] > 0"
								:key="day + 'a'"
								@click="editMode(item)"
								:class="'px-0 day-minute text-center Fri table-' + item._cellVariants[day]"
							>
								<div v-if="item[day]">
									{{ item[day] }}{{ activity.unit }}
								</div>
							</td>
							<td
								v-else-if="holidays.includes(day)"
								@click="editMode(item)"
								:key="day + 'b'"
								:class="'px-0 day-minute text-center Fri mywarning'"
							>
								<div v-if="item[day]">
									{{ item[day] }}{{ activity.unit }}
								</div>
							</td>
							<td
								v-else
								:key="day + 'c'"
								@click="editMode(item)"
								:class="[item[day] > 0 || holidays.includes(day) ? 'px-0 day-minute text-center Fri table-' + item._cellVariants[day] : 'px-0 day-minute text-center Fri table-text-center']"
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
			@close="showExcelImport=false"
			width="75%"
		>
			<ActivityExcelImport
				:group_id="group_id"
				table="minutes"
				@close="showExcelImport=false"
				:activity_id="activity.id"
			/>
		</Sidebar>

		<!-- Modal edit -->
		<b-modal
			v-model="showEditModal"
			title="Настройки активности"
			@ok="saveActivity()"
			size="lg"
			class="modalle"
		>
			<div class="row mb-3">
				<div class="col-5">
					<p class="">
						Название активности
					</p>
				</div>
				<div class="col-7">
					<input
						type="text"
						class="form-control form-control-sm"
						v-model="local_activity.name"
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
							:value="key"
							v-for="(value, key) in plan_units"
							:key="key"
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
						type="number"
						class="form-control form-control-sm"
						v-model="local_activity.daily_plan"
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
						type="number"
						class="form-control form-control-sm"
						v-model="local_activity.weekdays"
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
						type="text"
						class="form-control form-control-sm"
						v-model="local_activity.unit"
					>
				</div>
			</div>

			<div class="row">
				<div class="col-7 offset-5 d-flex align-items-center">
					<div class="custom-control custom-checkbox">
						<input
							id="checkbox-edit"
							type="checkbox"
							class="custom-control-input"
							v-model="local_activity.editable"
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
	</div>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar' // сайдбар table
import ActivityExcelImport from '@/components/imports/ActivityExcelImport' // импорт в активности

export default {
	name: 'TableActivityNew',
	components: {
		Sidebar,
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
			showExcelImport: false
		};
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
		getWeekends(){
			/*
            var d = new Date();
            var getTot = daysInMonth(d.getMonth(),d.getFullYear()); //Get total days in a month
            var sat = new Array();   //Declaring array for inserting Saturdays
            var sun = new Array();   //Declaring array for inserting Sundays

            for(var i=1;i<=getTot;i++){    //looping through days in month
                var newDate = new Date(d.getFullYear(),d.getMonth(),i)
                if(newDate.getDay()==0){   //if Sunday
                    sun.push(i);
                }
                if(newDate.getDay()==6){   //if Saturday
                    sat.push(i);
                }

            }
            console.log(sat);*/
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

			let arr = this.itemsArray;

			// let first_item = this.itemsArray[0];
			//this.itemsArray.shift();

			arr.sort((a, b) => Number(a.plan) < Number(b.plan)  ?
				1 : Number(a.plan) > Number(b.plan) ? -1 : 0);

			if(this.itemsArray.length > 3) {
				arr[0].show_cup = 1;
				arr[1].show_cup = 2;
				arr[2].show_cup = 3;
			}


			//this.itemsArray.unshift(first_item);
		},

		fetchData() {
			let loader = this.$loading.show();

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

			this.addButtonToFirstItem();

			loader.hide();


		},

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
					} else {
						this.itemsArray[0][key] = parseFloat(sum / percentage).toFixed(1);
						if(percentage != 0 && sum != 0) {
							total += parseFloat(sum / percentage);
							// quantity++;
						}
					}
				} else {
					this.itemsArray[0][key] = 0;
				}
			}

			console.log('TOTAL ' + total)



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
										} else {
											this.filtered[index]._cellVariants[key] = 'danger';
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

		editMode(item) {
			this.filtered.forEach(account => {
				account.editable = false
			})

			item.editable = item.name == 'Итого' ? false : true;
		},
		viewMode(item){
			console.log('viewMode', item)
			item.editable = false
		},

		updateSettings(e, data, index, key) {
			data.editable = false
			console.log(key);
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
			// let yesar = new Date().getFullYear();

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

			// let row0_avg = 0;
			// let row0_avg_items = 0;

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
				} else {
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
			this.showEditModal = true;
		},

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
.table td, .table th, .table thead th{
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
.analytics-page{
	.cell-input{
		padding: 0 !important;
	}
}
.bg-white {
    background: white;
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
</style>
