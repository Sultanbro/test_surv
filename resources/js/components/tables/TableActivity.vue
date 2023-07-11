<template>
	<div class="mb-3">
		<div
			class="d-flex align-items-center mb-2"
			v-if="show_headers"
		>
			<h4 class="mr-2">
				{{ activity.name }}
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


			<div v-if="group_id == 58 || group_id == 59 || group_id == 42">
				<!-- Ozon -->
				<a
					@click="showExcelImport = !showExcelImport"
					class="btn btn-success rounded mr-2 text-white"
				>
					<i class="fa fa-upload" />
					Импорт</a>
			</div>


			<div>
				<a
					href="#"
					@click="exportData()"
					class="btn btn-success rounded"
				>
					<i class="far fa-file-excel" />
					Экспорт</a>
			</div>
		</div>

		<table
			class="table b-table table-bordered table-sm table-responsive"
			:class="{'inverted' : color_invert}"
		>
			<tr>
				<th class="b-table-sticky-column text-left px-1 t-name bg-white">
					<div class="wd">
						Имя сотрудника
					</div>
				</th>

				<template v-if="activity.plan_unit == 'minutes'">
					<th class="text-center px-1 day-minute">
						<div>Ср.</div>
					</th>
					<th class="text-center px-1 day-minute">
						<div>План</div>
					</th>
					<th class="text-center px-1 day-minute plan">
						<div>Вып.</div>
					</th>
					<th class="text-center px-1 day-minute">
						<div>%</div>
					</th>
				</template>

				<template v-else>
					<th class="text-center px-1 day-minute">
						<div>План</div>
					</th>
					<th class="text-center px-1 day-minute">
						<div>Вып.</div>
					</th>
				</template>

				<th
					v-for="day in month.daysInMonth"
					:key="day"
					class="text-center px-1"
				>
					<div>{{ day }}</div>
				</th>
			</tr>

			<tr
				v-for="(item, index) in filtered"
				:key="index"
			>
				<td
					class="table-primary b-table-sticky-column text-left px-2 t-name"
					:title="item.id + ' ' + item.email"
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
								class="form-control cell-input"
							>
						</div>
					</td>
					<td
						v-else
						@click="editMode(item)"
						:key="day + 'a'"
						:class="'px-0 day-minute text-center Fri table-' + item._cellVariants[day]"
					>
						<div v-if="item[day]">
							{{ item[day] }}{{ activity.unit }}
						</div>
					</td>
				</template>
			</tr>
		</table>

		<sidebar
			title="Импорт EXCEL"
			:open="showExcelImport"
			@close="showExcelImport=false"
			v-if="showExcelImport"
			width="75%"
		>
			<activity-excel-import
				:group_id="group_id"
				table="minutes"
				@close="showExcelImport=false"
				:activity_id="activity.id"
			/>
		</sidebar>
	</div>
</template>

<script>

// const KASPI_NAP = 35;
// const KASPI_PROS = 42;
// const KASPI_PROS_JANSAYA = 56;
// const DM = 31;
// const OZON1 = 58;
// const OZON2 = 59;

export default {
	name: 'TActivity',
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
			items: [],
			filtered: [],
			fields: [],
			itemsArray: [],
			avgOfAverage: 0,
			totalCountDays: 0,
			sum: {},
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
		this.fetchData();
	},
	methods: {

		setLeaders() {
			let arr = this.itemsArray;
			arr.sort((a, b) => Number(a.plan) < Number(b.plan)  ?
				1 : Number(a.plan) > Number(b.plan) ? -1 : 0);

			if(this.itemsArray.length > 3) {
				arr[0].show_cup = 1;
				arr[1].show_cup = 2;
				arr[2].show_cup = 3;
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
		fetchData() {
			let loader = this.$loading.show();

			this.records = this.activity.records;
			this.accountsNumber = this.activity.records.length

			if(this.show_headers) this.setFirstRowAsTotals()
			this.calculateRecordsValues()
			if(this.show_headers)  this.calculateTotalsRow()
			if(this.show_headers) this.setAvgCell()
			if(!this.show_headers) this.setLeaders()
			this.items = this.itemsArray;
			this.filtered = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			loader.hide();
		},



		updateTable(items) {
			let loader = this.$loading.show();

			this.records = items;
			this.calculateRecordsValues();
			if(this.show_headers) this.calculateTotalsRow();
			this.updateAvgValuesOfRecords();
			if(this.show_headers) this.setAvgCell()

			this.items = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			loader.hide();
		},
		setAvgCell() {
			this.itemsArray[0]['avg'] = (this.avgOfAverage / this.totalCountDays).toFixed(2);
			if(this.activity.plan_unit == 'minutes') {
				this.itemsArray[0]['avg'] = '';
			}
		},

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

			let total = 0, quantity = 0;

			for (let key in this.sum) {
				if (this.sum.hasOwnProperty(key)) {
					let sum = isNaN(parseFloat(this.sum[key])) ? 0 : parseFloat(this.sum[key]);
					let percentage = isNaN(parseFloat(this.percentage[key])) ? 0 : parseFloat(this.percentage[key]);
					if(this.activity.plan_unit == 'minutes')  {
						this.itemsArray[0][key] = parseFloat(sum).toFixed(0);
						if(sum != 0)  {
							total += sum;
							quantity++;
						}
					} else {
						this.itemsArray[0][key] = parseFloat(sum / percentage).toFixed(1);
						if(percentage != 0 && sum != 0) {
							total += parseFloat(sum / percentage);
							quantity++;
						}
					}
				} else {
					this.itemsArray[0][key] = 0;
				}
			}

			let avg;
			avg = quantity != 0 ? total / quantity : 0;
			console.log('TOTAL ' + total)
			console.log('AVG ' + avg)
			let plan = quantity == 0 ? '' : Number(avg).toFixed(1);
			this.itemsArray[0]['plan'] = this.activity.unit == '%' && plan != '' ? plan + '%' : plan;

		},

		setCellVariants() {
			if (typeof this.activity === 'object') {

				let minutes = this.filtered;

				if(this.activity.plan_unit != 'less_sum') {

					minutes.forEach((account, index) => {
						if (index > 0) {
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
			// let year = new Date().getFullYear();

			this.updateTable(filtered);

			this.axios
				.post('/timetracking/update-settings-individually', {
					date: this.$moment(
						`${this.month.currentMonth} ${this.month.currentYear}`,
						'MMMM YYYY'
					).format('YYYY-MM-DD'),
					group_id: this.activity.group_id,
					employee_id: employee_id,
					day: key,
					table_type: this.activity.id,
					settings: this.itemsArray[index], // data of employee for 1 month
				})
				.then(() => {
					loader.hide();
				});

		},

		exportData() {
			var link = '/timetracking/analytics/activity/export';
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
				this.itemsArray = [{}];
			} else {
				this.itemsArray = [];
			}
			this.totalCountDays = 0;
			this.avgOfAverage = 0;
			this.percentage = []

			let row0_avg = 0;
			let row0_avg_items = 0;

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

						let average = (sumForOne / countWorkedDays).toFixed(0);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['avg'] = finishAverage;

						cellValues['month'] = account.applied_from != 0 ? Number(account.applied_from) * daily_plan : Number(this.activity.workdays) * daily_plan;

						cellValues['percent'] = this.toFloat(
							Number(sumForOne) / (Number(cellValues['month']) / 100)
						) + '%';
						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);

						cellValues['plan'] = Number(sumForOne).toFixed(2);
					}

					if(this.activity.plan_unit == 'percent') {
						let average = (sumForOne / countWorkedDays).toFixed(2);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['month'] = daily_plan;
						cellValues['plan'] = finishAverage;
						cellValues['avg'] = finishAverage;

						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);

					}

					if(this.activity.plan_unit == 'less_avg') {
						let average = (sumForOne / countWorkedDays).toFixed(2);
						let finishAverage = !isNaN(average) ? average : 0;
						cellValues['month'] = daily_plan;
						cellValues['plan'] = finishAverage;


						this.avgOfAverage = parseFloat(this.avgOfAverage) + parseFloat(finishAverage);
					}

					if(this.activity.plan_unit == 'less_sum') {
						cellValues['month'] = daily_plan;
						cellValues['plan'] =  Number(sumForOne).toFixed(0);

						this.avgOfAverage = parseFloat(this.avgOfAverage) + Number(sumForOne);
					}
				}

				if((this.user_types == 1 && account.fired == 1) || (this.user_types == 0 && account.fired == 0)) {
					this.itemsArray.push({
						name: account.name,
						lastname: account.lastname,
						id: account.id,
						editable: false,
						group: account.group,
						fired: account.fired,
						show_cup: 0,
						applied_from: account.applied_from == 0 ? this.activity.workdays : account.applied_from,
						full_time: account.full_time,
						email: account.email,
						...cellValues,
					});
				}

			});

			this.records.forEach(account => {
				if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {

					row0_avg += parseFloat(account['plan']);
					row0_avg_items++;
				}
			})

			let a = row0_avg / row0_avg_items;
			if(this.show_headers) this.itemsArray[0]['plan'] = isNaN(a) ? '' : Number(a).toFixed(2);
		},

		toFloat(number) {
			return Number(number).toFixed(2);
		},
	},
};
</script>

<style lang="scss">
.my-table.m2 tr .badge {
    opacity: 1;
}

.day-minute {
    padding: 0 !important;
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
    background: #d9edff;
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
    background: #007bff;
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
.bg-white {
    background: white;
}
.text-white {
    color:#fff;
}
</style>
