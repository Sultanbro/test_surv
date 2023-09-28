<template>
	<div class="TableDecomposition mb-3">
		<div class="table-container">
			<table class="TableDecomposition-table table table-bordered table-responsive">
				<thead class="TableDecomposition-thead">
					<tr class="TableDecomposition-tr">
						<th class="TableDecomposition-th b-table-sticky-column text-left">
							<div class="wd TableDecomposition-header">
								Декомпозиция на месяц
							</div>
						</th>
						<th
							class="TableDecomposition-th text-center px-1 border-r-2"
							colspan="2"
						>
							<b>Итого</b>
						</th>
						<th
							v-for="day in month.daysInMonth"
							:key="day"
							class="TableDecomposition-th text-center px-1 border-r-2"
							:class="{
								'weekend' : is_weekday[day],
							}"
							colspan="2"
						>
							<div>{{ day }}</div>
						</th>
					</tr>
					<tr class="TableDecomposition-tr TableDecomposition-subhead">
						<th class="TableDecomposition-th b-table-sticky-column text-left" />
						<th class="TableDecomposition-th">
							план
						</th>
						<th class="TableDecomposition-th">
							факт
						</th>
						<template v-for="day in month.daysInMonth">
							<th
								:key="day"
								class="TableDecomposition-th"
							>
								план
							</th>
							<th
								:key="day + 'a'"
								class="TableDecomposition-th border-r-2"
							>
								факт
							</th>
						</template>
					</tr>
				</thead>
				<tbody class="TableDecomposition-tbody">
					<tr
						v-for="(item, index) in items"
						:key="index"
						class="TableDecomposition-tr"
					>
						<td
							class="TableDecomposition-td b-table-sticky-column text-left px-2 t-name"
							:title="item.id"
						>
							<div class="wd d-flex align-items-center">
								<input
									v-model="item.name"
									type="text"
									class="TableDecomposition-inputName w-250 text-left"
									placeholder="напишите название активности"
									@change="updateSettings($event, item, index, 'name')"
								>
								<i
									class="fa fa-pencil pointer mr-2"
									title="Поставить план"
									@click="showModal(index)"
								/>
								<i
									class="fa fa-trash pointer"
									title="Удалить строку"
									@click="deleteRecord(item.id, index)"
								/>
							</div>
						</td>
						<td class="TableDecomposition-td">
							{{ Number(item.total_plan).toFixed(0) }}
						</td>
						<td class="TableDecomposition-td border-r-2">
							{{ Number(item.total_fact).toFixed(0) }}
						</td>
						<template v-for="day in month.daysInMonth">
							<td
								v-if="item.editable"
								:key="day"
								class="TableDecomposition-td px-0 day-minute text-center"
								:class="{
									'weekend' : is_weekday[day],
								}"
							>
								<input
									v-model="item[day].plan"
									type="number"
									class="TableDecomposition-inputNumber"
									@change="updateSettings($event, item, index, day)"
								>
							</td>
							<td
								v-else
								:key="day + 'a'"
								class="TableDecomposition-td px-0 day-minute text-center"
								:class="{
									'weekend' : is_weekday[day],
								}"
								@click="editMode(item)"
							>
								<div>{{ item[day].plan }}</div>
							</td>
							<td
								v-if="item.editable"
								:key="day + 'b'"
								class="TableDecomposition-td px-0 day-minute text-center border-r-2"
								:class="{
									'table-danger': Number(item[day].fact) != 0 && Number(item[day].plan) > Number(item[day].fact),
									'table-success': Number(item[day].fact) != 0 && Number(item[day].plan) <= Number(item[day].fact),
									'weekend' : is_weekday[day],
								}"
							>
								<input
									v-model="item[day].fact"
									type="number"
									class="TableDecomposition-inputNumber"
									@change="updateSettings($event, item, index, day)"
								>
							</td>
							<td
								v-else
								:key="day + 'c'"
								class="TableDecomposition-td px-0 day-minute text-center border-r-2"
								:class="{
									'table-danger': Number(item[day].fact) != 0 && Number(item[day].plan) > Number(item[day].fact),
									'table-success': Number(item[day].fact) != 0 && Number(item[day].plan) <= Number(item[day].fact),
									'weekend' : is_weekday[day],
								}"
								@click="editMode(item)"
							>
								<div>{{ item[day].fact }}</div>
							</td>
						</template>
					</tr>
				</tbody>
			</table>
		</div>
		<button
			class="btn btn-success"
			@click="addRecord"
		>
			+ Добавить
		</button>

		<b-modal
			ref="change-plan-modal"
			hide-footer
			title="Проставить план"
		>
			<div class="row">
				<div class="col-6">
					<label for="">С:</label>
					<input
						v-model.number="planner.from"
						type="number"
						class="form-control form-control-sm mb-2"
						min="1"
						max="31"
					>
				</div>
				<div class="col-6">
					<label for="">По:</label>
					<input
						v-model.number="planner.to"
						type="number"
						class="form-control form-control-sm mb-2"
						min="1"
						max="31"
					>
				</div>
				<div class="col-6">
					<label for="">План:</label>
					<input
						v-model.number="planner.value"
						type="number"
						class="form-control form-control-sm mb-2"
						placeholder="План"
						min="1"
					>
				</div>
				<div class="col-12 mt-3">
					<b-button
						class="mt-3"
						variant="primary"
						block
						@click="changePlan"
					>
						Проставить
					</b-button>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<script>
/* eslint-disable camelcase */

export default {
	name: 'TableDecomposition',
	props: {
		month: {
			type: Object,
			default: () => ({})
		},
		data: {
			type: Object,
			default: () => ({})
		},
	},
	data() {
		return {
			items: [],
			fields: [],
			itemsArray: [],
			records: [],
			is_weekday: {},
			planner: {
				value: null,
				from: 1,
				to: null,
				index: null,
			}
		};
	},
	watch: {
		data: function() {
			this.fetchData();
		},
	},
	created() {
		this.fetchData();

		this.getWeekdays()
		this.planner.to = this.month.daysInMonth
	},

	methods: {
		getWeekdays() {
			for (let i = 1; i <= this.month.daysInMonth; i++) {
				let day = i > 9 ? i : '0' + i;
				let month = Number(this.month.month) > 9 ? this.month.month : '0' + this.month.month;
				let date = new Date(this.month.currentYear + '-' + month  + '-' + day)
				if(date.getDay() == 0 || date.getDay() == 6) {
					this.is_weekday[i] = true
				}
				else {
					this.is_weekday[i] = false
				}
			}
		},

		fetchData() {
			let loader = this.$loading.show();

			this.records = this.data.records;
			this.accountsNumber = this.data.records.length

			this.calculateRecordsValues()
			//this.calcTotals()

			this.items = this.itemsArray;
			loader.hide();
		},

		addRecord() {
			let cells = {
				name:'',
				plan: 0,
				editable: false,
			};

			for (let i = 1; i <= this.month.daysInMonth; i++) {
				cells[i] = {
					'plan' : '',
					'fact' : '',
				}
			}

			this.items.push(cells)

			this.$toast.info('Пункт добавлен')
		},

		updateSettings(e, data, index) {
			data.editable = false
			this.updateTable(this.items);

			let post_data = {
				group_id: this.data.group_id,
				id: data.id,
				name: data.name,
				index: index,
				values: this.items[index],
			};

			this.reqSave(post_data);
		},

		reqSave(post_data) {
			let url = '/timetracking/analytics/decomposition/save';
			let loader = this.$loading.show();
			let self = this.items;
			let year = new Date().getFullYear();

			post_data.date = this.$moment(
				`${this.month.currentMonth} ${year}`,
				'MMMM YYYY'
			).format('YYYY-MM-DD');

			this.axios.post(url, post_data).then((response) => {
				if(post_data.id === undefined) {
					self[post_data.index].id = response.data.id
				}
			}).catch(error => {
				alert(error)
			});
			loader.hide();
		},

		showModal(index) {
			this.planner.index = index;
			this.$refs['change-plan-modal'].show();
		},

		changePlan() {
			if (this.planner.value != null && this.planner.value != '') {

				// Set plans from to
				let start = this.planner.from,
					end = this.planner.to > this.month.daysInMonth ? this.month.daysInMonth : this.planner.to;

				for (let i = start; i <= end; i++) {
					this.items[this.planner.index][i].plan = Number(this.planner.value);
				}

				// POST
				this.reqSave({
					group_id: this.data.group_id,
					id: this.items[this.planner.index].id,
					name: this.items[this.planner.index].name,
					values: this.items[this.planner.index],
					index: this.planner.index
				});

				// Count total plan
				let total_plan = 0;

				for (let i = 1; i <= this.month.daysInMonth; i++) {
					total_plan += Number(this.items[this.planner.index][i].plan);
				}
				this.items[this.planner.index].total_plan = total_plan
				// Reset planner
				this.planner = {
					value: null,
					from: 1,
					to: this.month.daysInMonth,
					index: null,
				}
				this.$toast.info('План проставлен')
			}

			// Hide modal
			this.$refs['change-plan-modal'].hide();
		},

		deleteRecord(id, index) {
			if (!confirm('Вы уверены?')) {
				return '';
			}

			if(id != 0) {
				let url = '/timetracking/analytics/decomposition/delete';

				this.axios.delete(url, {
					headers: {},
					data: {
						id: id
					}
				}).then(() => {
					this.$toast.info('Пункт удален')
				}).catch(error => {
					alert(error)
				});
			}

			this.items.splice(index, 1);
		},

		calculateRecordsValues() {
			this.sum = {};
			this.itemsArray = [];

			this.records.forEach(item => {

				let cellValues = [],
					totalPlan = 0,
					totalFact = 0;

				for (let i = 1; i <= this.month.daysInMonth; i++) {
					cellValues[i] = item[i];

					if(item[i] === undefined) {
						cellValues[i] = {
							'plan': '',
							'fact': ''
						};
						continue;
					}
					if(item[i].plan !== undefined) {
						totalPlan += Number(item[i].plan)
					}

					if(item[i].fact !== undefined) {
						totalFact += Number(item[i].fact)
					}
				}

				this.itemsArray.push({
					name: item.name,
					id: item.id,
					editable: false,
					total_plan: totalPlan,
					total_fact: totalFact,
					group_id: this.data.group_id,
					...cellValues,
				});
			});
		},

		updateTable(items) {
			let loader = this.$loading.show();

			this.records = items;
			this.calculateRecordsValues();

			this.totalColumn()
			this.items = this.itemsArray;

			loader.hide();
		},

		addCellVariantsArrayToRecords(){
			this.itemsArray.forEach((element, key) => {
				this.itemsArray[key]['_cellVariants'] = [];
			});
		},

		totalColumn() {
			// let row0_avg = 0;
			// this.itemsArray.forEach((account, index) => {
			//     if(parseFloat(account['plan']) != 0 && account['plan'] != undefined) {
			//         row0_avg += parseFloat(account['plan']);
			//         console.log(account['plan'])
			//     }
			// })

			// this.itemsArray[0]['plan'] = row0_avg
		},

		editMode(item) {
			this.items.forEach(account => {
				account.editable = false
			})
			item.editable = true
		},

		toFloat(number) {
			return Number(number).toFixed(2);
		},
	},
};
</script>

<style lang="scss">
.TableDecomposition{
	&-inputName{
		&::placeholder{
			font-style: italic;
		}
	}
	&-inputNumber{
		display: block;
		width: calc(100% + 20px);
		margin: 0 -10px;
		background: transparent;
		-moz-appearance: textfield;
		text-align: center;
		&::-webkit-outer-spin-button,
		&::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}
	}
	// &-table{}
	// &-thead{}
	&-header{
		font-size: 14px !important;
		font-weight: 700 !important;
	}
	&-subhead{
		.TableDecomposition-th{
			background-color: #dde9ff !important;
			border-color: #b5c1d7 !important;
		}
	}
	&-th{
		padding: 5px !important;
		font-size: 12px !important;
	}
	&-td{
		padding: 5px !important;
	}
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
.w-250 {
	min-width: 250px;
}
.wd {
	.fa {
		opacity: 0;
	}
	&:hover {
		.fa {
			opacity: 1;
		}
	}
}

.day-minute div {
	font-size: 0.8rem;
	font-weight: 500;
}
.border-r-2 {
	border-right: 2px solid #b5c1d7 !important;
}
</style>
