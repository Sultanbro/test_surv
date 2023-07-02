<template>
	<div class="mb-3">
		<h4 class="d-flex align-items-center justify-content-between">
			<div class="mr-2" />
			<div>
				<!-- <a href="#" @click="exportData()" class="btn btn-success btn-sm">
                <i class="far fa-file-excel"></i>
                Экспорт</a> -->
				<div v-if="is_admin && tenant === 'bp'">
					<!-- Ozon -->
					<a
						@click="showExcelImport = !showExcelImport"
						class="btn btn-success btn-sm rounded mr-2 text-white"
					>
						<i class="fa fa-upload" />
						Импорт</a>
				</div>
			</div>
			<!-- <b-button  size="sm" variant="primary" @click=""><i class="fa fa-pencil"></i>  Редактирование таблицы</b-button> -->
		</h4>

		<div class="table-container">
			<table
				:id="'sticky-'+ activity.id"
				class="table table-bordered table-responsive r-to"
			>
				<thead>
					<tr>
						<th class="b-table-sticky-column text-left px-1 t-name bg-white sticky-h1 z2233">
							<div class="wd">
								ФИО
								<i
									v-if="is_admin"
									class="fa fa-sort ml-2"
									@click="sort('fullname')"
								/>
							</div>
						</th>

						<th
							class="text-left px-1 sticky-h1"
							style="z-index: 5;"
							rowspan="2"
						>
							<div
								v-if="is_admin"
								class="wd"
							>
								Итог к выдаче
								<i
									v-if="is_admin"
									class="fa fa-sort ml-2"
									@click="sort('plan')"
								/>
							</div>
							<div
								class="wd"
								v-else
							/>
						</th>

						<th
							v-if="is_admin"
							class="text-left px-1 sticky-h1"
							rowspan="2"
						>
							<div class="wd">
								Сборы
								<i
									class="fa fa-sort ml-2"
									@click="sort('count')"
								/>
							</div>
						</th>

						<th
							v-for="day in month.daysInMonth"
							:key="day"
							class="text-center px-1 sticky-h1"
							colspan="2"
						>
							<div>{{ day }}</div>
						</th>
					</tr>
					<tr>
						<th
							class="b-table-sticky-column sticky-h2"
							style="z-index: 5;"
						/>
						<template v-for="day in month.daysInMonth">
							<th
								:key="'a' + day"
								class="sticky-h2"
							>
								сборы
							</th>
							<th
								:key="'b' + day"
								class="sticky-h2"
							>
								тенге
							</th>
						</template>
					</tr>
				</thead>
				<tbody>
					<tr
						v-for="(item, index) in items"
						:key="index"
					>
						<td
							class="table-primary b-table-sticky-column text-left px-2 t-name wdf"
							:title="item.id + ' ' + item.email"
						>
							<div class="wd d-flex align-items-center">
								{{ item.lastname }} {{ item.name }}
								<b-badge variant="success">
									Office
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

						<td>{{ item.plan }}</td>
						<td>{{ item.count }}</td>
						<template v-for="day in month.daysInMonth">
							<td
								v-if="item.editable"
								:key="day"
								:class="'lb px-0 day-minute text-center Fri table-' + item._cellVariants[day]"
								:title="day + ': сборы'"
							>
								<div>
									<input
										v-model="item[day]"
										type="number"
										@change="updateSettings($event, item, index, day)"
										class="form-control cell-input"
									>
								</div>
							</td>
							<td
								v-else
								:key="day + 'a'"
								:title="day + ': сборы'"
								@click="editMode(item)"
								:class="'lb px-0 day-minute text-center Fri table-' + item._cellVariants[day]"
							>
								<div>{{ item[day] }}</div>
							</td>

							<td
								v-if="!isNaN(Number(item[day]) * price)"
								:key="day + 'b'"
								:title="day + ': тенге'"
								class="rb"
							>
								{{ Number(item[day]) * price }}
							</td>
							<td
								v-else
								:key="day + 'c'"
								:title="day + ': тенге'"
								class="rb"
							/>
						</template>
					</tr>
				</tbody>
			</table>
		</div>

		<Sidebar
			v-if="showExcelImport"
			title="Импорт EXCEL"
			:open="showExcelImport"
			@close="showExcelImport=false"
			width="75%"
		>
			<ActivityExcelImport
				:group_id="42"
				table="minutes"
				@close="showExcelImport=false"
				:activity_id="activity.id"
			/>
		</Sidebar>
	</div>
</template>

<script>
import Sidebar from '@/components/ui/Sidebar'
import ActivityExcelImport from '@/components/imports/ActivityExcelImport' // импорт в активности

export default {
	name: 'TActivityCollection',
	components: {
		Sidebar,
		ActivityExcelImport,
	},
	props: {
		month: Object,
		activity: Object,
		is_admin: Boolean,
		price: {
			default: 50,
		}
	},
	data() {
		return {
			items: [],
			sorts: {},
			fields: [],
			itemsArray: [],
			avgOfAverage: 0,
			showExcelImport: false,
			totalCountDays: 0,
			sum: {},
			percentage: [],
			records: [],
			totalRowName: '',
			accountsNumber: 0,
			tenant: location.hostname.split('.')[0]
		};
	},
	watch: {
		activity() {
			this.fetchData();
		},
	},
	created() {
		this.fetchData();
	},
	mounted() {
		document.getElementById('sticky-' + this.activity.id).style.height = window.innerHeight + 'px';
	},
	methods: {
		setFirstRowAsTotals() {
			this.totalRowName = 'Сумма сборов'
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
			});
		},
		fetchData() {
			let loader = this.$loading.show();

			this.records = this.activity.records;
			this.accountsNumber = this.activity.records.length
			if(this.is_admin) this.setFirstRowAsTotals()
			this.calculateRecordsValues()
			if(this.is_admin) this.calculateTotalsRow()
			if(!this.is_admin) this.setLeaders()
			if(this.is_admin) this.setAvgCell()

			this.items = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			loader.hide();
		},
		updateTable(items) {
			let loader = this.$loading.show();

			this.records = items;
			this.calculateRecordsValues();
			if(this.is_admin) this.calculateTotalsRow();
			if(!this.is_admin) this.setLeaders()
			this.updateAvgValuesOfRecords();

			if(this.is_admin) this.setAvgCell()
			this.totalColumn()

			this.items = this.itemsArray;

			this.addCellVariantsArrayToRecords();
			this.setCellVariants();
			loader.hide();
		},

		setLeaders() {
			let arr = this.itemsArray;

			// let first_item = this.itemsArray[0];
			//this.itemsArray.shift();
			arr.sort((a, b) => Number(a.plan) < Number(b.plan)
				? 1
				: Number(a.plan) > Number(b.plan)
					? -1
					: 0);

			if(this.itemsArray.length > 3) {
				arr[0].show_cup = 1;
				arr[1].show_cup = 2;
				arr[2].show_cup = 3;
			}

			// this.itemsArray.unshift(first_item);
		},

		totalColumn() {
			let row0_avg = 0;
			this.itemsArray.forEach(account => {
				if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {
					row0_avg += parseFloat(account['plan']);
				}
			})

			if(this.is_admin) this.itemsArray[0]['plan'] = row0_avg
		},
		setAvgCell() {
			this.itemsArray[0]['avg'] = (this.avgOfAverage / this.totalCountDays).toFixed(2);
			this.itemsArray[0]['avg'] = '';
		},


		calculateTotalsRow() {
			let total = 0
			// вот здесь я считаю итоговые суммы минут по всем сотрудникам, и мне их видимо придется сохранить в бд
			for (let key in this.sum) {
				if (this.sum.hasOwnProperty(key)) {
					this.itemsArray[0][key] = parseFloat(this.sum[key]).toFixed(0);
					total += parseFloat(this.sum[key])
				}
				else {
					this.itemsArray[0][key] = 0;
				}
			}

			if(this.is_admin) this.itemsArray[0]['plan'] = parseFloat(total) * this.price
		},

		setCellVariants() {
			if (typeof this.activity === 'object') {
				const SPECIAL_VALUE = this.activity.daily_plan;
				let minutes = this.items;
				minutes.forEach((account, index) => {
					if (index > 0 || !this.is_admin) {
						for (let key in account) {
							if (key >= 1 && key <= 31) {
								if (
									account[key] >= SPECIAL_VALUE
									&& account[key] !== undefined
									&& account[key] !== null
								) {
									this.items[index]._cellVariants[key] = 'success';
								}
								else if (
									account[key] < SPECIAL_VALUE
									&& account[key] !== undefined
									&& account[key] !== null
								) {
									this.items[index]._cellVariants[key] = 'danger';
								}
							}
						}
					}
				});
			}
		},

		editMode(item) {
			if(!this.is_admin) return null;
			this.items.forEach(account => {
				account.editable = false
			})
			item.editable = true
		},

		updateSettings(e, data, index, key) {
			data.editable = false

			//var index = data.index;
			var clearedValue = e.target.value.replace(',', '.');

			var value = parseFloat(clearedValue) || null;

			if(value < 0) {
				this.items[index][key] = 0;
			}

			if(value > 999) {
				this.items[index][key] = 999;
			}

			this.items[index][key] = Number(this.items[index][key])

			// let settings = [];
			let employee_id = data.id;

			let items = this.items;

			let loader = this.$loading.show();
			// let year = new Date().getFullYear();

			this.updateTable(items);

			this.axios.post('/timetracking/analytics/update-stat', {
				month: this.month.month,
				year: this.month.currentYear,
				group_id: this.activity.group_id,
				employee_id: employee_id,
				id: this.activity.id,
				day: key,
				value: '' + (value || 0),
			}).then(() => {
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
			window.location.href = link;
		},

		calculateRecordsValues() {
			this.sum = {};
			this.itemsArray = [];
			this.totalCountDays = 0;
			this.avgOfAverage = 0;
			this.percentage = []

			// let row0_avg = 0;
			// let row0_avg_items = 0;

			this.records.forEach(account => {
				// let countWorkedDays = 0;
				let cellValues = [];

				if (account.name != this.totalRowName) {
					let sumForOne = 0;
					for (let key in account) {
						let value = account[key];

						if (key >= 1 && key <= 31) {
							cellValues[key] = Number(value);

							if (isNaN(this.sum[key])) this.sum[key] = 0;

							if (isNaN(this.percentage[key])) this.percentage[key] = 0;

							this.sum[key] = this.sum[key] + account[key]; // vertical sum

							if(account[key] > 0) {
								this.percentage[key] = this.percentage[key] + 1;
							}

							if (account[key] > 0) {
								sumForOne += account[key]; // horizontal sum
								// countWorkedDays++;
								this.totalCountDays++;
							}
						}
					}

					cellValues['plan_unit'] = this.activity.plan_unit;
					cellValues['plan'] = sumForOne * this.price;
					cellValues['count'] = sumForOne;
				}

				this.itemsArray.push({
					name: account.name,
					lastname: account.lastname,
					fullname: account.fullname,
					id: account.id,
					editable: false,
					group: account.group,
					email: account.email,
					show_cup: 0,
					...cellValues,
				});
			});
		},

		toFloat(number) {
			return Number(number).toFixed(2);
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
		background-color: #fd1600 !important;
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
.z2233 {
	z-index: 3 !important;
}
.r-to .wd .badge-success {
	display: none;
}
.r-to tr:hover td.wdf  {
	background: #51a5ff;
}
.r-to tr:hover td {
	background: #eaf3ff;
}
.lb {
	border-left: 1px solid #aaa !important;
}
.rb {
	border-right: 1px solid #aaa !important;
	background: whitesmoke;
}
.sticky-h1 {
	position: sticky;
	top: 0;
	z-index: 2;
	outline: 1px solid #ddd!important;
}
.sticky-h2 {
	position: sticky;
	top: 31px;
	z-index: 2;
	outline: 1px solid #ddd!important;
}
</style>
